<?php declare(strict_types=1);

namespace Jad\Serializers;

use Jad\Map\Annotations\Attribute;
use Jad\Map\Mapper;
use Jad\Request\JsonApiRequest;
use Jad\Common\Text;
use Jad\Common\ClassHelper;
use Jad\Map\MapItem;
use Jad\Exceptions\SerializerException;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\Mapping\Column;
use ReflectionClass;
use DateTime;

/**
 * Class AbstractSerializer
 * @package Jad\Serializers
 */
abstract class AbstractSerializer implements Serializer
{
    const DATE_FORMAT = 'Y-m-d';
    const TIME_FORMAT = 'H:i:s';
    const DATE_TIME_FORMAT = 'Y-m-d H:i:s';

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
     * @var \ReflectionClass
     */
    private $reflection;

    /**
     * @var AnnotationReader
     */
    private $annotationReader;

    /**
     * EntitySerializer constructor.
     * @param Mapper $mapper
     * @param $type
     * @param JsonApiRequest $request
     */
    public function __construct(Mapper $mapper, string $type, JsonApiRequest $request)
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
        return (string)ClassHelper::getPropertyValue($entity, $this->getMapItem()->getIdField());
    }

    /**
     * @return MapItem
     * @throws \Exception
     */
    public function getMapItem(): MapItem
    {
        $mapItem = $this->mapper->getMapItem($this->type);
        if (!$mapItem instanceof MapItem) {
            throw new SerializerException('Could not find map item for type: ' . $this->type);
        }
        return $mapItem;
    }

    /**
     * @param $entity
     * @return mixed|string
     * @throws \Exception
     */
    public function getType($entity): string
    {
        return $this->getMapItem()->getType();
    }

    /**
     * @param $entity
     * @param array $selectedFields
     * @return array
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @throws \Jad\Exceptions\JadException
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function getAttributes($entity, array $selectedFields = []): array
    {
        $attributes = [];

        foreach ($this->getFields() as $field) {
            if ($this->hasAssociation($field)) {
                continue;
            }

            if ($this->isIdField($field)) {
                continue;
            }

            if (!$this->isSelectedField($field, $selectedFields)) {
                continue;
            }

            if (!$this->isVisible($field)) {
                continue;
            }

            $fieldValue = ClassHelper::getPropertyValue($entity, $field);
            $value = $fieldValue;

            if ($fieldValue instanceof DateTime) {
                $value = $this->getDateTime($fieldValue, $this->getColumnType($field));
            }

            $attributes[Text::kebabify($field)] = $value;
        }

        return $attributes;
    }

    /**
     * @param \DateTime $dateTime
     * @param string $dateType
     * @return string
     */
    protected function getDateTime(DateTime $dateTime, $dateType = 'datetime'): string
    {
        switch ($dateType) {
            case 'date':
                return $dateTime->format(self::DATE_FORMAT);

            case 'time':
                return $dateTime->format(self::TIME_FORMAT);

            default:
                return $dateTime->format(self::DATE_TIME_FORMAT);
        }
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

    /**
     * @param string $field
     * @param array $selectedFields
     * @return bool
     */
    private function isSelectedField(string $field, array $selectedFields): bool
    {
        $selectedFields = array_map(function ($field) {
            return Text::deKebabify($field);
        }, $selectedFields);

        if (empty($selectedFields)) {
            return true;
        }

        return in_array($field, $selectedFields);
    }

    /**
     * @return array
     * @throws \ReflectionException
     * @throws \Exception
     */
    private function getFields(): array
    {
        $metaFields = $this->getMapItem()->getClassMeta()->getFieldNames();
        $reflection = $this->getReflection();
        $classFields = array_keys($reflection->getDefaultProperties());
        $mergedFields = array_unique(array_merge($metaFields, $classFields));

        return $mergedFields;
    }

    /**
     * @return ReflectionClass
     * @throws \ReflectionException
     * @throws \Exception
     */
    private function getReflection(): ReflectionClass
    {
        if (!$this->reflection instanceof ReflectionClass) {
            $this->reflection = new ReflectionClass($this->getMapItem()->getEntityClass());
        }

        return $this->reflection;
    }

    /**
     * @return AnnotationReader
     * @throws \Doctrine\Common\Annotations\AnnotationException
     */
    private function getAnnotationReader(): AnnotationReader
    {
        if (!$this->annotationReader instanceof AnnotationReader) {
            $this->annotationReader = new AnnotationReader();
        }

        return $this->annotationReader;
    }

    /**
     * @param string $field
     * @return mixed
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @throws \ReflectionException
     */
    private function getColumnType(string $field)
    {
        $annotation = $this->getAnnotationReader()->getPropertyAnnotation(
            $this->getReflection()->getProperty($field),
            Column::class
        );

        return $annotation->type;
    }

    /**
     * @param $field
     * @return bool
     * @throws \Exception
     */
    private function hasAssociation($field): bool
    {
        return $this->getMapItem()->getClassMeta()->hasAssociation($field);
    }

    /**
     * @param string $field
     * @return bool
     * @throws \Jad\Exceptions\JadException
     * @throws \Exception
     */
    private function isIdField(string $field): bool
    {
        return $field === $this->getMapItem()->getIdField();
    }

    /**
     * @param string $field
     * @return bool
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @throws \ReflectionException
     */
    private function isVisible(string $field): bool
    {
        /** @var Attribute $attribute */
        $attribute = $this->getAnnotationReader()->getPropertyAnnotation(
            $this->getReflection()->getProperty($field),
            'Jad\Map\Annotations\Attribute'
        );

        if ($attribute instanceof Attribute && !$attribute->isVisible()) {
            return false;
        }

        return true;
    }
}
