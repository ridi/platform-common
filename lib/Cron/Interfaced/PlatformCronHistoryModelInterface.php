<?php

namespace Ridibooks\Platform\Common\Cron\Interfaced;

interface PlatformCronHistoryModelInterface
{
	/**
	 * @param string $cron_class_name
	 *
	 * @return string|null
	 */
	public function getLastTime(string $cron_class_name);
	public function insertLogExecuted(string $cron_class_name): bool;
}
