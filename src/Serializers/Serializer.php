<?php

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
    public function __construct(Mapper $mapper, $type, JsonApiRequest $request);

    /**
     * @param $entity
     * @return string
     */
    public function getId($entity);

    /**
     * @param $entity
     * @return mixed
     */
    public function getType($entity);


    /**
     * @param $entity
     * @param array|null $fields
     * @return mixed
     */
    public function getAttributes($entity, array $fields = null);

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
    public function getIncluded($type, $entity, $fields);

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