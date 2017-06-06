<?php

namespace Jad\CRUD;

class Delete extends AbstractCRUD
{
    public function deleteResource()
    {
        $mapItem = $this->mapper->getMapItem($this->request->getType());
        $entity = $this->mapper->getEm()->getRepository($mapItem->getEntityClass())->find($this->request->getId());
        $entityClass = $mapItem->getEntityClass();

        if($entity instanceof $entityClass) {
            $this->mapper->getEm()->remove($entity);
            $this->mapper->getEm()->flush();
        }
    }
}