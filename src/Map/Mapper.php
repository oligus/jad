<?php declare(strict_types=1);

namespace Jad\Map;

use Doctrine\ORM\EntityManagerInterface;

/**
 * Interface Mapper
 * @package Jad\Map
 */
interface Mapper
{
    /**
     * @return EntityManagerInterface
     */
    public function getEm(): EntityManagerInterface;

    /**
     * @param string $type
     * @return MapItem|null
     */
    public function getMapItem(string $type): ?MapItem;

    /**
     * @param string $type
     * @return bool
     */
    public function hasMapItem(string $type): bool;

    /**
     * @param string $className
     * @return MapItem|null
     */
    public function getMapItemByClass(string $className): ?MapItem;
}
