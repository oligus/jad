<?php declare(strict_types=1);

namespace Jad\Common;

use Jad\Exceptions\JadException;
use ReflectionClass;
use ReflectionException;

/**
 * Class ClassHelper
 * @package Jad\Common
 */
class ClassHelper
{
    /**
     * @return mixed
     * @throws JadException
     */
    public static function getPropertyValue(object $class, string $property)
    {
        $method = 'get' . ucfirst($property);

        if (method_exists($class, $method)) {
            return $class->$method();
        }

        $reflection = new ReflectionClass($class);

        if ($reflection->hasProperty($property)) {
            $reflectionProperty = $reflection->getProperty($property);
            $reflectionProperty->setAccessible(true);
            return $reflectionProperty->getValue($class);
        }

        throw new JadException('Property [' . $property . '] not found in class [' . get_class($class) . ']');
    }

    /**
     * @param mixed $value
     * @throws ReflectionException
     */
    public static function setPropertyValue(object $class, string $property, $value): void
    {
        $method = 'set' . ucfirst($property);

        if (method_exists($class, $method)) {
            $class->$method($value);
            return;
        }

        $reflection = new ReflectionClass($class);

        if ($reflection->hasProperty($property)) {
            $reflectionProperty = $reflection->getProperty($property);
            $reflectionProperty->setAccessible(true);
            $reflectionProperty->setValue($class, $value);
        }
    }

    /**
     * @throws ReflectionException
     */
    public static function hasPropertyValue(object $class, string $property): bool
    {
        $method = 'set' . ucfirst($property);

        if (method_exists($class, $method)) {
            return true;
        }

        $reflection = new ReflectionClass($class);

        return $reflection->hasProperty($property);
    }
}
