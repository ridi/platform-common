<?php
declare(strict_types=1);

namespace Ridibooks\Platform\Common\Sentry\Clients;

use Ridibooks\Platform\Common\Sentry\SentryClientInterface;

class LumenSentryClient implements SentryClientInterface
{
    public static function init($sentry_key, $options = [], $error_types = self::DEFAULT_ERROR_TYPES)
    {
        app()->register('Sentry\Laravel\ServiceProvider');
        self::updateConfig($sentry_key, $options, $error_types);
    }

    private static function updateConfig(string $sentry_key, array $options, int $error_types): void
    {
        $option_namespace = \Sentry\Laravel\ServiceProvider::$abstract;
        $default_options = ['dsn' => $sentry_key, 'error_types' => $error_types];

        $configs = [];
        foreach (array_merge($default_options, $options) as $key => $value) {
            $configs["{$option_namespace}.{$key}"] = $value;
        }
        config($configs);
    }
}
