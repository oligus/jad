<?php

namespace Jad\Serializers;

use Jad\Exceptions\SerializerException;

/**
 * Class EntitySerializer
 * @package Jad\Serializers
 */
class IncludedSerializer extends AbstractSerializer implements Serializer
{
    /**
     * @param $entity
     * @return array
     */
    public function getRelationships($entity)
    {
        return array();
    }

    /**
     * @param $entity
     * @param $type
     * @return array|null
     * @throws SerializerException
     */
    public function getIncluded($type, $entity)
    {
        return array();
    }

    public function getLinks($entity)
    {
        // TODO: Implement getLinks() method.
    }

    public function getMeta($model)
    {
        // TODO: Implement getMeta() method.
    }
}