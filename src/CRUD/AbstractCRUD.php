<?php declare(strict_types=1);

namespace Jad\CRUD;

use DateTime;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Collections;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection as DoctrineCollection;
use Doctrine\ORM\Mapping\MappingException;
use Doctrine\ORM\ORMException;
use Exception;
use InvalidArgumentException;
use Jad\Common\ClassHelper;
use Jad\Common\Text;
use Jad\Exceptions\JadException;
use Jad\Exceptions\RequestException;
use Jad\Map\Annotations\Attribute;
use Jad\Map\MapItem;
use Jad\Map\Mapper;
use Jad\Request\JsonApiRequest;
use Jad\Response\ValidationErrors;
use ReflectionClass;
use ReflectionException;
use stdClass;
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
     * @return array<string>[]
     * @throws RequestException
     */
    public function getAttributes(): array
    {
        $input = $this->request->getInputJson();
        return isset($input->data->attributes) ? (array)$input->data->attributes : [];
    }

    /**
     * @return MapItem|null
     * @throws RequestException
     */
    public function getMapItem(): ?MapItem
    {
        $input = $this->request->getInputJson();

        $type = property_exists($input->data, 'type')
            ? $input->data->type
            : $this->request->getResourceType();

        return $this->mapper->getMapItem($type);
    }

    /**
     * @param $input
     * @param object $entity
     * @throws ORMException
     * @throws JadException
     * @throws ReflectionException
     */
    protected function addRelationships(stdClass $input, $entity): void
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

                    method_exists($entity, $method)
                        ?  $entity->$method($reference)
                        : $relatedProperty->add($reference);
                } else {
                    ClassHelper::setPropertyValue($entity, $relatedType, $reference);
                }
            }
        }
    }

    /**
     * @param MapItem $mapItem
     * @param array<string>[] $attributes
     * @param object $entity
     * @throws MappingException
     * @throws ReflectionException
     * @throws Exception
     */
    protected function addAttributes(MapItem $mapItem, array $attributes, $entity): void
    {
        $reader = new AnnotationReader();
        $reflection = new ReflectionClass($mapItem->getEntityClass());

        foreach ($attributes as $attribute => $value) {
            $attribute = Text::deKebabify((string)$attribute);

            if (!$mapItem->getClassMeta()->hasField($attribute)) {
                continue;
            }

            $jadAttribute = $reader->getPropertyAnnotation(
                $reflection->getProperty($attribute),
                Attribute::class
            );

            if ($jadAttribute instanceof Attribute && $jadAttribute->isReadOnly()) {
                continue;
            }

            $type = $mapItem->getClassMeta()->getFieldMapping($attribute)['type'];

            switch ($type) {
                case 'datetime':
                    $value = new DateTime((string)$value);
                    break;

                default:
            }

            // Update value
            ClassHelper::setPropertyValue($entity, $attribute, $value);
        }
    }

    /**
     * @param object $entity
     * @throws InvalidArgumentException
     */
    protected function validateEntity($entity): void
    {
        $validator = Validation::createValidatorBuilder()->enableAnnotationMapping()->getValidator();
        $errors = $validator->validate($entity);

        if (count($errors) > 0) {
            $error = new ValidationErrors($errors);
            $error->render();
            exit(1);
        }
    }
}
