<?php

namespace Jad\CRUD;

use Jad\Document\Resource;
use Jad\Document\Collection;
use Jad\Exceptions\JadException;
use Jad\Serializers\EntitySerializer;
use Jad\Exceptions\ResourceNotFoundException;
use Jad\Query\Paginator;
use Jad\Query\Filter;
use Doctrine\ORM\QueryBuilder;

/**
 * Class Read
 * @package Jad\CRUD
 */
class Read extends AbstractCRUD
{
    /**
     * @param $id
     * @return \Jad\Document\Resource
     * @throws ResourceNotFoundException
     */
    public function getResourceById($id)
    {
        $type = $this->request->getResourceType();

        /** @var \Jad\Map\MapItem $mapItem */
        $mapItem = $this->mapper->getMapItem($type);
        $entity = $this->mapper->getEm()->getRepository($mapItem->getEntityClass())->find($id);

        if(is_null($entity)) {
            throw new ResourceNotFoundException(
                'Resource of type [' . $type . '] with id [' . $id . '] could not be found.'
            );
        }

        $resource = new Resource($entity, new EntitySerializer($this->mapper, $type, $this->request));
        $resource->setFields($this->request->getParameters()->getFields());
        $resource->setIncludedParams($this->request->getParameters()->getInclude($mapItem->getClassMeta()->getAssociationNames()));

        return $resource;
    }

    public function getResources()
    {
        $type = $this->request->getResourceType();
        $mapItem = $this->mapper->getMapItem($type);

        $qb = new QueryBuilder($this->mapper->getEm());

        $qb->select('t');
        $qb->from($mapItem->getEntityClass(), 't');

        $filterParams = $this->request->getParameters()->getFilter($type);
        $filter = new Filter($filterParams, $qb);
        $filter->process();

        $paginator = new Paginator($this->request->getParameters());
        $limit = $paginator->getLimit();
        $offset = $paginator->getOffset();

        $qb = $filter->getQb();
        $qb->setMaxResults($limit);
        $qb->setFirstResult($offset);

        $sort = $this->getOrderBy($type);
        if(!empty($sort)) {
            foreach($sort as $property => $direction) {
                $qb->orderBy('t.' . $property, $direction);
            }
        }

        $entities = $qb->getQuery()->getResult();
        $collection = new Collection();

        foreach($entities as $entity) {
            $resource = new Resource($entity, new EntitySerializer($this->mapper, $type, $this->request));
            $resource->setFields($this->request->getParameters()->getFields());
            $resource->setIncludedParams($this->request->getParameters()->getInclude($mapItem->getClassMeta()->getAssociationNames()));
            $collection->add($resource);
        }

        return $collection;
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
}