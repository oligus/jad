<?php declare(strict_types=1);

namespace Jad\Serializers;

/**
 * Class EntitySerializer
 * @package Jad\Serializers
 */
class IncludedSerializer extends AbstractSerializer implements Serializer
{
    /**
     * @codeCoverageIgnore
     * @param $entity
     * @return array
     */
    public function getRelationships($entity): array
    {
        return [];
    }

    /**
     * @codeCoverageIgnore
     * @param $type
     * @param $entity
     * @param $fields
     * @return array|mixed
     */
    public function getIncluded(string $type, $entity, array $fields): array
    {
        return [];
    }
}