<?php
declare(strict_types=1);

namespace Ridibooks\Platform\Common\AWS;

use Aws\Exception\AwsException;
use Aws\Ssm\SsmClient;
use Ridibooks\Platform\Common\Util\SentryHelper;

/**
 * @property SsmClient $client
 */
class SsmService extends AbstractAwsService
{
    public const TYPE_STRING = 'String';
    public const TYPE_STRING_LIST = 'StringList';
    public const TYPE_SECURE_STRING = 'SecureString';

    protected function getAwsClass(): string
    {
        return SsmClient::class;
    }

    public function getParameter(string $name): array
    {
        $params = [
            'Name' => $name,
        ];

        try {
            $result = $this->client->getParameter($params);
            $result = $result->get('Parameter');

            $param_string = $result['Value'];
            $raw_params = array_filter(explode(PHP_EOL, $param_string));

            return $raw_params;
        } catch (AwsException $e) {
            SentryHelper::triggerSentryMessage(
                'Fail To get parameters from SSM :' . $name . PHP_EOL
                . 'Reason : ' . PHP_EOL
                . $e->getMessage()
            );
            throw $e;
        }
    }

    public function getParameterAsMap(string $name): array
    {
        try {
            $raw_params = $this->getParameter($name);

            $params = [];
            foreach ($raw_params as $raw_param) {
                $param = explode('=', $raw_param);
                $value = $param[1];
                if (is_numeric($value)) {
                    $value = (int)$value;
                } elseif (in_array($value, ['true', 'false'])) {
                    $value = (bool)$value;
                }

                $params[$param[0]] = $value;
            }

            return $params;
        } catch (AwsException $e) {
            throw $e;
        }
    }

    public function setParameter(string $name, array $params, bool $is_overwrite = true, array $options = []): void
    {
        $value = implode(PHP_EOL, $params);

        $this->putParameter($name, $value, $is_overwrite, $options);
    }

    public function setParameterFromMap(string $name, array $params, bool $is_overwrite = true, array $options = []): void
    {
        $value_string = '';
        foreach ($params as $key => $value) {
            if (is_bool($value)) {
                $value = $value ? 'true' : 'false';
            }
            $value_string .= $key . '=' . $value . PHP_EOL;
        }

        $this->putParameter($name, $value_string, $is_overwrite, $options);
    }

    private function putParameter(string $name, string $value, bool $is_overwrite, array $options): void
    {
        $params = [
            'Type' => self::TYPE_STRING,
            'Name' => $name,
            'Value' => $value,
            'Overwrite' => $is_overwrite,
        ];

        $params = array_merge($params, $options);

        try {
            $result = $this->client->putParameter($params);
        } catch (AwsException $e) {
            SentryHelper::triggerSentryMessage(
                'Fail To put parameters to SSM :' . $name . PHP_EOL
                . 'Reason : ' . PHP_EOL
                . $e->getMessage()
            );
            throw $e;
        }
    }
}
