<?php

namespace Jad;

use Jad\Map\EntityMap;
use Jad\RequestHandler;
use Doctrine\ORM\EntityManager;

class Jad
{
    /**
     * @var EntityManager $em
     */
    private $em;

    /**
     * @var EntityMap $entityMap
     */
    private $entityMap;

    /**
     * @var RequestHandler $requestHandler
     */
    private $requestHandler;

    /**
     * Jad constructor.
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->setEntityManager($em);
        $this->setRequestHandler(new RequestHandler());
    }

    /**
     * @param EntityManager $em
     */
    private function setEntityManager(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param EntityMap $entityMap
     */
    public function setEntityMap(EntityMap $entityMap)
    {
        $this->entityMap = $entityMap;
    }

    /**
     * @return \Jad\RequestHandler
     */
    public function getRequestHandler(): RequestHandler
    {
        return $this->requestHandler;
    }

    /**
     * @param RequestHandler $requestHandler
     */
    private function setRequestHandler(RequestHandler $requestHandler)
    {
        $this->requestHandler = $requestHandler;
    }

    /**
     * @param $pathPrefix
     */
    public function setPathPrefix($pathPrefix)
    {
        $this->getRequestHandler()->setPathPrefix($pathPrefix);
    }

    public function jsonApiResult()
    {
        /*
         * Pseudo
         *
         * Get type
         * Get id
         *
         * If request type is GET and id exists, fetch doctrine entity by find(id)
         * If request type is GET and id does not exists, fetch doctrine collection by findBy()
         *
         * serialize
         * json encode
         *
         */
        $entityKey = $this->getRequestHandler()->getType();

        //var_dump($entityKey);
    }

    private function process()
    {

    }

}