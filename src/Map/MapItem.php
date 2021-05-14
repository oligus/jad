<?php declare(strict_types=1);

namespace Jad\Map;

use Doctrine\ORM\Mapping\ClassMetadata;
use Jad\Exceptions\JadException;
use Jad\Map\Annotations\Header;
use Doctrine\Common\Annotations\AnnotationReader;
use ReflectionClass;
use ReflectionException;

/**
 * Class MapItem
 * @package Jad\Map
 * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
 */
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
     * @param mixed $params
     */
    public function __construct(string $type, $params, bool $paginate = false)
    {
        $this->setType($type);
        $this->setPaginate($paginate);

        if (is_string($params)) {
            $this->setEntityClass($params);
        }

        if (is_array($params)) {
            if (array_key_exists('entityClass', $params)) {
                $this->setEntityClass($params['entityClass']);
            }

            if (array_key_exists('classMeta', $params)) {
                $this->setClassMeta($params['classMeta']);
            }
        }
    }

    /**
     * @codeCoverageIgnore
     */
    private function setType(string $type): MapItem
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @codeCoverageIgnore
     */
    private function setEntityClass(string $entityClass): MapItem
    {
        $this->entityClass = $entityClass;
        return $this;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @throws JadException
     */
    public function getIdField(): string
    {
        if (!$this->classMeta instanceof ClassMetadata) {
            throw new JadException('No class meta data found');
        }

        $identifier = $this->classMeta->getIdentifier();

        if (count($identifier) > 1) {
            throw new JadException('Composite identifier not supported');
        }

        if (count($identifier) < 1) {
            throw new JadException('No identifier found');
        }

        return $identifier[0];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getClassMeta(): ClassMetadata
    {
        return $this->classMeta;
    }

    /**
     * @codeCoverageIgnore
     */
    public function setClassMeta(ClassMetadata $classMeta): MapItem
    {
        $this->classMeta = $classMeta;
        return $this;
    }

    /**
     * @codeCoverageIgnore
     */
    public function isPaginate(): bool
    {
        return $this->paginate;
    }

    /**
     * @codeCoverageIgnore
     */
    public function setPaginate(bool $paginate): void
    {
        $this->paginate = $paginate;
    }

    /**
     * @throws ReflectionException
     */
    public function isReadOnly(): bool
    {
        $reader = new AnnotationReader();
        $reflection = new ReflectionClass($this->getEntityClass());

        $header = $reader->getClassAnnotation($reflection, Header::class);

        if ($header instanceof Header) {
            if (property_exists($header, 'readOnly')) {
                $readOnly = !is_null($header->readOnly) && $header->readOnly;

                if ($readOnly) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getEntityClass(): string
    {
        return $this->entityClass;
    }
}
