<?php

namespace Jad\Exceptions;

/**
 * Class ResourceNotFoundException
 * @package Jad\Exceptions
 */
class ResourceNotFoundException extends \Exception
{
    /**
     * @var int
     */
    public $code = 404;

    /**
     * @var string
     */
    public $message = "Resource not found.";

    /**
     * @var string
     */
    protected $title;

    /**
     * @codeCoverageIgnore
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @codeCoverageIgnore
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }
}
