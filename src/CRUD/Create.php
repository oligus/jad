<?php

namespace Jad\CRUD;

use Jad\Common\ClassHelper;
use Jad\Common\Text;
use Doctrine\Common\Collections\Collection as DoctrineCollection;

/**
 * Class Create
 * @package Jad\CRUD
 */
class Create extends AbstractCRUD
{
    /**
     * @return mixed
     */
    public function createResource()
    {
        $input = $this->request->getInputJson();
        $type = $input->data->type;

        $attributes = isset($input->data->attributes) ? (array) $input->data->attributes : [];
        $mapItem = $this->mapper->getMapItem($type);
        $entityClass = $mapItem->getEntityClass();

        $entity = new $entityClass;

        foreach($attributes as $attr => $value) {
            $attribute = Text::deKebabify($attr);
            if($mapItem->getClassMeta()->hasField($attribute)) {
                ClassHelper::setPropertyValue($entity, $attribute, $value);
            }
        }

        $relationships = isset($input->data->relationships) ? (array) $input->data->relationships : [];

        foreach($relationships as $relatedType => $related) {
            $relatedData = $related->data;
            $related = is_array($relatedData) ? $relatedData : [$relatedData];
            $relatedProperty = ClassHelper::getPropertyValue($entity, $relatedType);

            foreach($related as $relationship) {
                $type = $relationship->type;
                $id = $relationship->id;

                $relationalMapItem = $this->mapper->getMapItem($type);
                $relationalClass = $relationalMapItem->getEntityClass();

                $reference = $this->mapper->getEm()->getReference($relationalClass, $id);

                if($relatedProperty instanceof DoctrineCollection) {

                    // First try entity add method, else add straight to collection
                    $method = 'add' . ucfirst($type);
                    if(method_exists($entity, $method)) {
                        $entity->$method($reference);
                    } else {
                        $relatedProperty->add($reference);
                    }
                } else {
                    ClassHelper::setPropertyValue($entity, $relatedProperty, $reference);
                }
            }
        }

        $this->mapper->getEm()->persist($entity);
        $this->mapper->getEm()->flush();

        /** @var \Jad\Map\MapItem $mapItem */
        $id = ClassHelper::getPropertyValue($entity, $mapItem->getIdField());

        return $id;
    }
}