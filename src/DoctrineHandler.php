<?php

namespace Jad;

use Jad\Map\EntityMapItem;
use Doctrine\ORM\EntityManagerInterface;
use Tobscure\JsonApi\Resource;

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

    /**
     * @param $id
     * @return \Tobscure\JsonApi\Resource
     */
    public function getEntityById($id): \Tobscure\JsonApi\Resource
    {
        $entityClass = $this->entityMapItem->getEntityClass();
        $entity = $this->em->getRepository($entityClass)->find($id);
        $metadata = $this->em->getClassMetadata($entityClass);

        $resource = new Resource($entity, new Serializer($this->entityMapItem->getType(), $metadata));
        $resource->fields($this->requestHandler->getParameters()->getFields());

        return $resource;
    }
}