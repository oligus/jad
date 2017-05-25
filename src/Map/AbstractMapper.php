<?php

namespace Jad\Map;

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
     * @return EntityManagerInterface
     */
    public function getEm(): EntityManagerInterface
    {
        return $this->em;
    }

    /**
     * @return array
     */
    public function getMap(): array
    {
        return $this->map;
    }

    /**
     * @param $type
     * @param $values
     */
    public function add($type, $values)
    {
        $mapItem = new MapItem($type, $values);

        $entityClass = $mapItem->getEntityClass();
        $mapItem->setClassMeta($this->em->getClassMetadata($entityClass));

        if(!$this->itemExists($mapItem)) {
            $this->map[] = $mapItem;
        }
    }

    /**
     * @param \Jad\Map\MapItem $item
     * @return bool
     */
    public function itemExists(MapItem $item)
    {
        /** @var MapItem $mapItem */
        foreach ($this->map as $mapItem) {
            if($mapItem->getType() === $item->getType()) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $type
     * @return bool
     */
    public function hasMapItem($type): bool
    {
        foreach ($this->map as $mapItem) {
            if($mapItem->getType() === $type) {
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
            if($mapItem->getType() === $type) {
                return $mapItem;
            }
        }

        throw new ResourceNotFoundException('Resource type not found (' . $type . ')');
    }
}