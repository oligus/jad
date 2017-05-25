<?php

namespace Jad\Serializers;

class ErrorDocument implements \JsonSerializable
{
    private $errors = [];

    public function addError(\Exception $e)
    {
        array_unshift($this->errors, $e);
    }

    public function jsonSerialize()
    {
        $document = new \stdClass();
        $document->errors = [];

        /** @var \Exception $exception */
        foreach($this->errors as $exception) {
            $error = new \stdClass();
            $error->status = $exception->getCode();
            $error->title = $this->getTitle($exception);
            $error->detail = $exception->getMessage();

            $document->errors[] = $error;
        }

        return $document;
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