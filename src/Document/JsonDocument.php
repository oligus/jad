<?php

namespace Jad\Document;

class JsonDocument implements \JsonSerializable
{
    /**
     * @var Element $element
     */
    private $element;

    /**
     * @var Links $link
     */
    private $links = null;

    /**
     * JsonApiResponse constructor.
     * @param \JsonSerializable $element
     */
    public function __construct(\JsonSerializable $element)
    {
        $this->element = $element;
    }

    /**
     * @param Links $links
     */
    public function addLinks(Links $links)
    {
        $this->links = $links;
    }

    /**
     * @return \stdClass
     */
    public function jsonSerialize()
    {
        $document = new \stdClass();
        $document->data = $this->element;

        if(!is_null($this->links)) {
            $document->links = $this->links;
        }
        return $document;
    }
}