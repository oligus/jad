<?php

namespace Jad\Request;

use Jad\Common\Text;
use Jad\Common\Inflect;
use Jad\Exceptions\RequestException;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class JsonApiRequest
 * @package Jad\Request
 */
class JsonApiRequest
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
    public function getRequest()
    {
        return $this->request;
    }
    /**
     * @return Parameters
     */
    public function getParameters()
    {
        return $this->parameters;
    }
    /**
     * @return string
     */
    public function getPathPrefix()
    {
        return $this->pathPrefix;
    }
    /**
     * @param string $pathPrefix
     */
    public function setPathPrefix($pathPrefix)
    {
        $this->pathPrefix = $pathPrefix;
    }
    /**
     * @return array
     */
    public function getItems()
    {
        $currentPath = $this->request->getPathInfo();
        $currentPath = trim($currentPath, '/');
        $prefix = trim($this->pathPrefix, '/');
        $path = preg_replace('!/?' . $prefix . '/?!', '', $currentPath);
        return explode('/', $path);
    }
    /**
     * @return string
     */
    public function getPath()
    {
        $items = $this->getItems();
        return $items[0];
    }

    /**
     * @return string
     */
    public function getType()
    {
        $path = $this->getPath();
        return Inflect::singularize($path);
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
     * @return bool
     */
    public function hasId()
    {
        return !is_null($this->getId());
    }

    /**
     * @return mixed|null
     * @throws \Exception
     */
    public function getRelationship()
    {
        $items = $this->getItems();
        $relationships = [];

        if(array_key_exists(2, $items)) {
            if($items[2] !== 'relationship') {
                $relationships['view'] = 'full';
                $relationships['type'] = Text::deKebabify($items[2]);
                return $relationships;
            }

            if(!array_key_exists(3, $items)) {
                throw new RequestException('Relationship entity missing');
            }

            $relationships['view'] = 'list';
            $relationships['type'] = Text::deKebabify($items[3]);

            return $relationships;
        }
        return null;
    }
    /**
     * @return bool
     */
    public function isCollection()
    {
        return is_null($this->getId());
    }

    /**
     * @return string
     */
    public function getCurrentUrl()
    {
        return $this->getRequest()->getSchemeAndHttpHost() . $this->getRequest()->getPathInfo();
    }

    /**
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->getRequest()->getSchemeAndHttpHost() . $this->getRequest()->getBaseUrl();
    }

    /**
     * Get streamed input for POST and PATCH operations
     *
     * @return \stdClass
     * @throws RequestException
     */
    public function getInputJson()
    {
        $input = file_get_contents("php://input");

        if(empty($input)) {
            throw new RequestException('Empty input on POST or PATCH');
        }

        $result = json_decode(file_get_contents("php://input"));

        if(json_last_error() !== JSON_ERROR_NONE) {
            $msg = ucfirst(json_last_error_msg());
            throw new RequestException('JSON ERROR: ' . $msg . '.');
        }

        if(empty($result) || !$result instanceof \stdClass) {
            throw new RequestException('Error json decoding input, check your formatting.');
        }

        return $result;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->request->getMethod();
    }
}