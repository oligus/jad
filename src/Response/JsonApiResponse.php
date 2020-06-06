<?php declare(strict_types=1);

namespace Jad\Response;

use Doctrine\Common\Annotations\AnnotationException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\PersistentCollection;
use Exception;
use InvalidArgumentException;
use Jad\Common\ClassHelper;
use Jad\Configure;
use Jad\CRUD\Create;
use Jad\CRUD\Delete;
use Jad\CRUD\Read;
use Jad\CRUD\Update;
use Jad\Document\Collection;
use Jad\Document\Element;
use Jad\Document\JsonDocument as Document;
use Jad\Document\Links;
use Jad\Document\Meta;
use Jad\Document\Resource;
use Jad\Exceptions\JadException;
use Jad\Exceptions\ParameterException;
use Jad\Exceptions\RequestException;
use Jad\Exceptions\ResourceNotFoundException;
use Jad\Map\Mapper;
use Jad\Request\JsonApiRequest as Request;
use Jad\Serializers\RelationshipSerializer;
use ReflectionException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class JsonApiResponse
 * @package Jad\Response
 */
class JsonApiResponse
{
    /**
     * @var Request $request
     */
    private $request;

    /**
     * @var Mapper $mapper
     */
    private $mapper;

    /**
     * JsonApiResponse constructor.
     * @param Request $request
     * @param Mapper $mapper
     */
    public function __construct(Request $request, Mapper $mapper)
    {
        $this->request = $request;
        $this->mapper = $mapper;
    }

    /**
     * @return null|string
     * @throws JadException
     * @throws ResourceNotFoundException
     * @throws AnnotationException
     * @throws NonUniqueResultException
     * @throws ORMException
     * @throws ParameterException
     * @throws RequestException
     * @throws JadException
     * @throws ReflectionException
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public function render(): ?string
    {
        if (!$this->mapper->hasMapItem($this->request->getResourceType())) {
            if (Configure::getInstance()->getConfig('strict')) {
                throw new ResourceNotFoundException('Resource type not found [' . $this->request->getResourceType() . ']');
            }
        }

        $method = $this->request->getMethod();

        switch ($method) {
            case 'PATCH':
                (new Update($this->request, $this->mapper))->updateResource();
                $resource = (new Read($this->request, $this->mapper))->getResourceById((string)$this->request->getId());
                $this->createDocument($resource);
                break;

            case 'POST':
                $id = (new Create($this->request, $this->mapper))->createResource();
                $resource = (new Read($this->request, $this->mapper))->getResourceById($id);
                $this->createDocument($resource);
                break;

            case 'DELETE':
                (new Delete($this->request, $this->mapper))->deleteResource();
                $this->setResponse('', [], 204);
                break;

            case 'GET':
                $this->fetchResources();
                break;

            default:
                if (Configure::getInstance()->getConfig('strict')) {
                    throw new JadException('Http method [' . $method . '] is not supported.');
                }
        }

        return null;
    }

    /**
     * @param Element $resource
     * @throws InvalidArgumentException
     */
    public function createDocument(Element $resource): void
    {
        $document = new Document($resource);

        $links = new Links();
        $links->setSelf($this->request->getCurrentUrl());
        $document->addLinks($links);

        $meta = new Meta();
        $document->addMeta($meta);

        if (Configure::getInstance()->getConfig('debug')) {
            $this->setResponse(json_encode($document, JSON_PRETTY_PRINT));
            return;
        }

        $this->setResponse(json_encode($document));
    }

    /**
     * @param $content
     * @param array<string> $headers
     * @param int $status
     * @throws InvalidArgumentException
     */
    private function setResponse(string $content, array $headers = [], int $status = 200)
    {
        $response = new Response();
        $headers['Content-Type'] = 'application/vnd.api+json';

        if (Configure::getInstance()->getConfig('cors')) {
            $headers['Access-Control-Allow-Origin'] = '*';
        }

        foreach ($headers as $key => $value) {
            $response->headers->set((string)$key, $value);
        }

        $response->setContent($content);
        $response->setStatusCode($status);
        $response->sendHeaders();
        $response->sendContent();
    }

    /**
     * @throws JadException
     * @throws ResourceNotFoundException
     * @throws NonUniqueResultException
     * @throws ParameterException
     * @throws RequestException
     * @throws JadException
     * @throws ReflectionException
     * @throws Exception
     */
    public function fetchResources(): void
    {
        $relationship = $this->request->getRelationship();

        if (!empty($relationship)) {
            $this->createDocument($this->getRelationship($relationship));
            return;
        }

        $resource = $this->request->hasId()
            ? (new Read($this->request, $this->mapper))->getResourceById((string)$this->request->getId())
            : (new Read($this->request, $this->mapper))->getResources();

        $this->createDocument($resource);
    }

    /**
     * @param array<string> $relationship
     * @return Element|null
     * @throws JadException
     * @throws ResourceNotFoundException
     * @throws ReflectionException
     */
    public function getRelationship(array $relationship): ?Element
    {
        $id = $this->request->getId();
        $type = $this->request->getResourceType();

        $mapItem = $this->mapper->getMapItem($type);
        $entity = $this->mapper->getEm()->getRepository($mapItem->getEntityClass())->find($id);

        if (empty($entity)) {
            throw new ResourceNotFoundException('Resource type not found [' . $type . '] with id [' . $id . ']');
        }

        $relatedType = $relationship['type'];

        $result = ClassHelper::getPropertyValue($entity, $relatedType);

        if (is_null($result)) {
            throw new ResourceNotFoundException('Related resource type not found [' . $relatedType . '] derived from [' . $type . ']');
        }

        $serializer = new RelationshipSerializer($this->mapper, $this->request->getResourceType(), $this->request);
        $serializer->setRelationship($relationship);

        if ($result instanceof PersistentCollection) {
            $collection = new Collection();
            foreach ($result as $entity) {
                $resource = new Resource($entity, $serializer);
                $resource->setFields($this->request->getParameters()->getFields());
                $collection->add($resource);
            }

            return $collection;
        }

        $resource = new Resource($result, $serializer);
        $resource->setFields($this->request->getParameters()->getFields());

        return $resource;
    }
}
