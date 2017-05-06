<?php

namespace Jad\Map;

class EntityMapItem
{
    /**
     * @var string
     */
    private $type = 'undefined';

    /**
     * @var string
     */
    private $entityClass = '';

    /**
     * @var string
     */
    private $idField = 'id';

    /**
     * EntityMapItem constructor.
     * @param $type
     * @param $params
     */
    public function __construct($type, $params)
    {
        $this->setType($type);

        if(is_string($params)) {
            $this->setEntityClass($params);
        }

        if(is_array($params)) {
            if(array_key_exists('entityClass', $params)) {
                $this->setEntityClass($params['entityClass']);
            }

            if(array_key_exists('idField', $params)) {
                $this->setIdField($params['idField']);
            }
        }
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return EntityMapItem
     */
    private function setType(string $type): EntityMapItem
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return string
     */
    public function getEntityClass(): string
    {
        return $this->entityClass;
    }

    /**
     * @param string $entityClass
     * @return EntityMapItem
     */
    private function setEntityClass(string $entityClass): EntityMapItem
    {
        $this->entityClass = $entityClass;
        return $this;
    }

    /**
     * @return string
     */
    public function getIdField(): string
    {
        return $this->idField;
    }

    /**
     * @param string $idField
     * @return EntityMapItem
     */
    private function setIdField(string $idField): EntityMapItem
    {
        $this->idField = $idField;
        return $this;
    }
}