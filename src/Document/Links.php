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
    /**
     * @var string
     */
    private $self = null;

    /**
     * @var null
     */
    private $first = null;

    /**
     * @var null
     */
    private $last = null;

    private $next;

    private $previous;

    /**
     * @codeCoverageIgnore
     * @param $self
     */
    public function setSelf($self): void
    {
        $this->self = $self;
    }

    /**
     * @codeCoverageIgnore
     * @param null $first
     */
    public function setFirst($first): void
    {
        $this->first = $first;
    }

    /**
     * @codeCoverageIgnore
     * @param null $last
     */
    public function setLast($last): void
    {
        $this->last = $last;
    }

    /**
     * @codeCoverageIgnore
     * @param mixed $next
     */
    public function setNext($next): void
    {
        $this->next = $next;
    }

    /**
     * @codeCoverageIgnore
     * @param mixed $previous
     */
    public function setPrevious($previous): void
    {
        $this->previous = $previous;
    }

    /**
     * @return stdClass
     */
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
