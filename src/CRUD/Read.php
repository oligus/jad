<?php

namespace Jad\CRUD;

use Jad\Document\Resource;
use Jad\Document\Collection;
use Jad\Serializers\EntitySerializer;
use Jad\Exceptions\ResourceNotFoundException;
use Jad\Query\Paginator;
use Jad\Query\Filter;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Mapping\ClassMetadata;
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
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Jad\Exceptions\JadException
     * @throws \Jad\Exceptions\ParameterException
     *
     */
    public function getResourceById($id)
    {
        /** @var \Jad\Map\MapItem $mapItem */
        $mapItem = $this->mapper->getMapItem($this->request->getResourceType());
        //$entity = $this->mapper->getEm()->getRepository($mapItem->getEntityClass())->find($id);

        $qb = new QueryBuilder($this->mapper->getEm());

        $qb->select('t');
        $qb->from($mapItem->getEntityClass(), 't');
        $qb->where(
            $qb->expr()->eq('t.' . $mapItem->getIdField(), ':id')
        );
        $qb->setParameter('id', $id);
        $query = $qb->getQuery();

        foreach(array_keys($mapItem->getClassMeta()->getAssociationMappings()) as $relation) {
            $query->setFetchMode($mapItem->getEntityClass(), $relation, ClassMetadata::FETCH_EAGER);
        }

        $entity = $query->getOneOrNullResult();

        if(is_null($entity)) {
            throw new ResourceNotFoundException(
                'Resource of type [' . $this->request->getResourceType() . '] with id [' . $id . '] could not be found.'
            );
        }

        $resource = new Resource($entity, new EntitySerializer($this->mapper, $this->request->getResourceType(), $this->request));
        $resource->setFields($this->request->getParameters()->getFields());
        $resource->setIncludedParams($this->request->getParameters()->getInclude($mapItem->getClassMeta()->getAssociationNames()));

        return $resource;
    }

    /**
     * @return Collection
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Jad\Exceptions\JadException
     * @throws \Jad\Exceptions\ParameterException
     */
    public function getResources(): Collection
    {
        $mapItem = $this->mapper->getMapItem($this->request->getResourceType());
        $qb = new QueryBuilder($this->mapper->getEm());

        $filterParams = $this->request->getParameters()->getFilter();
        $filter = new Filter($filterParams, $mapItem->getType());

        $qb->select($filter->getAliases());
        $qb->from($mapItem->getEntityClass(), $filter->getRootAlias());

        $filter->setQb($qb);
        $filter->process();

        $paginator = new Paginator($this->request);
        $paginator->setActive($mapItem->isPaginate());
        $limit = $paginator->getLimit();
        $offset = $paginator->getOffset();

        if($paginator->isActive()) {
            $countQb = clone $filter->getQb();
            $countQb->select($countQb->expr()->count($filter->getRootAlias() . '.'.$mapItem->getIdField()));
            $count = $countQb->getQuery()->getSingleScalarResult();
            $paginator->setCount($count);
        }

        $qb = $filter->getQb();
        $qb->setMaxResults($limit);
        $qb->setFirstResult($offset);

        $sort = $this->getOrderBy($this->request->getResourceType());

        if(!empty($sort)) {
            foreach($sort as $property => $direction) {
                $qb->addOrderBy($filter->getRootAlias() . '.' . $property, $direction);
            }
        }

        $query = $qb->getQuery();

        foreach(array_keys($mapItem->getClassMeta()->getAssociationMappings()) as $relation) {
            $query->setFetchMode($mapItem->getEntityClass(), $relation, ClassMetadata::FETCH_EAGER);
        }

        $entities = $query->getResult();
        $collection = new Collection();
        $collection->setPaginator($paginator);

        foreach($entities as $entity) {
            $resource = new Resource($entity, new EntitySerializer($this->mapper, $this->request->getResourceType(), $this->request));
            $resource->setFields($this->request->getParameters()->getFields());
            $resource->setIncludedParams($this->request->getParameters()->getInclude($mapItem->getClassMeta()->getAssociationNames()));
            $collection->add($resource);
        }

        return $collection;
    }

    /**
     * @param $type
     * @return array|null
     * @throws \Jad\Exceptions\ParameterException
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