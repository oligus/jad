<?php

namespace Jad\Document;

use Jad\Serializers\RelationshipSerializer;
use Jad\Serializers\Serializer;
use Jad\Common\Inflect;
use Jad\Common\ClassHelper;

use Doctrine\ORM\PersistentCollection;

/**
 * Class Resource
 * @package Klirr\JsonApi\Response
 */
class Resource implements \JsonSerializable
{
    /**
     * @var
     */
    private $entity;

    /**
     * @var Serializer $serializer
     */
    private $serializer;

    /**
     * @var null
     */
    private $fields = null;

    /**
     * @var null
     */
    private $included = null;

    /**
     * Resource constructor.
     * @param $entity
     * @param Serializer $serializer
     */
    public function __construct($entity, Serializer $serializer)
    {
        $this->entity = $entity;
        $this->serializer = $serializer;
    }

    /**
     * @param $fields
     */
    public function setFields($fields)
    {
        $this->fields = $fields;
    }

    /**
     * @param $included
     */
    public function setIncluded($included)
    {
        $this->included = $included;
    }

    /**
     * @return \stdClass
     */
    public function jsonSerialize()
    {
        $resource = new \stdClass();

        $entity = $this->entity;
        $type = $this->serializer->getType($entity);

        $fields = null;

        if(is_array($this->fields)) {
            $fields = array_key_exists($type, $this->fields) ? $this->fields[$type] : null;
        }

        $resource->id = $this->serializer->getId($entity);
        $resource->type = $type;

        if($this->serializer instanceof RelationshipSerializer) {
            $config = $this->serializer->getConfig();

            if($config['view'] !== 'list') {
                $resource->attributes = $this->serializer->getAttributes($entity, $fields);
            }
        } else {

            $resource->attributes = $this->serializer->getAttributes($entity, $fields);

            $relationships = $this->serializer->getRelationships($entity);

            if(!empty($relationships)) {
                $resource->relationships = $relationships;
            }
        }

        if(!empty($this->included)) {
            $resource->included = $this->getIncluded();
        }

        return $resource;
    }

    /**
     * @return array
     */
    protected function getIncluded()
    {
        $included = array();

        foreach ($this->included as $includes) {
            foreach ($includes as $includedType => $relation) {
                if (empty($relation)) {
                    $originalType = Inflect::singularize($includedType);
                    $included[] = $this->serializer->getIncluded($originalType, $this->entity);
                } else {
                    $path = explode('.', $relation);
                    array_unshift($path, $includedType);
                    $result = $this->crawlRelations($this->entity, $path);
                    $included[] = $this->serializer->getIncludedResources(Inflect::singularize($result['type']), $result['collection']);
                }
            }
        }

        return $included;
    }

    /**
     * Crawl entities to last one
     *
     * @param $entity
     * @param $relations
     * @return array
     */
    public function crawlRelations($entity, $relations)
    {
        $collection = array($entity);
        $type = end($relations);

        while($relation = array_shift($relations)) {
            $newCollection = array();

            foreach($collection as $entity) {
                $result = ClassHelper::getPropertyValue($entity, $relation);


                if($result instanceof PersistentCollection) {
                    $newCollection = array_merge($newCollection, $result->toArray());
                } else {
                    $newCollection =  array_merge($newCollection, array($result));
                }
            }

            $collection = $newCollection;
        }

        return array('type' => $type, 'collection' => $collection);
    }

}