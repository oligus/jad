<?php declare(strict_types=1);

namespace Jad\Document;

use Doctrine\Common\Collections\Collection;
use Exception;
use Jad\Common\ClassHelper;
use Jad\Common\Text;
use Jad\Exceptions\JadException;
use Jad\Exceptions\MappingException;
use Jad\Serializers\RelationshipSerializer;
use Jad\Serializers\Serializer;
use JsonSerializable;
use ReflectionException;
use stdClass;

/**
 * Class Resource
 * @package Klirr\JsonApi\Response
 */
class Resource implements JsonSerializable, Element
{
    private object $entity;
    private Serializer $serializer;
    private array $fields = [];
    private array $includedParams = [];

    /**
     * Resource constructor.
     * @param $entity
     * @param Serializer $serializer
     */
    public function __construct(object $entity, Serializer $serializer)
    {
        $this->entity = $entity;
        $this->serializer = $serializer;
    }

    /**
     * @codeCoverageIgnore
     * @param array<mixed> $fields
     */
    public function setFields(array $fields): void
    {
        $this->fields = $fields;
    }

    public function hasIncluded(): bool
    {
        return !empty($this->includedParams);
    }

    /**
     * @codeCoverageIgnore
     * @param array<mixed> $includedParams
     */
    public function setIncludedParams(array $includedParams): void
    {
        $this->includedParams = $includedParams;
    }

    /**
     * @throws JadException
     * @throws ReflectionException
     * @throws Exception
     */
    public function jsonSerialize(): stdClass
    {
        $resource = new stdClass();

        $entity = $this->entity;
        $type = $this->serializer->getType($entity);

        $fields = [];

        if (is_array($this->fields)) {
            $fields = array_key_exists($type, $this->fields) ? $this->fields[$type] : [];
        }

        $resource->id = $this->serializer->getId($entity);
        $resource->type = $type;

        if ($this->serializer instanceof RelationshipSerializer) {
            $relationship = $this->serializer->getRelationship();

            if ($relationship['view'] !== 'list') {
                $resource->attributes = $this->serializer->getAttributes($entity, $fields);
            }

            return $resource;
        }

        $resource->attributes = $this->serializer->getAttributes($entity, $fields);

        $relationships = $this->serializer->getRelationships($entity);

        if (!empty($relationships)) {
            $resource->relationships = $relationships;
        }

        return $resource;
    }

    /**
     * @return array<mixed>
     * @throws Exception
     */
    public function getIncluded(): array
    {
        $included = [];

        foreach ($this->includedParams ?? [] as $includes) {
            foreach ($includes as $includedType => $relation) {
                if (empty($relation)) {
                    $include = $this->serializer->getIncluded($includedType, $this->entity, $this->fields ?? []);

                    if (!is_array($include)) {
                        throw new MappingException('Included type [' . $includedType . '] not available, check if resource type is mapped correctly.');
                    }

                    $included = array_merge($included, $include);
                    continue;
                }

                $path = explode('.', $relation);
                array_unshift($path, $includedType);
                $result = $this->crawlRelations($this->entity, $path);
                $include = $this->serializer->getIncludedResources($result['type'], $result['collection']);
                $included = array_merge($included, $include);
            }
        }

        return $included;
    }

    /**
     * Crawl entities
     *
     * @param $entity
     * @param array<mixed> $relations
     * @return array<mixed>
     * @throws JadException
     */
    public function crawlRelations(object $entity, array $relations): array
    {
        $collection = [$entity];
        $type = end($relations);

        while ($relation = array_shift($relations)) {
            $newCollection = [];
            $property = Text::deKebabify($relation);

            foreach ($collection as $entity) {
                $result = ClassHelper::getPropertyValue($entity, $property);

                $newCollection = $result instanceof Collection
                    ? array_merge($newCollection, $result->toArray())
                    : array_merge($newCollection, [$result]);
            }

            $collection = $newCollection;
        }

        return ['type' => $type, 'collection' => $collection];
    }
}
