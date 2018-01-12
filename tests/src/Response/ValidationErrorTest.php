<?php

namespace Jad\Tests;

use Jad\Response\ValidationErrors;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

class ValidationErrorTest extends TestCase
{
    public function testRender()
    {
        $err = new ConstraintViolation(
            'Test error',
            'Test error',
            [],
            'root',
            'property',
            'invalid'
        );

        ob_start();
        $validationErrors = new ValidationErrors(new ConstraintViolationList([$err]));
        $validationErrors->render();
        $output = ob_get_clean();

        $this->assertEquals('{"errors":[{"status":"500","detail":"Test error [property]","title":"Validation Error"}]}', $output);
    }
}