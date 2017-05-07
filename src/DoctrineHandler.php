<?php

namespace Jad;

use Jad\Map\EntityMap;
use Doctrine\ORM\EntityManagerInterface;
use Tobscure\JsonApi\Resource;

class DoctrineHandler
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var RequestHandler
     */
    private $requestHandler;

    private $entityMap;

    /**
     * DoctrineHandler constructor.
     * @param EntityManagerInterface $em
     * @param \Jad\RequestHandler $requestHandler
     * @param EntityMap $entityMap
     */
    public function __construct(
        EntityManagerInterface $em,
        RequestHandler $requestHandler,
        EntityMap $entityMap
    )
    {
        $this->em = $em;
        $this->requestHandler = $requestHandler;
        $this->entityMap = $entityMap;
    }


    public function getEntity($entityClass, $entityId): Resource
    {
        $entity = $this->em->getRepository($entityClass)->find($entityId);
        $metadata = $this->em->getClassMetadata($entityClass);
    }
}