<?php

namespace Jad\Serializers;

use Tobscure\JsonApi\AbstractSerializer;

class RelationshipSerializer extends AbstractSerializer
{
    public function getId($entity)
    {
        return $entity->getId();
    }

    public function getType($entity)
    {

        return "moo";
    }
}