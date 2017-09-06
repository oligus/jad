<?php

namespace Jad\Document;

use Jad\Query\Paginator;

/**
 * Class Collection
 *
 * Collection of resourcers
 *
 * @package Klirr\JsonApi\Response
 */
class Collection implements \JsonSerializable
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
    private $resources = array();

    /**
     * @var Paginator
     */
    private $paginator;

    /**
     * @param \Jad\Document\Resource $resource
     */
    public function add(\Jad\Document\Resource $resource)
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
     * @param bool $included
     */
    private function setIncluded(bool $included)
    {
        $this->included = $included;
    }

    public function getIncluded()
    {
        return $this->includes;
    }

    public function loadIncludes()
    {
        /** @var \Jad\Document\Resource $resource */
        foreach($this->resources as $resource) {
            if($resource->hasIncluded()) {
                $this->setIncluded(true);
                $included = $resource->getIncluded();
                $this->includes = array_merge($this->includes, $included);
            }
        }
    }

    /**
     * @return Paginator|null
     */
    public function getPaginator()
    {
        return $this->paginator;
    }

    /**
     * @param Paginator $paginator
     */
    public function setPaginator(Paginator $paginator)
    {
        $this->paginator = $paginator;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->resources;
    }
}