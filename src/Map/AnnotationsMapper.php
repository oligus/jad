<?php

namespace Jad\Map;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;

class AnnotationsMapper extends AbstractMapper
{
    /**
     * AnnotationsMapper constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em);

        AnnotationRegistry::registerLoader('class_exists');

        $reader = new AnnotationReader();
        $metaData = $em->getMetadataFactory()->getAllMetadata();

        /** @var \Doctrine\ORM\Mapping\ClassMetadata $meta */
        foreach($metaData as $meta) {
            $head = $reader->getClassAnnotation($meta->getReflectionClass(), Annotations\Header::class);

            if(!empty($head) && !empty($head->type)) {
                $className = $meta->getName();
                $paginate = !!$head->paginate;
                $this->add($head->type, $className, $paginate);
            }
        }
    }
}