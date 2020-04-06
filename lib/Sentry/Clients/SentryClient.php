<?php
declare(strict_types=1);

namespace Ridibooks\Platform\Common\Sentry\Clients;

use Ridibooks\Platform\Common\Sentry\SentryClientInterface;
use function Sentry\init;

class SentryClient implements SentryClientInterface
{
    public static function init($sentry_key, $options = [], $error_types = self::DEFAULT_ERROR_TYPES)
    {
        self::registerRavenClient($sentry_key, $options, $error_types);
    }

    private static function registerRavenClient(string $sentry_key, array $options, int $error_types): void
    {
        $default_options = ['dsn' => $sentry_key, 'error_types' => $error_types];
        init(array_merge($default_options, $options));
    }
}
