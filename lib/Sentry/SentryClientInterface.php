<?php
declare(strict_types=1);

namespace Ridibooks\Platform\Common\Sentry;

use Sentry\Severity;

interface SentryClientInterface
{
    /**
     * @param \Exception $e
     * @return bool
     */
    public function triggerSentryException(\Exception $e);

    /**
     * @param string $string
     * @param Severity|null $level
     * @return bool
     */
    public static function triggerSentryMessage($string, ?Severity $level = null);
}
