<?php

namespace Jad\Map;

class EntityMap
{
    /**
     * @var array
     */
    private $entityMap = [];

    /**
     * EntityMap constructor.
     * @param array $params
     */
    public function __construct(array $params = [])
    {
        foreach($params as $key => $value) {
            $this->add($key, $value);
        }
    }

    /**
     * @param $type
     * @param $values
     */
    public function add($type, $values)
    {
        $mapItem = new EntityMapItem($type, $values);

        if(!$this->itemExists($mapItem)) {
            $this->entityMap[] = $mapItem;
        }
    }

    /**
     * @param \Jad\Map\EntityMapItem $item
     * @return bool
     */
    public function itemExists(EntityMapItem $item)
    {
        /** @var EntityMapItem $mapItem */
        foreach ($this->entityMap as $mapItem) {
            if($mapItem->getType() === $item->getType()) {
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
        return $this->entityMap;
    }
}