<?php declare(strict_types=1);

namespace Jad\Request;

use Jad\Common\Text;
use Jad\Exceptions\ParameterException;

/**
 * Class Parameters
 * @package Jad\Request
 */
class Parameters
{
    /**
     * @var array
     */
    private $arguments;

    /**
     * @var array
     */
    private $includes = [];

    /**
     * @param array $arguments
     */
    public function setArguments(array $arguments): void
    {
        $this->arguments = $arguments;

        $include = $this->getArgument('include');

        if(!is_null($include)) {
            $includes = explode(',', $include);

            $includes = array_map(function($item) {
                return trim($item);
            }, $includes);


            $includes = array_unique($includes);

            $this->includes = $includes;
        }
    }

    /**
     * @return array
     */
    public function getIncludes(): array
    {
       return $this->includes;
    }

    /**
     * @return bool
     */
    public function hasIncludes(): bool
    {
        return !empty($this->includes);
    }

    /**
     * @param string $key
     * @param string|null $default
     * @return mixed|string
     */
    protected function getArgument(string $key, string $default = null)
    {
        return isset($this->arguments[$key]) ? $this->arguments[$key] : $default;
    }

    /**
     * @param array $available
     * @return array
     * @throws ParameterException
     */
    public function getInclude(array $available = []): array
    {
        $validIncludes = [];

        if ($this->hasIncludes()) {
            $keys = [];

            foreach ($this->getIncludes() as $include) {
                $parts = explode('.', $include);
                $key = array_shift($parts);
                $keys[] = Text::deKebabify($key);
                $validIncludes[] = [$key => implode('.', $parts)];
            }

            $invalid = array_diff(array_unique($keys), $available);

            if (count($invalid)) {
                $resourceTypes = array_map(function ($resourceType) {
                    return Text::kebabify($resourceType);
                }, $available);

                $invalidTypes = array_map(function ($resourceType) {
                    return Text::kebabify($resourceType);
                }, $invalid);

                $resourceTypes = implode(', ', $resourceTypes);
                $invalidTypes = implode(', ', $invalidTypes);

                $message = 'Resource to include not found [' . $invalidTypes . ']';
                $message .= ', available resources [' . $resourceTypes . ']';

                throw new ParameterException($message, 404);
            }
        }

        return $validIncludes;
    }

    /**
     * @param null $perPage
     * @return int
     * @throws \Exception
     */
    public function getOffset(int $perPage = null): int
    {
        if ($perPage && ($offset = $this->getOffsetFromNumber($perPage))) {
            return $offset;
        }

        $offset = (int)$this->getPage('offset');

        if ($offset < 0) {
            throw new ParameterException('page[offset] must be >=0', 2);
        }

        return $offset;
    }

    /**
     * @param $perPage
     * @return int
     */
    protected function getOffsetFromNumber(int $perPage): int
    {
        $page = (int)$this->getPage('number');

        if ($page <= 1) {
            return 0;
        }

        return ($page - 1) * $perPage;
    }

    /**
     * Get the page.
     *
     * @param string $key
     *
     * @return string
     */
    protected function getPage($key)
    {
        $page = $this->getArgument('page');
        return isset($page[$key]) ? $page[$key] : '';
    }

    /**
     * @param int|null $max
     * @param int $default
     * @return int
     */
    public function getLimit(int $max = null, int $default = 25): int
    {
        $limit = $this->getPage('limit') ?: null;
        $size = $this->getPage('size') ?: $default;

        $limit = max($limit, $size);

        if (is_null($limit)) {
            $limit = $max;
        }

        if ($limit && $max) {
            $limit = min($max, $limit);
        }

        return $limit;
    }

    /**
     * @param array $available
     * @return array
     * @throws ParameterException
     */
    public function getSort(array $available = []): array
    {
        $sort = [];

        if ($input = $this->getArgument('sort')) {
            $fields = explode(',', $input);

            foreach ($fields as $field) {
                $field = trim($field);
                if (substr($field, 0, 1) === '-') {
                    $field = substr($field, 1);
                    $order = 'desc';
                } else {
                    $order = 'asc';
                }

                $field = Text::deKebabify($field);
                $sort[$field] = $order;
            }

            $invalid = array_diff(array_keys($sort), $available);

            if (count($invalid)) {
                throw new ParameterException('Invalid sort fields [' . implode(',', $invalid) . ']', 3);
            }
        }

        return $sort;
    }

    /**
     * Get the fields requested for inclusion.
     *
     * @return array
     */
    public function getFields(): array
    {
        $fields = $this->getArgument('fields');

        if (!is_array($fields)) {
            return [];
        }

        return array_map(function ($fields) {
            $fieldsArray = explode(',', $fields);

            return array_map(function ($field) {
                return trim($field);
            }, $fieldsArray);
        }, $fields);
    }

    /**
     * @return array
     */
    public function getFilter(): array
    {
        $filter = $this->getArgument('filter');

        return is_array($filter) ? $filter : [];
    }

    /**
     * @param int $default
     * @return int
     */
    public function getSize($default = 25)
    {
        return $this->getPage('size') ?: $default;

    }

}