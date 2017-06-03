<?php

namespace Jad\Document;

class JsonDocument implements \JsonSerializable
{
    /**
     * @var Element $element
     */
    private $element;

    /**
     * JsonApiResponse constructor.
     * @param \JsonSerializable $element
     */
    public function __construct(\JsonSerializable $element)
    {
        $this->element = $element;
    }

    /**
     * @return \stdClass
     */
    public function jsonSerialize()
    {
        $document = new \stdClass();
        $document->data = $this->element;
        return $document;
    }
}