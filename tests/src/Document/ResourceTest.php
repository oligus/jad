<?php

namespace Jad\Tests\Document;

use Jad\Tests\TestCase;
use Jad\Document\Resource;
use Doctrine\Common\Collections\ArrayCollection;

class ResourceTest extends TestCase
{
    /**
     * @expectedException           Jad\Exceptions\MappingException
     * @expectedExceptionMessage    Included type [tracks] not available, check if resource type is mapped correctly.
     */
    public function testGetIncluded()
    {
        $entity = $this->getMockBuilder('Jad\Database\Entities\Playlists')
            ->disableOriginalConstructor()
            ->setMethods(['test'])
            ->getMock();

        $serializer = $this->getMockBuilder('Jad\Serializers\EntitySerializer')
            ->disableOriginalConstructor()
            ->setMethods(['getIncluded'])
            ->getMock();

        $serializer
            ->expects($this->any())
            ->method('getIncluded')
            ->willReturn(null);

        $resource = new Resource($entity, $serializer);
        $resource->setIncludedParams([0 => [
            'tracks' => ''
        ]]);
        $resource->setFields([]);
        $resource->getIncluded();
    }

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