<?php

namespace Jad\Query;

use Jad\Request\Parameters;
use Jad\Request\JsonApiRequest;
use Jad\Configure;

/**
 * Class Paginator
 * @package Jad\Query
 */
class Paginator
{
    const DEFAULT_PER_PAGE = 25;

    /**
     * @var JsonApiRequest
     */
    private $request;

    /**
     * @var bool
     */
    private $active = false;

    /**
     * @var int
     */
    private $limit;

    /**
     * @var int
     */
    private $offset;

    /**
     * @var int
     */
    private $count = 0;

    /**
     * @var int
     */
    private $currentPage = 0;

    /**
     * @var
     */
    private $lastPage = 0;

    /**
     * @var int
     */
    private $nextPage = 0;

    /**
     * @var int
     */
    private $previousPage = 0;

    /**
     * @var int
     */
    private $maxPageSize = 25;

    /**
     * Paginator constructor.
     * @param JsonApiRequest $request
     */
    public function __construct(JsonApiRequest $request)
    {
        if((int) Configure::getInstance()->getConfig('max_page_size') > 0) {
            $this->maxPageSize = (int) Configure::getInstance()->getConfig('max_page_size');
        }

        $this->request = $request;
        $this->limit = $this->request->getParameters()->getLimit($this->maxPageSize);
        $this->offset = $this->request->getParameters()->getOffset($this->limit);
    }

    private function calculatePages()
    {
        $this->lastPage = ceil($this->count / $this->limit);
        $this->currentPage = ceil($this->offset / $this->limit) + 1;

        $this->nextPage = $this->currentPage + 1;

        if($this->nextPage > $this->lastPage) {
            $this->nextPage = null;
        }

        $this->previousPage = $this->currentPage - 1;

        if($this->previousPage < 1) {
            $this->previousPage = null;
        }
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * @param bool $active
     */
    public function setActive(bool $active)
    {
        $this->active = $active;
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->request->getParameters()->getLimit($this->maxPageSize);
    }

    /**
     * @return int
     */
    public function getOffset(): int
    {
        $size = $this->request->getParameters()->getSize(self::DEFAULT_PER_PAGE);
        return $this->request->getParameters()->getOffset($size);
    }

    /**
     * @return string
     */
    public function getCurrent()
    {
        $url = $this->request->getCurrentUrl();
        $url .= '?page[size]=' . $this->limit . '&page[number]=' . $this->currentPage;

        return $url;
    }

    /**
     * @return string
     */
    public function getFirst()
    {
        $url = $this->request->getCurrentUrl();
        $url .= '?page[size]=' . $this->limit . '&page[number]=1';

        return $url;
    }

    /**
     * @return string
     */
    public function getLast()
    {
        $url = $this->request->getCurrentUrl();
        $url .= '?page[size]=' . $this->limit . '&page[number]=' . $this->lastPage;

        return $url;
    }

    public function hasPrevious()
    {
        return $this->previousPage !== null;
    }

    public function getPrevious()
    {
        $url = $this->request->getCurrentUrl();
        $url .= '?page[size]=' . $this->limit . '&page[number]=' . $this->previousPage;

        return $url;
    }

    public function hasNext()
    {
        return $this->nextPage !== null;
    }

    public function getNext()
    {
        $url = $this->request->getCurrentUrl();
        $url .= '?page[size]=' . $this->limit . '&page[number]=' . $this->nextPage;

        return $url;
    }

    /**
     * @param int $count
     */
    public function setCount(int $count)
    {
        $this->count = (int) $count;
        $this->calculatePages();
    }

    /**
     * @return int
     */
    public function getCount(): int
    {
        return $this->count;
    }

    /**
     * @return int
     */
    public function getLastPage(): int
    {
        return $this->lastPage;
    }

}