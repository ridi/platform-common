<?php

declare(strict_types=1);

namespace Ridibooks\Platform\Common\Tests\Helper;

use Ridibooks\Platform\Common\PingService\AbstractPingUtils;

class ConcretePingUtils extends AbstractPingUtils
{
    protected function getBaseUrl(): string
    {
        return 'https://example.com';
    }

    protected function getMaxRetryCount(): int
    {
        return 1;
    }

    protected function getRetryWaitSeconds(): int
    {
        return 0;
    }
}
