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
     * @param $className
     * @param $property
     * @return mixed
     * @throws JadException
     */
    public static function getPropertyValue($className, $property)
    {
        $methodName = 'get' . ucfirst($property);

        if(method_exists($className, $methodName)) {
            return $className->$methodName();
        }

        $reflection = new \ReflectionClass($className);

        if($reflection->hasProperty($property)) {
            $reflectionProperty = $reflection->getProperty($property);
            $reflectionProperty->isPublic();
            $reflectionProperty->setAccessible(true);
            return $reflectionProperty->getValue($className);
        }

        throw new JadException('Property [' . $property . '] not found in class [' . get_class($className) . ']');
    }

    /**
     * @param $className
     * @param $property
     * @param $value
     */
    public static function setPropertyValue($className, $property, $value)
    {
        $methodName = 'set' . ucfirst($property);

        if (method_exists($className, $methodName)) {
            $className->$methodName($value);
        } else {
            $reflection = new \ReflectionClass($className);
            if ($reflection->hasProperty($property)) {
                $reflectionProperty = $reflection->getProperty($property);
                $reflectionProperty->setAccessible(true);
                $reflectionProperty->setValue($className, $value);
            }
        }
    }
}