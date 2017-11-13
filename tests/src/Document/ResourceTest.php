<?php

namespace Jad\Tests\Document;

use Jad\Tests\TestCase;
use Jad\Document\Resource;
use Doctrine\Common\Collections\ArrayCollection;

class ResourceTest extends TestCase
{
    public function testCrawlRelations()
    {
        $serializer = $this->getMockBuilder('Jad\Serializers\EntitySerializer')
            ->disableOriginalConstructor()
            ->setMethods(['getArtists'])
            ->getMock();


        $albums = new ArrayCollection();

        $album = $this->getMockBuilder('Jad\Database\Entities\Album')
            ->disableOriginalConstructor()
            ->setMethods(['getAlbum'])
            ->getMock();

        $albums->add($album);

        $entity = $this->getMockBuilder('Jad\Database\Entities\Tracks')
            ->disableOriginalConstructor()
            ->setMethods(['getAlbum'])
            ->getMock();

        $entity->expects($this->any())
            ->method('getAlbum')
            ->willReturn($albums);

        $resource = new Resource($entity, $serializer);

        $result = $resource->crawlRelations($entity, ['album']);

        $this->assertEquals('album', $result['type']);
    }
}