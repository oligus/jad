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

        $availableAssociations = $mapItem->getClassMeta()->getAssociationNames();
        $relationships = $this->requestHandler->getParameters()->getInclude($availableAssociations);

        if(!empty($relationships)) {
            $resource->with($relationships);
        }

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

        $availableAssociations = $mapItem->getClassMeta()->getAssociationNames();
        $relationships = $this->requestHandler->getParameters()->getInclude($availableAssociations);

        if(!empty($relationships)) {
            $collection->with($relationships);
        }

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

    public function updateEntity()
    {
        $input = json_decode(file_get_contents("php://input"));

        $type = $input->data->type;
        $id = $input->data->id;
        $attributes = (array) $input->data->attributes;
        $mapItem = $this->mapper->getMapItem($type);

        $entity = $this->mapper->getEm()->getRepository($mapItem->getEntityClass())->find($id);
        $entityClass = $mapItem->getEntityClass();

        if($entity instanceof $entityClass) {
            foreach($attributes as $attribute => $value) {
                if($mapItem->getClassMeta()->hasField($attribute)) {
                    $methodName = 'set' . ucfirst($attribute);

                    if(method_exists($entity, $methodName)) {
                        $entity->$methodName($value);
                    } else {
                        $reflection = new \ReflectionClass($entity);

                        if($reflection->hasProperty($attribute)) {
                            $reflectionProperty = $reflection->getProperty($attribute);
                            $reflectionProperty->setAccessible(true);
                            $reflectionProperty->setValue($entity, $value);
                        }
                    }
                }
            }
        }

        $this->mapper->getEm()->flush();
    }

}