<?php declare(strict_types=1);

namespace Jad\Serializers;

use Jad\Map\Mapper;
use Jad\Request\JsonApiRequest;

/**
 * Interface Serializer
 * @package Jad\Serializers
 */
interface Serializer
{
    /**
     * Serializer constructor.
     * @param Mapper $mapper
     * @param $type
     * @param JsonApiRequest $request
     */
    public function __construct(Mapper $mapper, string $type, JsonApiRequest $request);

    /**
     * @param $entity
     * @return string
     */
    public function getId($entity): string;

    /**
     * @param $entity
     * @return mixed
     */
    public function getType($entity): string;


    /**
     * @param $entity
     * @param array|null $fields
     * @return mixed
     */
    public function getAttributes($entity, ?array $fields): array;

    /**
     * @param $entity
     * @return mixed
     */
    public function getRelationships($entity);

    /**
     * @param $type
     * @param $entity
     * @param $fields
     * @return mixed
     */
    public function getIncluded(string $type, $entity, array $fields);

    /**
     * @param string $type
     * @param $collection
     * @param array $fields
     * @return array
     */
    public function getIncludedResources(string $type, $collection, array $fields = []): array;

    /**
     * @return mixed
     */
    public function getMapItem();
}
