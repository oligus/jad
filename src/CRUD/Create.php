<?php declare(strict_types=1);

namespace Jad\CRUD;

use Jad\Common\ClassHelper;
use Doctrine\Common\Annotations\AnnotationException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\Mapping\MappingException;
use Jad\Exceptions\JadException;
use Jad\Exceptions\RequestException;
use ReflectionException;
use InvalidArgumentException;
use Exception;

/**
 * Class Create
 * @package Jad\CRUD
 */
class Create extends AbstractCRUD
{
    /**
     * @return string|null
     * @throws AnnotationException
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

        /** @var \Jad\Map\MapItem $mapItem */
        $id = (string)ClassHelper::getPropertyValue($entity, $mapItem->getIdField());

        return $id;
    }
}
