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

    /**
     * @var string
     */
    private $path;

    /**
     * @var array
     */
    private $relatedPaths = [];

    /**
     * Filter constructor.
     * @param array $filter
     * @param string $defaultType
     */
    public function __construct(array $filter, $defaultType = '')
    {
        if(!empty($filter)) {
            if($this->getArrayDepth($filter) < 3 && !empty($defaultType)) {
                $newFilter = [];
                $newFilter[$defaultType] = $filter;
                $filter = $newFilter;
            }

            if(count($filter) === 1) {
                $this->path = current(array_keys($filter));
            } else {
                foreach ($filter as $path => $data) {
                    if($this->isRelational($path)) {
                        $this->relatedPaths[] = $path;
                    } else {
                        $this->path = $path;
                    }
                }
            }

            $this->filter = $filter;
        }
    }

    /**
     * @param array $array
     * @return int
     */
    public function getArrayDepth(array $array): int
    {
        if(!is_array($array) || empty($array)) {
            return 0;
        }

        $exploded = explode(',', json_encode($array, JSON_FORCE_OBJECT)."\n\n");

        $longest = 0;
        foreach($exploded as $row){
            $longest = (substr_count($row, ':')>$longest)?
                substr_count($row, ':'):$longest;
        }

        return (int) $longest;
    }

    /**
     * @return QueryBuilder
     * @throws JadException
     */
    public function process(): QueryBuilder
    {
        if(!is_array($this->filter) || empty($this->filter)) {
            return $this->qb;
        }

        if($this->isRelational($this->path)) {
            $this->addJoin($this->path);
        }

        if(!empty($this->relatedPaths)) {
            foreach($this->relatedPaths as $path) {
                if($this->isRelational($path)) {
                    $this->addJoin($path);
                }
            }
        }

        if($this->getFilterType($this->filter) === 'conditional') {
            $this->addConditionalFilter($this->path);

            if(!empty($this->relatedPaths)) {
                foreach($this->relatedPaths as $path) {
                    $this->addConditionalFilter($path);
                }
            }

        }

        if($this->getFilterType($this->filter) === 'single') {
            $this->addSingleFilter();
        }

        return $this->qb;
    }

    /**
     * @param $path
     * @return bool
     */
    private function isRelational(string $path): bool
    {
        return strpos($path, '.') > 0;
    }

    /**
     * @return QueryBuilder
     */
    public function getQb(): QueryBuilder
    {
        return $this->qb;
    }

    /**
     * @param QueryBuilder $qb
     */
    public function setQb(QueryBuilder $qb): void
    {
        $this->qb = $qb;
    }

    /**
     * @param $filter
     * @return bool|string
     */
    public function getFilterType(array $filter): string
    {
        if(!is_array($filter) || empty($filter)) {
            return false;
        }

        if(array_key_exists('and', $filter[$this->path]) || array_key_exists('or', $filter[$this->path])) {
            return 'conditional';
        }

        return 'single';
    }


    /**
     * @return string
     */
    public function getAliases(): string
    {
        $path = is_string($this->path) ? $this->path : '';
        return implode(',', array_keys($this->createAliases($path)));
    }

    /**
     * @return string
     */
    public function getRootAlias(): string
    {
        $path = is_string($this->path) ? $this->path : '';
        $aliases = array_keys($this->createAliases($path));
        return $aliases[0];
    }

    /**
     * @return string
     */
    public function getLastAlias(): string
    {
        $aliases = array_keys($this->createAliases($this->path));
        return end($aliases);
    }


    /**
     * @param string $path
     * @return array
     */
    public function createAliases(string $path): array
    {
        $parts = explode('.', $path);

        $aliases = array();

        foreach($parts as $key => $part) {
            $aliases['a' . $key . Text::deKebabify($part)] = $part;
        }

        return $aliases;
    }

    /**
     * @param $path
     * @return array
     */
    public function getJoins($path): array
    {
        $count = 0;
        $key = '';
        $joins = array();

        foreach($this->createAliases($path) as $alias => $relation) {
            if($count === 0) {
                $key = $alias;
            } else {
                $key .= '.' . $relation;
                $joins[$key] = $alias;
                $key = $alias;
            }

            $count++;
        }

        return $joins;
    }

    private function addSingleFilter(): void
    {
        $property = array_keys($this->filter[$this->path])[0];
        $condition = array_keys($this->filter[$this->path][$property])[0];
        $value = array_values($this->filter[$this->path][$property])[0];

        $joins = $this->getJoins($this->path);
        $alias = current($joins);

        if($this->isRelational($this->path)) {
            $alias = $this->getLastAlias();
        }

        if(empty($alias)) {
            $alias = $this->getRootAlias();
        }

        $this->addFilter($property, $condition, $value, 'and', $alias);
    }

    /**
     * @param $path
     * @throws JadException
     */
    private function addConditionalFilter($path): void
    {
        foreach($this->filter[$path] as $propertyConditional => $value) {
            foreach($value as $property => $conditions) {
                if(!is_array($conditions)) {
                    throw new JadException('Conditional filter value is not an array, check if [and] - [or] is present.');
                }

                $joins = $this->getJoins($path);
                $alias = current($joins);

                if(empty($alias)) {
                    $alias = $this->getRootAlias();
                }

                foreach($conditions as $condition => $val) {
                    $this->addFilter($property, $condition, $val, $propertyConditional, $alias);
                }
            }
        }
    }

    public function addJoin($path): void
    {
        foreach($this->getJoins($path) as $relation => $alias) {
            $this->qb->innerJoin($relation, $alias);
        }
    }

    /**
     * @param $property
     * @param $condition
     * @param $value
     * @param string $where
     * @param null $alias
     * @throws JadException
     */
    private function addFilter($property, $condition, $value, $where = 'and', $alias = null)
    {
        $value = urldecode($value);

        if(!method_exists('Doctrine\ORM\Query\Expr', $condition)) {
            throw new JadException('Filter condition [' . $condition . '] not available.');
        }

        $field = Text::deKebabify($property);
        $fieldName1 = $field . '_' . uniqid();
        $fieldName2 = $field . '_' . uniqid();

        $whereCondition = $where === 'and' ? 'andWhere' : 'orWhere';

        if(empty($alias)) {
            $alias = $this->getRootAlias();
        }

        if(in_array($condition, ['like', 'notLike'])) {
            $value = '%' . $value . '%';
        }

        if(in_array($condition, ['in', 'notIn', 'between'])) {
            $value = explode(',', $value);
        }

        if(in_array($condition, ['between'])) {
            $this->qb->$whereCondition($this->qb->expr()->$condition($alias . '.' . $field, ':' . $fieldName1,  ':' . $fieldName2));
            $this->qb->setParameter($fieldName1, $value[0]);
            $this->qb->setParameter($fieldName2, $value[1]);
        } else {
            $this->qb->$whereCondition($this->qb->expr()->$condition($alias . '.' . $field, ':' . $fieldName1));
            $this->qb->setParameter($fieldName1, $value);
        }

    }
}
