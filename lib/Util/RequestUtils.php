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
}
