<?php

namespace Jad\E2E;

use Jad\Jad;
use Jad\Configure;
use Jad\Tests\TestCase;
use Jad\Database\Manager;
use Jad\Map\AnnotationsMapper;

use PHPUnit\DbUnit\TestCaseTrait;
use PHPUnit\DbUnit\DataSet\CsvDataSet;

class PlaylistTest extends TestCase
{
    use TestCaseTrait;

    public function setUp()
    {
        parent::setUp();

        $this->databaseTester = null;

        $this->getDatabaseTester()->setSetUpOperation($this->getSetUpOperation());
        $this->getDatabaseTester()->setDataSet($this->getDataSet());
        $this->getDatabaseTester()->onSetUp();

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
        $dataSet->addTable('playlists', dirname(__DIR__ ) . '/fixtures/playlists.csv');
        $dataSet->addTable('tracks', dirname(__DIR__ ) . '/fixtures/tracks.csv');
        $dataSet->addTable('playlist_track', dirname(__DIR__ ) . '/fixtures/playlist_track.csv');
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

    public function testCreateRelationship()
    {
        Configure::getInstance()->setConfig('testMode', true);
        $_SERVER = ['REQUEST_URI' => '/playlist'];
        $_SERVER['REQUEST_METHOD'] = 'POST';

        $input = new \stdClass();
        $input->data = new \stdClass();
        $input->data->type = 'playlist';
        $input->data->attributes = new \stdClass();
        $input->data->attributes->name = 'New Playlist';
        $input->data->relationships = new \stdClass();
        $input->data->relationships->tracks = new \stdClass();
        $input->data->relationships->tracks->data = [];
        $input->data->relationships->tracks->data[] = [ 'type' => 'track', 'id' => 15];
        $input->data->relationships->tracks->data[] = [ 'type' => 'track', 'id' => 43];
        $input->data->relationships->tracks->data[] = [ 'type' => 'track', 'id' => 77];
        $input->data->relationships->tracks->data[] = [ 'type' => 'track', 'id' => 117];
        $input->data->relationships->tracks->data[] = [ 'type' => 'track', 'id' => 351];

        $_POST = ['input' => json_encode($input)];

        $mapper = new AnnotationsMapper(Manager::getInstance()->getEm());
        $jad = new Jad($mapper);

        $expected = '{"data":{"id":4,"type":"playlist","attributes":{"name":"New Playlist"},"relationships":{"tracks":{"links":{"self":"http:\/\/:\/playlist\/4\/relationship\/tracks","related":"http:\/\/:\/playlist\/4\/tracks"}}}},"links":{"self":"http:\/\/:\/playlist"}}';
        $jad->jsonApiResult();
        $this->expectOutputString($expected);
    }

    /**
     * @depends testCreateRelationship
     */
    public function testCreateRelationshipVerify()
    {
        $_SERVER = ['REQUEST_URI' => '/playlist/4/relationship/tracks'];
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $mapper = new AnnotationsMapper(Manager::getInstance()->getEm());
        $jad = new Jad($mapper);

        $expected = '{"data":[{"id":15,"type":"track"},{"id":43,"type":"track"},{"id":77,"type":"track"},{"id":117,"type":"track"},{"id":351,"type":"track"}],"links":{"self":"http:\/\/:\/playlist\/4\/relationship\/tracks"}}';
        $jad->jsonApiResult();
        $this->expectOutputString($expected);
    }

    /**
     * @depends testCreateRelationship
     */
    public function testUpdateAddRelationship()
    {
        Configure::getInstance()->setConfig('testMode', true);
        $_SERVER = ['REQUEST_URI' => '/playlist/4'];
        $_SERVER['REQUEST_METHOD'] = 'PATCH';

        $input = new \stdClass();
        $input->data = new \stdClass();
        $input->data->type = 'playlist';
        $input->data->relationships = new \stdClass();
        $input->data->relationships->tracks = new \stdClass();
        $input->data->relationships->tracks->data = new \stdClass();
        $input->data->relationships->tracks->data->type = 'track';
        $input->data->relationships->tracks->data->id = 422;

        $_POST = ['input' => json_encode($input)];

        $mapper = new AnnotationsMapper(Manager::getInstance()->getEm());
        $jad = new Jad($mapper);

        $expected = '{"data":{"id":4,"type":"playlist","attributes":{"name":"New Playlist"},"relationships":{"tracks":{"links":{"self":"http:\/\/:\/playlist\/4\/relationship\/tracks","related":"http:\/\/:\/playlist\/4\/tracks"}}}},"links":{"self":"http:\/\/:\/playlist\/4"}}';
        $jad->jsonApiResult();
        $this->expectOutputString($expected);
    }

    /**
     * @depends testUpdateAddRelationship
     */
    public function testUpdateAddRelationshipVerify()
    {
        $_SERVER = ['REQUEST_URI' => '/playlist/4/relationship/tracks'];
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $mapper = new AnnotationsMapper(Manager::getInstance()->getEm());
        $jad = new Jad($mapper);

        $expected = '{"data":[{"id":15,"type":"track"},{"id":43,"type":"track"},{"id":77,"type":"track"},{"id":117,"type":"track"},{"id":351,"type":"track"},{"id":422,"type":"track"}],"links":{"self":"http:\/\/:\/playlist\/4\/relationship\/tracks"}}';
        $jad->jsonApiResult();
        $this->expectOutputString($expected);
    }
}