<?php

namespace Jad\Tests\Query;

use Jad\Exceptions\JadException;
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
    /**
     * @throws \Jad\Exceptions\JadException
     * @throws \ReflectionException
     */
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

        $filter2 = [
            'customer.invoices' => [
                'total' => [
                    'lt' => '5'
                ]
            ],
            'customer' => [
                'email' => [
                    'eq' => 'test@moo.com'
                ]
            ],
        ];

        $filter = new Filter($filter2);
        $this->assertEquals('customer', ClassHelper::getPropertyValue($filter, 'path'));
        $this->assertEquals(['customer.invoices'], ClassHelper::getPropertyValue($filter, 'relatedPaths'));
        $expected = [
            'customer.invoices' => ['total' => ['lt' => 5 ]],
            'customer' => ['email' => [ 'eq' => 'test@moo.com']]
        ];
        $this->assertEquals($expected, ClassHelper::getPropertyValue($filter, 'filter'));

        $filter3 = [
            'total' => [
                'lt' => '5'
            ]
        ];

        $filter = new Filter($filter3, 'defaultType');
        $this->assertEquals('defaultType', ClassHelper::getPropertyValue($filter, 'path'));
    }

    /**
     * @throws \ReflectionException
     */
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

    /**
     * @throws \ReflectionException
     */
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

        $this->assertFalse( $filter->getFilterType([]));
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

    /**
     * @throws JadException
     */
    public function testSingleFilter()
    {
        $dql = $this->getDQLFromFilter(['total' => ['lt' => '5']]);
        $this->assertRegExp('/SELECT a0testType FROM TestEntityClass a0testType WHERE a0testType.total < :total_[a-z0-9]{13}$/', $dql);

        $dql = $this->getDQLFromFilter(['customers' => ['total' => ['lt' => '5']]]);
        $this->assertRegExp('/SELECT a0customers FROM TestEntityClass a0customers WHERE a0customers.total < :total_[a-z0-9]{13}$/', $dql);
    }

    /**
     * @throws JadException
     */
    public function testSingleRelationalFilter()
    {
        $dql = $this->getDQLFromFilter(['customers.invoices' => ['total' => ['lt' => '5']]]);
        $this->assertRegExp('/SELECT a0customers,a1invoices FROM TestEntityClass a0customers INNER JOIN a0customers.invoices a1invoices WHERE a1invoices.total < :total_[a-z0-9]{13}$/', $dql);
    }

    /**
     * @throws JadException
     */
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

    /**
     * @throws \ReflectionException
     */
    public function testAddFilter()
    {
        $this->expectException(JadException::class);
        $this->expectExceptionMessage('Filter condition [unknown] not available.');

        $filter = new Filter([]);
        $filter->setQb(new QueryBuilder(Manager::getInstance()->getEm()));
        $method = $this->getMethod('Jad\Query\Filter', 'addFilter');
        $method->invokeArgs($filter, ['total', 'eq', 5]);
        $dql = $filter->getQb()->getDQL();
        $this->assertRegExp('#^SELECT WHERE a0.total = :total_[a-z0-9]{13}$#', $dql);

        $filter = new Filter([]);
        $filter->setQb(new QueryBuilder(Manager::getInstance()->getEm()));
        $method = $this->getMethod('Jad\Query\Filter', 'addFilter');
        $method->invokeArgs($filter, ['total', 'like', 'test']);
        $dql = $filter->getQb()->getDQL();
        $this->assertRegExp('#^SELECT WHERE a0.total LIKE :total_[a-z0-9]{13}$#', $dql);

        $filter = new Filter([]);
        $filter->setQb(new QueryBuilder(Manager::getInstance()->getEm()));
        $method = $this->getMethod('Jad\Query\Filter', 'addFilter');
        $method->invokeArgs($filter, ['total', 'in', 'test1, test2']);
        $dql = $filter->getQb()->getDQL();
        $this->assertRegExp('#^SELECT WHERE a0.total IN\(:total_[a-z0-9]{13}\)$#', $dql);

        $filter = new Filter([]);
        $filter->setQb(new QueryBuilder(Manager::getInstance()->getEm()));
        $method = $this->getMethod('Jad\Query\Filter', 'addFilter');
        $method->invokeArgs($filter, ['total', 'between', '1, 10']);
        $dql = $filter->getQb()->getDQL();
        $this->assertRegExp('#^SELECT WHERE a0.total BETWEEN :total_[a-z0-9]{13} AND :total_[a-z0-9]{13}$#', $dql);

        $filter = new Filter([]);
        $method->invokeArgs($filter, ['total', 'unknown', 'test']);
    }

    /**
     * @throws \ReflectionException
     */
    public function testAddConditionalFilter()
    {
        $filter1 = [
            'invoices' => [
                'and' => ['price' => ['lt' => '1', 'gt' => '0.5']],
                'or' => ['genre' => ['eq' => '5']]
            ]
        ];

        $filter = $this->getMockBuilder('Jad\Query\Filter')
            ->setConstructorArgs([$filter1])
            ->setMethods(['getJoins', 'getRootAlias'])
            ->getMock();

        $filter->setQb(new QueryBuilder(Manager::getInstance()->getEm()));

        $filter->expects($this->any())
            ->method('getJoins')
            ->willReturn([]);

        $filter->expects($this->any())
            ->method('getRootAlias')
            ->willReturn('a0');

        $method = $this->getMethod('Jad\Query\Filter', 'addConditionalFilter');
        $method->invokeArgs($filter, ['invoices']);

        $dql = $filter->getQb()->getDQL();
        $this->assertRegExp('#^SELECT WHERE \(a0.price < :price_[a-z0-9]{13} AND a0.price > :price_[a-z0-9]{13}\) OR a0.genre = :genre_[a-z0-9]{13}$#', $dql);
    }

    /**
     * @throws \ReflectionException
     */
    public function testAddConditionalFilterException()
    {
        $this->expectException(JadException::class);
        $this->expectExceptionMessage('Conditional filter value is not an array, check if [and] - [or] is present.');

        $filter = new Filter(['invoices' => ['and' => ['price']]]);
        $filter->setQb(new QueryBuilder(Manager::getInstance()->getEm()));
        $method = $this->getMethod('Jad\Query\Filter', 'addConditionalFilter');
        $method->invokeArgs($filter, ['invoices']);
    }

    /**
     * @throws \Jad\Exceptions\JadException
     */
    public function testProcess()
    {
        $filterParams = [
            'customer.invoices' => [
                'and' => ['price' => ['lt' => '1', 'gt' => '0.5']],
                'or' => ['genre' => ['eq' => '5']]
            ],
            'customer' => [
                'email' => [
                    'eq' => 'test@moo.com'
                ]
            ]
        ];

        $filter = new Filter($filterParams);

        $qb = new QueryBuilder(Manager::getInstance()->getEm());
        $qb->select($filter->getAliases());
        $qb->from('EntityClass', $filter->getRootAlias());

        $filter->setQb($qb);
        $filter->process();

        $dql = $filter->getQb()->getDQL();
        $this->assertRegExp('#^SELECT a0customer FROM EntityClass a0customer INNER JOIN a0customer.invoices a1invoices WHERE a0customer.email = :email_[a-z0-9]{13}$#', $dql);
    }

    /**
     * @throws \Jad\Exceptions\JadException
     */
    public function testProcessConditional()
    {
        $filterParams = [
            'customer.invoices' => [
                'and' => ['price' => ['lt' => '1', 'gt' => '0.5']],
                'or' => ['genre' => ['eq' => '5']]
            ],
        ];

        $filter = new Filter($filterParams);

        $qb = new QueryBuilder(Manager::getInstance()->getEm());
        $qb->select($filter->getAliases());
        $qb->from('EntityClass', $filter->getRootAlias());

        $filter->setQb($qb);
        $filter->process();

        $dql = $filter->getQb()->getDQL();
        $this->assertRegExp('#^SELECT a0customer,a1invoices FROM EntityClass a0customer INNER JOIN a0customer.invoices a1invoices WHERE \(a1invoices.price < :price_[a-z0-9]{13} AND a1invoices.price > :price_[a-z0-9]{13}\) OR a1invoices.genre = :genre_[a-z0-9]{13}$#', $dql);
    }

    /**
     * @param $filterParam
     * @return string
     * @throws \Jad\Exceptions\JadException
     */
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
