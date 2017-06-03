<?php

namespace Jad\Response;

use Jad\Map\Mapper;
use Jad\Common\ClassHelper;
use Jad\Document\Collection;
use Jad\Document\Resource;
use Jad\Document\JsonDocument as Document;
use Jad\Request\JsonApiRequest as Request;
use Jad\Serializers\EntitySerializer;
use Jad\Serializers\RelationshipSerializer;
use Jad\Exceptions\JadException;

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
     * @throws JadException
     */
    public function render()
    {
        $method = $this->request->getMethod();

        switch($method) {
            case 'PATCH':
                $this->updateResource();
                break;

            case 'POST':
                $this->createResource();
                break;

            case 'DELETE':
                $this->deleteResource();
                break;

            case 'GET':
                $this->fetchResources();
                break;

            default:
                throw new JadException('Http method [' . $method . '] is not supported.');
        }
    }

    public function updateResource()
    {

    }

    public function createResource()
    {

    }

    public function deleteResource()
    {

    }

    public function fetchResources()
    {
        $relationship = $this->request->getRelationship();

        if(is_null($relationship)) {
            if($this->request->hasId()) {
                $resource = $this->getEntityById($this->request->getId());
                $document = new Document($resource);
            } else {
                $collection = $this->getEntities();
                $document = new Document($collection);
            }
        } else {
            $resource = $this->getRelationship($relationship);
            $document = new Document($resource);
        }

        $this->setResponse(json_encode($document));
    }

    /**
     * @param $id
     * @return Resource
     * @throws \Exception
     */
    public function getEntityById($id)
    {
        /** @var \Jad\Map\MapItem $mapItem */
        $mapItem = $this->mapper->getMapItem($this->request->getType());
        $entity = $this->mapper->getEm()->getRepository($mapItem->getEntityClass())->find($id);

        if(is_null($entity)) {
            throw new JadException(
                'Resource of type [' . $this->request->getType() . '] with id [' . $id . '] could not be found.', 404
            );
        }

        $resource = new Resource($entity, new EntitySerializer($this->mapper, $this->request->getType(), $this->request));
        $resource->setFields($this->request->getParameters()->getFields());
        $resource->setIncluded($this->request->getParameters()->getInclude($mapItem->getClassMeta()->getAssociationNames()));

        return $resource;
    }

    /**
     * @return Collection
     */
    public function getEntities()
    {
        $mapItem = $this->mapper->getMapItem($this->request->getType());

        $limit = $this->request->getParameters()->getLimit(25);
        $offset = $this->request->getParameters()->getOffset(25);
        $filter = $this->request->getParameters()->getFilter($this->request->getType());

        $criteria = array();

        if(!is_null($filter)) {
            // TODO full criteria support
            //$criteria = new \Doctrine\Common\Collections\Criteria();
            //$criteria->where($criteria->expr()->gt('prize', 200));

            foreach($filter as $field => $values) {
                $criteria[$field] = $values['eq'];
            }
        }

        $entities = $this->mapper->getEm()->getRepository($mapItem->getEntityClass())
            ->findBy($criteria, $this->getOrderBy($this->request->getType()), $limit, $offset);

        $collection = new Collection();

        foreach($entities as $entity) {
            $resource = new Resource($entity, new EntitySerializer($this->mapper, $this->request->getType(), $this->request));
            $resource->setFields($this->request->getParameters()->getFields());
            $collection->add($resource);
        }

        return $collection;
    }

    public function getRelationship($relationship)
    {
        /** @var \Jad\Map\MapItem $mapItem */
        $mapItem = $this->mapper->getMapItem($this->request->getType());
        $entity = $this->mapper->getEm()->getRepository($mapItem->getEntityClass())->find($this->request->getId());

        $result = ClassHelper::getPropertyValue($entity, $relationship['type']);

        $serializer = new RelationshipSerializer($this->mapper, $this->request->getType(), $this->request);
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
     * @param $type
     * @return array|null
     */
    public function getOrderBy($type)
    {
        $orderBy = null;
        $mapItem = $this->mapper->getMapItem($type);
        $available = $mapItem->getClassMeta()->getFieldNames();

        $result = $this->request->getParameters()->getSort($available);

        if(!empty($result)) {
            $orderBy = $result;
        }

        return $orderBy;
    }

    /**
     * @param $content
     * @param array $headers
     * @param int $status
     */
    private function setResponse($content, array $headers = array(), $status = 200)
    {
        $response = new Response();
        $headers['Content-Type'] = 'application/vnd.api+json; charset=UTF-8';

        foreach($headers as $key => $value) {
            $response->headers->set($key, $value);
        }

        $response->setContent($content);
        $response->setStatusCode($status);
        $response->sendHeaders();
        $response->sendContent();
    }
}
