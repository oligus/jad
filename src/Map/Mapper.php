<?php

namespace Jad\Map;

use Doctrine\ORM\EntityManagerInterface;

interface Mapper
{
    /**
     * @return EntityManagerInterface
     */
    public function getEm(): EntityManagerInterface;

    /**
     * @param $type
     * @return MapItem
     */
    public function getMapItem($type);

    /**
     * @param $type
     * @return bool
     */
    public function hasMapItem($type): bool;
}