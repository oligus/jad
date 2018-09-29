<?php declare(strict_types=1);

namespace Jad\Tests\Document;

use Jad\Tests\TestCase;
use Jad\Document\Collection;
use Jad\Common\ClassHelper;

class CollectionTest extends TestCase
{
    /**
     * @throws \Jad\Exceptions\JadException
     * @throws \ReflectionException
     */
    public function testAdd()
    {
        $resource = $this->getMockBuilder('Jad\Document\Resource')
            ->disableOriginalConstructor()
            ->getMock();

        $collection = new Collection();
        $collection->add($resource);
        $collection->add($resource);
        $collection->add($resource);

        $result = ClassHelper::getPropertyValue($collection, 'resources');

        $this->assertTrue(is_array($result));
        $this->assertEquals(3, count($result));
    }

    /**
     * @throws \ReflectionException
     */
    public function testLoadIncludes()
    {
        $resource = $this->getMockBuilder('Jad\Document\Resource')
            ->disableOriginalConstructor()
            ->setMethods(['hasIncluded', 'getIncluded'])
            ->getMock();

        $included = $this->getMockBuilder('Jad\Document\Resource')
            ->disableOriginalConstructor()
            ->getMock();

        $resource
            ->expects($this->any())
            ->method('hasIncluded')
            ->willReturn(true);

        $resource
            ->expects($this->any())
            ->method('getIncluded')
            ->willReturn([$included]);


        $collection = new Collection();
        $collection->add($resource);
        ClassHelper::setPropertyValue($collection, 'includes',  [$resource]);

        $collection->loadIncludes();

        $this->assertTrue($collection->hasIncluded());
        $includes = $collection->getIncluded();

        $this->assertEquals(2, count($includes));
    }
}