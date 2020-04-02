<?php
declare(strict_types=1);

namespace Ridibooks\Platform\Common\Sentry;

class SentryHelper
{
    public const DEFAULT_RAVEN_CLIENT_NAME = '__RAVEN_CLIENT';

    public static function enableSentry(SentryClientInterface $client)
    {
        // TODO: Implement enableSentry() method.
    }

    /**
     * @param \Exception $e
     * @param string $raven_client_name
     * @return bool
     */
    public static function triggerSentryException(\Exception $e, $raven_client_name = self::DEFAULT_RAVEN_CLIENT_NAME)
    {
        // TODO: Implement triggerSentryException() method.
    }

    /**
     * @param string $string
     * @param array $params
     * @param array $level_or_options
     * @param string $raven_client_name
     * @return bool
     */
    public static function triggerSentryMessage(
        $string,
        $params = [],
        $level_or_options = [],
        $raven_client_name = self::DEFAULT_RAVEN_CLIENT_NAME
    ) {
        // TODO: Implement triggerSentryMessage() method.
    }

    /**
     * @param string $sentry_key
     * @param string $raven_client_name
     * @param array $options
     * @return Raven_Client
     */
    public static function registerRavenClient(
        $sentry_key,
        $raven_client_name = self::DEFAULT_RAVEN_CLIENT_NAME,
        $options = []
    ) {
        // TODO: Implement registerRavenClient() method.
    }

    /**
     * @param Raven_Client $raven_client
     * @param bool $call_existing
     * @param int|null $error_types
     */
    public static function registerHandlers($raven_client, $call_existing = true, $error_types = null)
    {
        // TODO: Implement registerHandlers() method.
    }

    /**
     * @param string $raven_client_name
     * @return bool
     */
    public static function isRavenClientInitialized($raven_client_name = self::DEFAULT_RAVEN_CLIENT_NAME)
    {
        // TODO: Implement isRavenClientInitialized() method.
    }

    /**
     * @param string $raven_client_name
     * @return Raven_Client|null
     */
    public static function getRavenClient($raven_client_name = self::DEFAULT_RAVEN_CLIENT_NAME)
    {
        // TODO: Implement getRavenClient() method.
    }
}
