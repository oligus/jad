<?php

namespace Jad\Tests;

use Jad\Common\ClassHelper;

class ClassHelperTest extends TestCase
{
    /**
     * @throws \Jad\Exceptions\JadException
     * @expectedException \Jad\Exceptions\JadException
     * @expectedExceptionMessage Property [property3] not found in class [Jad\Tests\TestClass]
     */
    public function testGetPropertyValue()
    {
        $class = new TestClass();
        ClassHelper::setPropertyValue($class, 'property1', 'test');

        $this->assertEquals('test', ClassHelper::getPropertyValue($class, 'property1'));
        $this->assertEquals('prop2', ClassHelper::getPropertyValue($class, 'property2'));
        $this->expectException(ClassHelper::getPropertyValue($class, 'property3'));
    }

    /**
     * @throws \Jad\Exceptions\JadException
     */
    public function testSetPropertyValue()
    {
        $class = new TestClass();
        ClassHelper::setPropertyValue($class, 'property1', 'test');
        $this->assertEquals('test', ClassHelper::getPropertyValue($class, 'property1'));

        ClassHelper::setPropertyValue($class, 'property2', 'test2');
        $this->assertEquals('test', ClassHelper::getPropertyValue($class, 'property1'));
    }

    public function testHasPropertyValue()
    {
        $class = new TestClass();
        $this->assertFalse(ClassHelper::hasPropertyValue($class, 'testProperty'));
        $this->assertTrue(ClassHelper::hasPropertyValue($class, 'property1'));
        $this->assertTrue(ClassHelper::hasPropertyValue($class, 'property2'));
    }

}

class TestClass
{
    public $property1;

    public $property2;

    public function getProperty2()
    {
        return 'prop2';
    }

    public function setProperty2($value)
    {
        $this->property2 = $value;
    }
}