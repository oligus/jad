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
     * @var array<string<array>>
     */
    private $arguments;

    /**
     * @var array<int,string>
     */
    private $includes = [];

    /**
     * @param array<string<array>> $arguments
     */
    public function setArguments(array $arguments): void
    {
        $this->arguments = $arguments;

        $include = $this->getArgument('include');

        if (!is_null($include)) {
            $includes = explode(',', $include);

            $includes = array_map(function (string $item): string {
                return trim($item);
            }, $includes);


            $includes = array_unique($includes);

            $this->includes = $includes;
        }
    }

    /**
     * @return array<int,string>
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
        return $this->arguments[$key] ?? $default;
    }

    /**
     * @param array<string> $available
     * @return array<int,array<string,string>>|array{}
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
                $resourceTypes = array_map(function (string $resourceType): string {
                    return Text::kebabify($resourceType);
                }, $available);

                $invalidTypes = array_map(function (string $resourceType): string {
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
     * @param int|null $perPage
     * @return int
     * @throws ParameterException
     */
    public function getOffset(int $perPage = null): int
    {
        if (!is_null($perPage)) {
            $offset = $this->getOffsetFromNumber($perPage);
            if ($offset > 0) {
                return $offset;
            }
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
        return $page[$key] ?? '';
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

        return (int)$limit;
    }

    /**
     * @param array<string> $available
     * @return array<string>
     * @throws ParameterException
     */
    public function getSort(array $available = []): array
    {
        $sort = [];

        $input = $this->getArgument('sort');

        if (!is_null($input)) {
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
     * @return array<int,string>[]
     */
    public function getFields(): array
    {
        $fields = $this->getArgument('fields');

        if (!is_array($fields)) {
            return [];
        }

        /** @return array<int,string> */
        return array_map(function (string $fields): array {
            $fieldsArray = explode(',', $fields);

            return array_map(function (string $field): string {
                return trim($field);
            }, $fieldsArray);
        }, $fields);
    }

    /**
     * @return array<array>
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
    public function getSize($default = 25): int
    {
        return (int)$this->getPage('size') ?: $default;
    }
}
