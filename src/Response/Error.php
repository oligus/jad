<?php declare(strict_types=1);

namespace Jad\Response;

use Symfony\Component\HttpFoundation\Response;
use Jad\Configure;

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

    public function render(): void
    {
        $document = new \stdClass();
        $document->errors = [];

        $error = new \stdClass();
        $error->status = (string)$this->exception->getCode();
        $error->title = $this->getTitle($this->exception) . ' error';
        $error->detail = $this->exception->getMessage();

        $document->errors[] = $error;

        $response = new Response();
        $headers['Content-Type'] = 'application/vnd.api+json';

        foreach ($headers as $key => $value) {
            $response->headers->set($key, $value);
        }

        $response->setContent(json_encode($document));
        $response->setStatusCode(500);
        $response->sendHeaders();
        $response->sendContent();

        if (!Configure::getInstance()->getConfig('test_mode')) {
            exit(0);
        }
    }

    /**
     * @param \Exception $e
     * @return string
     */
    private function getTitle(\Exception $e): string
    {
        $class = preg_replace('/^.*\\\(.+?)(Exception)?$/', '\1', get_class($e));
        $words = preg_split('/(?=[A-Z])/', $class);
        return trim(implode(" ", $words));
    }
}
