<?php declare(strict_types=1);

namespace Jad\Serializers;

use Exception;
use Jad\Document\Resource;
use Jad\Common\Text;
use Jad\Common\ClassHelper;
use Jad\Exceptions\JadException;
use Jad\Exceptions\SerializerException;
use Doctrine\ORM\PersistentCollection;
use ReflectionException;

/**
 * Class EntitySerializer
 * @package Jad\Serializers
 */
class EntitySerializer extends AbstractSerializer
{
    /**
     * @var array
     */
    private $includeMeta = [];

    /**
     * @param $entity
     * @return array
     * @throws JadException
     * @throws ReflectionException
     * @throws Exception
     */
    public function getRelationships($entity): array
    {
        $relationships = [];

        $associations = $this->getMapItem()->getClassMeta()->getAssociationMappings();

        foreach ($associations as $association) {
            $assocName = Text::kebabify($association['fieldName']);

            if ($this->request->hasId()) {
                $relationships[$assocName] = [
                    'links' => [
                        'self' => $this->request->getCurrentUrl() . '/relationship/' . $assocName,
                        'related' => $this->request->getCurrentUrl() . '/' . $assocName
                    ]
                ];
            } else {
                $id = method_exists($entity, 'get' .  ucfirst($this->getMapItem()->getIdField()))
                    ? $entity->getId()
                    : ClassHelper::getPropertyValue($entity, $this->getMapItem()->getIdField());

                $relationships[$assocName] = array(
                    'links' => [
                        'self' => $this->request->getCurrentUrl() . '/' . $id . '/relationship/' . $assocName,
                        'related' => $this->request->getCurrentUrl() . '/' . $id . '/' . $assocName
                    ]
                );
            }

            if (array_key_exists($assocName, $this->includeMeta)) {
                $relationships[$assocName]['data'] = array();
                foreach ($this->includeMeta[$assocName] as $id) {
                    $relationships[$assocName]['data'][] = array(
                        'type' => $assocName,
                        'id' => (string) $id
                    );
                }
            }
        }

        return $relationships;
    }

    /**
     * @param $type
     * @param $entity
     * @param $fields
     * @return array|mixed|null
     * @throws SerializerException
     * @throws Exception
     * @throws JadException
     */
    public function getIncluded(string $type, $entity, array $fields): ?array
    {
        if (!$this->mapper->hasMapItem($type)) {
            return null;
        }

        if (!$this->getMapItem()->getClassMeta()->hasAssociation(Text::deKebabify($type))) {
            throw new SerializerException('Cannot find relationship resource [' . $type . '] for inclusion.');
        }

        $result = ClassHelper::getPropertyValue($entity, Text::deKebabify($type));

        if ($result instanceof PersistentCollection) {
            return $this->getIncludedResources($type, $result, $fields);
        }

        return $this->getIncludedResources($type, [$result], $fields);
    }

    /**
     * @param string $type
     * @param $entityCollection
     * @param array $fields
     * @return array
     * @throws JadException
     * @throws ReflectionException
     */
    public function getIncludedResources(string $type, $entityCollection, array $fields = []): array
    {
        $resources = [];
        $this->includeMeta[$type] = [];

        foreach ($entityCollection as $associatedEntity) {
            if (empty($associatedEntity)) {
                continue;
            }

            $resource = new Resource($associatedEntity, new IncludedSerializer($this->mapper, $type, $this->request));
            $resource->setFields($fields);
            $resources[] = $resource;
            $this->includeMeta[$type][] = ClassHelper::getPropertyValue($associatedEntity, 'id');
        }

        return $resources;
    }
}
