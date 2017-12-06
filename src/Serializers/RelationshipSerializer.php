<?php

namespace Jad\Serializers;

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

    /**
     * @return array
     */
    public function getRelationship(): array
    {
        return $this->relationship;
    }

    /**
     * @param $entity
     * @return string
     */
    public function getType($entity): string
    {
        return Text::kebabify($this->relationship['type']);
    }

    /**
     * @param $entity
     * @return array|mixed
     * @throws \Exception
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

    /**
     * @param $entity
     * @return mixed|void
     */
    public function getLinks($entity) { }

    /**
     * @param $model
     * @return mixed|void
     */
    public function getMeta($model) { }

    /**
     * @return MapItem
     * @throws \Exception
     */
    public function getMapItem()
    {
        /** @var \Jad\Map\MapItem $mapItem */
        $mapItem            = $this->mapper->getMapItem($this->type);
        $association        = $mapItem->getClassMeta()->getAssociationMapping($this->relationship['type']);
        $relatedMapItem     = $this->mapper->getMapItemByClass($association['targetEntity']);

        if(!$relatedMapItem instanceof MapItem) {
            throw new \Exception('Could not find map item for type: ' . $this->relationship['type']);
        }

        return $relatedMapItem;
    }

    /**
     * @param $type
     * @param $entity
     * @param $fields
     * @return mixed|void
     */
    public function getIncluded($type, $entity, $fields) {}
}