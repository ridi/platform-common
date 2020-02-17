<?php

namespace Ridibooks\Platform\Common\Base;

use ReflectionClass;
use ReflectionProperty;
use Symfony\Component\HttpFoundation\Request;

/**
 * 이 클래스는 "상속받은 클래스가 DTO" 임을 의미하고, 이와 관련된 최소한의 정보만 다룬다.
 * @deprecated AdminBaseDto
 */
class AdminBaseDto
{
    /**
     * @var
     * @depecated Request에 종속적인 정보
     */
    public $command; //명령어 (insert / update / ...)
    /**
     * @var
     * @depecated DB에 종속적인 정보, id는 모델에서 저장하도록
     */
    public $id; //공통으로 쓰이는 id
    /**
     * @var
     * @depecated Request에 종속적인 정보
     */
    public $page; //paging에서 공통으로 쓰이는 page
    /**
     * @var
     * @depecated Request에 종속적인 정보
     */
    public $search_text; //공통으로 쓰이는 검색어

    /**
     * AdminBaseDto constructor.
     * @param null $param
     * @deprecated 암시적으로 생성자 사용하지 않고, 명시적으로 import, export 호출
     */
    public function __construct($param = null)
    {
        if ($param instanceof Request) {
            $this->importFromRequest($param);
        } elseif ($param instanceof \stdClass) {
            $this->importFromStdClass($param);
        } elseif (is_array($param)) {
            $this->importFromArray($param);
        }
    }

    /**
     * Request class를 이용하여 클래스를 초기화한다.
     * @param Request $request
     * @internal public 이 아닌 protected 로 변경필요
     */
    public function importFromRequest($request)
    {
        $reflect = new ReflectionClass(get_called_class());
        $properties = $reflect->getProperties(ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED);
        foreach ($properties as $property) {
            $property->setValue($this, $request->get($property->getName()));
        }
    }

    /**stdClass 일 경우 클래스 초기화
     * @param \stdClass $stdClass
     * @internal public 이 아닌 protected 로 변경필요
     */
    public function importFromStdClass($stdClass)
    {
        $reflect = new ReflectionClass(get_called_class());
        $properties = $reflect->getDefaultProperties();
        foreach ($properties as $key => $value) {
            $this->{$key} = $stdClass->{$key};
        }
    }

    /**interface의 function을 가져와 클래스를 초기화 한다.
     * @param $reader
     * @deprecated UniversalBookReader 를 상속받아 구현할것
     */
    public function importFromInterface($reader)
    {
        $reflect = new ReflectionClass(get_called_class());
        $default_properties = $reflect->getDefaultProperties();
        foreach ($default_properties as $key => $value) {
            if (method_exists($reader, $key)) {
                $this->{$key} = $reader->$key();
            }
        }
    }

    /**
     * 배열을 이용하여 클래스를 초기화한다
     * @param array $array
     * @throws \Exception
     * @internal public 이 아닌 protected 로 변경필요
     */
    public function importFromArray($array)
    {
        if (!is_array($array)) {
            throw new \Exception('invalid array');
        }
        $reflect = new ReflectionClass(get_called_class());
        $properties = $reflect->getDefaultProperties();
        foreach ($properties as $key => $value) {
            if (array_key_exists($key, $array)) {
                $this->{$key} = $array[$key];
            }
        }
    }

    /**
     * 함수를 호출한 클래스의 기본 멤버변수만을(동적, 부모 멤버변수 제외) 리턴한다.
     * @return array
     * @internal public 이 아닌 protected 로 변경필요
     */
    public function exportAsArray()
    {
        $reflect = new ReflectionClass(get_called_class());
        $reflect_parent = $reflect->getParentClass();
        $default_properties = $reflect->getDefaultProperties();

        $columns = [];
        foreach ($default_properties as $key => $value) {
            if ($reflect_parent->hasProperty($key)) {
                // 부모 클래스의 properties는 무시한다.
                continue;
            }
            $columns = array_merge($columns, [$key => $this->{$key}]);
        }

        return $columns;
    }

    /**
     * 함수를 호출한 클래스의 기본 멤버변수만큼(동적, 부모 멤버변수 제외) 리턴한다.
     * 단, Null값을 가진 column은 제외한다.
     * @return array
     * @internal public 이 아닌 protected 로 변경필요
     * @deprecated use exportAsArray
     */
    public function exportAsArrayExceptNull()
    {
        $columns = $this->exportAsArray();

        foreach ($columns as $key => $value) {
            if ($value === null) {
                unset($columns[$key]);
            }
        }

        return $columns;
    }
}
