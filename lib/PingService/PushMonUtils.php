<?php

namespace Ridibooks\Platform\Common\PingService;

class PushMonUtils extends AbstractPingUtils
{
    protected function getBaseUrl(): string
    {
        return 'http://pshmn.com/';
    }
}
