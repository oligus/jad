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

}