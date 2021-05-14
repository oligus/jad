<?php declare(strict_types=1);

namespace Jad\Document;

use JsonSerializable;
use stdClass;

/**
 * Class Links
 * @package Jad\Document
 */
class Links implements JsonSerializable
{
    private ?string $self = null;
    private ?string $first = null;
    private ?string $last = null;
    private ?string $next = null;
    private ?string $previous = null;

    /**
     * @codeCoverageIgnore
     */
    public function setSelf(?string $self): void
    {
        $this->self = $self;
    }

    /**
     * @codeCoverageIgnore
     */
    public function setFirst(?string $first): void
    {
        $this->first = $first;
    }

    /**
     * @codeCoverageIgnore
     */
    public function setLast(?string $last): void
    {
        $this->last = $last;
    }

    /**
     * @codeCoverageIgnore
     */
    public function setNext(?string $next): void
    {
        $this->next = $next;
    }

    /**
     * @codeCoverageIgnore
     */
    public function setPrevious(?string $previous): void
    {
        $this->previous = $previous;
    }

    public function jsonSerialize(): stdClass
    {
        $links = new stdClass();

        if (!is_null($this->self)) {
            $links->self = $this->self;
        }

        if (!is_null($this->first)) {
            $links->first = $this->first;
        }

        if (!is_null($this->last)) {
            $links->last = $this->last;
        }

        if (!is_null($this->next)) {
            $links->next = $this->next;
        }

        if (!is_null($this->previous)) {
            $links->previous = $this->previous;
        }

        return $links;
    }
}
