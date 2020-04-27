<?php

namespace Ridibooks\Platform\Common\PingService;

class HealthCheckUtils extends AbstractPingUtils
{
    protected function getBaseUrl(): string
    {
        return 'https://hchk.io/';
    }
}
