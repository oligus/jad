<?php declare(strict_types=1);

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
    public const TYPE_SINGLE = 'single';
    public const TYPE_CONDITIONAL = 'conditional';

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
        if (!empty($filter)) {
            if ($this->getArrayDepth($filter) < 3 && !empty($defaultType)) {
                $newFilter = [];
                $newFilter[$defaultType] = $filter;
                $filter = $newFilter;
            }

            if (count($filter) === 1) {
                $this->path = current(array_keys($filter));
                $this->filter = $filter;
                return;
            }

            foreach (array_keys($filter) as $path) {
                $this->isRelational($path)
                    ? $this->relatedPaths[] = $path
                    : $this->path = $path;
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
        if (!is_array($array) || empty($array)) {
            return 0;
        }

        $exploded = explode(',', json_encode($array, JSON_FORCE_OBJECT) . "\n\n");

        $longest = 0;
        foreach ($exploded as $row) {
            $longest = (substr_count($row, ':') > $longest) ?
                substr_count($row, ':') : $longest;
        }

        return (int)$longest;
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
     * @throws JadException
     */
    public function process(): QueryBuilder
    {
        if (!$this->hasFilter()) {
            return $this->qb;
        }

        $this->addRelational();

        if (!empty($this->relatedPaths)) {
            foreach ($this->relatedPaths as $path) {
                if ($this->isRelational($path)) {
                    $this->addJoin($path);
                }
            }
        }

        if ($this->isConditional()) {
            $this->addConditionalFilter($this->path);

            if (!empty($this->relatedPaths)) {
                foreach ($this->relatedPaths as $path) {
                    $this->addConditionalFilter($path);
                }
            }
        }

        if ($this->isSingle()) {
            $this->addSingleFilter();
        }

        return $this->qb;
    }

    public function addJoin(string $path): void
    {
        foreach ($this->getJoins($path) as $relation => $alias) {
            $this->qb->innerJoin($relation, $alias);
        }
    }

    /**
     * @param $path
     * @return array
     */
    public function getJoins(string $path): array
    {
        $count = 0;
        $key = '';
        $joins = [];

        foreach ($this->createAliases($path) as $alias => $relation) {
            if ($count === 0) {
                $key = $alias;
            }

            if ($count !== 0) {
                $key .= '.' . $relation;
                $joins[$key] = $alias;
                $key = $alias;
            }

            $count++;
        }

        return $joins;
    }

    /**
     * @param string $path
     * @return array
     */
    public function createAliases(string $path): array
    {
        $parts = explode('.', $path);

        $aliases = [];

        foreach ($parts as $key => $part) {
            $aliases['a' . $key . Text::deKebabify($part)] = $part;
        }

        return $aliases;
    }

    /**
     * @param $filter
     * @return bool|string
     */
    public function getFilterType(array $filter)
    {
        if (empty($filter)) {
            return false;
        }

        if (array_key_exists('and', $filter[$this->path]) || array_key_exists('or', $filter[$this->path])) {
            return self::TYPE_CONDITIONAL;
        }

        return self::TYPE_SINGLE;
    }

    /**
     * @param $path
     * @throws JadException
     */
    private function addConditionalFilter(string $path): void
    {
        foreach ($this->filter[$path] as $propertyConditional => $value) {
            foreach ($value as $property => $conditions) {
                if (!is_array($conditions)) {
                    throw new JadException('Conditional filter value is not an array, check if [and] - [or] is present.');
                }

                $joins = $this->getJoins($path);
                $alias = current($joins);

                if (empty($alias)) {
                    $alias = $this->getRootAlias();
                }

                foreach ($conditions as $condition => $val) {
                    $this->addFilter($property, $condition, $val, $propertyConditional, $alias);
                }
            }
        }
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
     * @param $property
     * @param $condition
     * @param $value
     * @param string $where
     * @param string|null $alias
     * @throws JadException
     */
    private function addFilter(
        string $property,
        string $condition,
        string $value,
        string $where = 'and',
        string $alias = null
    ): void {
        $value = urldecode($value);

        if (!method_exists('Doctrine\ORM\Query\Expr', $condition)) {
            throw new JadException('Filter condition [' . $condition . '] not available.');
        }

        $field = Text::deKebabify($property);
        $fieldName1 = $field . '_' . uniqid();
        $fieldName2 = $field . '_' . uniqid();

        $whereCondition = $where === 'and' ? 'andWhere' : 'orWhere';

        if (empty($alias)) {
            $alias = $this->getRootAlias();
        }

        if (in_array($condition, ['like', 'notLike'])) {
            $value = '%' . $value . '%';
        }

        if (in_array($condition, ['in', 'notIn', 'between'])) {
            $value = explode(',', $value);
        }

        if (in_array($condition, ['between'])) {
            $this->qb->$whereCondition($this->qb->expr()->$condition(
                $alias . '.' . $field,
                ':' . $fieldName1,
                ':' . $fieldName2
            ));
            $this->qb->setParameter($fieldName1, $value[0]);
            $this->qb->setParameter($fieldName2, $value[1]);
            return;
        }

        $this->qb->$whereCondition($this->qb->expr()->$condition($alias . '.' . $field, ':' . $fieldName1));
        if (!in_array($condition, ['isNull', 'isNotNull'])) {
            $this->qb->setParameter($fieldName1, $value);
        }
    }

    /**
     * @throws JadException
     */
    private function addSingleFilter(): void
    {
        $property = array_keys($this->filter[$this->path])[0];
        $condition = array_keys($this->filter[$this->path][$property])[0];
        $value = array_values($this->filter[$this->path][$property])[0];

        $joins = $this->getJoins($this->path);
        $alias = current($joins);

        if ($this->isRelational($this->path)) {
            $alias = $this->getLastAlias();
        }

        if (empty($alias)) {
            $alias = $this->getRootAlias();
        }

        $this->addFilter($property, $condition, $value, 'and', $alias);
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
     * @codeCoverageIgnore
     * @return QueryBuilder
     */
    public function getQb(): QueryBuilder
    {
        return $this->qb;
    }

    /**
     * @codeCoverageIgnore
     * @param QueryBuilder $qb
     */
    public function setQb(QueryBuilder $qb): void
    {
        $this->qb = $qb;
    }

    /**
     * @return string
     */
    public function getAliases(): string
    {
        $path = is_string($this->path) ? $this->path : '';
        return implode(',', array_keys($this->createAliases($path)));
    }

    public function hasFilter()
    {
        return is_array($this->filter) && !empty($this->filter);
    }

    public function isSingle()
    {
        return $this->getFilterType($this->filter) === self::TYPE_SINGLE;
    }

    public function isConditional()
    {
        return $this->getFilterType($this->filter) === self::TYPE_CONDITIONAL;
    }

    public function addRelational(): void
    {
        if ($this->isRelational($this->path)) {
            $this->addJoin($this->path);
        }
    }
}
