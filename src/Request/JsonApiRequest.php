<?php declare(strict_types=1);

namespace Jad\Request;

use Jad\Configure;
use Jad\Common\Text;
use Jad\Exceptions\RequestException;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class JsonApiRequest
 * @package Jad\Request
 */
class JsonApiRequest
{
    /**
     * @var Request $request ;
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

    public function __construct(Request $request, Parameters $parameters)
    {
        $this->request = $request;
        $this->parameters = $parameters;
    }

    /**
     * @codeCoverageIgnore
     * @return Parameters
     */
    public function getParameters(): Parameters
    {
        return $this->parameters;
    }

    /**
     * @codeCoverageIgnore
     * @return string
     */
    public function getPathPrefix(): string
    {
        return $this->pathPrefix;
    }

    /**
     * @codeCoverageIgnore
     * @param string $pathPrefix
     */
    public function setPathPrefix(string $pathPrefix): void
    {
        $this->pathPrefix = $pathPrefix;
    }

    /**
     * @return string
     */
    public function getResourceType(): string
    {
        $items = $this->getItems();
        return $items[0];
    }

    /**
     * @return array
     */
    public function getItems(): array
    {
        $currentPath = $this->request->getPathInfo();
        $currentPath = trim($currentPath, '/');
        $prefix = trim($this->pathPrefix, '/');
        $path = preg_replace('!/?' . $prefix . '/?!', '', $currentPath);
        return explode('/', $path);
    }

    /**
     * @return bool
     */
    public function hasId(): bool
    {
        return !is_null($this->getId());
    }

    /**
     * @return mixed|null
     */
    public function getId()
    {
        $items = $this->getItems();
        if (array_key_exists(1, $items)) {
            return $items[1];
        }
        return null;
    }

    /**
     * @return array
     * @throws RequestException
     */
    public function getRelationship(): array
    {
        $items = $this->getItems();
        $relationships = [];

        if (array_key_exists(2, $items)) {
            if ($items[2] !== 'relationships') {
                $relationships['view'] = 'full';
                $relationships['type'] = Text::deKebabify($items[2]);
                return $relationships;
            }

            if (!array_key_exists(3, $items)) {
                throw new RequestException('Relationship resource type missing');
            }

            $relationships['view'] = 'list';
            $relationships['type'] = Text::deKebabify($items[3]);
        }
        return $relationships;
    }

    /**
     * @return bool
     */
    public function isCollection(): bool
    {
        return is_null($this->getId());
    }

    /**
     * @return string
     */
    public function getCurrentUrl(): string
    {
        return $this->getRequest()->getSchemeAndHttpHost() . $this->getRequest()->getPathInfo();
    }

    /**
     * @codeCoverageIgnore
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * @return string
     */
    public function getBaseUrl(): string
    {
        return $this->getRequest()->getSchemeAndHttpHost() . $this->getRequest()->getBaseUrl();
    }

    /**
     * Get streamed input for POST and PATCH operations
     *
     * @return \stdClass
     * @throws RequestException
     */
    public function getInputJson(): \stdClass
    {
        $input = file_get_contents("php://input");

        if (Configure::getInstance()->getConfig('test_mode')) {
            $input = $this->request->request->get('input');
        }

        if (empty($input)) {
            throw new RequestException('Empty input on POST or PATCH');
        }

        $input = preg_replace('/,\s*([\]}])/m', '$1', $input);

        $result = json_decode($input);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $msg = ucfirst(json_last_error_msg());
            throw new RequestException('JSON ERROR: ' . $msg . '.');
        }

        if (empty($result) || !$result instanceof \stdClass) {
            throw new RequestException('Error json decoding input, check your formatting.');
        }

        return $result;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->request->getMethod();
    }
}