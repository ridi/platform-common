<?php
declare(strict_types=1);

namespace Ridibooks\Platform\Common\Util;

use Symfony\Component\HttpFoundation\Request;

class RequestUtils
{
    public static function getContent(Request $request): array
    {
        if (!StringUtils::isEmpty($request->headers->get('Content-Type'))
            && 0 === strpos($request->headers->get('Content-Type'), 'application/json')
        ) {
            $result = json_decode($request->getContent(), true);
        } else {
            $result = self::getAllParams($request);
        }

        return $result;
    }

    public static function getAllParams(Request $request): array
    {
        // Request::get 의 검색 순서대로 merge 하기 위해 역순으로 배치
        return array_merge($request->request->all(), $request->attributes->all(), $request->query->all());
    }

    public static function getValuesByKey(Request $request, string $key): array
    {
        $request_params = self::getContent($request);

        return self::getValuesFromContent($request_params, $key);
    }

    public static function getValuesByKeys(Request $request, array $keys): array
    {
        $request_params = self::getContent($request);

        $result = [];

        foreach ($keys as $key) {
            $result[$key] = self::getValuesFromContent($request_params, $key);
        }

        return $result;
    }

    private static function getValuesFromContent(array $request_params, string $key): array
    {
        $values = [];
        if (isset($request_params[$key])) {
            $values = $request_params[$key];
            if (is_string($values) && !StringUtils::isEmpty($values)) {
                // , 구분 일 경우
                $values = str_replace(['"', "'", ' '], '', $values);
                $values = explode(',', $values);
            } elseif (is_array($values) && !empty($values)) {
                // array 로 넘어왔을 경우
                $values = collect($values)
                    ->map(function ($value) {
                        return str_replace(['"', "'", ' '], '', $value);
                    })
                    ->all();
            }
        }

        return $values;
    }
}
