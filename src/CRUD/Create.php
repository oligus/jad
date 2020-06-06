<?php declare(strict_types=1);

namespace Jad\CRUD;

use Doctrine\ORM\Mapping\MappingException;
use Doctrine\ORM\ORMException;
use Exception;
use InvalidArgumentException;
use Jad\Common\ClassHelper;
use Jad\Exceptions\JadException;
use Jad\Exceptions\RequestException;
use ReflectionException;

/**
 * Class Create
 * @package Jad\CRUD
 */
class Create extends AbstractCRUD
{
    /**
     * @throws JadException
     * @throws MappingException
     * @throws ORMException
     * @throws ReflectionException
     * @throws RequestException
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function createResource(): ?string
    {
        $mapItem = $this->getMapItem();
        $entityClass = $mapItem->getEntityClass();
        $entity = new $entityClass;

        if ($mapItem->isReadOnly()) {
            return null;
        }

        $this->addAttributes($mapItem, $this->getAttributes(), $entity);
        $this->validateEntity($entity);
        $this->addRelationships($this->request->getInputJson(), $entity);

        $this->mapper->getEm()->persist($entity);
        $this->mapper->getEm()->flush();

        return (string)ClassHelper::getPropertyValue($entity, $mapItem->getIdField());
    }
}
