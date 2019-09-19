<?php

namespace Ridibooks\Platform\Common\PingService;

class PushMonUtils extends AbstractPingUtils
{
    protected static function getBaseUrl(): string
    {
        return 'http://pshmn.com/';
    }
}
