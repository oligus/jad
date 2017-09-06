<?php

namespace Jad\Document;

use Jad\Query\Paginator;

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

        if($this->hasPagination($this->element)) {
            /** @var Paginator $paginator */
            $paginator = $this->element->getPaginator();

            $document->links->setSelf($paginator->getCurrent());
            $document->links->setFirst($paginator->getFirst());
            $document->links->setLast($paginator->getLast());

            if($paginator->hasNext()) {
                $document->links->setNext($paginator->getNext());
            }

            if($paginator->hasPrevious()) {
                $document->links->setPrevious($paginator->getPrevious());
            }
        }

        return $document;
    }

    /**
     * @param $element
     * @return bool
     */
    private function hasPagination($element)
    {
        if(!$element instanceof Collection) {
            return false;
        }

        $paginator = $element->getPaginator();

        if($paginator === null) {
            return false;
        }

        if(!$paginator instanceof Paginator) {
            return false;
        }

        return $paginator->isActive();
    }
}