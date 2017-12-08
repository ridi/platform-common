<?php

namespace Ridibooks\Platform\Common;

/**
 * sqlObjects 통해서 얻어진 배열에 대해 자주 사용하는 함수들
 */
class ObjectsUtils
{
    /**
     * @param $objects \stdClass[]
     * @param $key
     * @return array
     *
     * Objects 에서 특정 key를 가진 값들을 모두 얻기
     *
     * $dicts = $db->sqlObjects('select * from tb_book');
     * $b_ids = ObjectsUtils::extractValuesByKey($objects, 'id');
     */
    public static function extractValuesByKey($objects, $key)
    {
        $return = [];
        foreach ($objects as $object) {
            $return[] = $object->$key;
        }
        return $return;
    }

    /**
     * @param $objects
     * @param $key
     * @return array
     *
     * Objects 에서 특정 key 를 기준으로 재배치(key 기준으로 하나밖에 없을떄)
     *
     * $objects = $db->sqlObjects('select * from cpdp_books');
     * $cpdp_books_by_bid = ObjectsUtils::alignByKey($objects, 'b_id');
     * $cpdp_books_111011110 = $tb_book_property_by_bid['111011110'];
     *
     * $cpdp_books_111011110 => ['id' => '123123', 'b_id'=>'111011110', 'title' => '바람과 함께 사라지다']
     */
    public static function alignByKey($objects, $key)
    {
        $return = [];
        foreach ($objects as $object) {
            $return[$object->$key] = $object;
        }
        return $return;
    }

    /**
     * @param $objects
     * @param $key
     * @return array
     *
     * Objects 에서 특정 key 를 기준으로 재배치(key 기준으로 여러개 있을떄)
     *
     * $dicts = $db->sqlObjects('select * from tb_book_property');
     * $tb_book_propertys_by_bid = ObjectsUtils::alignListByKey($dicts, 'b_id');
     * $tb_book_propertys_111011110 = $tb_book_propertys_by_bid['111011110'];
     *
     * $tb_book_propertys_111011110 => [
     *      ['b_id'=>'111011110', 'key' => 'num_pages', 'vaule' => 123123],
     *      ['b_id'=>'111011110', 'key' => 'type', 'vaule' => 'pdf'],
     * ]
     */
    public static function alignListByKey($objects, $key)
    {
        $return = [];
        foreach ($objects as $object) {
            $return[$object->$key][] = $object;
        }
        return $return;
    }
}
