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
     * @return array
     */
    public function jsonSerialize()
    {
        $data = [];

        foreach($this->resources as $resource) {
            $data[] = $resource;
        }

        return $data;
    }
}