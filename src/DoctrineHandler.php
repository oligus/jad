<?php

namespace Jad;

use Jad\Map\EntityMapItem;
use Doctrine\ORM\EntityManagerInterface;
use Tobscure\JsonApi\Resource;
use Tobscure\JsonApi\Collection;

class DoctrineHandler
{
    /**
     * @var EntityMapItem
     */
    private $entityMapItem;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var RequestHandler $requestHandler
     */
    private $requestHandler;

    /**
     * DoctrineHandler constructor.
     * @param EntityMapItem $entityMapItem
     * @param EntityManagerInterface $em
     * @param RequestHandler $requestHandler
     */
    public function __construct(EntityMapItem $entityMapItem, EntityManagerInterface $em, RequestHandler $requestHandler)
    {
        $this->entityMapItem = $entityMapItem;
        $this->em = $em;
        $this->requestHandler = $requestHandler;
    }

    public function getEntityById($id): Resource
    {
        $entityClass = $this->entityMapItem->getEntityClass();
        $this->entityMapItem->setClassMeta($this->em->getClassMetadata($entityClass));
        $entity = $this->em->getRepository($entityClass)->find($id);

        $resource = new Resource($entity, new Serializer($this->entityMapItem));
        $resource->fields($this->requestHandler->getParameters()->getFields());

        return $resource;
    }

    public function getEntities()
    {
        $entityClass = $this->entityMapItem->getEntityClass();
        $this->entityMapItem->setClassMeta($this->em->getClassMetadata($entityClass));

        $entities = $this->em->getRepository($entityClass)
            ->findBy($criteria = [], $orderBy = null, $limit = null, $offset = null);

        $collection = new Collection($entities, new Serializer($this->entityMapItem));
        $collection->fields($this->requestHandler->getParameters()->getFields());

        return $collection;
    }
}