<?php
declare(strict_types=1);

namespace Ridibooks\Platform\Common\Sentry;

interface SentryClientInterface
{
    public const DEFAULT_ERROR_TYPES = E_ALL & ~E_NOTICE & ~E_STRICT;
    public const DEFAULT_OPTIONS = ['attach_stacktrace' => true];

    public static function init($sentry_key, $options = [], $error_types = self::DEFAULT_ERROR_TYPES);
}
