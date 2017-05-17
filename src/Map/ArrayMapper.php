<?php

namespace Jad\Map;

use Doctrine\ORM\EntityManagerInterface;

class ArrayMapper implements Mapper
{
    /**
     * @var EntityManagerInterface $em
     */
    private $em;

    /**
     * @var array
     */
    private $map = [];

    /**
     * ArrayMapper constructor.
     * @param $em
     */
    public function __construct($em)
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
     * @return MapItem
     */
    public function getMapItem($type): MapItem
    {
        foreach ($this->map as $mapItem) {
            if($mapItem->getType() === $type) {
                return $mapItem;
            }
        }

        return new MapItem($type, ucfirst($type));
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
     * @return array
     */
    public function getMap()
    {
        return $this->map;
    }
}