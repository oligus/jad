<?php declare(strict_types=1);

namespace Jad\CRUD;

/**
 * Class Update
 * @package Jad\CRUD
 */
class Update extends AbstractCRUD
{
    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws \Exception
     * @throws \Jad\Exceptions\JadException
     * @throws \Jad\Exceptions\RequestException
     */
    public function updateResource(): void
    {
        $mapItem = $this->getMapItem();
        $entityClass = $mapItem->getEntityClass();
        $entity = $this->mapper->getEm()->getRepository($mapItem->getEntityClass())->find($this->request->getId());

        if (!$entity instanceof $entityClass) {
            throw new \Exception('Entity not found');
        }

        if ($mapItem->isReadOnly()) {
            return;
        }

        $this->addAttributes($mapItem, $this->getAttributes(), $entity);
        $this->validateEntity($entity);
        $this->addRelationships($this->request->getInputJson(), $entity);

        $this->mapper->getEm()->flush();
    }
}
