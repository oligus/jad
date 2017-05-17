<?php

namespace Jad;

use Jad\Map\Mapper;
use Jad\Serializers\EntitySerializer;
use Tobscure\JsonApi\Resource;
use Tobscure\JsonApi\Collection;

class DoctrineHandler
{
    /**
     * @var Mapper $mapper
     */
    private $mapper;

    /**
     * @var RequestHandler $requestHandler
     */
    private $requestHandler;

    /**
     * DoctrineHandler constructor.
     * @param Mapper $mapper
     * @param RequestHandler $requestHandler
     */
    public function __construct(Mapper $mapper, RequestHandler $requestHandler)
    {
        $this->mapper = $mapper;
        $this->requestHandler = $requestHandler;
    }

    /**
     * @param $id
     * @return \Tobscure\JsonApi\Resource
     */
    public function getEntityById($id): \Tobscure\JsonApi\Resource
    {
        $type = $this->requestHandler->getType();
        $mapItem = $this->mapper->getMapItem($type);
        $entity = $this->mapper->getEm()->getRepository($mapItem->getEntityClass())->find($id);

        $resource = new Resource($entity, new EntitySerializer($this->mapper, $type));
        $resource->fields($this->requestHandler->getParameters()->getFields());

        return $resource;
    }

    /**
     * @return Collection
     */
    public function getEntities(): Collection
    {
        $type = $this->requestHandler->getType();
        $mapItem = $this->mapper->getMapItem($type);

        $limit = $this->requestHandler->getParameters()->getLimit(100);
        $offset = $this->requestHandler->getParameters()->getOffset(25);

        $entities = $this->mapper->getEm()->getRepository($mapItem->getEntityClass())
            ->findBy($criteria = [], $this->getOrderBy($type), $limit, $offset);

        $collection = new Collection($entities, new EntitySerializer($this->mapper, $type));
        $collection->fields($this->requestHandler->getParameters()->getFields());

        return $collection;
    }

    public function getOrderBy($type)
    {
        $orderBy = null;
        $mapItem = $this->mapper->getMapItem($type);
        $available = $mapItem->getClassMeta()->getFieldNames();

        $result = $this->requestHandler->getParameters()->getSort($available);

        if(!empty($result)) {
            $orderBy = $result;
        }

        return $orderBy;
    }
}