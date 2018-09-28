<?php declare(strict_types=1);

namespace Jad\CRUD;

/**
 * Class Delete
 * @package Jad\CRUD
 */
class Delete extends AbstractCRUD
{
    public function deleteResource(): void
    {
        $mapItem = $this->mapper->getMapItem($this->request->getResourceType());
        $entity = $this->mapper->getEm()->getRepository($mapItem->getEntityClass())->find($this->request->getId());
        $entityClass = $mapItem->getEntityClass();

        if ($entity instanceof $entityClass) {
            $this->mapper->getEm()->remove($entity);
            $this->mapper->getEm()->flush();
        }
    }
}