<?php

namespace Ridibooks\Platform\Common;

/**
 * sqlDicts 통해서 얻어진 배열에 대해 자주 사용하는 함수들
 */
class DictsUtils
{
    /**
     * Dicts 에서 특정 key를 가진 값들을 모두 얻기
     *
     * $dicts = $db->sqlDicts('select * from tb_book');
     * $b_ids = DictsUtils::extractValuesByKey($dicts, 'id');
     *
     * @param $dicts
     * @param $key
     * @return array
     */
    public static function extractValuesByKey($dicts, $key)
    {
        $return = [];
        foreach ($dicts as $dict) {
            $return[] = $dict[$key];
        }
        return $return;
    }

    /**
     * Dicts 에서 특정 key 를 기준으로 재배치(key 기준으로 하나밖에 없을떄)
     *
     * $dicts = $db->sqlDicts('select * from cpdp_books');
     * $cpdp_books_by_bid = DictsUtils::alignByKey($dicts, 'b_id');
     * $cpdp_books_111011110 = $tb_book_property_by_bid['111011110'];
     *
     * $cpdp_books_111011110 => ['id' => '123123', 'b_id'=>'111011110', 'title' => '바람과 함께 사라지다']
     *
     * @param $dicts
     * @param $key
     * @return array
     */
    public static function alignByKey($dicts, $key)
    {
        $return = [];
        foreach ($dicts as $dict) {
            $return[$dict[$key]] = $dict;
        }
        return $return;
    }

    /**
     * Dicts 에서 특정 key 를 기준으로 재배치(key 기준으로 여러개 있을떄)
     *
     * $dicts = $db->sqlDicts('select * from tb_book_property');
     * $tb_book_propertys_by_bid = DictsUtils::alignListByKey($dicts, 'b_id');
     * $tb_book_propertys_111011110 = $tb_book_propertys_by_bid['111011110'];
     *
     * $tb_book_propertys_111011110 => [
     *      ['b_id'=>'111011110', 'key' => 'num_pages', 'vaule' => 123123],
     *      ['b_id'=>'111011110', 'key' => 'type', 'vaule' => 'pdf'],
     * ]
     *
     * @param $dicts
     * @param $key
     * @return array
     */
    public static function alignListByKey($dicts, $key)
    {
        $return = [];
        foreach ($dicts as $dict) {
            $return[$dict[$key]][] = $dict;
        }
        return $return;
    }

    /**
     * Dicts 와 Dicts 끼리 join 할때 (SQL 에서 join 하지 않고 PHP에서 join)
     *
     * $dictA = $db->sqlDicts('select t_id, amount point from tb_point');
     * $dictB = $db->sqlDicts('select t_id, amount cash from tb_cash');
     * $dictNew = DictsUtils::join($dictA, $dictB);
     *
     * $dictNew => [
     *      [t_id => '111', 'point' => 123, 'cash' => 456],
     *      [t_id => '112', 'point' => 124, 'cash' => 457],
     * ]
     *
     * @param $left_dicts
     * @param $right_dicts
     * @param int $left_dicts_column_index_to_join
     * @param int $right_dicts_column_index_to_join
     * @return mixed
     */
    public static function join(
        $left_dicts,
        $right_dicts,
        $left_dicts_column_index_to_join = 0,
        $right_dicts_column_index_to_join = 0
    ) {
        if (count($left_dicts) == 0) {
            return $right_dicts;
        }
        if (count($right_dicts) == 0) {
            return $left_dicts;
        }

        $left_keys = array_keys($left_dicts[0]);
        $left_key_to_join = $left_keys[$left_dicts_column_index_to_join];
        $right_keys = array_keys($right_dicts[0]);
        $right_key_to_join = $right_keys[$right_dicts_column_index_to_join];

        foreach ($left_dicts as $lk => $lv) {
            foreach ($right_dicts as $rk => $rv) {
                if ($lv[$left_key_to_join] != $rv[$right_key_to_join]) {
                    continue;
                }
                $left_dicts[$lk] = array_merge($lv, $rv);
            }
        }

        $keys = [];
        foreach ($left_dicts as $dict) {
            $keys = array_unique(array_merge($keys, array_keys($dict)));
        }

        foreach ($left_dicts as $dictKey => $dict) {
            foreach ($keys as $key) {
                if (!isset($left_dicts[$dictKey][$key])) {
                    $left_dicts[$dictKey][$key] = null;
                }
            }
        }

        return $left_dicts;
    }

    public static function convertDictsToHtmlTable($dicts)
    {
        $th = '';
        $tr = '';
        foreach ($dicts as $index => $dict) {
            $td = '';
            foreach ($dict as $key => $value) {
                if ($index == 0) {
                    $th = $th . "<th align='left'>" . $key . "</th>\n";
                }
                $td = $td . "<td align='left'>" . $value . "</td>\n";
            }
            $tr = $tr . "<tr>" . $td . "</tr>\n";
        }

        $html_table = "
        <table cellspacing='0' cellpadding='0' style='width: 100%; border-collapse: collapse; color: #333; font-size: 13px; line-height: 1.7em; border-top:1px solid #848484;'>"
            . "<thead>" . "<tr>" . $th . "</tr>" . "</thead>"
            . "<tbody>" . $tr . "</tbody>" . "</table>";

        return $html_table;
    }
}
