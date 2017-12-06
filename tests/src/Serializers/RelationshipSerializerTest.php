<?php

namespace Jad\Tests;

use Jad\Serializers\RelationshipSerializer;
use Jad\Map\MapItem;

class RelationshipSerializerTest extends TestCase
{
    /**
     * @throws \Exception
     */
    public function testGetRelationships()
    {
        $mapper = $this->getMockBuilder('Jad\Map\AnnotationsMapper')
            ->disableOriginalConstructor()
            ->setMethods(['getMapItem', 'getMapItemByClass'])
            ->getMock();

        $classMeta = $this->getMockBuilder('Doctrine\ORM\Mapping\ClassMetadata')
            ->disableOriginalConstructor()
            ->setMethods(['getAssociationMapping', 'getAssociationMappings'])
            ->getMock();

        $classMeta
            ->expects($this->any())
            ->method('getAssociationMapping')
            ->with('test')
            ->willReturn([
                'targetEntity' => 'related'
            ]);

        $classMeta
            ->expects($this->any())
            ->method('getAssociationMappings')
            ->willReturn([
                'related' => [
                    'fieldName' => 'field'
                ]
            ]);

        $mapItem = new MapItem('test', 'TestClass');
        $mapItem->setClassMeta($classMeta);

        $mapper
            ->expects($this->any())
            ->method('getMapItem')
            ->with('test')
            ->willReturn($mapItem);

        $relatedItem = new MapItem('related', 'RelatedClass');
        $relatedItem->setClassMeta($classMeta);

        $mapper
            ->expects($this->any())
            ->method('getMapItemByClass')
            ->with('related')
            ->willReturn($relatedItem);

        $request = $this->getMockBuilder('Jad\Request\JsonApiRequest')
            ->disableOriginalConstructor()
            ->setMethods(['getCurrentUrl'])
            ->getMock();

        $request
            ->expects($this->any())
            ->method('getCurrentUrl')
            ->willReturn('http://unit.com/');


        $type = 'test';

        $relationshipSerializer = new RelationshipSerializer($mapper, $type, $request);
        $relationshipSerializer->setRelationship(['type' => 'test']);
        $result = $relationshipSerializer->getRelationships(null);


        $this->assertEquals($result, [
            "field" => [
                'links' => [
                    'self' => 'http://unit.com//relationship/field',
                    'related' => 'http://unit.com//field'
                ]
            ]
        ]);
    }

    public function testGetType()
    {
        $mapper = $this->getMockBuilder('Jad\Map\AnnotationsMapper')
            ->disableOriginalConstructor()
            ->setMethods(['test'])
            ->getMock();

        $request = $this->getMockBuilder('Jad\Request\JsonApiRequest')
            ->disableOriginalConstructor()
            ->setMethods(['test'])
            ->getMock();

        $type = 'test';

        $relationshipSerializer = new RelationshipSerializer($mapper, $type, $request);
        $relationshipSerializer->setRelationship(['type' => 'moo']);
        $this->assertEquals('moo', $relationshipSerializer->getType(null));
    }
}