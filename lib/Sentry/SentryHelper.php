<?php
declare(strict_types=1);

namespace Ridibooks\Platform\Common\Sentry;

use Ridibooks\Platform\Common\Sentry\Clients\SentryClient;
use Sentry\ClientInterface;
use Sentry\SentrySdk;
use Sentry\Severity;
use Sentry\State\Scope;
use function Sentry\captureException;
use function Sentry\captureMessage;
use function Sentry\withScope;

class SentryHelper
{
    public const DEFAULT_RAVEN_CLIENT_NAME = '__RAVEN_CLIENT';

    /**
     * @param string $sentry_key
     * @param array $options
     * @param int|null $error_types
     */
    public static function enableSentry($sentry_key, $options = [], $error_types = SentryClientInterface::DEFAULT_ERROR_TYPES)
    {
        if (self::isRavenClientInitialized()) {
            trigger_error('Raven client is already installed.');
        }

        SentryClient::init($sentry_key, $options, $error_types);
    }

    /**
     * 기존에 enableSentry가 불린 경우 이를 무시하거나 추가로 등록하기 위해 사용한다.
     *
     * @param string $sentry_key
     * @param bool $call_existing 기존에 등록된 핸들러를 무시하려면 false, 오버로딩하려면 true
     * @param int|null $error_types
     */
    public static function overrideSentry($sentry_key, $call_existing = false, $error_types = SentryClientInterface::DEFAULT_ERROR_TYPES)
    {
        if ($call_existing) {
            trigger_error('Not support call_existing');
        }

        SentryClient::init($sentry_key, [], $error_types);
    }

    public static function triggerSentryException(
        \Throwable $e,
        $raven_client_name = self::DEFAULT_RAVEN_CLIENT_NAME
    ): bool {
        $response = captureException($e);

        return $response !== null;
    }

    public static function triggerSentryMessage(
        string $message,
        array $params = [],
        $level_or_options = [],
        string $raven_client_name = self::DEFAULT_RAVEN_CLIENT_NAME
    ): bool {
        if (!empty($params)) {
            $formatted_message = vsprintf($message, $params);
        } else {
            $formatted_message = $message;
        }

        withScope(function (Scope $scope) use ($formatted_message, $level_or_options, &$response) {
            $level = Severity::info();
            if ($level_or_options instanceof Severity) {
                $level = $level_or_options;
            } elseif (is_array($level_or_options)) {
                if (
                    isset($level_or_options['level'])
                    && in_array($level_or_options['level'], Severity::ALLOWED_SEVERITIES)
                ) {
                    $level = new Severity($level_or_options['level']);
                }
            } elseif (is_string($level_or_options) && in_array($level_or_options, Severity::ALLOWED_SEVERITIES)) {
                $level = new Severity($level_or_options);
            }

            if (isset($level_or_options['user']) && is_array($level_or_options['user'])) {
                $scope->setUser($level_or_options['user']);
            }
            if (isset($level_or_options['extra']) && is_array($level_or_options['extra'])) {
                $scope->setExtras($level_or_options['extra']);
            }

            $response = captureMessage($formatted_message, $level);
        });

        return $response !== null;
    }

    public static function isRavenClientInitialized($raven_client_name = self::DEFAULT_RAVEN_CLIENT_NAME): bool
    {
        return self::getRavenClient() !== null;
    }

    public static function getRavenClient($raven_client_name = self::DEFAULT_RAVEN_CLIENT_NAME): ?ClientInterface
    {
        return SentrySdk::getCurrentHub()->getClient();
    }
}
