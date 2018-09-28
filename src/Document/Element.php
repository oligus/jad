<?php declare(strict_types=1);

namespace Jad\Document;

/**
 * Interface Element
 * @package Jad\Document
 */
interface Element
{
    /**
     * @return mixed
     */
    public function serialize();
}