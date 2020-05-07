<?php
declare(strict_types=1);

namespace Ridibooks\Platform\Common\Logger;

use Monolog\Logger;
use Ridibooks\Platform\Common\Util\MonologHelper;

/**
 * Class ElapsedTime
 * @package Ridibooks\Platform\Common\Logger
 */
class ElapsedTime
{
    /** @var string */
    private $group_title;
    /** @var Logger */
    private $logger;
    /** @var int */
    private $group_start_time;
    /** @var int */
    private $sub_group_start_time;

    public function __construct(string $group_title, ?Logger $logger = null)
    {
        $this->group_title = $group_title;
        $this->logger = $logger ?? MonologHelper::createForCron('elapsed_time');
        $this->logger->info("{$this->group_title} INIT");
        $this->init(time());
    }

    public function __destruct()
    {
        $this->endTotalElapsed();
    }

    private function init(int $now): void
    {
        $this->group_start_time = $now;
        $this->sub_group_start_time = $now;
    }

    public function start(): void
    {
        $now = time();
        $this->sub_group_start_time = $now;
    }

    public function endElapsed(string $sub_group_title): void
    {
        $now = time();
        $elapsed_time = $now - $this->sub_group_start_time;

        $this->logger->info("{$this->group_title}'s {$sub_group_title} ELAPSED TIME: {$elapsed_time}s");

        $this->sub_group_start_time = $now;
    }

    public function endTotalElapsed(): void
    {
        $now = time();
        $end_total_elapsed = $now - $this->group_start_time;

        $this->logger->info("{$this->group_title} TOTAL ELAPSED TIME: {$end_total_elapsed}s");

        $this->init($now);
    }
}
