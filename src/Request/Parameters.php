<?php

namespace Jad\Request;

use Jad\Common\Text;
use Jad\Exceptions\ParameterException;

/**
 * Class Parameters
 *
 * Based on https://github.com/tobscure/json-api Parameter
 *
 * @package Jad\Request
 */
class Parameters
{
    /**
     * @var array
     */
    protected $input;

    /**
     * @param array $input
     */
    public function __construct(array $input)
    {
        $this->input = $input;
    }

    /**
     * @param array $available
     * @return array
     * @throws ParameterException
     */
    /**
     * @param array $available
     * @return array
     * @throws ParameterException
     */
    public function getInclude(array $available = [])
    {
        $relationships = [];

        if ($include = $this->getInput('include')) {
            $includes = explode(',', $include);

            $keys = [];

            foreach ($includes as $include) {
                $tmpArray = [];
                $parts = explode('.', trim($include));
                $key = array_shift($parts);
                $keys[] = Text::deKebabify($key);
                $tmpArray[$key] = implode('.', $parts);
                $relationships[] = $tmpArray;
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

        return $relationships;
    }

    /**
     * @param null $perPage
     * @return int
     * @throws \Exception
     */
    public function getOffset($perPage = null)
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
    protected function getOffsetFromNumber($perPage)
    {
        $page = (int)$this->getPage('number');

        if ($page <= 1) {
            return 0;
        }

        return ($page - 1) * $perPage;
    }

    /**
     * @param null $max
     * @param int $default
     * @return mixed|null
     */
    public function getLimit($max = null, $default = 25)
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
    public function getSort(array $available = [])
    {
        $sort = [];

        if ($input = $this->getInput('sort')) {
            $fields = explode(',', $input);

            foreach ($fields as $field) {
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
    public function getFields()
    {
        $fields = $this->getInput('fields');

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
     * @return array|mixed
     */
    public function getFilter()
    {
        $input = $this->getInput('filter');

        return is_array($input) ? $input : [];
    }

    /**
     * Get an input item.
     *
     * @param string $key
     * @param null $default
     *
     * @return mixed
     */
    protected function getInput($key, $default = null)
    {
        return isset($this->input[$key]) ? $this->input[$key] : $default;
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
        $page = $this->getInput('page');

        return isset($page[$key]) ? $page[$key] : '';
    }

    public function getSize($default = 25)
    {
        return $this->getPage('size') ?: $default;

    }

}