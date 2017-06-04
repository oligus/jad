<?php

namespace Jad\Serializers;

use Doctrine\DBAL\Schema\Index;
use Jad\Common\Inflect;
use Jad\Common\Text;
use Jad\Map\MapItem;


/**
 * Class EntitySerializer
 * @package Jad\Serializers
 */
class RelationshipSerializer extends AbstractSerializer implements Serializer
{
    /**
     * @var array
     */
    private $relationship;

    /**
     * @param array $relationship
     */
    public function setRelationship($relationship)
    {
        $this->relationship = $relationship;
    }

    public function getConfig()
    {
        return $this->relationship;
    }

    /**
     * @param $entity
     * @return mixed
     */
    public function getType($entity)
    {
        return Text::kebabify($this->relationship['type']);
    }

    /**
     * @param $entity
     * @return array
     */
    public function getRelationships($entity)
    {
        $relationships = array();

        $associations = $this->getMapItem()->getClassMeta()->getAssociationMappings();

        foreach($associations as $association) {
            $assocName = $association['fieldName'];

            $relationships[$assocName] = array(
                'links' => array(
                    'self' => $this->request->getCurrentUrl() . '/relationship/' . $assocName,
                    'related' => $this->request->getCurrentUrl() . '/' . $assocName
                )
            );
        }

        return $relationships;
    }

    public function getLinks($entity)
    {
        // TODO: Implement getLinks() method.
    }

    public function getMeta($model)
    {
        // TODO: Implement getMeta() method.
    }


    /**
     * @return MapItem
     * @throws \Exception
     */
    public function getMapItem()
    {
        /** @var \Jad\Map\MapItem $mapItem */
        $mapItem = $this->mapper->getMapItem($this->type);

        $resourceType = $this->relationship['type'];

        if(!$mapItem->getClassMeta()->hasAssociation($resourceType)) {
            $resourceType = Inflect::pluralize($resourceType);
        }

        $association = $mapItem->getClassMeta()->getAssociationMapping($resourceType);

        $entityClass = $association['targetEntity'];

        $relatedMapItem = $this->mapper->getMapItemByClass($entityClass);

        if(!$relatedMapItem instanceof MapItem) {
            throw new \Exception('Could not find map item for type: ' . $this->relationship['type']);
        }

        return $relatedMapItem;
    }

    public function getRelatedEntities()
    {

    }

    public function getIncluded($type, $entity)
    {
        // TODO: Implement getIncluded() method.
    }
}