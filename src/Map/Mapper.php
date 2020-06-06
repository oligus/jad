<?php declare(strict_types=1);

namespace Jad\Map;

use Doctrine\ORM\EntityManagerInterface;

/**
 * Interface Mapper
 * @package Jad\Map
 */
interface Mapper
{
    public function getEm(): EntityManagerInterface;

    public function getMapItem(string $type): MapItem;

    public function hasMapItem(string $type): bool;

    public function getMapItemByClass(string $className): ?MapItem;
}
