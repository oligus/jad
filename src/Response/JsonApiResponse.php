<?php

namespace Jad\Response;

use Jad\Configure;
use Jad\Document\Meta;
use Jad\Exceptions\ResourceNotFoundException;
use Jad\Map\Mapper;
use Jad\Common\ClassHelper;
use Jad\Document\Collection;
use Jad\Document\Resource;
use Jad\Document\Links;
use Jad\Document\JsonDocument as Document;
use Jad\Request\JsonApiRequest as Request;
use Jad\Serializers\RelationshipSerializer;
use Jad\Exceptions\JadException;

use Jad\CRUD\Create;
use Jad\CRUD\Read;
use Jad\CRUD\Update;
use Jad\CRUD\Delete;

use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\PersistentCollection;

/**
 * Class JsonApiResponse
 * @package Jad\Response
 */
class JsonApiResponse
{
    /**
     * @var Request $request
     */
    private $request;

    /**
     * @var Mapper $mapper
     */
    private $mapper;

    /**
     * JsonApiResponse constructor.
     * @param Request $request
     * @param Mapper $mapper
     */
    public function __construct(Request $request, Mapper $mapper)
    {
        $this->request = $request;
        $this->mapper = $mapper;
    }

    /**
     * @return string
     * @throws JadException
     * @throws ResourceNotFoundException
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Exception
     * @throws \Jad\Exceptions\ParameterException
     * @throws \Jad\Exceptions\RequestException
     */
    public function render()
    {
        if(!$this->mapper->hasMapItem($this->request->getResourceType())) {
            if(Configure::getInstance()->getConfig('strict')) {
                throw new ResourceNotFoundException('Resource type not found [' . $this->request->getResourceType() . ']');
            } else {
                return '';
            }
        }

        $method = $this->request->getMethod();

        switch($method) {
            case 'PATCH':
                (new Update($this->request, $this->mapper))->updateResource();
                $resource = (new Read($this->request, $this->mapper))->getResourceById($this->request->getId());
                $this->createDocument($resource);
                break;

            case 'POST':
                $id = (new Create($this->request, $this->mapper))->createResource();
                $resource = (new Read($this->request, $this->mapper))->getResourceById($id);
                $this->createDocument($resource);
                break;

            case 'DELETE':
                (new Delete($this->request, $this->mapper))->deleteResource();
                $this->setResponse('', array(), 204);
                break;

            case 'GET':
                $this->fetchResources();
                break;

            default:
                throw new JadException('Http method [' . $method . '] is not supported.');
        }
    }

    /**
     * @throws JadException
     * @throws ResourceNotFoundException
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Jad\Exceptions\ParameterException
     * @throws \Jad\Exceptions\RequestException
     */
    public function fetchResources()
    {
        $relationship = $this->request->getRelationship();

        if(empty($relationship)) {
            if($this->request->hasId()) {
                $resource = (new Read($this->request, $this->mapper))->getResourceById($this->request->getId());
                $this->createDocument($resource);
            } else {
                $resources = (new Read($this->request, $this->mapper))->getResources();
                $this->createDocument($resources);
            }
        } else {
            $this->createDocument($this->getRelationship($relationship));
        }
    }

    /**
     * @param $relationship
     * @return Collection|Resource
     * @throws JadException
     * @throws ResourceNotFoundException
     */
    public function getRelationship($relationship)
    {
        $id = $this->request->getId();
        $type = $this->request->getResourceType();

        /** @var \Jad\Map\MapItem $mapItem */
        $mapItem = $this->mapper->getMapItem($type);
        $entity = $this->mapper->getEm()->getRepository($mapItem->getEntityClass())->find($id);

        if(empty($entity)) {
            throw new ResourceNotFoundException('Resource type not found [' . $type . '] with id [' . $id . ']');
        }

        $relatedType = $relationship['type'];

        $result = ClassHelper::getPropertyValue($entity, $relatedType);

        if(is_null($result)) {
            throw new ResourceNotFoundException('Related resource type not found [' . $relatedType . '] derived from [' . $type . ']');
        }

        $serializer = new RelationshipSerializer($this->mapper, $this->request->getResourceType(), $this->request);
        $serializer->setRelationship($relationship);

        if($result instanceof PersistentCollection) {
            $collection = new Collection();
            foreach($result as $entity ) {
                $resource = new Resource($entity, $serializer);
                $resource->setFields($this->request->getParameters()->getFields());
                $collection->add($resource);
            }

            return $collection;
        }

        $resource = new Resource($result, $serializer);
        $resource->setFields($this->request->getParameters()->getFields());

        return $resource;
    }

    /**
     * @param \JsonSerializable $resource
     */
    private function createDocument(\JsonSerializable $resource)
    {
        $document = new Document($resource);

        $links = new Links();
        $links->setSelf($this->request->getCurrentUrl());
        $document->addLinks($links);

        $meta = new Meta();
        $document->addMeta($meta);

        if(Configure::getInstance()->getConfig('debug')) {
            $this->setResponse(json_encode($document, JSON_PRETTY_PRINT));
        } else {
            $this->setResponse(json_encode($document));
        }

    }
    /**
     * @param $content
     * @param array $headers
     * @param int $status
     */
    private function setResponse($content, array $headers = array(), $status = 200)
    {
        $response = new Response();
        $headers['Content-Type'] = 'application/vnd.api+json';

        if(Configure::getInstance()->getConfig('cors')) {
            $headers['Access-Control-Allow-Origin'] = '*';
        }

        foreach($headers as $key => $value) {
            $response->headers->set($key, $value);
        }

        $response->setContent($content);
        $response->setStatusCode($status);
        $response->sendHeaders();
        $response->sendContent();
    }
}
