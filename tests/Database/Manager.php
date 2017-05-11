<?php

namespace Jad\Database;

use Doctrine\ORM\EntityManager;

class Manager
{
    /**
     * @var Manager $instance
     */
    private static $instance;

    /**
     * @var EntityManager $em
     */
    private $em;

    public static function getInstance(): Manager
    {
        if(!self::$instance instanceof Manager) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @param EntityManager $em
     */
    public function setEm(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @return EntityManager
     */
    public function getEm(): EntityManager
    {
        return $this->em;
    }


}