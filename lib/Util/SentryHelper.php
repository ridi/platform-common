<?php

namespace Ridibooks\Platform\Common\Util;

use Raven_Client;

class SentryHelper
{
    const DEFAULT_RAVEN_CLIENT_NAME = '__RAVEN_CLIENT';

    /**
     * @param string $sentry_key
     * @param array $options
     * @param int|null $error_types
     */
    public static function enableSentry($sentry_key, $options = [], $error_types = E_ALL & ~E_NOTICE & ~E_STRICT)
    {
        if (self::isRavenClientInitialized()) {
            trigger_error('Raven client is already installed.');
        }

        \Raven_Autoloader::register();

        $client = self::registerRavenClient($sentry_key, self::DEFAULT_RAVEN_CLIENT_NAME, $options);
        self::registerHandlers($client, true, $error_types);
    }

    /**
     * 기존에 enableSentry가 불린 경우 이를 무시하거나 추가로 등록하기 위해 사용한다.
     *
     * @param string $sentry_key
     * @param bool $call_existing 기존에 등록된 핸들러를 무시하려면 false, 오버로딩하려면 true
     * @param int|null $error_types
     */
    public static function overrideSentry($sentry_key, $call_existing = false, $error_types = E_ALL & ~E_NOTICE & ~E_STRICT)
    {
        $client = self::registerRavenClient($sentry_key);
        self::registerHandlers($client, $call_existing, $error_types);
    }

    /**
     * @param \Exception $e
     * @param string $raven_client_name
     * @return bool
     */
    public static function triggerSentryException(\Exception $e, $raven_client_name = self::DEFAULT_RAVEN_CLIENT_NAME)
    {
        if (!self::isRavenClientInitialized($raven_client_name)) {
            return false;
        }

        $client = self::getRavenClient($raven_client_name);
        if (!($client instanceof Raven_Client)) {
            return false;
        }

        $client->captureException($e);

        return true;
    }

    /**
     * @param string $string
     * @param array $params
     * @param array $level_or_options
     * @param string $raven_client_name
     * @return bool
     */
    public static function triggerSentryMessage($string, $params = [], $level_or_options = [], $raven_client_name = self::DEFAULT_RAVEN_CLIENT_NAME)
    {
        if (!self::isRavenClientInitialized($raven_client_name)) {
            return false;
        }

        $client = self::getRavenClient($raven_client_name);
        if (!($client instanceof Raven_Client)) {
            return false;
        }

        $client->captureMessage($string, $params, $level_or_options, true);

        return true;
    }

    /**
     * @param string $sentry_key
     * @param string $raven_client_name
     * @param array $options
     * @return Raven_Client
     */
    public static function registerRavenClient($sentry_key, $raven_client_name = self::DEFAULT_RAVEN_CLIENT_NAME, $options = [])
    {
        // processors의 기본값으로 Raven_SanitizeDataProcessor가 들어가서 제거함.
        // (LGD_OID, t_id등을 ********로 가림. 이후 개인정보 문제가 발생하는 케이스가 생기면 수정필요.)
        $default_options = ['processors' => []];
        $options_merged = array_merge($default_options, $options);

        $client = new \Raven_Client($sentry_key, $options_merged);
        $GLOBALS[$raven_client_name] = $client;
        return $client;
    }

    /**
     * @param Raven_Client $raven_client
     * @param bool $call_existing
     * @param int|null $error_types
     */
    private static function registerHandlers($raven_client, $call_existing = true, $error_types = null)
    {
        $error_handler = new \Raven_ErrorHandler($raven_client);
        $error_handler->registerExceptionHandler($call_existing);
        $error_handler->registerErrorHandler($call_existing, $error_types);
        $error_handler->registerShutdownFunction($call_existing);
    }

    /**
     * @param string $raven_client_name
     * @return bool
     */
    public static function isRavenClientInitialized($raven_client_name = self::DEFAULT_RAVEN_CLIENT_NAME)
    {
        return isset($GLOBALS[$raven_client_name]);
    }

    /**
     * @param string $raven_client_name
     * @return Raven_Client|null
     */
    public static function getRavenClient($raven_client_name = self::DEFAULT_RAVEN_CLIENT_NAME)
    {
        return self::isRavenClientInitialized($raven_client_name) ? $GLOBALS[$raven_client_name] : null;
    }
}
