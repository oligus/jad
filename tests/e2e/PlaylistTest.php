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
        $_SERVER = ['REQUEST_URI' => '/playlists'];

        $mapper = new AnnotationsMapper(Manager::getInstance()->getEm());
        $jad = new Jad($mapper);

        $expected = '{"data":[{"id":1,"type":"playlists","attributes":{"name":"All tracks"},"relationships":{"tracks":{"links":{"self":"http:\/\/:\/playlists\/1\/relationship\/tracks","related":"http:\/\/:\/playlists\/1\/tracks"}}}},{"id":2,"type":"playlists","attributes":{"name":"Some tracks"},"relationships":{"tracks":{"links":{"self":"http:\/\/:\/playlists\/2\/relationship\/tracks","related":"http:\/\/:\/playlists\/2\/tracks"}}}},{"id":3,"type":"playlists","attributes":{"name":"No tracks"},"relationships":{"tracks":{"links":{"self":"http:\/\/:\/playlists\/3\/relationship\/tracks","related":"http:\/\/:\/playlists\/3\/tracks"}}}}],"links":{"self":"http:\/\/:\/playlists"}}';
        $jad->jsonApiResult();
        $this->expectOutputString($expected);
    }

    public function testFetchCollectionFields()
    {
        $_SERVER = ['REQUEST_URI' => '/tracks'];
        $_GET = ['page' => ['offset' => 0, 'limit' => 5], 'fields' => [ 'tracks' => 'name']];

        $mapper = new AnnotationsMapper(Manager::getInstance()->getEm());
        $jad = new Jad($mapper);
        $expected = '{"data":[{"id":15,"type":"tracks","attributes":{"name":"Go Down"},"relationships":{"album":{"links":{"self":"http:\/\/:\/tracks\/15\/relationship\/album","related":"http:\/\/:\/tracks\/15\/album"}},"media-type":{"links":{"self":"http:\/\/:\/tracks\/15\/relationship\/media-type","related":"http:\/\/:\/tracks\/15\/media-type"}},"genre":{"links":{"self":"http:\/\/:\/tracks\/15\/relationship\/genre","related":"http:\/\/:\/tracks\/15\/genre"}},"playlists":{"links":{"self":"http:\/\/:\/tracks\/15\/relationship\/playlists","related":"http:\/\/:\/tracks\/15\/playlists"}}}},{"id":43,"type":"tracks","attributes":{"name":"Forgiven"},"relationships":{"album":{"links":{"self":"http:\/\/:\/tracks\/43\/relationship\/album","related":"http:\/\/:\/tracks\/43\/album"}},"media-type":{"links":{"self":"http:\/\/:\/tracks\/43\/relationship\/media-type","related":"http:\/\/:\/tracks\/43\/media-type"}},"genre":{"links":{"self":"http:\/\/:\/tracks\/43\/relationship\/genre","related":"http:\/\/:\/tracks\/43\/genre"}},"playlists":{"links":{"self":"http:\/\/:\/tracks\/43\/relationship\/playlists","related":"http:\/\/:\/tracks\/43\/playlists"}}}},{"id":77,"type":"tracks","attributes":{"name":"Enter Sandman"},"relationships":{"album":{"links":{"self":"http:\/\/:\/tracks\/77\/relationship\/album","related":"http:\/\/:\/tracks\/77\/album"}},"media-type":{"links":{"self":"http:\/\/:\/tracks\/77\/relationship\/media-type","related":"http:\/\/:\/tracks\/77\/media-type"}},"genre":{"links":{"self":"http:\/\/:\/tracks\/77\/relationship\/genre","related":"http:\/\/:\/tracks\/77\/genre"}},"playlists":{"links":{"self":"http:\/\/:\/tracks\/77\/relationship\/playlists","related":"http:\/\/:\/tracks\/77\/playlists"}}}},{"id":117,"type":"tracks","attributes":{"name":"Rock \'N\' Roll Music"},"relationships":{"album":{"links":{"self":"http:\/\/:\/tracks\/117\/relationship\/album","related":"http:\/\/:\/tracks\/117\/album"}},"media-type":{"links":{"self":"http:\/\/:\/tracks\/117\/relationship\/media-type","related":"http:\/\/:\/tracks\/117\/media-type"}},"genre":{"links":{"self":"http:\/\/:\/tracks\/117\/relationship\/genre","related":"http:\/\/:\/tracks\/117\/genre"}},"playlists":{"links":{"self":"http:\/\/:\/tracks\/117\/relationship\/playlists","related":"http:\/\/:\/tracks\/117\/playlists"}}}},{"id":351,"type":"tracks","attributes":{"name":"Debra Kadabra"},"relationships":{"album":{"links":{"self":"http:\/\/:\/tracks\/351\/relationship\/album","related":"http:\/\/:\/tracks\/351\/album"}},"media-type":{"links":{"self":"http:\/\/:\/tracks\/351\/relationship\/media-type","related":"http:\/\/:\/tracks\/351\/media-type"}},"genre":{"links":{"self":"http:\/\/:\/tracks\/351\/relationship\/genre","related":"http:\/\/:\/tracks\/351\/genre"}},"playlists":{"links":{"self":"http:\/\/:\/tracks\/351\/relationship\/playlists","related":"http:\/\/:\/tracks\/351\/playlists"}}}}],"links":{"self":"http:\/\/:\/tracks"}}';

        $jad->jsonApiResult();
        $this->expectOutputString($expected);
    }

    public function testGetTracks()
    {
        $_SERVER = ['REQUEST_URI' => '/tracks/15'];

        $mapper = new AnnotationsMapper(Manager::getInstance()->getEm());
        $jad = new Jad($mapper);
        $expected = '{"data":{"id":15,"type":"tracks","attributes":{"name":"Go Down","composer":"AC\/DC","price":"0.99"},"relationships":{"album":{"links":{"self":"http:\/\/:\/tracks\/15\/relationship\/album","related":"http:\/\/:\/tracks\/15\/album"}},"media-type":{"links":{"self":"http:\/\/:\/tracks\/15\/relationship\/media-type","related":"http:\/\/:\/tracks\/15\/media-type"}},"genre":{"links":{"self":"http:\/\/:\/tracks\/15\/relationship\/genre","related":"http:\/\/:\/tracks\/15\/genre"}},"playlists":{"links":{"self":"http:\/\/:\/tracks\/15\/relationship\/playlists","related":"http:\/\/:\/tracks\/15\/playlists"}}}},"links":{"self":"http:\/\/:\/tracks\/15"}}';

        $jad->jsonApiResult();
        $this->expectOutputString($expected);
    }

    public function testGetRelationshipFull()
    {
        $_SERVER = ['REQUEST_URI' => '/tracks/15/playlists'];

        $mapper = new AnnotationsMapper(Manager::getInstance()->getEm());
        $jad = new Jad($mapper);

        $expected = '{"data":[{"id":1,"type":"playlists","attributes":{"name":"All tracks"}},{"id":2,"type":"playlists","attributes":{"name":"Some tracks"}}],"links":{"self":"http:\/\/:\/tracks\/15\/playlists"}}';
        $jad->jsonApiResult();
        $this->expectOutputString($expected);
    }

    public function testGetRelationshipList()
    {
        $_SERVER = ['REQUEST_URI' => '/tracks/15/relationships/playlists'];

        $mapper = new AnnotationsMapper(Manager::getInstance()->getEm());
        $jad = new Jad($mapper);

        $expected = '{"data":[{"id":1,"type":"playlists"},{"id":2,"type":"playlists"}],"links":{"self":"http:\/\/:\/tracks\/15\/relationships\/playlists"}}';
        $jad->jsonApiResult();
        $this->expectOutputString($expected);
    }

    public function testCreateRelationship()
    {
        Configure::getInstance()->setConfig('testMode', true);
        $_SERVER = ['REQUEST_URI' => '/playlists'];
        $_SERVER['REQUEST_METHOD'] = 'POST';

        $input = new \stdClass();
        $input->data = new \stdClass();
        $input->data->type = 'playlists';
        $input->data->attributes = new \stdClass();
        $input->data->attributes->name = 'New Playlist';
        $input->data->relationships = new \stdClass();
        $input->data->relationships->tracks = new \stdClass();
        $input->data->relationships->tracks->data = [];
        $input->data->relationships->tracks->data[] = [ 'type' => 'tracks', 'id' => 15];
        $input->data->relationships->tracks->data[] = [ 'type' => 'tracks', 'id' => 43];
        $input->data->relationships->tracks->data[] = [ 'type' => 'tracks', 'id' => 77];
        $input->data->relationships->tracks->data[] = [ 'type' => 'tracks', 'id' => 117];
        $input->data->relationships->tracks->data[] = [ 'type' => 'tracks', 'id' => 351];

        $_POST = ['input' => json_encode($input)];

        $mapper = new AnnotationsMapper(Manager::getInstance()->getEm());
        $jad = new Jad($mapper);

        $expected = '{"data":{"id":4,"type":"playlists","attributes":{"name":"New Playlist"},"relationships":{"tracks":{"links":{"self":"http:\/\/:\/playlists\/4\/relationship\/tracks","related":"http:\/\/:\/playlists\/4\/tracks"}}}},"links":{"self":"http:\/\/:\/playlists"}}';
        $jad->jsonApiResult();
        $this->expectOutputString($expected);
    }

    /**
     * @depends testCreateRelationship
     */
    public function testCreateRelationshipVerify()
    {
        $_SERVER = ['REQUEST_URI' => '/playlists/4/relationships/tracks'];
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $mapper = new AnnotationsMapper(Manager::getInstance()->getEm());
        $jad = new Jad($mapper);

        $expected = '{"data":[{"id":15,"type":"tracks"},{"id":43,"type":"tracks"},{"id":77,"type":"tracks"},{"id":117,"type":"tracks"},{"id":351,"type":"tracks"}],"links":{"self":"http:\/\/:\/playlists\/4\/relationships\/tracks"}}';
        $jad->jsonApiResult();
        $this->expectOutputString($expected);
    }

    /**
     * @depends testCreateRelationship
     */
    public function testUpdateAddRelationship()
    {
        Configure::getInstance()->setConfig('testMode', true);
        $_SERVER = ['REQUEST_URI' => '/playlists/4'];
        $_SERVER['REQUEST_METHOD'] = 'PATCH';

        $input = new \stdClass();
        $input->data = new \stdClass();
        $input->data->type = 'playlists';
        $input->data->relationships = new \stdClass();
        $input->data->relationships->tracks = new \stdClass();
        $input->data->relationships->tracks->data = new \stdClass();
        $input->data->relationships->tracks->data->type = 'tracks';
        $input->data->relationships->tracks->data->id = 422;

        $_POST = ['input' => json_encode($input)];

        $mapper = new AnnotationsMapper(Manager::getInstance()->getEm());
        $jad = new Jad($mapper);

        $expected = '{"data":{"id":4,"type":"playlists","attributes":{"name":"New Playlist"},"relationships":{"tracks":{"links":{"self":"http:\/\/:\/playlists\/4\/relationship\/tracks","related":"http:\/\/:\/playlists\/4\/tracks"}}}},"links":{"self":"http:\/\/:\/playlists\/4"}}';
        $jad->jsonApiResult();
        $this->expectOutputString($expected);
    }

    /**
     * @depends testUpdateAddRelationship
     */
    public function testUpdateAddRelationshipVerify()
    {
        $_SERVER = ['REQUEST_URI' => '/playlists/4/relationships/tracks'];
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $mapper = new AnnotationsMapper(Manager::getInstance()->getEm());
        $jad = new Jad($mapper);

        $expected = '{"data":[{"id":15,"type":"tracks"},{"id":43,"type":"tracks"},{"id":77,"type":"tracks"},{"id":117,"type":"tracks"},{"id":351,"type":"tracks"},{"id":422,"type":"tracks"}],"links":{"self":"http:\/\/:\/playlists\/4\/relationships\/tracks"}}';
        $jad->jsonApiResult();
        $this->expectOutputString($expected);
    }
}