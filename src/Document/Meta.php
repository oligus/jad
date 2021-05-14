<?php declare(strict_types=1);

namespace Jad\Document;

use JsonSerializable;
use stdClass;

/**
 * Class Meta
 * @package Jad\Document
 */
class Meta implements JsonSerializable
{
    private ?int $count = null;
    private ?int $pages = null;

    /**
     * @codeCoverageIgnore
     */
    public function getCount(): ?int
    {
        return $this->count;
    }

    /**
     * @codeCoverageIgnore
     */
    public function setCount(int $count): void
    {
        $this->count = $count;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getPages(): ?int
    {
        return $this->pages;
    }

    /**
     * @codeCoverageIgnore
     */
    public function setPages(int $pages): void
    {
        $this->pages = $pages;
    }

    public function isEmpty(): bool
    {
        $isEmpty = true;

        if ($this->count !== null) {
            $isEmpty = false;
        }

        if ($this->pages !== null) {
            $isEmpty = false;
        }

        return $isEmpty;
    }

    public function jsonSerialize(): stdClass
    {
        $meta = new stdClass();

        if ($this->count !== null) {
            $meta->count = $this->count;
        }

        if ($this->pages !== null) {
            $meta->pages = $this->pages;
        }

        return $meta;
    }
}
