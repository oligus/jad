<?php declare(strict_types=1);

namespace Jad\Document;

use Exception;
use Jad\Query\Paginator;
use JsonSerializable;
use stdClass;

/**
 * Class JsonDocument
 * @package Jad\Document
 */
class JsonDocument implements JsonSerializable
{
    /**
     * @var Element
     */
    private $element;

    /**
     * @var Links $link
     */
    private $links = null;

    /**
     * @var Meta $meta
     */
    private $meta = null;

    /**
     * JsonDocument constructor.
     * @param Element $element
     */
    public function __construct(Element $element)
    {
        $this->element = $element;
    }

    /**
     * @param Links $links
     */
    public function addLinks(Links $links): void
    {
        $this->links = $links;
    }

    /**
     * @param Meta $meta
     */
    public function addMeta(Meta $meta): void
    {
        $this->meta = $meta;
    }

    /**
     * @return stdClass
     * @throws Exception
     */
    public function jsonSerialize(): stdClass
    {
        $document = new stdClass();

        if ($this->element->hasIncluded()) {
            $document->included = $this->element->getIncluded();
        }

        if (!is_null($this->links)) {
            $document->links = $this->links;
        }

        if (!is_null($this->meta)) {
            $document->meta = $this->meta;
        }

        if ($this->hasPagination($this->element)) {
            /** @var Paginator $paginator */
            $paginator = $this->element->getPaginator();

            $document->links->setSelf($paginator->getCurrent());
            $document->links->setFirst($paginator->getFirst());
            $document->links->setLast($paginator->getLast());

            if ($paginator->hasNext()) {
                $document->links->setNext($paginator->getNext());
            }

            if ($paginator->hasPrevious()) {
                $document->links->setPrevious($paginator->getPrevious());
            }

            $document->meta->setCount($paginator->getCount());
            $document->meta->setPages($paginator->getLastPage());
        }

        if ($document->meta->isEmpty()) {
            unset($document->meta);
        }

        if ($this->element instanceof Collection) {
            $document->data = $this->element;
            $this->element->loadIncludes();
            return $document;
        }

        $document->data = $this->element;

        return $document;
    }

    /**
     * @param $element
     * @return bool
     */
    private function hasPagination($element): bool
    {
        if (!$element instanceof Collection) {
            return false;
        }

        $paginator = $element->getPaginator();

        if ($paginator === null) {
            return false;
        }

        if (!$paginator instanceof Paginator) {
            return false;
        }

        return $paginator->isActive();
    }
}
