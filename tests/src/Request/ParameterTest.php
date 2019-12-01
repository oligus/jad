<?php

namespace Jad\Tests\Request;

use Jad\Exceptions\ParameterException;
use Jad\Tests\TestCase;
use Jad\Request\Parameters;

/**
 * Class ParameterTest
 * @package Jad\Tests\Request
 */
class ParameterTest extends TestCase
{
    /**
     * @throws \Jad\Exceptions\ParameterException
     */
    public function testGetIncludes()
    {
        $this->expectException(ParameterException::class);
        $this->expectExceptionMessage('Resource to include not found [test1], available resources [author, test2]');

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
     * @throws \Exception
     */
    public function testGetOffset()
    {
        $this->expectException(ParameterException::class);
        $this->expectExceptionMessage('page[offset] must be >=0');

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
     * @throws \Jad\Exceptions\ParameterException
     */
    public function testGetSort()
    {
        $this->expectException(ParameterException::class);
        $this->expectExceptionMessage('Invalid sort fields [test1,test2]');

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
