<?php declare(strict_types=1);

namespace Jad\Map;

use Jad\Exceptions\MappingException;
use Jad\Exceptions\ResourceNotFoundException;
use Doctrine\ORM\EntityManagerInterface;

abstract class AbstractMapper implements Mapper
{
    /**
     * @var EntityManagerInterface $em
     */
    protected $em;

    /**
     * @var array
     */
    protected $map = [];

    /**
     * AbstractMapper constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @codeCoverageIgnore
     * @return EntityManagerInterface
     */
    public function getEm(): EntityManagerInterface
    {
        return $this->em;
    }

    /**
     * @codeCoverageIgnore
     * @return array
     */
    public function getMap(): array
    {
        return $this->map;
    }

    /**
     * @param $type
     * @param $values
     * @param bool $paginate
     */
    public function add(string $type, $values, bool $paginate = false): void
    {
        $mapItem = new MapItem($type, $values, $paginate);

        $entityClass = $mapItem->getEntityClass();
        $mapItem->setClassMeta($this->em->getClassMetadata($entityClass));

        if (!$this->itemExists($mapItem)) {
            $this->map[] = $mapItem;
        }
    }

    /**
     * @param \Jad\Map\MapItem $item
     * @return bool
     */
    public function itemExists(MapItem $item): bool
    {
        /** @var MapItem $mapItem */
        foreach ($this->map as $mapItem) {
            if ($mapItem->getType() === $item->getType()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $type
     * @return bool
     */
    public function hasMapItem(string $type): bool
    {
        foreach ($this->map as $mapItem) {
            if ($mapItem->getType() === $type) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $type
     * @return MapItem
     * @throws ResourceNotFoundException
     */
    public function getMapItem($type): MapItem
    {
        foreach ($this->map as $mapItem) {
            if ($mapItem->getType() === $type) {
                return $mapItem;
            }
        }

        throw new ResourceNotFoundException('Resource type not found [' . $type . ']');
    }

    /**
     * @param $className
     * @return mixed
     * @throws MappingException
     */
    public function getMapItemByClass(string $className): MapItem
    {
        foreach ($this->map as $mapItem) {
            if ($mapItem->getEntityClass() === $className) {
                return $mapItem;
            }
        }

        throw new MappingException('Map item with class name [' . $className . '] not found.', 400);
    }
}