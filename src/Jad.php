<?php declare(strict_types=1);

namespace Jad;

use Jad\Map\Mapper;
use Jad\Response\JsonApiResponse;
use Jad\Response\Error;
use Jad\Request\JsonApiRequest;
use Jad\Exceptions\ResourceNotFoundException;
use Jad\Request\Parameters;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class Jad
 * @package Jad
 */
class Jad
{
    /**
     * @var Mapper $mapper
     */
    private $mapper;

    /**
     * @var JsonApiRequest $jsonApiRequest
     */
    private $jsonApiRequest;

    /**
     * Jad constructor.
     * @param Mapper $mapper
     */
    public function __construct(Mapper $mapper)
    {
        $request = Request::createFromGlobals();
        $parameters = new Parameters($request->query->all());
        $this->mapper = $mapper;
        $this->jsonApiRequest = new JsonApiRequest($request, $parameters);
    }

    /**
     * @return JsonApiRequest
     */
    public function getJsonApiRequest(): JsonApiRequest
    {
        return $this->jsonApiRequest;
    }

    /**
     * @param $pathPrefix
     */
    public function setPathPrefix(string $pathPrefix): void
    {
        $this->getJsonApiRequest()->setPathPrefix($pathPrefix);
    }

    /**
     * @return bool
     */
    public function jsonApiResult(): bool
    {
        $success = true;

        try {
            $response = new JsonApiResponse($this->jsonApiRequest, $this->mapper);
            $response->render();
        } catch (ResourceNotFoundException $exception) {
            if(Configure::getInstance()->getConfig('strict')) {
                $success = false;
                $error = new Error($exception);
                $error->render();
            }
        } catch (\Exception $exception) {
            $success = false;
            $error = new Error($exception);
            $error->render();
        }

        return $success;
    }
}