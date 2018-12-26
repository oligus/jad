<?php declare(strict_types=1);

namespace Jad\Document;

use Jad\Query\Paginator;

/**
 * Class Collection
 * @package Jad\Document
 */
class Collection implements \JsonSerializable, Element
{
    /**
     * @var bool
     */
    private $included = false;

    /**
     * @var array
     */
    private $includes = [];

    /**
     * @var array
     */
    private $resources = [];

    /**
     * @var Paginator
     */
    private $paginator;

    /**
     * @param \Jad\Document\Resource $resource
     */
    public function add(Resource $resource): void
    {
        $this->resources[] = $resource;
    }

    /**
     * @return bool
     */
    public function hasIncluded(): bool
    {
        return $this->included;
    }

    /**
     * @codeCoverageIgnore
     * @return array
     */
    public function getIncluded(): array
    {
        return $this->includes;
    }

    /**
     * @throws \Exception
     */
    public function loadIncludes(): void
    {
        /** @var \Jad\Document\Resource $resource */
        foreach ($this->resources as $resource) {
            if ($resource->hasIncluded()) {
                $this->setIncluded(true);
                $included = $resource->getIncluded();
                $this->includes = array_merge($this->includes, $included);
            }
        }
    }

    /**
     * @codeCoverageIgnore
     * @param bool $included
     */
    private function setIncluded(bool $included): void
    {
        $this->included = $included;
    }

    /**
     * @codeCoverageIgnore
     * @return Paginator|null
     */
    public function getPaginator(): ?Paginator
    {
        return $this->paginator;
    }

    /**
     * @codeCoverageIgnore
     * @param Paginator $paginator
     */
    public function setPaginator(Paginator $paginator): void
    {
        $this->paginator = $paginator;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return $this->resources;
    }
}
