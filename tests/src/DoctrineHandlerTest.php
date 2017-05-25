<?php

namespace Jad\Tests;

use Jad\DoctrineHandler;
use Jad\RequestHandler;
use Jad\Map\ArrayMapper;
use Jad\Database\Manager;
use Tobscure\JsonApi\Document;

use PHPUnit\DbUnit\TestCaseTrait;
use PHPUnit\DbUnit\DataSet\CsvDataSet;

class DoctrineHandlerTest extends TestCase
{
    use TestCaseTrait;

    public function getConnection()
    {
        $pdo = new \PDO('sqlite://' . dirname(__DIR__ ) . '/fixtures/test_db.sqlite');
        return $this->createDefaultDBConnection($pdo, ':memory:');
    }

    public function getDataSet()
    {
        $dataSet = new CsvDataSet();
        $dataSet->addTable('artists', dirname(__DIR__ ) . '/fixtures/artists.csv');
        $dataSet->addTable('tracks', dirname(__DIR__ ) . '/fixtures/tracks.csv');
        return $dataSet;
    }

    public function testGetEntityById()
    {
        $mapper = new ArrayMapper(Manager::getInstance()->getEm());
        $mapper->add('artists', [
            'entityClass' => 'Jad\Database\Entities\Artists'
        ]);

        $_SERVER = ['REQUEST_URI' => '/artists'];
        $dh = new DoctrineHandler($mapper, new RequestHandler());

        $resource = $dh->getEntityById(5);
        $document = new Document($resource);

        $expected = '{"data":{"type":"artists","id":"5","attributes":{"name":"Alice In Chains"}}}';
        $this->assertEquals($expected, json_encode($document));
    }

    public function testGetEntities()
    {
        $_GET = [
            'sort' => '-name',
            'page' => [
                'offset' => 0,
                'limit' => 10
            ],
        ];

        $_SERVER = ['REQUEST_URI' => '/artists'];

        $mapper = new ArrayMapper(Manager::getInstance()->getEm());
        $mapper->add('artists', [
            'entityClass' => 'Jad\Database\Entities\Artists'
        ]);

        $dh = new DoctrineHandler($mapper, new RequestHandler());
        $collection = $dh->getEntities();
        $document = new Document($collection);

        $expected = '{"data":[{"type":"artists","id":"155","attributes":{"name":"Zeca Pagodinho"}},{"type":"artists","id":"168","attributes":{"name":"Youssou N\'Dour"}},{"type":"artists","id":"212","attributes":{"name":"Yo-Yo Ma"}},{"type":"artists","id":"255","attributes":{"name":"Yehudi Menuhin"}},{"type":"artists","id":"181","attributes":{"name":"Xis"}},{"type":"artists","id":"211","attributes":{"name":"Wilhelm Kempff"}},{"type":"artists","id":"154","attributes":{"name":"Whitesnake"}},{"type":"artists","id":"73","attributes":{"name":"Vin\u00edcius E Qurteto Em Cy"}},{"type":"artists","id":"74","attributes":{"name":"Vin\u00edcius E Odette Lara"}},{"type":"artists","id":"71","attributes":{"name":"Vin\u00edcius De Moraes & Baden Powell"}}]}';
        $this->assertEquals($expected, json_encode($document));

        $_GET = [
            'sort' => 'name',
            'page' => [
                'offset' => 10,
                'limit' => 5
            ],
        ];

        $dh = new DoctrineHandler($mapper, new RequestHandler());
        $collection = $dh->getEntities();
        $document = new Document($collection);

        $expected = '{"data":[{"type":"artists","id":"260","attributes":{"name":"Adrian Leaper & Doreen de Feis"}},{"type":"artists","id":"3","attributes":{"name":"Aerosmith"}},{"type":"artists","id":"161","attributes":{"name":"Aerosmith & Sierra Leone\'s Refugee Allstars"}},{"type":"artists","id":"197","attributes":{"name":"Aisha Duo"}},{"type":"artists","id":"4","attributes":{"name":"Alanis Morissette"}}]}';
        $this->assertEquals($expected, json_encode($document));
    }

    public function testUpdateEntity()
    {
        $_SERVER = ['REQUEST_URI' => '/artists'];

        $mapper = new ArrayMapper(Manager::getInstance()->getEm());
        $mapper->add('artists', [
            'entityClass' => 'Jad\Database\Entities\Artists'
        ]);

        $dh = new DoctrineHandler($mapper, new RequestHandler());
        $input = new \stdClass();
        $input->data = new \stdClass();
        $input->data->id = 22;
        $input->data->type = 'artists';
        $input->data->attributes = new \stdClass();
        $input->data->attributes->name = 'Test Artist';

        $dh->updateEntity($input);

        /** @var \Jad\Database\Entities\Artists $result */
        $result = Manager::getInstance()->getEm()->getRepository('Jad\Database\Entities\Artists')->find(22);
        $this->assertEquals('Test Artist', $result->getName());
    }

    public function testCreateEntity()
    {
        $_SERVER = ['REQUEST_URI' => '/artists'];

        $mapper = new ArrayMapper(Manager::getInstance()->getEm());
        $mapper->add('artists', [
            'entityClass' => 'Jad\Database\Entities\Artists'
        ]);

        $dh = new DoctrineHandler($mapper, new RequestHandler());
        $input = new \stdClass();
        $input->data = new \stdClass();
        $input->data->type = 'artists';
        $input->data->attributes = new \stdClass();
        $input->data->attributes->name = 'New Created Artist';

        $dh->createEntity($input);

        $result = Manager::getInstance()->getEm()->getRepository('Jad\Database\Entities\Artists')
            ->findOneBy([], ['id' => 'DESC']);
        $this->assertEquals('New Created Artist', $result->getName());
    }

    public function testSetEntityAttribute()
    {
        $_SERVER = ['REQUEST_URI' => '/artists'];

        $mapper = new ArrayMapper(Manager::getInstance()->getEm());
        $mapper->add('artists', [
            'entityClass' => 'Jad\Database\Entities\Artists'
        ]);

        $dh = new DoctrineHandler($mapper, new RequestHandler());
        $method = $this->getMethod('Jad\DoctrineHandler', 'setEntityAttribute');

        $artist = Manager::getInstance()->getEm()->getRepository('Jad\Database\Entities\Artists')->find(56);
        $this->assertEquals('Gonzaguinha', $artist->getName());

        $method->invokeArgs($dh, [$artist, 'name', 'Ted Apple']);

        $artist = Manager::getInstance()->getEm()->getRepository('Jad\Database\Entities\Artists')->find(56);
        $this->assertEquals('Ted Apple', $artist->getName());
    }

    public function testDeleteEntity()
    {
        $_SERVER = ['REQUEST_URI' => '/artists'];

        $mapper = new ArrayMapper(Manager::getInstance()->getEm());
        $mapper->add('artists', [
            'entityClass' => 'Jad\Database\Entities\Artists'
        ]);

        $artist = Manager::getInstance()->getEm()->getRepository('Jad\Database\Entities\Artists')->find(44);
        $this->assertInstanceOf('Jad\Database\Entities\Artists', $artist);
        $this->assertEquals('Kid Abelha', $artist->getName());

        $dh = new DoctrineHandler($mapper, new RequestHandler());
        $dh->deleteEntity(44);

        $artist = Manager::getInstance()->getEm()->getRepository('Jad\Database\Entities\Artists')->find(44);
        $this->assertNull($artist);
    }

    public function testGetEntity()
    {
        $_SERVER = ['REQUEST_URI' => '/tracks'];

        $mapper = new ArrayMapper(Manager::getInstance()->getEm());
        $mapper->add('tracks', ['entityClass' => 'Jad\Database\Entities\Tracks']);

        $dh = new DoctrineHandler($mapper, new RequestHandler());
        $track = $dh->getEntity('tracks', 43);
        $this->assertInstanceOf('Jad\Database\Entities\Tracks', $track);
        $this->assertEquals($track->getName(), 'Forgiven');
    }
}