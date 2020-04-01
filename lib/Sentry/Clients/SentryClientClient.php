<?php
declare(strict_types=1);

namespace Ridibooks\Platform\Common\Sentry\Clients;

use Ridibooks\Platform\Common\Sentry\SentryClientInterface;
use Sentry\SentrySdk;
use Sentry\Severity;

class SentryClientClient implements SentryClientInterface
{
    private const DEFAULT_ERROR_TYPES = E_ALL & ~E_NOTICE & ~E_STRICT;

    public function __construct($sentry_key, $options = [], $error_types = self::DEFAULT_ERROR_TYPES)
    {
        $this->registerRavenClient($sentry_key, $options, $error_types);
        self::registerHandlers($client, true, $error_types);
    }

    /**
     * @param \Exception $e
     * @param string $raven_client_name
     * @return bool
     */
    public function triggerSentryException(\Exception $e)
    {
        $response = \Sentry\captureException($e);

        return $response !== null;
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

    private function registerRavenClient(string $sentry_key, array $options, int $error_types): void
    {
        $default_options = ['dsn' => $sentry_key, 'error_types' => $error_types];
        \Sentry\init(array_merge($default_options, $options));
    }

    /**
     * @param Raven_Client $raven_client
     * @param bool $call_existing
     * @param int|null $error_types
     */
    public static function registerHandlers($raven_client, $call_existing = true, $error_types = null)
    {
        // TODO ...
        $error_handler = new \Raven_ErrorHandler($raven_client);
        $error_handler->registerExceptionHandler($call_existing);
        $error_handler->registerErrorHandler($call_existing, $error_types);
        $error_handler->registerShutdownFunction($call_existing);
    }

    public static function isRavenClientInitialized(): bool
    {
        return SentrySdk::getCurrentHub()->getClient() !== null;
    }
}
