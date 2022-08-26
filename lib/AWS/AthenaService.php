<?php

declare(strict_types=1);

namespace Ridibooks\Platform\Common\AWS;

use Aws\Athena\AthenaClient;
use Aws\Result;
use Ridibooks\Platform\Common\Exception\MsgException;

/**
 * @property AthenaClient $client
 */
class AthenaService extends AbstractAwsService
{
    protected function __construct()
    {
    }

    protected function getAwsClass(): string
    {
        return AthenaClient::class;
    }

    /**
     * @return Result[]
     */
    public function execute(string $db, string $output_location, string $query, ?int $wait_seconds = 20, ?int $max_tries = 3): array
    {
        $options = [
            'QueryExecutionContext' => [
                'Catalog' => 'AwsDataCatalog',
                'Database' => $db,
            ],
            'QueryString' => $query,
            'ResultConfiguration' => [
                'EncryptionConfiguration' => ['EncryptionOption' => 'SSE_S3'],
                'OutputLocation' => $output_location,
            ],
        ];

        $query_id = $this->client->startQueryExecution($options)->get('QueryExecutionId');

        return $this->getQueryResults($query_id, $wait_seconds, $max_tries);
    }

    /**
     * @return Result[]
     */
    private function getQueryResults(string $id, int $wait_seconds, int $max_tries): array
    {
        $succeeded = false;
        $tries = 0;
        while (!$succeeded) {
            if ($tries > $max_tries) {
                $this->client->stopQueryExecution(['QueryExecutionId' => $id]);
                throw new MsgException('Athena query timeout');
            }

            $status = $this->client->getQueryExecution(['QueryExecutionId' => $id])
                ->get('QueryExecution')['Status'];

            switch ($status['State']) {
                case 'SUCCEEDED':
                    $succeeded = true;
                    break;
                case 'FAILED':
                case 'CANCELLED':
                    throw new MsgException('쿼리 실행 중 오류가 발생했습니다. ' . $status['StateChangeReason']);
                default:
                    $tries++;
                    sleep($wait_seconds);
            }
        }

        $result = $this->client->getQueryResults(['QueryExecutionId' => $id]);
        return AwsResultSetParser::parseMultipleResultsWithHeader($this->getAllPages($result, $id));
    }

    /**
     * @return Result[]
     */
    private function getAllPages(Result $result, string $id): array
    {
        $next_token = $result->get('NextToken');
        $results = [$result];
        while ($next_token) {
            $next_page_result = $this->client->getQueryResults(['QueryExecutionId' => $id, 'NextToken' => $next_token]);
            array_push($results, $next_page_result);

            $next_token = $next_page_result->get('NextToken');
        }

        return $results;
    }
}
