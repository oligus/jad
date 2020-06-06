<?php declare(strict_types=1);

namespace Jad\CRUD;

use Doctrine\ORM\NonUniqueResultException;
use Exception;
use Jad\Document\Resource as JadResource;
use Jad\Document\Collection;
use Jad\Exceptions\JadException;
use Jad\Exceptions\ParameterException;
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
     * @throws ResourceNotFoundException
     * @throws NonUniqueResultException
     * @throws JadException
     * @throws ParameterException
     *
     */
    public function getResourceById(string $id): JadResource
    {
        $mapItem = $this->mapper->getMapItem($this->request->getResourceType());

        $qb = new QueryBuilder($this->mapper->getEm());

        $qb->select('t');
        $qb->from($mapItem->getEntityClass(), 't');
        $qb->where(
            $qb->expr()->eq('t.' . $mapItem->getIdField(), ':id')
        );
        $qb->setParameter('id', $id);
        $query = $qb->getQuery();

        foreach (array_keys($mapItem->getClassMeta()->getAssociationMappings()) as $relation) {
            $query->setFetchMode($mapItem->getEntityClass(), (string) $relation, ClassMetadata::FETCH_EAGER);
        }

        $entity = $query->getOneOrNullResult();

        if (is_null($entity)) {
            throw new ResourceNotFoundException(
                'Resource of type [' . $this->request->getResourceType() . '] with id [' . $id . '] could not be found.'
            );
        }

        $resource = new JadResource(
            $entity,
            new EntitySerializer($this->mapper, $this->request->getResourceType(), $this->request)
        );
        $resource->setFields($this->request->getParameters()->getFields());
        $resource->setIncludedParams($this->request->getParameters()->getInclude($mapItem->getClassMeta()->getAssociationNames()));

        return $resource;
    }

    /**
     * @throws NonUniqueResultException
     * @throws JadException
     * @throws ParameterException
     * @throws Exception
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

        if ($paginator->isActive()) {
            $countQb = clone $filter->getQb();
            $countQb->select($countQb->expr()->count($filter->getRootAlias() . '.' . $mapItem->getIdField()));
            $count = (int)$countQb->getQuery()->getSingleScalarResult();
            $paginator->setCount($count);
        }

        $qb = $filter->getQb();
        $qb->setMaxResults($limit);
        $qb->setFirstResult($offset);

        $sort = $this->getOrderBy($this->request->getResourceType());

        if (!empty($sort)) {
            foreach ($sort as $property => $direction) {
                $qb->addOrderBy($filter->getRootAlias() . '.' . $property, $direction);
            }
        }

        $query = $qb->getQuery();

        foreach (array_keys($mapItem->getClassMeta()->getAssociationMappings()) as $relation) {
            $query->setFetchMode($mapItem->getEntityClass(), (string)$relation, ClassMetadata::FETCH_EAGER);
        }

        $entities = $query->getResult();
        $collection = new Collection();
        $collection->setPaginator($paginator);

        foreach ($entities as $entity) {
            $resource = new JadResource(
                $entity,
                new EntitySerializer($this->mapper, $this->request->getResourceType(), $this->request)
            );
            $resource->setFields($this->request->getParameters()->getFields());
            $resource->setIncludedParams($this->request->getParameters()->getInclude($mapItem->getClassMeta()->getAssociationNames()));
            $collection->add($resource);
        }

        return $collection;
    }

    /**
     * @return array<string>|null
     * @throws ParameterException
     */
    public function getOrderBy(string $resourceType): ?array
    {
        $orderBy = null;
        $mapItem = $this->mapper->getMapItem($resourceType);
        $available = $mapItem->getClassMeta()->getFieldNames();

        $result = $this->request->getParameters()->getSort($available);

        if (!empty($result)) {
            $orderBy = $result;
        }

        return $orderBy;
    }
}
