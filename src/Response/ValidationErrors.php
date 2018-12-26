<?php declare(strict_types=1);

namespace Jad\Response;

use Jad\Common\Text;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * Class ValidationErrors
 * @package Jad\Response
 */
class ValidationErrors
{
    /**
     * @var ConstraintViolationListInterface
     */
    private $errorList;

    /**
     * ValidationErrors constructor.
     * @param ConstraintViolationListInterface $errorList
     */
    public function __construct(ConstraintViolationListInterface $errorList)
    {
        $this->errorList = $errorList;
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function render(): void
    {
        $document = new \stdClass();
        $document->errors = [];

        /** @var ConstraintViolationInterface $validationError */
        foreach ($this->errorList as $validationError) {
            $error = new \stdClass();
            $attribute = Text::kebabify($validationError->getPropertyPath());

            $error->status = "500";
            $error->detail = $validationError->getMessage() . ' [' . $attribute . ']';
            $error->title = 'Validation Error';
            $document->errors[] = $error;
        }

        $response = new Response();
        $headers = [];
        $headers['Content-Type'] = 'application/vnd.api+json';

        foreach ($headers as $key => $value) {
            $response->headers->set($key, $value);
        }

        $response->setContent(json_encode($document));
        $response->setStatusCode(500);
        $response->sendHeaders();
        $response->sendContent();
    }
}
