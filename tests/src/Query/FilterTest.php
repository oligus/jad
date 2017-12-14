<?php

namespace Jad\Tests\Query;

use Jad\Tests\TestCase;
use Jad\Query\Filter;
use Jad\Common\ClassHelper;
use Jad\Database\Manager;
use Doctrine\ORM\QueryBuilder;

/**
 * Class FilterTest
 * @package Jad\Tests\Query
 */
class FilterTest extends TestCase
{
    public function testConstructor()
    {
        $filter1 = [
            'customer.invoices' => [
                'total' => [
                    'lt' => '5'
                ]
            ]
        ];

        $filter = new Filter($filter1);

        $this->assertEquals('customer.invoices', ClassHelper::getPropertyValue($filter, 'path'));
        $this->assertEquals(['customer.invoices' => ['total' => ['lt' => 5 ]]], ClassHelper::getPropertyValue($filter, 'filter'));
    }

    public function testGetArrayDepth()
    {
        $filter = new Filter([]);
        $method = $this->getMethod('Jad\Query\Filter', 'getArrayDepth');
        $this->assertEquals(0, $method->invokeArgs($filter, [[]]));
        $this->assertEquals(1, $method->invokeArgs($filter, [[
            'test' => 'moo'
        ]]));
        $this->assertEquals(2, $method->invokeArgs($filter, [[
            'test' => ['moo' => 'test']
        ]]));
        $this->assertEquals(3, $method->invokeArgs($filter, [[
            'test' => ['moo' => ['level' => 3]]
        ]]));
        $this->assertEquals(4, $method->invokeArgs($filter, [[
            'test' => ['moo' => ['level' => ['last' => 'test']]]
        ]]));
    }

    public function testIsRelational()
    {
        $filter = new Filter([]);
        $method = $this->getMethod('Jad\Query\Filter', 'isRelational');
        $this->assertFalse($method->invokeArgs($filter, ['test']));
        $this->assertTrue($method->invokeArgs($filter, ['test.relation']));
    }

    public function testGetAliases()
    {
        $filter1 = [
            'customer.invoices.invoice-items' => [
                'total' => [
                    'lt' => '5'
                ]
            ]
        ];

        $filter = new Filter($filter1);
        $this->assertEquals('a0customer,a1invoices,a2invoiceItems', $filter->getAliases());
    }

    public function testGetRootAlias()
    {
        $filter1 = [
            'customer.invoices.invoice-items' => [
                'total' => [
                    'lt' => '5'
                ]
            ]
        ];

        $filter = new Filter($filter1);

        $this->assertEquals('a0customer', $filter->getRootAlias());
    }

    public function testGetJoins()
    {
        $filterParam = array (
            'contacts.accounts.invoices' => array ('total' => array ('eq' => '500'))
        );

        $filter = new Filter($filterParam);
        $expected = array(
            'a0contacts.accounts' => 'a1accounts',
            'a1accounts.invoices' => 'a2invoices'
        );
        $this->assertEquals($expected, $filter->getJoins('contacts.accounts.invoices'));
    }

    public function testGetFilterType()
    {
        $filterParam = [ 'customer' =>  ['price' => [ 'eq' => '1' ]]];
        $filter = new Filter($filterParam);
        $this->assertEquals('single',  $filter->getFilterType($filterParam));

        $filterParam = [ 'customer' => ['price' => [ 'gt' => '0', 'lt' => 2 ]]];
        $filter = new Filter($filterParam);
        $this->assertEquals('single',  $filter->getFilterType($filterParam));

        $filterParam = [ 'customer' =>  ['and' => ['price' => [ 'gt' => '0', 'lt' => 2 ]]]];
        $filter = new Filter($filterParam);
        $this->assertEquals('conditional',  $filter->getFilterType($filterParam));

        $filterParam = ['customer' => ['and' => ['price' => ['lt' => '1', 'gt' => '0.5']], 'or' => ['genre' => ['eq' => '5']]]];
        $filter = new Filter($filterParam);
        $this->assertEquals('conditional',  $filter->getFilterType($filterParam));
    }

    public function testGetLastAlias()
    {
        $filter1 = [
            'customer.invoices.invoice-items' => [
                'total' => [
                    'lt' => '5'
                ]
            ]
        ];

        $filter = new Filter($filter1);
        $this->assertEquals('a2invoiceItems', $filter->getLastAlias());
    }

    public function testSingleFilter()
    {
        $dql = $this->getDQLFromFilter(['total' => ['lt' => '5']]);
        $this->assertRegExp('/SELECT a0testType FROM TestEntityClass a0testType WHERE a0testType.total < :total_[a-z0-9]{13}$/', $dql);

        $dql = $this->getDQLFromFilter(['customers' => ['total' => ['lt' => '5']]]);
        $this->assertRegExp('/SELECT a0customers FROM TestEntityClass a0customers WHERE a0customers.total < :total_[a-z0-9]{13}$/', $dql);
    }

    public function testSingleRelationalFilter()
    {
        $dql = $this->getDQLFromFilter(['customers.invoices' => ['total' => ['lt' => '5']]]);
        $this->assertRegExp('/SELECT a0customers,a1invoices FROM TestEntityClass a0customers INNER JOIN a0customers.invoices a1invoices WHERE a1invoices.total < :total_[a-z0-9]{13}$/', $dql);
    }

    public function testConditionalFilter()
    {
        $filter = [
            'customers' => [
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
            ]
        ];

        $dql = $this->getDQLFromFilter($filter);
        $this->assertRegExp('#^SELECT a0customers FROM TestEntityClass a0customers WHERE \(a0customers.price < :price_[a-z0-9]{13} AND a0customers.price > :price_[a-z0-9]{13}\) OR a0customers.genre = :genre_[a-z0-9]{13}$#', $dql);
    }

    public function testConditionalRelationalFilter()
    {
        $filter = [
            'customers.invoices' => [
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
            ]
        ];

        $dql = $this->getDQLFromFilter($filter);
        $this->assertRegExp('#^SELECT a0customers,a1invoices FROM TestEntityClass a0customers INNER JOIN a0customers.invoices a1invoices WHERE \(a1invoices.price < :price_[a-z0-9]{13} AND a1invoices.price > :price_[a-z0-9]{13}\) OR a1invoices.genre = :genre_[a-z0-9]{13}$#', $dql);
    }

    private function getDQLFromFilter($filterParam)
    {
        $qb = new QueryBuilder(Manager::getInstance()->getEm());

        $filter = new Filter($filterParam, 'test-type');

        $qb->select($filter->getAliases());
        $qb->from('TestEntityClass', $filter->getRootAlias());

        $filter->setQb($qb);
        $filter->process();

        return $qb->getDQL();
    }
}
