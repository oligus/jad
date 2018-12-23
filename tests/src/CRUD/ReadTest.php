<?php declare(strict_types=1);

namespace Jad\Tests\CRUD;

use Jad\Map\AnnotationsMapper;
use Jad\Request\Parameters;
use Jad\Tests\TestCase;
use Jad\CRUD\Read;
use Jad\Database\Manager;
use Jad\Document\Collection;

class ReadTest extends TestCase
{
    /**
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Jad\Exceptions\JadException
     * @throws \Jad\Exceptions\ParameterException
     */
    public function testGetResources()
    {
        $mapper = new AnnotationsMapper(Manager::getInstance()->getEm());
        $parameters = new Parameters();
        $parameters->setArguments([
            'filter' => [
                'tracks' => [
                'name' => [
                    'like' => 'and'
                ]
            ]
                ]
        ]);

        $request = $this->getMockBuilder('Jad\Request\JsonApiRequest')
            ->disableOriginalConstructor()
            ->setMethods(['getResourceType', 'getParameters'])
            ->getMock();

        $request->expects($this->any())
            ->method('getResourceType')
            ->willReturn('tracks');

        $request->expects($this->any())
            ->method('getParameters')
            ->willReturn($parameters);

        $read = new Read($request, $mapper);

        /** @var Collection $result */
        $result = $read->getResources();

        $this->assertInstanceOf('Jad\Document\Collection', $result);
        $this->assertEquals(2,  $result->getPaginator()->getCount());
    }
}