<?php
declare(strict_types=1);

namespace Ridibooks\Platform\Common\Sentry;

interface SentryClientInterface
{
    // TODO: enableSentry, overrideSentry 제거 완료 후 protected로 변경
    public const DEFAULT_ERROR_TYPES = E_ALL & ~E_NOTICE & ~E_STRICT;
    protected const DEFAULT_OPTIONS = ['attach_stacktrace' => true];

    public static function init($sentry_key, $options = [], $error_types = self::DEFAULT_ERROR_TYPES);
}
