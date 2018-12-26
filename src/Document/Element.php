<?php declare(strict_types=1);

namespace Jad\Document;

/**
 * Interface Element
 * @package Jad\Document
 */
interface Element
{
    /**
     * @return bool
     */
    public function hasIncluded(): bool;
}
