<?php

namespace Jad\Exceptions;

class ResourceNotFoundException extends \Exception
{
    /**
     * @var int
     */
    protected $code = 404;

    /**
     * @var string
     */
    protected $message = "Resource not found.";

    /**
     * @var
     */
    protected $title;

    /**
     * @return mixed
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle(string $title)
    {
        $this->title = $title;
    }
}