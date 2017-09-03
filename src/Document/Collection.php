<?php

namespace Jad\Document;

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
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->resources;
    }
}