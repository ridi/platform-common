<?php

namespace Ridibooks\Platform\Common\PingService;

class HealthCheckUtils extends AbstractPingUtils
{
    protected static function getBaseUrl(): string
    {
        return 'https://hchk.io/';
    }
}
