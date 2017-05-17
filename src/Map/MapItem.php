<?php

namespace Jad\Map;

use Doctrine\ORM\Mapping\ClassMetadata;
use Jad\Exceptions\JadException;

class MapItem
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
     * @var ClassMetadata $classMeta
     */
    private $classMeta;

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

            if(array_key_exists('classMeta', $params)) {
                $this->setClassMeta($params['classMeta']);
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
     * @return MapItem
     */
    private function setType(string $type): MapItem
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
     * @return MapItem
     */
    private function setEntityClass(string $entityClass): MapItem
    {
        $this->entityClass = $entityClass;
        return $this;
    }

    /**
     * @return string
     * @throws JadException
     */
    public function getIdField(): string
    {
        if(!$this->classMeta instanceof ClassMetadata) {
            throw new JadException('No class meta data found');
        }

        $identifier = $this->classMeta->getIdentifier();

        if(count($identifier) > 1) {
            throw new JadException('Composite identifier not supported');
        }

        if(count($identifier) < 1) {
            throw new JadException('No identifier found');
        }

        return $identifier[0];
    }

    /**
     * @return ClassMetadata
     */
    public function getClassMeta(): ClassMetadata
    {
        return $this->classMeta;
    }

    /**
     * @param ClassMetadata $classMeta
     * @return $this
     */
    public function setClassMeta(ClassMetadata $classMeta)
    {
        $this->classMeta = $classMeta;
        return $this;
    }

}