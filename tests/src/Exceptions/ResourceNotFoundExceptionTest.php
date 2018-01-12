<?php

namespace Jad\Tests\Query;

use Jad\Tests\TestCase;
use Jad\Exceptions\ResourceNotFoundException;

class ResourceNotFoundExceptionTest extends TestCase
{
    public function testTitle()
    {
        $exception = new ResourceNotFoundException();
        $exception->setTitle('Test Title');
        $this->assertEquals('Test Title', $exception->getTitle());
    }
}