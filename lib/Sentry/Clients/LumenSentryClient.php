<?php
declare(strict_types=1);

namespace Ridibooks\Platform\Common\Sentry\Clients;

use Ridibooks\Platform\Common\Sentry\SentryClientInterface;
use Sentry\Laravel\ServiceProvider;

class LumenSentryClient implements SentryClientInterface
{
    public static function init($sentry_key, $options = [], $error_types = self::DEFAULT_ERROR_TYPES)
    {
        app()->register('Sentry\Laravel\ServiceProvider');
        self::updateConfig($sentry_key, $options, $error_types);
    }

    private static function updateConfig(string $sentry_key, array $options, int $error_types): void
    {
        $option_namespace = ServiceProvider::$abstract;
        $options = array_merge(
            self::DEFAULT_OPTIONS,
            ['dsn' => $sentry_key, 'error_types' => $error_types],
            $options
        );

        $configs = [];
        foreach ($options as $key => $value) {
            $configs["{$option_namespace}.{$key}"] = $value;
        }
        config($configs);
    }
}
