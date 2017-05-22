<?php

namespace Jad;

use Jad\Map\Mapper;
use Tobscure\JsonApi\Document;

class Jad
{
    /**
     * @var Mapper $entityMap
     */
    private $entityMap;

    /**
     * @var RequestHandler $requestHandler
     */
    private $requestHandler;

    /**
     * Jad constructor.
     * @param Mapper $entityMap
     */
    public function __construct(Mapper $entityMap)
    {
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
        $method = $this->requestHandler->getRequest()->getMethod();
        $dh = new DoctrineHandler($this->entityMap, $this->requestHandler);

        switch($method) {
            case 'PATCH':
                $dh->updateEntity();
                break;
        }

        if($this->requestHandler->hasId()) {
            $resource = $dh->getEntityById($this->requestHandler->getId());
            //$resource->with('artists');
            $document = new Document($resource);
        } else {
            $collection = $dh->getEntities();
            $document = new Document($collection);
        }

        $document->addLink('self', $this->getUrl());

        return json_encode($document);
    }

    private function getUrl()
    {
        return $this->requestHandler->getRequest()->getSchemeAndHttpHost()
            . $this->requestHandler->getRequest()->getPathInfo();
    }
}