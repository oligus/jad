<?php

namespace Jad;

use Jad\Map\Mapper;
use Jad\Serializers\ErrorDocument;
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
        try {
            $document = $this->getDocument();
            return json_encode($document);
        } catch (\Exception $e) {
            $errorDocument = new ErrorDocument();
            $errorDocument->addError($e);
            return json_encode($errorDocument);
        }
    }

    private function getDocument()
    {
        $method = $this->requestHandler->getRequest()->getMethod();
        $dh = new DoctrineHandler($this->entityMap, $this->requestHandler);

        switch($method) {
            case 'PATCH':
                $input = json_decode(file_get_contents("php://input"));
                $dh->updateEntity($input);
                break;

            case 'POST':
                $input = json_decode(file_get_contents("php://input"));
                $dh->createEntity($input);
                break;

            case 'DELETE':
                $dh->deleteEntity($this->requestHandler->getId());
                break;
        }

        if($this->requestHandler->hasId()) {
            $resource = $dh->getEntityById($this->requestHandler->getId());
            $document = new Document($resource);
        } else {
            $collection = $dh->getEntities();
            $document = new Document($collection);
        }

        $document->addLink('self', $this->getUrl());

        return $document;
    }

    private function getUrl()
    {
        return $this->requestHandler->getRequest()->getSchemeAndHttpHost()
            . $this->requestHandler->getRequest()->getPathInfo();
    }
}