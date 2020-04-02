<?php
declare(strict_types=1);

namespace Ridibooks\Platform\Common\Sentry\Clients;

use Ridibooks\Platform\Common\Sentry\SentryClientInterface;
use Sentry\Severity;

class LumenSentryClient implements SentryClientInterface
{
    private const DEFAULT_ERROR_TYPES = E_ALL & ~E_NOTICE & ~E_STRICT;

    public function __construct($sentry_key, $options = [], $error_types = self::DEFAULT_ERROR_TYPES)
    {
        app()->register('Sentry\Laravel\ServiceProvider');
        $this->updateConfig($sentry_key, $options, $error_types);
    }

    /**
     * @param \Exception $e
     * @throws \Exception
     * @return bool
     */
    public function triggerSentryException(\Exception $e)
    {
        throw $e;
    }

    /**
     * @param string $string
     * @param Severity|null $level_or_options
     * @return bool
     */
    public static function triggerSentryMessage($string, ?Severity $level = null)
    {
        $response = \Sentry\captureMessage($string, $level);

        return $response !== null;
    }

    private function updateConfig(string $sentry_key, array $options, int $error_types): void
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
