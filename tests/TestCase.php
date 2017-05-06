<?php

use PHPUnit\Framework\TestCase as PHPUnitTestCase;

class TestCase extends PHPUnitTestCase
{
    protected function setUp()
    {
        parent::setUp();
    }

    /**
     * @param $className
     * @param $methodName
     * @return ReflectionMethod
     */
    protected static function getMethod($className, $methodName) {
        $class = new ReflectionClass($className);
        $method = $class->getMethod($methodName);
        $method->setAccessible(true);
        return $method;
    }

    /**
     * @param $className
     * @param array $options
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function getClassMock($className, array $options = [])
    {
        $mock = $this->getMockBuilder($className)
            ->setMethods(array_keys($options))
            ->getMock();

        foreach($options as $method => $value) {
            $mock->expects($this->any())
                ->method($method)
                ->will($this->returnValue($value));
        }

        return $mock;
    }

    /**
     * @param $className
     * @param $property
     * @param $value
     */
    public function setProtectedProperty($className, $property, $value)
    {
        $reflection = new ReflectionClass($className);
        $reflection_property = $reflection->getProperty($property);
        $reflection_property->setAccessible(true);
        $reflection_property->setValue($className, $value);
    }
}
