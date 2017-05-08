<?php

namespace Jad;

use Jad\Map\EntityMap;
use Doctrine\ORM\EntityManager;
use Tobscure\JsonApi\Document;

class Jad
{
    /**
     * @var EntityManager $em
     */
    private $em;

    /**
     * @var EntityMap $entityMap
     */
    private $entityMap;

    /**
     * @var RequestHandler $requestHandler
     */
    private $requestHandler;

    /**
     * Jad constructor.
     * @param EntityManager $em
     * @param EntityMap $entityMap
     */
    public function __construct(EntityManager $em, EntityMap $entityMap)
    {
        $this->em = $em;
        $this->entityMap = $entityMap;
        $this->requestHandler = new RequestHandler();
    }

    /**
     * @return \Jad\RequestHandler
     */
    public function getRequestHandler(): RequestHandler
    {
        return $this->requestHandler;
    }

    /**
     * @param $pathPrefix
     */
    public function setPathPrefix($pathPrefix)
    {
        $this->getRequestHandler()->setPathPrefix($pathPrefix);
    }

    public function jsonApiResult()
    {
        $type = $this->requestHandler->getType();
        $mapItem = $this->entityMap->getEntityMapItem($type);

        $dh = new DoctrineHandler($mapItem, $this->em, $this->requestHandler);

        $resource = $dh->getEntityById($this->requestHandler->getId());
        $document = new Document($resource);

        return json_encode($document);
    }
}