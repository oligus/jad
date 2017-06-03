<?php

namespace Jad\Tests\Request;

use Jad\Tests\TestCase;
use Jad\Request\Parameters;

class ParameterTest extends TestCase
{
    /*
     * array(3) {
  'page' =>
  array(2) {
    'offset' =>
    int(0)
    'limit' =>
    int(5)
  }
  'sort' =>
  string(5) "-name"

}
     */

    public function testGetFields()
    {
        $parameters = new Parameters([
            'fields' => [
                'tracks' => 'test1, test2'
            ]
        ]);

        $this->assertEquals([
            'tracks' => ['test1', 'test2']
        ], $parameters->getFields());
    }
}