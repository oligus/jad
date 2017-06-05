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
     * @param $className
     * @param string $property
     * @return mixed
     * @throws JadException
     */
    public static function getPropertyValue($className, string $property)
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
     * @param string $property
     * @param $value
     */
    public static function setPropertyValue($className, string $property, $value)
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

    /**
     * @param $className
     * @param string $property
     * @return bool
     */
    public static function hasPropertyValue($className, string $property): bool
    {
        $methodName = 'set' . ucfirst($property);

        if (method_exists($className, $methodName)) {
            return true;
        } else {
            $reflection = new \ReflectionClass($className);

            if ($reflection->hasProperty($property)) {
                return true;
            }
        }

        return false;
    }
}