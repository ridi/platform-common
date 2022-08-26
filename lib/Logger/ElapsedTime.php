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
    private $main_title;
    /** @var Logger */
    private $logger;
    /** @var int */
    private $init_start_time;
    /** @var int */
    private $start_time;
    /** @var string[] */
    private $titles;
    /** @var int[] */
    private $elapsed_times;

    public function __construct(string $main_title)
    {
        $this->main_title = $main_title;
        $this->logger = MonologHelper::createForCron('elapsed_time');
        $this->logger->info("{$main_title} INIT");
        $this->init(time());
    }

    public function start(): void
    {
        $now = time();
        $this->initStartTime($now);
    }

    public function endElapsed(string $title): void
    {
        $now = time();
        $end_elapsed = $now - $this->start_time;
        $this->initStartTime($now);
        $this->titles[] = $title;
        $this->elapsed_times[] = $end_elapsed;
    }

    public function endTotalElapsed(): void
    {
        $now = time();
        $end_total_elapsed = $now - $this->init_start_time;
        $titles_to_elapsed_times = array_combine($this->getTitles(), $this->getElapsedTimes());
        foreach ($titles_to_elapsed_times as $title => $elapsed_time) {
            $this->logger->info("{$this->main_title}'s {$title} ELAPSED TIME: {$elapsed_time}s");
        }

        $this->logger->info("{$this->main_title} TOTAL ELAPSED TIME: {$end_total_elapsed}s");

        $this->init($now);
    }

    /**
     * @return int[]
     */
    private function getElapsedTimes(): array
    {
        return $this->elapsed_times;
    }

    /**
     * @return string[]
     */
    private function getTitles(): array
    {
        return $this->titles;
    }

    private function init(int $now): void
    {
        $this->init_start_time = $now;
        $this->start_time = $now;
        $this->titles = [];
        $this->elapsed_times = [];
    }

    private function initStartTime(int $now): void
    {
        $this->start_time = $now;
    }
}
