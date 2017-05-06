<?php

namespace Jad;

use App\Http\Requests\Request;
use Doctrine\ORM\EntityManager;
use Tobscure\JsonApi\Collection;
use Tobscure\JsonApi\Document;
use Tobscure\JsonApi\Resource;

class Load
{
    /** @var \Doctrine\ORM\EntityManager $em */
    private $em;

    /** @var Request $request */
    private $request;

    private $pathPrefix = 'api/v1';

    private $entityId;
    private $type;

    const ENTITY_MAP = [
        'accounts' => 'Markviss\Base\Users\Entities\UserCustomerDatabases'
    ];

    public function __construct(Request $request, EntityManager $em)
    {
        var_export($_GET); die;
        $this->em = $em;
        $this->request = $request;

        $this->getEntity($this->getEntityName());
    }

    public function getEntity($entityName)
    {
        $entity = $this->em->getRepository($entityName)->find($this->entityId);
        $metadata = $this->em->getClassMetadata($entityName);

        $e = new EntitySerializer('entity');
        $e->setClassMeta($metadata);

        $r = new Resource($entity, $e);
        $d = new Document($r);
        print_R(json_encode($d)); die;

    }

    public function getEntityName()
    {
        $path = preg_replace('!' . $this->pathPrefix . '/?!', '', $this->request->path());
        $items = explode('/', $path);

        $entityName = $items[0];

        $this->type = $items[0];

        if(count($items) > 1) {
            $this->entityId = $items[1];
        }

        // TODO relations

        if(array_key_exists($entityName, self::ENTITY_MAP)) {
            return self::ENTITY_MAP[$entityName];
        }

        return $entityName;
    }

}