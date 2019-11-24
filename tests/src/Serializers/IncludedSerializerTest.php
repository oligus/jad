<?php

namespace Jad\Tests;

use Doctrine\Common\Annotations\AnnotationReader;
use Jad\Database\Entities\Invoices;
use Jad\Database\Manager;
use Jad\Serializers\IncludedSerializer;
use Jad\Map\AnnotationsMapper;

class IncludedSerializerTest extends TestCase
{
    public function testGetRelationships()
    {
        $mapper = $this->getMockBuilder('Jad\Map\AnnotationsMapper')
            ->disableOriginalConstructor()
            ->setMethods(['test'])
            ->getMock();

        $request = $this->getMockBuilder('Jad\Request\JsonApiRequest')
            ->disableOriginalConstructor()
            ->setMethods(['test'])
            ->getMock();

        $entity = $this->getMockBuilder('Jad\Database\Entities\Albums')->getMock();

        $type = 'test';

        $serializer = new IncludedSerializer($mapper, $type, $request);

        $this->assertEquals([], $serializer->getRelationships($entity));
        $this->assertEquals([], $serializer->getIncluded($type, $entity, []));
    }

    /**
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @throws \Jad\Exceptions\JadException
     * @throws \ReflectionException
     */
    public function testGetAttributes()
    {
        $em = Manager::getInstance()->getEm();

        $mapper = new AnnotationsMapper($em);

        $request = $this->getMockBuilder('Jad\Request\JsonApiRequest')
            ->setMethods(['getInputJson', 'getMethod'])
            ->disableOriginalConstructor()
            ->getMock();

        $serializer = new IncludedSerializer($mapper, 'invoices', $request);

        /** @var $invoice $invoice */
        $invoice = $em->getRepository(Invoices::class)->find(1);

        $expected = [
            "invoice-date" => "2009-01-01 00:00:00",
            "billing-address" => "Theodor-Heuss-Straße 34",
            "billing-city" => "Stuttgart",
            "billing-state" => null,
            "billing-postal-code" => "70174",
            "total" => "1.98",
        ];

        $this->assertEquals($expected, $serializer->getAttributes($invoice, []));
    }
}