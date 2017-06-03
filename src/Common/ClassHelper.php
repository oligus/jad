<?php

namespace Jad\Common;

use Jad\Exceptions\JadException;

/**
 * Class ClassHelper
 * @package Jad\Common
 */
class ClassHelper
{
    /**
     * Get property from class
     *
     * @param $entity
     * @param $property
     * @return mixed
     * @throws JadException
     */
    public static function getPropertyValue($entity, $property)
    {
        $methodName = 'get' . ucfirst($property);

        if(method_exists($entity, $methodName)) {
            return $entity->$methodName();
        }

        $reflection = new \ReflectionClass($entity);

        if($reflection->hasProperty($property)) {
            $reflectionProperty = $reflection->getProperty($property);
            $reflectionProperty->isPublic();
            $reflectionProperty->setAccessible(true);
            return $reflectionProperty->getValue($entity);
        }

        throw new JadException('Property [' . $property . '] not found in class [' . get_class($entity) . ']');
    }
}