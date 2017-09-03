<?php

namespace Jad\Query;

use Jad\Request\Parameters;

/**
 * Class Paginator
 * @package Jad\Query
 */
class Paginator
{
    const DEFAULT_LIMIT = 25;
    const DEFAULT_PER_PAGE = 25;

    /**
     * @var Parameters
     */
    private $parameters;

    /**
     * Paginator constructor.
     * @param Parameters $parameters
     */
    public function __construct(Parameters $parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->parameters->getLimit(self::DEFAULT_LIMIT);
    }

    /**
     * @return int
     */
    public function getOffset(): int
    {
        return $this->parameters->getOffset(self::DEFAULT_PER_PAGE);
    }
}