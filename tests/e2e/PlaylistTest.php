<?php

namespace Jad\E2E;

use Jad\Tests\TestCase;
use Jad\Database\Manager;
use Jad\Map\AnnotationsMapper;
use Jad\Jad;

use PHPUnit\DbUnit\TestCaseTrait;
use PHPUnit\DbUnit\DataSet\CsvDataSet;

class PlaylistTest extends TestCase
{
    use TestCaseTrait;

    public function setUp()
    {
        parent::setUp();
        $_GET = [];
    }

    public function getConnection()
    {
        $pdo = new \PDO('sqlite://' . dirname(__DIR__ ) . '/fixtures/test_db.sqlite');
        return $this->createDefaultDBConnection($pdo, ':memory:');
    }

    public function getDataSet()
    {
        $dataSet = new CsvDataSet();
        $dataSet->addTable('genres', dirname(__DIR__ ) . '/fixtures/genres.csv');
        return $dataSet;
    }

    public function testFetchCollection()
    {
        $_SERVER = ['REQUEST_URI' => '/playlist'];

        $mapper = new AnnotationsMapper(Manager::getInstance()->getEm());
        $jad = new Jad($mapper);

        $expected = '{"data":[{"id":1,"type":"playlist","attributes":{"name":"All tracks"},"relationships":{"tracks":{"links":{"self":"http:\/\/:\/playlist\/1\/relationship\/tracks","related":"http:\/\/:\/playlist\/1\/tracks"}}}},{"id":2,"type":"playlist","attributes":{"name":"Some tracks"},"relationships":{"tracks":{"links":{"self":"http:\/\/:\/playlist\/2\/relationship\/tracks","related":"http:\/\/:\/playlist\/2\/tracks"}}}},{"id":3,"type":"playlist","attributes":{"name":"No tracks"},"relationships":{"tracks":{"links":{"self":"http:\/\/:\/playlist\/3\/relationship\/tracks","related":"http:\/\/:\/playlist\/3\/tracks"}}}}],"links":{"self":"http:\/\/:\/playlist"}}';
        $jad->jsonApiResult();
        $this->expectOutputString($expected);
    }

    public function testFetchCollectionFields()
    {
        $_SERVER = ['REQUEST_URI' => '/tracks'];
        $_GET = ['page' => ['offset' => 0, 'limit' => 5], 'fields' => [ 'track' => 'name']];

        $mapper = new AnnotationsMapper(Manager::getInstance()->getEm());
        $jad = new Jad($mapper);
        $expected = '{"data":[{"id":15,"type":"track","attributes":{"name":"Go Down"},"relationships":{"album":{"links":{"self":"http:\/\/:\/tracks\/15\/relationship\/album","related":"http:\/\/:\/tracks\/15\/album"}},"playlists":{"links":{"self":"http:\/\/:\/tracks\/15\/relationship\/playlists","related":"http:\/\/:\/tracks\/15\/playlists"}}}},{"id":43,"type":"track","attributes":{"name":"Forgiven"},"relationships":{"album":{"links":{"self":"http:\/\/:\/tracks\/43\/relationship\/album","related":"http:\/\/:\/tracks\/43\/album"}},"playlists":{"links":{"self":"http:\/\/:\/tracks\/43\/relationship\/playlists","related":"http:\/\/:\/tracks\/43\/playlists"}}}},{"id":77,"type":"track","attributes":{"name":"Enter Sandman"},"relationships":{"album":{"links":{"self":"http:\/\/:\/tracks\/77\/relationship\/album","related":"http:\/\/:\/tracks\/77\/album"}},"playlists":{"links":{"self":"http:\/\/:\/tracks\/77\/relationship\/playlists","related":"http:\/\/:\/tracks\/77\/playlists"}}}},{"id":117,"type":"track","attributes":{"name":"Rock \'N\' Roll Music"},"relationships":{"album":{"links":{"self":"http:\/\/:\/tracks\/117\/relationship\/album","related":"http:\/\/:\/tracks\/117\/album"}},"playlists":{"links":{"self":"http:\/\/:\/tracks\/117\/relationship\/playlists","related":"http:\/\/:\/tracks\/117\/playlists"}}}},{"id":351,"type":"track","attributes":{"name":"Debra Kadabra"},"relationships":{"album":{"links":{"self":"http:\/\/:\/tracks\/351\/relationship\/album","related":"http:\/\/:\/tracks\/351\/album"}},"playlists":{"links":{"self":"http:\/\/:\/tracks\/351\/relationship\/playlists","related":"http:\/\/:\/tracks\/351\/playlists"}}}}],"links":{"self":"http:\/\/:\/tracks"}}';

        $jad->jsonApiResult();
        $this->expectOutputString($expected);
    }

    public function testGetTracks()
    {
        $_SERVER = ['REQUEST_URI' => '/tracks/15'];

        $mapper = new AnnotationsMapper(Manager::getInstance()->getEm());
        $jad = new Jad($mapper);

        $expected = '{"data":{"id":15,"type":"track","attributes":{"name":"Go Down","composer":"AC\/DC","price":"0.99"},"relationships":{"album":{"links":{"self":"http:\/\/:\/tracks\/15\/relationship\/album","related":"http:\/\/:\/tracks\/15\/album"}},"playlists":{"links":{"self":"http:\/\/:\/tracks\/15\/relationship\/playlists","related":"http:\/\/:\/tracks\/15\/playlists"}}}},"links":{"self":"http:\/\/:\/tracks\/15"}}';
        $jad->jsonApiResult();
        $this->expectOutputString($expected);
    }

    public function testGetRelationshipFull()
    {
        $_SERVER = ['REQUEST_URI' => '/tracks/15/playlists'];

        $mapper = new AnnotationsMapper(Manager::getInstance()->getEm());
        $jad = new Jad($mapper);

        $expected = '{"data":[{"id":1,"type":"playlist","attributes":{"name":"All tracks"}},{"id":2,"type":"playlist","attributes":{"name":"Some tracks"}}],"links":{"self":"http:\/\/:\/tracks\/15\/playlists"}}';
        $jad->jsonApiResult();
        $this->expectOutputString($expected);
    }

    public function testGetRelationshipList()
    {
        $_SERVER = ['REQUEST_URI' => '/tracks/15/relationship/playlists'];

        $mapper = new AnnotationsMapper(Manager::getInstance()->getEm());
        $jad = new Jad($mapper);

        $expected = '{"data":[{"id":1,"type":"playlist"},{"id":2,"type":"playlist"}],"links":{"self":"http:\/\/:\/tracks\/15\/relationship\/playlists"}}';
        $jad->jsonApiResult();
        $this->expectOutputString($expected);
    }

}