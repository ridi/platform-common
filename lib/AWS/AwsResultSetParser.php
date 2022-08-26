<?php

declare(strict_types=1);

namespace Ridibooks\Platform\Common\AWS;

use Aws\Result;

class AwsResultSetParser
{
    /**
     * @param Result[] $results
     */
    public static function parseMultipleResultsWithHeader(array $results): array
    {
        $flattened_results = array_map(function (Result $result): array {
            return self::flattenResult($result);
        }, $results);

        $headers = (array) array_shift($flattened_results[0]);

        $results_with_header = [];
        foreach ($flattened_results as $result) {
            foreach ($result as $single_row) {
                $row_with_headers = [];
                $column_index = 0;

                foreach ((array) $single_row as $columns) {
                    $row_with_headers[$headers[$column_index]] = $columns;
                    $column_index++;
                }
                $results_with_header[] = $row_with_headers;
            }
        }

        return $results_with_header;
    }

    private static function flattenResult(Result $result): array
    {
        $data = array_map(function (array $row): array {
            return (array) call_user_func_array('array_merge_recursive', $row['Data']);
        }, $result->get('ResultSet')['Rows']);

        return array_map(function ($data) {
            return array_values($data)[0];
        }, $data);
    }
}
