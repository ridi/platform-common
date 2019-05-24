<?php

namespace Ridibooks\Platform\Common\DB;

use Doctrine\DBAL\Cache\QueryCacheProfile;
use Doctrine\DBAL\Connection;
use Ridibooks\Platform\Common\Constant\TimeConstant;

class CustomPdoConnection extends Connection
{
    /**
     * Doctrine의 cache 시스템이 FETCH_OBJ 방식을 지원하지 않음.
     * 따라서 일단 fetch를 FETCH_ASSOC 형태로 하고 직접 object로 변경하는 유틸리티성 함수를 만듦.
     * @param string $sql
     * @param array $params
     * @param array $types
     * @param int $ttl
     * @param string|null $cache_key
     * @param int $fetch_mode
     *
     * @return array
     * @throws \Doctrine\DBAL\Cache\CacheException
     */
    public function fetchAllWithCache(
        $sql,
        $params = [],
        $types = [],
        $ttl = TimeConstant::SEC_IN_MINUTE * 5,
        $cache_key = null,
        $fetch_mode = \PDO::FETCH_OBJ
    ) {
        if ($cache_key === null) {
            $cache_key = $this->generateCacheKey($sql, $params, $types, $ttl);
        }

        $stmt = $this->executeCacheQuery($sql, $params, $types, new QueryCacheProfile($ttl, $cache_key));

        if ($fetch_mode === \PDO::FETCH_OBJ) {
            // 캐싱 시스템이 PDO::FETCH_OBJ 타입을 지원하지 않아 PDO::FETCH_ASSOC 타입으로 조회
            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } else {
            $result = $stmt->fetchAll($fetch_mode);
        }

        // 캐싱이 되지 않은 데이터를 조회할 경우 ResultStatement가 아닌 ResultCacheStatement가 반환되는데, closeCursor()에서 데이터를 캐싱.
        // ResultCacheStatement->fetchAll()로 모든 row를 조회했을 경우에만 캐싱이 가능하도록 되어 있음. 관련 코드 참고.
        $stmt->closeCursor();

        if ($fetch_mode === \PDO::FETCH_OBJ) {
            // PDO::FETCH_OBJ 타입으로 데이터 변환
            $arr = [];
            foreach ($result as $item) {
                $object = new \stdClass();
                foreach ($item as $key => $value) {
                    $object->$key = $value;
                }
                $arr[] = $object;
            }
            return $arr;
        } else {
            return $result;
        }
    }

    /**
     * Doctrine의 cache 시스템이 fetchColumn()을 지원하지 않아서 fetchAll()로 데이터를 조회한 다음 컬럼에 해당하는 데이터를 반환하는 형태로 구현
     *
     * @param string $sql
     * @param array $params
     * @param int $colnum
     * @param array $types
     * @param int $ttl
     * @param string|null $cache_key
     *
     * @return bool|string
     * @throws \Doctrine\DBAL\Cache\CacheException
     */
    public function fetchColumnWithCache(
        $sql,
        $params = [],
        $colnum = 0,
        $types = [],
        $ttl = TimeConstant::SEC_IN_MINUTE * 5,
        $cache_key = null
    ) {
        $result = $this->fetchAllWithCache($sql, $params, $types, $ttl, $cache_key, \PDO::FETCH_ASSOC);

        // fetchAll()에서 가져온 데이터에서 컬럼에 해당하는 데이터 선택
        if (count($result) > 0) {
            $value = array_values($result[0])[$colnum];
        } else {
            $value = false;
        }

        return $value;
    }

    /**
     * @param $statement
     * @param array $params
     * @param array $types
     *
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     */
    public function fetchColumnAll($statement, $params = [], $types = [])
    {
        return $this->executeQuery($statement, $params, $types)->fetchAll(\PDO::FETCH_COLUMN);
    }

    /**
     * @param $sql
     * @param array $params
     * @param array $types
     * @param int $ttl
     * @param string|null $cache_key
     *
     * @return array
     * @throws \Doctrine\DBAL\Cache\CacheException
     */
    public function fetchColumnAllWithCache(
        $sql,
        $params = [],
        $types = [],
        $ttl = TimeConstant::SEC_IN_MINUTE * 5,
        $cache_key = null
    ) {
        return $this->fetchAllWithCache($sql, $params, $types, $ttl, $cache_key, \PDO::FETCH_COLUMN);
    }

    /**
     * @param $sql
     * @param array $params
     * @param array $types
     * @param int $ttl
     * @param string|null $cache_key
     *
     * @return object|null
     *
     * @throws \Doctrine\DBAL\Cache\CacheException
     */
    public function fetchObjectWithCache(
        $sql,
        $params = [],
        $types = [],
        $ttl = TimeConstant::SEC_IN_MINUTE * 5,
        $cache_key = null
    ) {
        $result = $this->fetchAllWithCache($sql, $params, $types, $ttl, $cache_key, \PDO::FETCH_OBJ);
        if (count($result) === 0) {
            return null;
        }
        return $result[0];
    }

    /**
     * @param string $statement
     * @param array $params
     * @param array $types
     *
     * @return object|null
     * @throws \Doctrine\DBAL\DBALException
     */
    public function fetchObject($statement, $params = [], $types = [])
    {
        return $this->executeQuery($statement, $params, $types)->fetch(\PDO::FETCH_OBJ);
    }

    /**
     * @param $sql
     * @param array $params
     * @param array $types
     *
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     */
    public function fetchAssocAll($sql, $params = [], $types = [])
    {
        return $this->executeQuery($sql, $params, $types)->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * @param string $query
     * @param array $params
     * @param array $types
     * @param int $ttl
     * @return string
     */
    private function generateCacheKey($query, $params, $types, $ttl)
    {
        // TTL을 녹인 이유는 동일 쿼리에 대해 다른 TTL로 요청할 경우 각각의 TTL을 보장해주기 위함.
        // (쿼리 + 파라미터 + 타입)을 녹인 이유는 다양한 쿼리가 동일한 cache key를 가질 경우 한 cache key에 대한 cache data가 너무 커지기 때문.
        return sha1($query . "-" . serialize($params) . "-" . serialize($types) . '-' . $ttl);
    }
}
