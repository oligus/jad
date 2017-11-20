<?php

namespace Jad\Serializers;

use Jad\Map\Mapper;
use Jad\Request\JsonApiRequest;

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
    public function getLinks($entity);

    /**
     * @param $entity
     * @return mixed
     */
    public function getMeta($entity);

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
     * @param $type
     * @param $collection
     * @return mixed
     */
    public function getIncludedResources($type, $collection);

    /**
     * @return mixed
     */
    public function getMapItem();

}