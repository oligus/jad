<?php

namespace Jad\Serializers;

use Jad\Document\Resource;
use Jad\Common\Text;
use Jad\Common\Inflect;
use Jad\Common\ClassHelper;
use Jad\Exceptions\SerializerException;
use Doctrine\ORM\PersistentCollection;

/**
 * Class EntitySerializer
 * @package Jad\Serializers
 */
class EntitySerializer extends AbstractSerializer
{
    /**
     * @param $entity
     * @return array
     */
    public function getRelationships($entity)
    {
        $relationships = array();

        $associations = $this->getMapItem()->getClassMeta()->getAssociationMappings();

        foreach($associations as $association) {
            $assocName = Text::kebabify($association['fieldName']);

            if($this->request->hasId()) {
                $relationships[$assocName] = array(
                    'links' => array(
                        'self' => $this->request->getCurrentUrl() . '/relationship/' . $assocName,
                        'related' => $this->request->getCurrentUrl() . '/' . $assocName
                    )
                );
            } else {
                $id = ClassHelper::getPropertyValue($entity, $this->getMapItem()->getIdField());
                $relationships[$assocName] = array(
                    'links' => array(
                        'self' => $this->request->getCurrentUrl() . '/' . $id . '/relationship/' . $assocName,
                        'related' => $this->request->getCurrentUrl() . '/' . $id . '/' . $assocName
                    )
                );
            }

        }

        return $relationships;
    }

    /**
     * @param $type
     * @param $entity
     * @return array|null
     * @throws SerializerException
     */
    public function getIncluded($type, $entity)
    {
        if(!$this->mapper->hasMapItem($type)) {
            return null;
        }

        $pluralType = Inflect::pluralize($type);

        if (!$this->getMapItem()->getClassMeta()->hasAssociation($pluralType)) {
            throw new SerializerException('Cannot find relationship ' . $pluralType . ' for inclusion');
        }

        $result = ClassHelper::getPropertyValue($entity, $pluralType);

        if($result instanceof PersistentCollection) {
            return $this->getIncludedResources($type, $result);
        } else {
            throw new SerializerException('Singular not implemented.');
        }
    }

    /**
     * @param $type
     * @param $entityCollection
     * @return array
     */
    public function getIncludedResources($type, $entityCollection)
    {
        $resources = array();

        foreach ($entityCollection as $associatedEntity) {
            $resources[] = new Resource($associatedEntity, new IncludedSerializer($this->mapper, $type, $this->request));
        }
        return $resources;
    }

    public function getLinks($entity)
    {
        // TODO: Implement getLinks() method.
    }

    public function getMeta($model)
    {
        // TODO: Implement getMeta() method.
    }

}