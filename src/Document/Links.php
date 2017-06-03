<?php

namespace Jad\Document;

class Links implements \JsonSerializable
{
    /**
     * @var string
     */
    private $self = null;

    /**
     * @param $self
     */
    public function setSelf($self)
    {
        $this->self = $self;
    }

    /**
     *
     */
    public function jsonSerialize()
    {
        $links = new \stdClass();

        if(!is_null($this->self)) {
            $links->self = $this->self;
        }

        return $links;
    }
}