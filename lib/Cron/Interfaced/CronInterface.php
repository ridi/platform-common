<?php

namespace Ridibooks\Platform\Common\Cron\Interfaced;

interface CronInterface
{
    public function isTimeToRun(\DateTime $last_executed_datetime): bool;
    public function run(): bool;
}
