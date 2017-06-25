<?php

namespace Jad\Tests\Query;

use Jad\Tests\TestCase;
use Jad\Query\Filter;

function method_exists()
{
    return true;
}

class FilterTest extends TestCase
{
    public function testSingleFilter()
    {
        $expr = $this->getMockBuilder('Doctrine\ORM\Query\Expr')
            ->disableOriginalConstructor()
            ->setMethods(['eq'])
            ->getMock();

        $expr->expects($this->at(0))
            ->method('eq')
            ->willReturn('equal');

        $qb = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
            ->disableOriginalConstructor()
            ->setMethods(['andWhere', 'setParameter', 'expr'])
            ->getMock();

        $qb->expects($this->at(0))
            ->method('expr')
            ->willReturn($expr);

        $qb->expects($this->at(1))
            ->method('andWhere')
            ->with('equal');

        $filter = [
            'price' => [ 'eq' => '1' ]
        ];

        $filter = new Filter($filter, $qb);

        $method = $this->getMethod('Jad\Query\Filter', 'addSingleFilter');
        $method->invokeArgs($filter, []);
    }

    public function testAddFilter()
    {
        $expr = $this->getMockBuilder('Doctrine\ORM\Query\Expr')
            ->disableOriginalConstructor()
            ->setMethods(['eq'])
            ->getMock();

        $expr->expects($this->at(0))
            ->method('eq')
            ->willReturn('equal');

        $qb = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
            ->disableOriginalConstructor()
            ->setMethods(['andWhere', 'setParameter', 'expr'])
            ->getMock();

        $qb->expects($this->any())
            ->method('expr')
            ->willReturn($expr);

        $qb->expects($this->at(1))
            ->method('andWhere')
            ->with('equal');

        $qb->expects($this->any())
            ->method('setParameter')
            ->with($this->matchesRegularExpression('/price_/'), 5);

        $filter = ['price' => [ 'eq' => '1' ]];
        $filter = new Filter($filter, $qb);

        $method = $this->getMethod('Jad\Query\Filter', 'addFilter');
        $method->invokeArgs($filter, ['price', 'eq', 5, 'and']);
    }

    public function testAddConditionalFilter()
    {
        $expr = $this->getMockBuilder('Doctrine\ORM\Query\Expr')
            ->disableOriginalConstructor()
            ->setMethods(['eq', 'lt', 'gt'])
            ->getMock();

        $expr->expects($this->at(0))
            ->method('lt')
            ->willReturn('lessThan');

        $expr->expects($this->at(0))
            ->method('gt')
            ->willReturn('greater than');

        $expr->expects($this->at(0))
            ->method('eq')
            ->willReturn('equal');

        $qb = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
            ->disableOriginalConstructor()
            ->setMethods(['andWhere', 'setParameter', 'expr'])
            ->getMock();

        $qb->expects($this->any())
            ->method('expr')
            ->willReturn($expr);

        $fltr = [
            'and' => [
                'price' => [
                    'lt' => '1',
                    'gt' => '0.5'
                ]
            ],
            'or' => [
                'genre' => [
                    'eq' => '5'
                ]
            ]
        ];

        $filter = new Filter($fltr, $qb);

        $method = $this->getMethod('Jad\Query\Filter', 'addConditionalFilter');
        $method->invokeArgs($filter, []);
    }

    public function testGetFilterType()
    {
        $qb = $this->getMockBuilder('Doctrine\ORM\QueryBuilder')
            ->disableOriginalConstructor()
            ->setMethods(['andWhere', 'setParameter', 'expr'])
            ->getMock();

        $fltr = [

            'price' => [ 'eq' => '1' ]
        ];

        $filter = new Filter($fltr, $qb);

        $method = $this->getMethod('Jad\Query\Filter', 'getFilterType');
        $this->assertEquals('single',  $method->invokeArgs($filter, []));

        $fltr = [
            'price' => [ 'gt' => '0', 'lt' => 2 ]
        ];

        $filter = new Filter($fltr, $qb);

        $method = $this->getMethod('Jad\Query\Filter', 'getFilterType');
        $this->assertEquals('single',  $method->invokeArgs($filter, []));

        $fltr = [
            'and' => [
                'price' => [
                    'lt' => '1',
                    'gt' => '0.5'
                ]
            ]
        ];

        $filter = new Filter($fltr, $qb);

        $method = $this->getMethod('Jad\Query\Filter', 'getFilterType');
        $this->assertEquals('conditional',  $method->invokeArgs($filter, []));

        $fltr = [
            'and' => [
                'price' => [
                    'lt' => '1',
                    'gt' => '0.5'
                ]
            ],
            'or' => [
                'genre' => [
                    'eq' => '5'
                ]
            ]
        ];

        $filter = new Filter($fltr, $qb);

        $method = $this->getMethod('Jad\Query\Filter', 'getFilterType');
        $this->assertEquals('conditional',  $method->invokeArgs($filter, []));
    }

}