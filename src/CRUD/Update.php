<?php declare(strict_types=1);

namespace Jad\CRUD;

use Doctrine\ORM\ORMException;
use Exception;
use Jad\Exceptions\JadException;
use Jad\Exceptions\RequestException;

/**
 * Class Update
 * @package Jad\CRUD
 */
class Update extends AbstractCRUD
{
    /**
     * @throws ORMException
     * @throws Exception
     * @throws JadException
     * @throws RequestException
     */
    public function updateResource(): void
    {
        $mapItem = $this->getMapItem();
        $entityClass = $mapItem->getEntityClass();
        $entity = $this->mapper->getEm()->getRepository($mapItem->getEntityClass())->find($this->request->getId());

        if (!$entity instanceof $entityClass) {
            throw new Exception('Entity not found');
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
