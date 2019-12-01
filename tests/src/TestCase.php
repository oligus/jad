<?php

namespace Jad\Tests;

use Jad\Database\Manager;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Jad\Configure;

class TestCase extends PHPUnitTestCase
{
    /**
     * @throws \Doctrine\Common\Persistence\Mapping\MappingException
     */
    protected function setUp(): void
    {
        parent::setUp();
        Configure::getInstance()->setConfig('test_mode', true);
        Configure::getInstance()->setConfig('strict', true);

        Manager::getInstance()->getEm()->clear();
        $_GET = [];
        $_SERVER['REQUEST_URI']  = '';
        $_SERVER['REQUEST_METHOD']  = 'GET';
    }

    /**
     * @before
     */
    public function setupTestDatabase()
    {
        system('cp tests/fixtures/test_db_origin.sqlite tests/fixtures/test_db.sqlite ');
    }

    /**
     * @param $className
     * @param $methodName
     * @return \ReflectionMethod
     * @throws \ReflectionException
     */
    protected static function getMethod($className, $methodName) {
        $class = new \ReflectionClass($className);
        $method = $class->getMethod($methodName);
        $method->setAccessible(true);
        return $method;
    }

    /**
     * @param $className
     * @param array $options
     * @return \PHPUnit_Framework_MockObject_MockObject
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
     * @throws \ReflectionException
     */
    public function setProtectedProperty($className, $property, $value)
    {
        $reflection = new \ReflectionClass($className);
        $reflection_property = $reflection->getProperty($property);
        $reflection_property->setAccessible(true);
        $reflection_property->setValue($className, $value);
    }
}
