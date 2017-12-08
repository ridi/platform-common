<?php

namespace Ridibooks\Platform\Common\Cron\Dto;

use Ridibooks\Platform\Common\Cron\Interfaced\HealthCheckPingServiceInterface;
use Ridibooks\Platform\Common\Cron\Interfaced\PlatformCronHistoryModelInterface;

class CronMasterConfigDto
{
    /** @var PlatformCronHistoryModelInterface */
    public $cron_history_model;
    /** @var HealthCheckPingServiceInterface */
    public $health_check_ping_service;
    /** @var string */
    public $file_lock_prefix;
    /** @var string */
    public $project_name;
    /** @var array */
    public $working_classes;
    /** @var bool */
    public $is_under_dev = true;

    /**
     * CronMaster constructor.
     *
     * @param PlatformCronHistoryModelInterface $cron_history_model
     * @param HealthCheckPingServiceInterface   $health_check_ping_service
     * @param string                            $file_lock_prefix
     * @param string                            $project_name
     * @param array                             $working_classes
     * @param bool                              $is_under_dev
     *
     * @return CronMasterConfigDto
     */
    public static function importFromInit(
        PlatformCronHistoryModelInterface $cron_history_model,
        HealthCheckPingServiceInterface $health_check_ping_service,
        string $file_lock_prefix,
        string $project_name,
        array $working_classes,
        bool $is_under_dev
    ): self {

        $dto = new self;

        $dto->cron_history_model = $cron_history_model;
        $dto->health_check_ping_service = $health_check_ping_service;
        $dto->file_lock_prefix = $file_lock_prefix;
        $dto->project_name = $project_name;
        $dto->working_classes = $working_classes;
        $dto->is_under_dev = $is_under_dev;

        return $dto;
    }
}
