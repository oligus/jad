<?php

namespace Jad;

use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManager;
use Tobscure\JsonApi\Parameters;

class Jad
{
    /**
     * @var string
     */
    private $pathPrefix;

    /**
     * @var Request $request;
     */
    private $request;

    /**
     * @var EntityManager $em
     */
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->setEntityManager($em);
        $this->setRequest(Request::createFromGlobals());
    }

    /**
     * @param $pathPrefix
     */
    public function setPathPrefix($pathPrefix)
    {
        $this->pathPrefix = $pathPrefix;
    }

    /**
     * @param Request $request
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @param EntityManager $em
     */
    public function setEntityManager(EntityManager $em)
    {
        $this->em = $em;
    }

}