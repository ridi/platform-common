<?php

namespace Ridibooks\Platform\Common\Cron;

use Exception;
use Ridibooks\Exception\MsgException;
use Ridibooks\Library\SentryHelper;
use Ridibooks\Platform\Common\Cron\Dto\CronMasterConfigDto;
use Ridibooks\Platform\Common\Cron\Interfaced\CronInterface;

class CronMaster
{
	/** @var CronMasterConfigDto */
	private $config_dto;

	public function __construct(CronMasterConfigDto $config_dto)
	{
		$this->config_dto = $config_dto;
	}

	public function run()
	{
		$pid_to_cron_class = [];
		foreach ($this->config_dto->working_classes as $cron_class) {
			try {
				/** @var $cron CronInterface */
				$cron = new $cron_class;
				$lock_fd = $this->tryLock($this->getShortClassName($cron));
				if (!$lock_fd) {
					continue;
				}

				$pid = pcntl_fork();
				if ($pid === -1) {
					throw new MsgException('could not fork : ' . $cron_class);
				} elseif ($pid > 0) { //parent
					$pid_to_cron_class[$pid] = $cron_class;
					continue;
				}

				$this->runChildProcess($cron);

				fclose($lock_fd);

				return;
			} catch (\Exception $e) {
				SentryHelper::triggerSentryException($e);
			}
		}

		//waiting for children
		while (true) {
			$pid = pcntl_waitpid(0, $status);
			if ($pid === -1) {
				break;
			}

			if (!pcntl_wifexited($status)) {
				$message = 'process not normal exit : ' . $pid_to_cron_class[$pid] . ' / ' . $status;
				SentryHelper::triggerSentryMessage($message);
			}

			$first_pid = array_keys($pid_to_cron_class)[0];
			if ($pid === $first_pid) {
				$this->notifyCronIsRunning();
			}
		}
	}

	private function runChildProcess(CronInterface $cron)
	{
		try {
			$cron_unique_name = $this->getShortClassName($cron);

			$last_executed_datetime = $this->getLastExecutedDatetime($cron_unique_name);
			if (!$cron->isTimeToRun($last_executed_datetime)) {
				return;
			}
			if ($cron->run()) {
				$this->config_dto->cron_history_model->insertLogExecuted($cron_unique_name);
			}
		} catch (MsgException $e) {
			SentryHelper::triggerSentryException($e);
		}
	}

	private function notifyCronIsRunning()
	{
		if ($this->config_dto->is_under_dev) {
			return;
		}

		$this->config_dto->health_check_ping_service->ping();
	}

	private function getLastExecutedDatetime(string $cron_class_name): \DateTime
	{
		$last_time = $this->config_dto->cron_history_model->getLastTime($cron_class_name);
		if (!empty($last_time)) {
			return new \DateTime($last_time);
		}

		return new \DateTime('1970-01-01 00:00:00 GMT');
	}

	/**
	 * @param string $lock_unique_name
	 *
	 * @return resource|null|bool
	 * @throws Exception
	 */
	private function tryLock(string $lock_unique_name)
	{
		$lock = fopen(
			sys_get_temp_dir() . '/' . $this->config_dto->file_lock_prefix . '.'
			. $this->config_dto->project_name . '.' . $lock_unique_name . '.lock',
			'c+'
		);
		if (!$lock) {
			throw new Exception('failed on tryLock : ' . $lock_unique_name);
		}
		if (flock($lock, LOCK_EX | LOCK_NB)) {
			return $lock;
		}

		return null;
	}

	private function getShortClassName($obj): string
	{
		$path = explode('\\', get_class($obj));

		return array_pop($path);
	}
}
