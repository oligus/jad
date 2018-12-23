<?php declare(strict_types=1);

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
    public function getMapItem($type): MapItem;

    /**
     * @param $type
     * @return bool
     */
    public function hasMapItem(string $type): bool;

    /**
     * @param $className
     * @return MapItem
     */
    public function getMapItemByClass(string $className): MapItem;
}