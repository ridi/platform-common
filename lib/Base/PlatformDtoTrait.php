<?php

namespace Ridibooks\Platform\Common\Base;

use ReflectionClass;
use ReflectionProperty;
use Symfony\Component\HttpFoundation\Request;

trait PlatformDtoTrait
{
    /**
     * Request class를 이용하여 클래스를 초기화한다.
     * @param Request $request
     *
     * @throws \ReflectionException
     */
    public function importFromRequest(Request $request): void
    {
        $reflect = new ReflectionClass(get_called_class());
        $properties = $reflect->getProperties(ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED);
        foreach ($properties as $property) {
            $property->setValue($this, $request->get($property->getName()));
        }
    }

    /**
     * 배열을 이용하여 클래스를 초기화한다
     * @param array $array
     *
     * @throws \Exception
     */
    public function importFromArray(array $array): void
    {
        $reflect = new ReflectionClass(get_called_class());
        $properties = $reflect->getDefaultProperties();
        foreach ($properties as $key => $value) {
            if (array_key_exists($key, $array)) {
                $this->{$key} = $array[$key];
            }
        }
    }
}
