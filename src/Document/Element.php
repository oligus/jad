<?php declare(strict_types=1);

namespace Jad\Document;

/**
 * Interface Element
 * @package Jad\Document
 */
interface Element
{
    public function hasIncluded(): bool;

    /**
     * @return array<mixed>
     */
    public function getIncluded(): array;
}
