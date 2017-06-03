<?php

namespace Jad;

use Jad\Map\MapItem;
use Jad\Map\Mapper;
use Jad\Document\Resource;
use Jad\Document\EntitySerializer;
use Jad\Request\JsonApiRequest;

/**
 * Class DoctrineHandler
 * @package Jad
 */
class DoctrineHandler
{
    /**
     * @var Mapper $mapper
     */
    private $mapper;

    /**
     * @var string
     */
    private $type;

    /**
     * @var JsonApiRequest $jsonApiRequest
     */
    private $jsonApiRequest;

    /**
     * DoctrineHandler constructor.
     * @param Mapper $mapper
     * @param JsonApiRequest $jsonApiRequest
     */
    public function __construct(Mapper $mapper, JsonApiRequest $jsonApiRequest)
    {
        $this->mapper = $mapper;
        $this->jsonApiRequest = $jsonApiRequest;
        $this->type = $jsonApiRequest->getType();
    }

    /**
     * @return array
     */
    public function getEntityCollection(): array
    {
        $mapItem = $this->mapper->getMapItem($this->type);
        $limit = $this->jsonApiRequest->getParameters()->getLimit(100);
        $offset = $this->jsonApiRequest->getParameters()->getOffset(25);

        $entities = $this->mapper->getEm()->getRepository($mapItem->getEntityClass())
            ->findBy($criteria = [], $this->getOrderBy($this->type), $limit, $offset);

        $collection = [];

        foreach($entities as $entity) {
            $resource = new Resource($entity, new EntitySerializer($this->mapper, $this->type));
            $resource->setFields($this->jsonApiRequest->getParameters()->getFields());

            $availableAssociations = $mapItem->getClassMeta()->getAssociationNames();
            $relationships = $this->jsonApiRequest->getParameters()->getInclude($availableAssociations);
            $resource->setRelationships($availableAssociations);

            if(!empty($relationships)) {
                die('$resource->with($relationships');
                //$resource->with($relationships);
            }

            $collection[] = $resource;
        }

        return $collection;
    }

    /**
     * @param $id
     * @return Document\Resource
     */
    public function getEntityById($id): Document\Resource
    {
        $mapItem = $this->mapper->getMapItem($this->type);
        $entity = $this->mapper->getEm()->getRepository($mapItem->getEntityClass())->find($id);

        $resource = new Resource($entity, new EntitySerializer($this->mapper, $this->type));
        $resource->setFields($this->jsonApiRequest->getParameters()->getFields());

        $availableAssociations = $mapItem->getClassMeta()->getAssociationNames();
        $relationships = $this->jsonApiRequest->getParameters()->getInclude($availableAssociations);
        $resource->setRelationships($availableAssociations);

        if(!empty($relationships)) {
            die('$resource->with($relationships');
            //$resource->with($relationships);
        }

        return $resource;
    }

    public function updateEntity($input)
    {
        $type = $input->data->type;
        $id = $input->data->id;
        $attributes = isset($input->data->attributes) ? (array) $input->data->attributes : [];
        $mapItem = $this->mapper->getMapItem($type);

        $entity = $this->mapper->getEm()->getRepository($mapItem->getEntityClass())->find($id);
        $entityClass = $mapItem->getEntityClass();

        if($entity instanceof $entityClass) {
            foreach($attributes as $attribute => $value) {
                if($mapItem->getClassMeta()->hasField($attribute)) {
                    $this->setEntityAttribute($entity, $attribute, $value);
                }
            }

            $relationships = isset($input->data->relationship) ? (array) $input->data->relationship : [];

            foreach($relationships as $relationship) {
                $type = $relationship->data->type;
                $id = $relationship->data->id;

                $relationalMapItem = $this->mapper->getMapItem($type);
                $relationalClass = $relationalMapItem->getEntityClass();

                $reference = $this->mapper->getEm()->getReference($relationalClass, $id);

                $method = 'add' . ucfirst($type);

                if(method_exists($entity, $method)) {
                    $entity->$method($reference);
                }
            }

            $this->mapper->getEm()->persist($entity);
            $this->mapper->getEm()->flush();
        }
    }

    public function createEntity($input)
    {
        $type = $input->data->type;
        $attributes = isset($input->data->attributes) ? (array) $input->data->attributes : [];
        $mapItem = $this->mapper->getMapItem($type);
        $entityClass = $mapItem->getEntityClass();

        $entity = new $entityClass;

        foreach($attributes as $attribute => $value) {
            if($mapItem->getClassMeta()->hasField($attribute)) {
                $this->setEntityAttribute($entity, $attribute, $value);
            }
        }

        $relationships = isset($input->data->relationship) ? (array) $input->data->relationship : [];

        foreach($relationships as $relationship) {
            $type = $relationship->data->type;
            $id = $relationship->data->id;

            $relationalMapItem = $this->mapper->getMapItem($type);
            $relationalClass = $relationalMapItem->getEntityClass();

            $reference = $this->mapper->getEm()->getReference($relationalClass, $id);

            $method = 'add' . ucfirst($type);

            if(method_exists($entity, $method)) {
                $entity->$method($reference);
            }
        }

        $this->mapper->getEm()->persist($entity);
        $this->mapper->getEm()->flush();
    }

    /**
     * @param $id
     */
    public function deleteEntity($id)
    {
        $mapItem = $this->mapper->getMapItem($this->type);
        $entity = $this->mapper->getEm()->getRepository($mapItem->getEntityClass())->find($id);
        $entityClass = $mapItem->getEntityClass();

        if($entity instanceof $entityClass) {
            $this->mapper->getEm()->remove($entity);
            $this->mapper->getEm()->flush();
        }
    }

    /**
     * @param $type
     * @param $id
     * @return null|object
     */
    public function getEntity($type, $id)
    {
        $mapItem = $this->mapper->getMapItem($type);

        if($mapItem instanceof MapItem) {
            $entity = $this->mapper->getEm()->getRepository($mapItem->getEntityClass())->find($id);
            return $entity;
        }

        return null;
    }

    /**
     * @param $entity
     * @param $attribute
     * @param $value
     */
    protected function setEntityAttribute($entity, $attribute, $value)
    {
        $methodName = 'set' . ucfirst($attribute);

        if (method_exists($entity, $methodName)) {
            $entity->$methodName($value);
        } else {
            $reflection = new \ReflectionClass($entity);
            if ($reflection->hasProperty($attribute)) {
                $reflectionProperty = $reflection->getProperty($attribute);
                $reflectionProperty->setAccessible(true);
                $reflectionProperty->setValue($entity, $value);
            }
        }
    }

    /**
     * @param $type
     * @return array|null
     */
    public function getOrderBy($type)
    {
        $orderBy = null;
        $mapItem = $this->mapper->getMapItem($type);
        $available = $mapItem->getClassMeta()->getFieldNames();

        $result = $this->jsonApiRequest->getParameters()->getSort($available);

        if(!empty($result)) {
            $orderBy = $result;
        }

        return $orderBy;
    }
}