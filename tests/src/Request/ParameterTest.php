<?php

namespace Jad\Tests\Request;

use Jad\Tests\TestCase;
use Jad\Request\Parameters;

/**
 * Class ParameterTest
 * @package Jad\Tests\Request
 */
class ParameterTest extends TestCase
{
    /**
     * @expectedException \Jad\Exceptions\ParameterException
     * @expectedExceptionMessage Resource to include not found [test1], available resources [author, test2]
     * @throws \Jad\Exceptions\ParameterException
     */
    public function testGetIncludes()
    {
        $parameters = new Parameters();
        $parameters->setArguments([
            'include' => 'author, author'
        ]);

        $this->assertEquals(['author'], $parameters->getIncludes());
        $this->assertEquals([['author' => '']], $parameters->getInclude(['author']));

        $parameters->setArguments([
            'include' => 'author, test1, test2, test1'
        ]);

        $this->assertEquals(['author', 'test1', 'test2'], $parameters->getIncludes());

        $expected = [
            ['author' => ''],
            ['test1' => ''],
            ['test2' => ''],
        ];
        $this->assertEquals($expected, $parameters->getInclude(['author', 'test1', 'test2']));

        $this->assertEquals([['test1' => '']], $parameters->getInclude(['author', 'test2']));
    }

    /**
     * @expectedException \Jad\Exceptions\ParameterException
     * @expectedExceptionMessage page[offset] must be >=0
     * @throws \Exception
     */
    public function testGetOffset()
    {
        $parameters = new Parameters();
        $parameters->setArguments([
            'page' => [
                'number' => 2,
                'size' => 5,
            ]
        ]);

        $this->assertEquals(25, $parameters->getOffset(25));

        $parameters->setArguments([
            'page' => [
                'offset' => -1,
            ]
        ]);
        $parameters->getOffset(25);
    }

    public function testGetSize()
    {
        $parameters = new Parameters();
        $parameters->setArguments([
            'page' => [
                'number' => 2,
                'size' => 5,
            ]
        ]);

        $this->assertEquals(5, $parameters->getSize(25));
    }

    public function testGetFields()
    {
        $parameters = new Parameters();
        $parameters->setArguments([
            'fields' => [
                'tracks' => 'test1, test2'
            ]
        ]);

        $this->assertEquals([
            'tracks' => ['test1', 'test2']
        ], $parameters->getFields());
    }

    public function testGetFilter()
    {
        $parameters = new Parameters();
        $parameters->setArguments([
            'filter' => [
                'customers.invoice' => [
                    'total' => [
                        'lte' => 5
                    ]
                ]
            ]
        ]);

        $this->assertEquals([
            'customers.invoice' => [
                'total' => [
                    'lte' => 5
                ]
            ]
        ], $parameters->getFilter());

        $this->assertEquals([
            'customers.invoice' => [
                'total' => [
                    'lte' => 5
                ]
            ]
        ], $parameters->getFilter());
    }

    public function testGetLimit()
    {
        $parameters = new Parameters();
        $parameters->setArguments([
            'page' => [
                'size' => '',
                'limit' => '',
            ]
        ]);

        $this->assertEquals(5, $parameters->getLimit(5, 0));
    }

    /**
     * @expectedException \Jad\Exceptions\ParameterException
     * @expectedExceptionMessage Invalid sort fields [test1,test2]
     * @throws \Jad\Exceptions\ParameterException
     */
    public function testGetSort()
    {
        $parameters = new Parameters();
        $parameters->setArguments([
            'sort' => 'test1, -test2'
        ]);

        $expected = [
            "test1" => "asc",
            "test2" => "desc"
        ];

        $this->assertEquals($expected, $parameters->getSort(['test1', 'test2']));
        $parameters->getSort(['unknown']);
    }
}