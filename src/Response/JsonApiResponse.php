<?php

namespace Jad\Response;

use Jad\Common\Inflect;
use Jad\Exceptions\ResourceNotFoundException;
use Jad\Map\Mapper;
use Jad\Common\ClassHelper;
use Jad\Document\Collection;
use Jad\Document\Resource;
use Jad\Document\Links;
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
        $input = $this->request->getInputJson();
        $type = $input->data->type;
        $id = $this->request->getId();

        $attributes = isset($input->data->attributes) ? (array) $input->data->attributes : [];
        $mapItem = $this->mapper->getMapItem($type);
        $entityClass = $mapItem->getEntityClass();

        $entity = $this->mapper->getEm()->getRepository($mapItem->getEntityClass())->find($id);

        if(!$entity instanceof $entityClass) {
            throw new \Exception('Entity not found');
        }

        foreach($attributes as $attribute => $value) {
            if($mapItem->getClassMeta()->hasField($attribute)) {
                ClassHelper::setPropertyValue($entity, $attribute, $value);
            }
        }


        $relationships = isset($input->data->relationships) ? (array) $input->data->relationships : [];

        foreach($relationships as $relatedType => $related) {
            $relatedData = $related->data;
            $related = is_array($relatedData) ? $relatedData : [$relatedData];
            $relatedProperty = ClassHelper::getPropertyValue($entity, $relatedType);

            foreach($related as $relationship) {
                $type = $relationship->type;
                $id = $relationship->id;
                ;
                $relationalMapItem = $this->mapper->getMapItem($type);
                $relationalClass = $relationalMapItem->getEntityClass();

                $reference = $this->mapper->getEm()->getReference($relationalClass, $id);

                if($relatedProperty instanceof \Doctrine\Common\Collections\Collection) {
                    // First try entity add method, else add straight to collection
                    $method1 = 'add' . ucfirst($type);
                    $method2 = 'add' . ucfirst($relatedType);
                    $method = method_exists($entity, $method1) ? $method1 : $method2;



                    if(method_exists($entity, $method)) {
                        $entity->$method($reference);
                    } else {
                        $relatedProperty->add($reference);
                        echo "sdfsdf"; die;
                    }
                } else {
                    ClassHelper::setPropertyValue($entity, $relatedProperty, $reference);
                }
            }
        }

        $this->mapper->getEm()->persist($entity);
        $this->mapper->getEm()->flush();

        $this->fetchSingleResourceById($this->request->getId());
    }

    public function createResource()
    {
        $input = $this->request->getInputJson();
        $type = $input->data->type;

        $attributes = isset($input->data->attributes) ? (array) $input->data->attributes : [];
        $mapItem = $this->mapper->getMapItem($type);
        $entityClass = $mapItem->getEntityClass();

        $entity = new $entityClass;

        foreach($attributes as $attribute => $value) {
            if($mapItem->getClassMeta()->hasField($attribute)) {
                ClassHelper::setPropertyValue($entity, $attribute, $value);
            }
        }

        $relationships = isset($input->data->relationships) ? (array) $input->data->relationships : [];

        foreach($relationships as $relatedType => $related) {
            $relatedData = $related->data;
            $related = is_array($relatedData) ? $relatedData : [$relatedData];
            $relatedProperty = ClassHelper::getPropertyValue($entity, $relatedType);

            foreach($related as $relationship) {
                $type = $relationship->type;
                $id = $relationship->id;

                $relationalMapItem = $this->mapper->getMapItem($type);
                $relationalClass = $relationalMapItem->getEntityClass();

                $reference = $this->mapper->getEm()->getReference($relationalClass, $id);

                if($relatedProperty instanceof \Doctrine\Common\Collections\Collection) {

                    // First try entity add method, else add straight to collection
                    $method = 'add' . ucfirst($type);
                    if(method_exists($entity, $method)) {
                        $entity->$method($reference);
                    } else {
                        $relatedProperty->add($reference);
                    }
                } else {
                    ClassHelper::setPropertyValue($entity, $relatedProperty, $reference);
                }
            }
        }

        $this->mapper->getEm()->persist($entity);
        $this->mapper->getEm()->flush();

        /** @var \Jad\Map\MapItem $mapItem */
        $id = ClassHelper::getPropertyValue($entity, $mapItem->getIdField());

        $this->fetchSingleResourceById($id);
    }

    public function deleteResource()
    {
        $mapItem = $this->mapper->getMapItem($this->request->getType());
        $entity = $this->mapper->getEm()->getRepository($mapItem->getEntityClass())->find($this->request->getId());
        $entityClass = $mapItem->getEntityClass();

        if($entity instanceof $entityClass) {
            $this->mapper->getEm()->remove($entity);
            $this->mapper->getEm()->flush();
        }

        $this->setResponse('', array(), 204);
    }

    public function fetchResources()
    {
        $relationship = $this->request->getRelationship();

        if(is_null($relationship)) {
            if($this->request->hasId()) {
                $this->fetchSingleResourceById($this->request->getId());
            } else {
                $this->createDocument($this->getEntities());
            }
        } else {
            $this->createDocument($this->getRelationship($relationship));
        }
    }

    public function fetchSingleResourceById($id)
    {
        $resource = $this->getEntityById($id);
        $this->createDocument($resource);
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

        if(empty($entity)) {
            throw new ResourceNotFoundException('Resource type not found [' . $this->request->getType() . '] with id [' . $this->request->getId() . ']');
        }

        $resourceType = $relationship['type'];

        if(!ClassHelper::hasPropertyValue($entity, $resourceType)) {
            $resourceType = Inflect::pluralize($resourceType);
        }

        $result = ClassHelper::getPropertyValue($entity, $resourceType);

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
     * @param \JsonSerializable $resource
     */
    private function createDocument(\JsonSerializable $resource)
    {
        $document = new Document($resource);

        $links = new Links();
        $links->setSelf($this->request->getCurrentUrl());
        $document->addLinks($links);

        $this->setResponse(json_encode($document));
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
