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

        if($this->element instanceof Collection) {
            $document->data = $this->element;
            $this->element->loadIncludes();
        } else {
            $document->data = [$this->element];
        }

        if($this->element->hasIncluded()) {
            $document->included = $this->element->getIncluded();
        }

        if(!is_null($this->links)) {
            $document->links = $this->links;
        }

        return $document;
    }
}