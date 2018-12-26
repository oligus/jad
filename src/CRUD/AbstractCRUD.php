<?php declare(strict_types=1);

namespace Jad\CRUD;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections;
use Jad\Map\Mapper;
use Jad\Request\JsonApiRequest;
use Jad\Common\ClassHelper;
use Jad\Common\Text;
use Jad\Map\MapItem;
use Doctrine\Common\Collections\Collection as DoctrineCollection;
use Doctrine\Common\Annotations\AnnotationReader;
use Jad\Response\ValidationErrors;
use Symfony\Component\Validator\Validation;

/**
 * Class AbstractCRUD
 * @package Jad\CRUD
 */
class AbstractCRUD
{
    /**
     * @var JsonApiRequest
     */
    protected $request;

    /**
     * @var Mapper
     */
    protected $mapper;

    /**
     * AbstractCRUD constructor.
     * @param JsonApiRequest $request
     * @param Mapper $mapper
     */
    public function __construct(JsonApiRequest $request, Mapper $mapper)
    {
        $this->request = $request;
        $this->mapper = $mapper;
    }

    /**
     * @return array
     * @throws \Jad\Exceptions\RequestException
     */
    public function getAttributes(): array
    {
        $input = $this->request->getInputJson();
        return isset($input->data->attributes) ? (array)$input->data->attributes : [];
    }

    /**
     * @return MapItem
     * @throws \Jad\Exceptions\RequestException
     */
    public function getMapItem(): MapItem
    {
        $input = $this->request->getInputJson();

        if (!array_key_exists('type', $input->data)) {
            $type = $this->request->getResourceType();
        } else {
            $type = $input->data->type;
        }

        return $this->mapper->getMapItem($type);
    }

    /**
     * @param $input
     * @param object $entity
     * @throws \Doctrine\ORM\ORMException
     * @throws \Jad\Exceptions\JadException
     * @throws \ReflectionException
     */
    protected function addRelationships(\stdClass $input, $entity): void
    {
        $relationships = isset($input->data->relationships) ? (array)$input->data->relationships : [];

        foreach ($relationships as $relatedType => $related) {
            $relatedType = Text::deKebabify($relatedType);
            $related = is_array($related->data) ? $related->data : [$related->data];
            $relatedProperty = ClassHelper::getPropertyValue($entity, $relatedType);

            /**
             * Clear collection on PATCH
             */
            if ($this->request->getMethod() === 'PATCH') {
                $attribute = ClassHelper::getPropertyValue($entity, $relatedType);

                if ($attribute instanceof Collections\Collection) {
                    ClassHelper::setPropertyValue($entity, $relatedType, new ArrayCollection());
                }
            }

            foreach ($related as $relationship) {
                $relationalMapItem = $this->mapper->getMapItem($relationship->type);
                $relationalClass = $relationalMapItem->getEntityClass();

                $reference = $this->mapper->getEm()->getReference($relationalClass, $relationship->id);

                if ($relatedProperty instanceof DoctrineCollection) {
                    // First try entity add method, else add straight to collection
                    $method1 = 'add' . ucfirst($relationship->type);
                    $method2 = 'add' . ucfirst($relatedType);
                    $method = method_exists($entity, $method1) ? $method1 : $method2;

                    if (method_exists($entity, $method)) {
                        $entity->$method($reference);
                    } else {
                        $relatedProperty->add($reference);
                    }
                } else {
                    ClassHelper::setPropertyValue($entity, $relatedType, $reference);
                }
            }
        }
    }

    /**
     * @param MapItem $mapItem
     * @param array $attributes
     * @param object $entity
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @throws \Doctrine\ORM\Mapping\MappingException
     * @throws \ReflectionException
     */
    protected function addAttributes(MapItem $mapItem, array $attributes, $entity): void
    {
        $reader = new AnnotationReader();
        $reflection = new \ReflectionClass($mapItem->getEntityClass());

        foreach ($attributes as $attribute => $value) {
            $attribute = Text::deKebabify($attribute);

            if (!$mapItem->getClassMeta()->hasField($attribute)) {
                continue;
            }

            $jadAnnotation = $reader->getPropertyAnnotation(
                $reflection->getProperty($attribute),
                'Jad\Map\Annotations\Attribute'
            );

            if (!is_null($jadAnnotation)) {
                if (property_exists($jadAnnotation, 'readOnly')) {
                    $readOnly = is_null($jadAnnotation->readOnly) ? true : (bool)$jadAnnotation->readOnly;

                    if ($readOnly) {
                        continue;
                    }
                }
            }

            $type = $mapItem->getClassMeta()->getFieldMapping($attribute)['type'];

            switch ($type) {
                case 'datetime':
                    $value = new \DateTime($value);
                    break;

                default:
            }

            // Update value
            ClassHelper::setPropertyValue($entity, $attribute, $value);
        }
    }

    /**
     * @param object $entity
     */
    protected function validateEntity($entity): void
    {
        /**
         * Validate input
         */
        $validator = Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator();
        $errors = $validator->validate($entity);

        if (count($errors) > 0) {
            $error = new ValidationErrors($errors);
            $error->render();
            exit(1);
        }
    }
}
