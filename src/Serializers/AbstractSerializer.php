<?php

namespace Jad\Serializers;

use Jad\Map\Mapper;
use Jad\Request\JsonApiRequest;
use Jad\Common\Text;
use Jad\Common\ClassHelper;
use Jad\Map\MapItem;
use Jad\Exceptions\SerializerException;
use Doctrine\Common\Annotations\AnnotationReader;

/**
 * Class AbstractSerializer
 * @package Jad\Serializers
 */
abstract class AbstractSerializer implements Serializer
{
    const DATE_FORMAT = 'Y-m-d';
    const TIME_FORMAT = 'H:i:s';
    const DATE_TIME_FORMAT =  'Y-m-d H:i:s';

    /**
     * @var Mapper $mapper
     */
    protected $mapper;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var JsonApiRequest $request
     */
    protected $request;

    /**
     * EntitySerializer constructor.
     * @param Mapper $mapper
     * @param $type
     * @param JsonApiRequest $request
     */
    public function __construct(Mapper $mapper, $type, JsonApiRequest $request)
    {
        $this->mapper = $mapper;
        $this->type = $type;
        $this->request = $request;
    }

    /**
     * @param $entity
     * @return string
     * @throws \Exception
     * @throws \Jad\Exceptions\JadException
     */
    public function getId($entity): string
    {
        return (string) ClassHelper::getPropertyValue($entity, $this->getMapItem()->getIdField());
    }

    /**
     * @param $model
     * @return mixed|string
     * @throws \Exception
     */
    public function getType($model)
    {
        return $this->getMapItem()->getType();
    }

    /**
     * @param $entity
     * @param array|null $fields
     * @return array|mixed
     * @throws \Exception
     * @throws \Jad\Exceptions\JadException
     */
    public function getAttributes($entity, array $fields = null)
    {
        $reader         = new AnnotationReader();
        $attributes     = [];

        if(is_array($fields)) {
            $fields = array_map(function($field) {
                return Text::deKebabify($field);
            }, $fields);
        }

        $metaFields = $this->getMapItem()->getClassMeta()->getFieldNames();
        $reflection = new \ReflectionClass($this->getMapItem()->getEntityClass());
        $classFields = array_keys($reflection->getDefaultProperties());

        $mergedFields = array_unique(array_merge($metaFields, $classFields));

        foreach($mergedFields as $field) {

            // Do not display association
            if($this->getMapItem()->getClassMeta()->hasAssociation($field)) {
                continue;
            }

            // Do not display id field
            if($field === $this->getMapItem()->getIdField()) {
                continue;
            }

            // If filtered fields, only show selected fields
            if(!empty($fields) && !in_array($field, $fields)) {
                continue;
            }

            $i = 0;
            $bool = false;
            $aProperty = json_decode(json_encode($reader->getPropertyAnnotations($reflection->getProperty($field))), true);
            foreach($aProperty as $key => $aVal) {
                foreach($aVal as $k => $v) {
                    if($k === 'visible') {
                        $i = $key;
                        $bool = true;
                    }
                }
            }
            if($bool) {
                $jadAnnotation = $reader->getPropertyAnnotations($reflection->getProperty($field))[$i];
                if(!is_null($jadAnnotation)) {
                    if(property_exists($jadAnnotation, 'visible')) {
                        $visible = is_null($jadAnnotation->visible) ? true : (bool) $jadAnnotation->visible;
                        if(!$visible) {
                            continue;
                        }
                    }
                }
            }

            $fieldValue = ClassHelper::getPropertyValue($entity, $field);
            $value = $fieldValue;

            $annotation = $reader->getPropertyAnnotation(
                $reflection->getProperty($field),
                'Doctrine\ORM\Mapping\Column'
            );

            if($fieldValue instanceof \DateTime) {
                $value = $this->getDateTime($fieldValue, $annotation->type);
            }

            $attributes[Text::kebabify($field)] = $value;
        }

        return $attributes;
    }

    /**
     * @param \DateTime $value
     * @param string $dateType
     * @return string
     */
    protected function getDateTime(\DateTime $value, $dateType = 'datetime'): string
    {
        switch($dateType)
        {
            case 'date':
                return $value->format(self::DATE_FORMAT);

            case 'time':
                return $value->format(self::TIME_FORMAT);

            default:
                return $value->format(self::DATE_TIME_FORMAT);
        }
    }


    /**
     * @return MapItem
     * @throws \Exception
     */
    public function getMapItem(): MapItem
    {
        $mapItem = $this->mapper->getMapItem($this->type);
        if(!$mapItem instanceof MapItem) {
            throw new SerializerException('Could not find map item for type: ' . $this->type);
        }
        return $mapItem;
    }

    /**
     * @param string $type
     * @param $collection
     * @param array $fields
     * @return array
     */
    public function getIncludedResources(string $type, $collection, array $fields = []): array
    {
        return [];
    }
}
