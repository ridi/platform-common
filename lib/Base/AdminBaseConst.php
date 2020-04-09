<?php

namespace Ridibooks\Platform\Common\Base;

use ReflectionClass;

class AdminBaseConst
{
    private static $property_cache = [];

    /**
     * Array 이름이 클래스 이름과 같아야 한다.
     * @return \ReflectionProperty
     */
    private static function getProperty()
    {
        $class = get_called_class();
        if (!isset(self::$property_cache[$class])) {
            $reflect = new ReflectionClass($class);
            self::$property_cache[$class] = $reflect->getProperty($reflect->getShortName());
        }

        return self::$property_cache[$class];
    }

    /**
     * 클래스 이름과 같은 상수 Array 가져온다.
     * [const => string] 과 같은 구조의 array
     * @return array
     */
    public static function getArray()
    {
        return self::getProperty()->getValue();
    }

    /**
     * 키에 해당하는 array value 가져온다.
     * @param int $key
     * @return string
     */
    public static function getName($key)
    {
        return self::getArray()[$key];
    }

    /**
     * value에 해당하는 key 가져온다.
     * @param string $value
     * @return int
     */
    public static function getKey($value)
    {
        return array_search($value, self::getArray());
    }

    /**
     * 클래스 내 모든 상수 가져온다.
     * @return array
     */
    public static function getConstants()
    {
        $ref = new ReflectionClass(get_called_class());

        return $ref->getConstants();
    }

    /**
     * 입력받은 array의 key값들을 반환한다.
     * parameter가 없을 경우 해당 클래스 이름과 같은 상수 array에서 가져온다.
     * @param null $array
     * @return array
     */
    public static function getArrayKeys($array = null)
    {
        if (is_null($array)) {
            return array_keys(self::getArray());
        } else {
            return array_keys($array);
        }
    }
}
