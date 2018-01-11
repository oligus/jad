<?php

namespace Jad\CRUD;

use Jad\Common\ClassHelper;

/**
 * Class Create
 * @package Jad\CRUD
 */
class Create extends AbstractCRUD
{
    /**
     * @return mixed|null
     * @throws \Doctrine\ORM\ORMException
     * @throws \Jad\Exceptions\JadException
     * @throws \Jad\Exceptions\RequestException
     */
    public function createResource()
    {
        $mapItem        = $this->getMapItem();
        $entityClass    = $mapItem->getEntityClass();
        $entity         = new $entityClass;

        if($mapItem->isReadOnly()) {
            return null;
        }

        $this->addAttributes($mapItem, $this->getAttributes(), $entity);
        $this->validateEntity($entity);
        $this->addRelationships($this->request->getInputJson(), $entity);

        $this->mapper->getEm()->persist($entity);
        $this->mapper->getEm()->flush();

        /** @var \Jad\Map\MapItem $mapItem */
        $id = (string) ClassHelper::getPropertyValue($entity, $mapItem->getIdField());

        return $id;
    }
}