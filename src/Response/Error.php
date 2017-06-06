<?php

namespace Jad\Response;

use Symfony\Component\HttpFoundation\Response;

class Error
{
    /**
     * @var \Exception $exception
     */
    private $exception;

    /**
     * Error constructor.
     * @param \Exception $exception
     */
    public function __construct(\Exception $exception)
    {
        $this->exception = $exception;
    }

    public function render()
    {
        $document = new \stdClass();
        $document->errors = array();

        $error = new \stdClass();
        $error->status = $this->exception->getCode();
        $error->title = $this->getTitle($this->exception) . ' error';
        $error->detail = $this->exception->getMessage();

        $document->errors[] = $error;

        $response = new Response();
        $response->setContent(json_encode($document));
        $response->headers->set('Content-Type', 'application/vnd.api+json');
        $response->setStatusCode(500);
        //$response->sendHeaders();
        $response->sendContent();
    }

    /**
     * @param \Exception $e
     * @return string
     */
    private function getTitle(\Exception $e)
    {
        $class = preg_replace('/^.*\\\(.+?)(Exception)?$/', '\1', get_class($e) );
        $words = preg_split('/(?=[A-Z])/',$class);
        return trim(implode(" ", $words));
    }
}