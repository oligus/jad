<?php

namespace Jad\CRUD;

use Jad\Map\Mapper;
use Jad\Request\JsonApiRequest;

/**
 * Class AbstractCRUD
 * @package Jad\CRUD
 */
class AbstractCRUD
{
    /**
     * @var JsonApiRequest
     */
    protected $request;

    /**
     * @var Mapper
     */
    protected $mapper;

    /**
     * AbstractCRUD constructor.
     * @param JsonApiRequest $request
     * @param Mapper $mapper
     */
    public function __construct(JsonApiRequest $request, Mapper $mapper)
    {
        $this->request = $request;
        $this->mapper = $mapper;
    }
}