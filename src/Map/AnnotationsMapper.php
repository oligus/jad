<?php

namespace Jad\Map;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Annotations\AnnotationReader;

class AnnotationsMapper extends AbstractMapper
{
    /**
     * AnnotationsMapper constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em);

        $reader = new AnnotationReader();
        $metaData = $em->getMetadataFactory()->getAllMetadata();

        /** @var \Doctrine\ORM\Mapping\ClassMetadata $meta */
        foreach($metaData as $meta) {
            $annotation = $reader->getClassAnnotation($meta->getReflectionClass(), Annotations::class);

            if(!empty($annotation) && !empty($annotation->type)) {
                $className = $meta->getName();
                $this->add($annotation->type, $className);
            }
        }
    }
}