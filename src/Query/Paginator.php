<?php declare(strict_types=1);

namespace Jad\Query;

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
     * @var int
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
     * @throws \Exception
     */
    public function __construct(JsonApiRequest $request)
    {
        if ((int)Configure::getInstance()->getConfig('max_page_size') > 0) {
            $this->maxPageSize = (int)Configure::getInstance()->getConfig('max_page_size');
        }

        $this->request = $request;
        $this->limit = (int)$this->request->getParameters()->getLimit($this->maxPageSize);
        $this->offset = (int)$this->request->getParameters()->getOffset($this->limit);
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
        return (int)$this->request->getParameters()->getLimit($this->maxPageSize);
    }

    /**
     * @return int
     * @throws \Exception
     */
    public function getOffset(): int
    {
        $size = $this->request->getParameters()->getSize(self::DEFAULT_PER_PAGE);
        return (int)$this->request->getParameters()->getOffset($size);
    }

    /**
     * @return string
     */
    public function getCurrent(): string
    {
        $url = $this->request->getCurrentUrl();
        $url .= '?page[size]=' . $this->limit . '&page[number]=' . $this->currentPage;

        return $url;
    }

    /**
     * @return string
     */
    public function getFirst(): string
    {
        $url = $this->request->getCurrentUrl();
        $url .= '?page[size]=' . $this->limit . '&page[number]=1';

        return $url;
    }

    /**
     * @return string
     */
    public function getLast(): string
    {
        $url = $this->request->getCurrentUrl();
        $url .= '?page[size]=' . $this->limit . '&page[number]=' . $this->lastPage;

        return $url;
    }

    public function hasPrevious(): bool
    {
        return $this->previousPage !== null;
    }

    public function getPrevious(): string
    {
        $url = $this->request->getCurrentUrl();
        $url .= '?page[size]=' . $this->limit . '&page[number]=' . $this->previousPage;

        return $url;
    }

    public function hasNext(): bool
    {
        return $this->nextPage !== null;
    }

    public function getNext(): string
    {
        $url = $this->request->getCurrentUrl();
        $url .= '?page[size]=' . $this->limit . '&page[number]=' . $this->nextPage;

        return $url;
    }

    /**
     * @return int
     */
    public function getCount(): int
    {
        return (int)$this->count;
    }

    /**
     * @param int $count
     */
    public function setCount(int $count): void
    {
        $this->count = (int)$count;
        $this->calculatePages();
    }

    private function calculatePages(): void
    {
        $this->lastPage = ceil($this->count / $this->limit);
        $this->currentPage = ceil($this->offset / $this->limit) + 1;

        $this->nextPage = $this->currentPage + 1;

        if ($this->nextPage > $this->lastPage) {
            $this->nextPage = null;
        }

        $this->previousPage = $this->currentPage - 1;

        if ($this->previousPage < 1) {
            $this->previousPage = null;
        }
    }

    /**
     * @return int
     */
    public function getLastPage(): int
    {
        return (int)$this->lastPage;
    }

}