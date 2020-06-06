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
    /**
     * @var
     */
    private $entity;

    /**
     * @var Serializer $serializer
     */
    private $serializer;

    /**
     * @var null
     */
    private $fields = null;

    /**
     * @var null
     */
    private $includedParams = null;

    /**
     * Resource constructor.
     * @param $entity
     * @param Serializer $serializer
     */
    public function __construct($entity, Serializer $serializer)
    {
        $this->entity = $entity;
        $this->serializer = $serializer;
    }

    /**
     * @codeCoverageIgnore
     * @param $fields
     */
    public function setFields($fields): void
    {
        $this->fields = $fields;
    }

    /**
     * @return bool
     */
    public function hasIncluded(): bool
    {
        return !empty($this->includedParams);
    }

    /**
     * @codeCoverageIgnore
     * @param mixed $includedParams
     */
    public function setIncludedParams($includedParams)
    {
        $this->includedParams = $includedParams;
    }

    /**
     * @return stdClass
     * @throws JadException
     * @throws ReflectionException
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
     * @return array
     * @throws Exception
     */
    public function getIncluded(): array
    {
        $included = [];

        foreach ($this->includedParams as $includes) {
            foreach ($includes as $includedType => $relation) {
                if (empty($relation)) {
                    $include = $this->serializer->getIncluded($includedType, $this->entity, $this->fields);

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
     * @param $relations
     * @return array
     * @throws JadException
     * @throws ReflectionException
     */
    public function crawlRelations($entity, array $relations): array
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
