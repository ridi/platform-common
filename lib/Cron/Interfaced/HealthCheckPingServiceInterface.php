<?php

namespace Ridibooks\Platform\Common\Cron\Interfaced;

interface HealthCheckPingServiceInterface
{
	public function ping(): array;
}
