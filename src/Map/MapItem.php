<?php

namespace Jad\Map;

use Doctrine\ORM\Mapping\ClassMetadata;
use Jad\Exceptions\JadException;
use Jad\Map\Annotations\Header;
use Doctrine\Common\Annotations\AnnotationReader;

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
     * @var bool
     */
    private $paginate = false;

    /**
     * MapItem constructor.
     * @param $type
     * @param $params
     * @param bool $paginate
     */
    public function __construct($type, $params, $paginate = false)
    {
        $this->setType($type);
        $this->setPaginate($paginate);

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

    /**
     * @return bool
     */
    public function isPaginate(): bool
    {
        return $this->paginate;
    }

    /**
     * @param $paginate
     */
    public function setPaginate($paginate)
    {
        $this->paginate = $paginate;
    }

    /**
     * @return bool
     */
    public function isReadOnly(): bool
    {
        $reader     = new AnnotationReader();
        $reflection = new \ReflectionClass($this->getEntityClass());

        $header = $reader->getClassAnnotation($reflection, Header::class);

        if (!is_null($header)) {
            if (property_exists($header, 'readOnly')) {
                $readOnly = is_null($header->readOnly) ? false : (bool)$header->readOnly;

                if ($readOnly) {
                    return true;
                }
            }
        }
        return false;
    }
}