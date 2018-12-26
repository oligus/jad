<?php declare(strict_types=1);

namespace Jad\Serializers;

use Jad\Common\Text;
use Jad\Map\MapItem;

/**
 * Class EntitySerializer
 * @package Jad\Serializers
 */
class RelationshipSerializer extends AbstractSerializer implements Serializer
{
    /**
     * @var array
     */
    private $relationship;

    /**
     * @codeCoverageIgnore
     * @return array
     */
    public function getRelationship(): array
    {
        return $this->relationship;
    }

    /**
     * @codeCoverageIgnore
     * @param array $relationship
     */
    public function setRelationship($relationship): void
    {
        $this->relationship = $relationship;
    }

    /**
     * @param $entity
     * @return string
     */
    public function getType($entity): string
    {
        return Text::kebabify($this->relationship['type']);
    }

    /**
     * @param $entity
     * @return array|mixed
     * @throws \Exception
     */
    public function getRelationships($entity): array
    {
        $relationships = [];

        $associations = $this->getMapItem()->getClassMeta()->getAssociationMappings();

        foreach ($associations as $association) {
            $assocName = $association['fieldName'];

            $relationships[$assocName] = array(
                'links' => array(
                    'self' => $this->request->getCurrentUrl() . '/relationship/' . $assocName,
                    'related' => $this->request->getCurrentUrl() . '/' . $assocName
                )
            );
        }

        return $relationships;
    }

    /**
     * @return MapItem
     * @throws \Doctrine\ORM\Mapping\MappingException
     * @throws \Exception
     */
    public function getMapItem(): MapItem
    {
        /** @var \Jad\Map\MapItem $mapItem */
        $mapItem = $this->mapper->getMapItem($this->type);
        $association = $mapItem->getClassMeta()->getAssociationMapping($this->relationship['type']);
        $relatedMapItem = $this->mapper->getMapItemByClass($association['targetEntity']);

        if (!$relatedMapItem instanceof MapItem) {
            throw new \Exception('Could not find map item for type: ' . $this->relationship['type']);
        }

        return $relatedMapItem;
    }

    /**
     * @codeCoverageIgnore
     * @param string $type
     * @param $entity
     * @param array $fields
     * @return array
     */
    public function getIncluded(string $type, $entity, array $fields): array
    {
        return [];
    }
}
