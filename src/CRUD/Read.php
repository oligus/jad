<?php

namespace Jad\CRUD;

use Jad\Document\Resource;
use Jad\Document\Collection;
use Jad\Serializers\EntitySerializer;
use Jad\Exceptions\ResourceNotFoundException;

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
        $resource->setIncluded($this->request->getParameters()->getInclude($mapItem->getClassMeta()->getAssociationNames()));

        return $resource;
    }

    public function getResources()
    {
        $type = $this->request->getResourceType();

        $mapItem = $this->mapper->getMapItem($type);

        $limit = $this->request->getParameters()->getLimit(25);
        $offset = $this->request->getParameters()->getOffset(25);
        $filter = $this->request->getParameters()->getFilter($type);

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
            ->findBy($criteria, $this->getOrderBy($type), $limit, $offset);

        $collection = new Collection();

        foreach($entities as $entity) {
            $resource = new Resource($entity, new EntitySerializer($this->mapper, $type, $this->request));
            $resource->setFields($this->request->getParameters()->getFields());
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