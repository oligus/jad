<?php

namespace Jad\Query;

use Jad\Common\Text;
use Jad\Exceptions\JadException;
use Doctrine\ORM\QueryBuilder;

/**
 * Class Filter
 * @package Jad\Query
 */
class Filter
{
    /**
     * @var QueryBuilder
     */
    private $qb;

    /**
     * @var array
     */
    private $filter;

    public function __construct(array $filter, QueryBuilder $qb)
    {
        $this->qb = $qb;
        $this->filter = $filter;
    }

    public function process()
    {
        if(!$type = $this->getFilterType()) {
            return $this->qb;
        }

        if($type === 'conditional') {
            $this->addConditionalFilter();
        }

        if($type === 'single') {
            $this->addSingleFilter();
        }

        return $this->qb;
    }

    public function getQb()
    {
        return $this->qb;
    }

    /**
     * @return bool|string
     */
    private function getFilterType()
    {
        if(!is_array($this->filter) || empty($this->filter)) {
            return false;
        }

        if(array_key_exists('and', $this->filter) || array_key_exists('or', $this->filter)) {
            return 'conditional';
        }

        return 'single';
    }


    private function addSingleFilter()
    {
        $property = array_keys($this->filter)[0];
        $condition = array_keys($this->filter[$property])[0];
        $value = array_values($this->filter[$property])[0];
        $this->addFilter($property, $condition, $value);
    }

    private function addConditionalFilter()
    {
        foreach($this->filter as $propertyConditional => $value) {
            $property = array_keys($value)[0];
            $conditions = $value[$property];

            foreach($conditions as $condition => $val) {
                $this->addFilter($property, $condition, $val, $propertyConditional);
            }
        }
    }

    /**
     * @param $property
     * @param $condition
     * @param $value
     * @param string $where
     * @throws JadException
     */
    private function addFilter($property, $condition, $value, $where = 'and')
    {
        if(!method_exists('Doctrine\ORM\Query\Expr', $condition)) {
            throw new JadException('Filter condition [' . $condition . '] not available.');
        }

        $field = Text::deKebabify($property);
        $fieldName = $field . '_' . uniqid();

        $whereCondition = $where === 'and' ? 'andWhere' : 'orWhere';

        if(in_array($condition, ['like', 'notLike'])) {
            $value = '%' . $value . '%';
        }

        $this->qb->$whereCondition($this->qb->expr()->$condition('t.' . $field, ':' . $fieldName));
        $this->qb->setParameter($fieldName, $value);
    }
}