<?php

namespace Jad;

use Jad\Exceptions\JadException;
use Symfony\Component\HttpFoundation\Request;
use Tobscure\JsonApi\Parameters;

class RequestHandler
{
    /**
     * @var Request $request;
     */
    private $request;

    /**
     * @var string
     */
    private $pathPrefix = ' ';

    /**
     * @var Parameters
     */
    private $parameters;

    public function __construct()
    {
        $this->request = Request::createFromGlobals();
        $this->parameters = new Parameters($this->request->query->all());
    }

    /**
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * @return Parameters
     */
    public function getParameters(): Parameters
    {
        return $this->parameters;
    }

    /**
     * @return string
     */
    public function getPathPrefix(): string
    {
        return $this->pathPrefix;
    }

    /**
     * @param string $pathPrefix
     */
    public function setPathPrefix(string $pathPrefix)
    {
        $this->pathPrefix = $pathPrefix;
    }

    /**
     * @return array
     */
    public function getItems()
    {
        $path = preg_replace('!/?' . $this->pathPrefix . '/?!', '', $this->request->getPathInfo());
        return explode('/', $path);
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->getItems()[0];
    }

    /**
     * @return mixed|null
     */
    public function getId()
    {
        $items = $this->getItems();

        if(array_key_exists(1, $items)) {
            return $items[1];
        }

        return null;
    }

    /**
     * @return mixed|null
     * @throws JadException
     */
    public function getRelationship()
    {
        $items = $this->getItems();

        if(array_key_exists(2, $items)) {
            if($items[2] !== 'relationship') {
                return $items[2];
            }

            if(!array_key_exists(3, $items)) {
                throw new JadException('Relationship entity missing');
            }

            return $items[3];
        }

        return null;
    }
}