<?php
declare(strict_types=1);

namespace Ridibooks\Platform\Common\Sentry\Clients;

use Ridibooks\Platform\Common\Sentry\SentryClientInterface;
use Sentry\Severity;

class SentryClientClient implements SentryClientInterface
{
    private const DEFAULT_ERROR_TYPES = E_ALL & ~E_NOTICE & ~E_STRICT;

    public function __construct($sentry_key, $options = [], $error_types = self::DEFAULT_ERROR_TYPES)
    {
        $this->registerRavenClient($sentry_key, $options, $error_types);
    }

    /**
     * @param \Exception $e
     * @return bool
     */
    public function triggerSentryException(\Exception $e)
    {
        $response = \Sentry\captureException($e);

        return $response !== null;
    }

    /**
     * @param string $string
     * @param Severity|null $level
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
}
