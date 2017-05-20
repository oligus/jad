<?php

namespace Jad\Map;

use Doctrine\ORM\EntityManagerInterface;

class AutoMapper extends AbstractMapper
{
    /**
     * AutoMapper constructor.
     * @param EntityManagerInterface $em
     * @param array $excluded
     */
    public function __construct(EntityManagerInterface $em, array $excluded = [])
    {
        parent::__construct($em);

        $metaData = $em->getMetadataFactory()->getAllMetadata();

        /** @var \Doctrine\ORM\Mapping\ClassMetadata $meta */
        foreach($metaData as $meta) {
            $className = $meta->getName();

            if(preg_match('/^.*\\\(.+?)$/', $className, $matches) && !empty($matches[1])) {
                $type = strtolower($matches[1]);

                if(!in_array($type, $excluded)) {
                    $this->add($type, $className);
                }
            }
        }
    }
}