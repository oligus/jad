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
        $_SERVER['REQUEST_URI']  = '/playlists';

        $mapper = new AnnotationsMapper(Manager::getInstance()->getEm());
        $jad = new Jad($mapper);

        $expected = '{"data":[{"id":"1","type":"playlists","attributes":{"name":"All tracks"},"relationships":{"tracks":{"links":{"self":"http:\/\/:\/playlists\/1\/relationship\/tracks","related":"http:\/\/:\/playlists\/1\/tracks"}}}},{"id":"2","type":"playlists","attributes":{"name":"Some tracks"},"relationships":{"tracks":{"links":{"self":"http:\/\/:\/playlists\/2\/relationship\/tracks","related":"http:\/\/:\/playlists\/2\/tracks"}}}},{"id":"3","type":"playlists","attributes":{"name":"No tracks"},"relationships":{"tracks":{"links":{"self":"http:\/\/:\/playlists\/3\/relationship\/tracks","related":"http:\/\/:\/playlists\/3\/tracks"}}}}],"links":{"self":"http:\/\/:\/playlists"}}';
        $jad->jsonApiResult();
        $this->expectOutputString($expected);
    }

    public function testFetchCollectionFields()
    {
        $_SERVER['REQUEST_URI']  = '/tracks';
        $_GET = ['page' => ['offset' => 0, 'limit' => 5], 'fields' => [ 'tracks' => 'name']];

        $mapper = new AnnotationsMapper(Manager::getInstance()->getEm());
        $jad = new Jad($mapper);
        $expected = '{"data":[{"id":"15","type":"tracks","attributes":{"name":"Go Down"},"relationships":{"albums":{"links":{"self":"http:\/\/:\/tracks\/15\/relationship\/albums","related":"http:\/\/:\/tracks\/15\/albums"}},"media-types":{"links":{"self":"http:\/\/:\/tracks\/15\/relationship\/media-types","related":"http:\/\/:\/tracks\/15\/media-types"}},"genres":{"links":{"self":"http:\/\/:\/tracks\/15\/relationship\/genres","related":"http:\/\/:\/tracks\/15\/genres"}},"playlists":{"links":{"self":"http:\/\/:\/tracks\/15\/relationship\/playlists","related":"http:\/\/:\/tracks\/15\/playlists"}}}},{"id":"43","type":"tracks","attributes":{"name":"Forgiven"},"relationships":{"albums":{"links":{"self":"http:\/\/:\/tracks\/43\/relationship\/albums","related":"http:\/\/:\/tracks\/43\/albums"}},"media-types":{"links":{"self":"http:\/\/:\/tracks\/43\/relationship\/media-types","related":"http:\/\/:\/tracks\/43\/media-types"}},"genres":{"links":{"self":"http:\/\/:\/tracks\/43\/relationship\/genres","related":"http:\/\/:\/tracks\/43\/genres"}},"playlists":{"links":{"self":"http:\/\/:\/tracks\/43\/relationship\/playlists","related":"http:\/\/:\/tracks\/43\/playlists"}}}},{"id":"77","type":"tracks","attributes":{"name":"Enter Sandman"},"relationships":{"albums":{"links":{"self":"http:\/\/:\/tracks\/77\/relationship\/albums","related":"http:\/\/:\/tracks\/77\/albums"}},"media-types":{"links":{"self":"http:\/\/:\/tracks\/77\/relationship\/media-types","related":"http:\/\/:\/tracks\/77\/media-types"}},"genres":{"links":{"self":"http:\/\/:\/tracks\/77\/relationship\/genres","related":"http:\/\/:\/tracks\/77\/genres"}},"playlists":{"links":{"self":"http:\/\/:\/tracks\/77\/relationship\/playlists","related":"http:\/\/:\/tracks\/77\/playlists"}}}},{"id":"117","type":"tracks","attributes":{"name":"Rock \'N\' Roll Music"},"relationships":{"albums":{"links":{"self":"http:\/\/:\/tracks\/117\/relationship\/albums","related":"http:\/\/:\/tracks\/117\/albums"}},"media-types":{"links":{"self":"http:\/\/:\/tracks\/117\/relationship\/media-types","related":"http:\/\/:\/tracks\/117\/media-types"}},"genres":{"links":{"self":"http:\/\/:\/tracks\/117\/relationship\/genres","related":"http:\/\/:\/tracks\/117\/genres"}},"playlists":{"links":{"self":"http:\/\/:\/tracks\/117\/relationship\/playlists","related":"http:\/\/:\/tracks\/117\/playlists"}}}},{"id":"351","type":"tracks","attributes":{"name":"Debra Kadabra"},"relationships":{"albums":{"links":{"self":"http:\/\/:\/tracks\/351\/relationship\/albums","related":"http:\/\/:\/tracks\/351\/albums"}},"media-types":{"links":{"self":"http:\/\/:\/tracks\/351\/relationship\/media-types","related":"http:\/\/:\/tracks\/351\/media-types"}},"genres":{"links":{"self":"http:\/\/:\/tracks\/351\/relationship\/genres","related":"http:\/\/:\/tracks\/351\/genres"}},"playlists":{"links":{"self":"http:\/\/:\/tracks\/351\/relationship\/playlists","related":"http:\/\/:\/tracks\/351\/playlists"}}}},{"id":"422","type":"tracks","attributes":{"name":"I Want It All"},"relationships":{"albums":{"links":{"self":"http:\/\/:\/tracks\/422\/relationship\/albums","related":"http:\/\/:\/tracks\/422\/albums"}},"media-types":{"links":{"self":"http:\/\/:\/tracks\/422\/relationship\/media-types","related":"http:\/\/:\/tracks\/422\/media-types"}},"genres":{"links":{"self":"http:\/\/:\/tracks\/422\/relationship\/genres","related":"http:\/\/:\/tracks\/422\/genres"}},"playlists":{"links":{"self":"http:\/\/:\/tracks\/422\/relationship\/playlists","related":"http:\/\/:\/tracks\/422\/playlists"}}}},{"id":"603","type":"tracks","attributes":{"name":"Bye Bye Blackbird"},"relationships":{"albums":{"links":{"self":"http:\/\/:\/tracks\/603\/relationship\/albums","related":"http:\/\/:\/tracks\/603\/albums"}},"media-types":{"links":{"self":"http:\/\/:\/tracks\/603\/relationship\/media-types","related":"http:\/\/:\/tracks\/603\/media-types"}},"genres":{"links":{"self":"http:\/\/:\/tracks\/603\/relationship\/genres","related":"http:\/\/:\/tracks\/603\/genres"}},"playlists":{"links":{"self":"http:\/\/:\/tracks\/603\/relationship\/playlists","related":"http:\/\/:\/tracks\/603\/playlists"}}}},{"id":"645","type":"tracks","attributes":{"name":"Swedish Schnapps"},"relationships":{"albums":{"links":{"self":"http:\/\/:\/tracks\/645\/relationship\/albums","related":"http:\/\/:\/tracks\/645\/albums"}},"media-types":{"links":{"self":"http:\/\/:\/tracks\/645\/relationship\/media-types","related":"http:\/\/:\/tracks\/645\/media-types"}},"genres":{"links":{"self":"http:\/\/:\/tracks\/645\/relationship\/genres","related":"http:\/\/:\/tracks\/645\/genres"}},"playlists":{"links":{"self":"http:\/\/:\/tracks\/645\/relationship\/playlists","related":"http:\/\/:\/tracks\/645\/playlists"}}}},{"id":"678","type":"tracks","attributes":{"name":"Bad Moon Rising"},"relationships":{"albums":{"links":{"self":"http:\/\/:\/tracks\/678\/relationship\/albums","related":"http:\/\/:\/tracks\/678\/albums"}},"media-types":{"links":{"self":"http:\/\/:\/tracks\/678\/relationship\/media-types","related":"http:\/\/:\/tracks\/678\/media-types"}},"genres":{"links":{"self":"http:\/\/:\/tracks\/678\/relationship\/genres","related":"http:\/\/:\/tracks\/678\/genres"}},"playlists":{"links":{"self":"http:\/\/:\/tracks\/678\/relationship\/playlists","related":"http:\/\/:\/tracks\/678\/playlists"}}}},{"id":"1139","type":"tracks","attributes":{"name":"Give Me Novacaine"},"relationships":{"albums":{"links":{"self":"http:\/\/:\/tracks\/1139\/relationship\/albums","related":"http:\/\/:\/tracks\/1139\/albums"}},"media-types":{"links":{"self":"http:\/\/:\/tracks\/1139\/relationship\/media-types","related":"http:\/\/:\/tracks\/1139\/media-types"}},"genres":{"links":{"self":"http:\/\/:\/tracks\/1139\/relationship\/genres","related":"http:\/\/:\/tracks\/1139\/genres"}},"playlists":{"links":{"self":"http:\/\/:\/tracks\/1139\/relationship\/playlists","related":"http:\/\/:\/tracks\/1139\/playlists"}}}},{"id":"1246","type":"tracks","attributes":{"name":"Rainmaker"},"relationships":{"albums":{"links":{"self":"http:\/\/:\/tracks\/1246\/relationship\/albums","related":"http:\/\/:\/tracks\/1246\/albums"}},"media-types":{"links":{"self":"http:\/\/:\/tracks\/1246\/relationship\/media-types","related":"http:\/\/:\/tracks\/1246\/media-types"}},"genres":{"links":{"self":"http:\/\/:\/tracks\/1246\/relationship\/genres","related":"http:\/\/:\/tracks\/1246\/genres"}},"playlists":{"links":{"self":"http:\/\/:\/tracks\/1246\/relationship\/playlists","related":"http:\/\/:\/tracks\/1246\/playlists"}}}},{"id":"1490","type":"tracks","attributes":{"name":"Hey Joe"},"relationships":{"albums":{"links":{"self":"http:\/\/:\/tracks\/1490\/relationship\/albums","related":"http:\/\/:\/tracks\/1490\/albums"}},"media-types":{"links":{"self":"http:\/\/:\/tracks\/1490\/relationship\/media-types","related":"http:\/\/:\/tracks\/1490\/media-types"}},"genres":{"links":{"self":"http:\/\/:\/tracks\/1490\/relationship\/genres","related":"http:\/\/:\/tracks\/1490\/genres"}},"playlists":{"links":{"self":"http:\/\/:\/tracks\/1490\/relationship\/playlists","related":"http:\/\/:\/tracks\/1490\/playlists"}}}},{"id":"1492","type":"tracks","attributes":{"name":"Purple Haze"},"relationships":{"albums":{"links":{"self":"http:\/\/:\/tracks\/1492\/relationship\/albums","related":"http:\/\/:\/tracks\/1492\/albums"}},"media-types":{"links":{"self":"http:\/\/:\/tracks\/1492\/relationship\/media-types","related":"http:\/\/:\/tracks\/1492\/media-types"}},"genres":{"links":{"self":"http:\/\/:\/tracks\/1492\/relationship\/genres","related":"http:\/\/:\/tracks\/1492\/genres"}},"playlists":{"links":{"self":"http:\/\/:\/tracks\/1492\/relationship\/playlists","related":"http:\/\/:\/tracks\/1492\/playlists"}}}},{"id":"1874","type":"tracks","attributes":{"name":"Fight Fire With Fire"},"relationships":{"albums":{"links":{"self":"http:\/\/:\/tracks\/1874\/relationship\/albums","related":"http:\/\/:\/tracks\/1874\/albums"}},"media-types":{"links":{"self":"http:\/\/:\/tracks\/1874\/relationship\/media-types","related":"http:\/\/:\/tracks\/1874\/media-types"}},"genres":{"links":{"self":"http:\/\/:\/tracks\/1874\/relationship\/genres","related":"http:\/\/:\/tracks\/1874\/genres"}},"playlists":{"links":{"self":"http:\/\/:\/tracks\/1874\/relationship\/playlists","related":"http:\/\/:\/tracks\/1874\/playlists"}}}},{"id":"1876","type":"tracks","attributes":{"name":"For Whom The Bell Tolls"},"relationships":{"albums":{"links":{"self":"http:\/\/:\/tracks\/1876\/relationship\/albums","related":"http:\/\/:\/tracks\/1876\/albums"}},"media-types":{"links":{"self":"http:\/\/:\/tracks\/1876\/relationship\/media-types","related":"http:\/\/:\/tracks\/1876\/media-types"}},"genres":{"links":{"self":"http:\/\/:\/tracks\/1876\/relationship\/genres","related":"http:\/\/:\/tracks\/1876\/genres"}},"playlists":{"links":{"self":"http:\/\/:\/tracks\/1876\/relationship\/playlists","related":"http:\/\/:\/tracks\/1876\/playlists"}}}},{"id":"1877","type":"tracks","attributes":{"name":"Fade To Black"},"relationships":{"albums":{"links":{"self":"http:\/\/:\/tracks\/1877\/relationship\/albums","related":"http:\/\/:\/tracks\/1877\/albums"}},"media-types":{"links":{"self":"http:\/\/:\/tracks\/1877\/relationship\/media-types","related":"http:\/\/:\/tracks\/1877\/media-types"}},"genres":{"links":{"self":"http:\/\/:\/tracks\/1877\/relationship\/genres","related":"http:\/\/:\/tracks\/1877\/genres"}},"playlists":{"links":{"self":"http:\/\/:\/tracks\/1877\/relationship\/playlists","related":"http:\/\/:\/tracks\/1877\/playlists"}}}},{"id":"2003","type":"tracks","attributes":{"name":"Smells Like Teen Spirit"},"relationships":{"albums":{"links":{"self":"http:\/\/:\/tracks\/2003\/relationship\/albums","related":"http:\/\/:\/tracks\/2003\/albums"}},"media-types":{"links":{"self":"http:\/\/:\/tracks\/2003\/relationship\/media-types","related":"http:\/\/:\/tracks\/2003\/media-types"}},"genres":{"links":{"self":"http:\/\/:\/tracks\/2003\/relationship\/genres","related":"http:\/\/:\/tracks\/2003\/genres"}},"playlists":{"links":{"self":"http:\/\/:\/tracks\/2003\/relationship\/playlists","related":"http:\/\/:\/tracks\/2003\/playlists"}}}},{"id":"2005","type":"tracks","attributes":{"name":"Come As You Are"},"relationships":{"albums":{"links":{"self":"http:\/\/:\/tracks\/2005\/relationship\/albums","related":"http:\/\/:\/tracks\/2005\/albums"}},"media-types":{"links":{"self":"http:\/\/:\/tracks\/2005\/relationship\/media-types","related":"http:\/\/:\/tracks\/2005\/media-types"}},"genres":{"links":{"self":"http:\/\/:\/tracks\/2005\/relationship\/genres","related":"http:\/\/:\/tracks\/2005\/genres"}},"playlists":{"links":{"self":"http:\/\/:\/tracks\/2005\/relationship\/playlists","related":"http:\/\/:\/tracks\/2005\/playlists"}}}},{"id":"2008","type":"tracks","attributes":{"name":"Polly"},"relationships":{"albums":{"links":{"self":"http:\/\/:\/tracks\/2008\/relationship\/albums","related":"http:\/\/:\/tracks\/2008\/albums"}},"media-types":{"links":{"self":"http:\/\/:\/tracks\/2008\/relationship\/media-types","related":"http:\/\/:\/tracks\/2008\/media-types"}},"genres":{"links":{"self":"http:\/\/:\/tracks\/2008\/relationship\/genres","related":"http:\/\/:\/tracks\/2008\/genres"}},"playlists":{"links":{"self":"http:\/\/:\/tracks\/2008\/relationship\/playlists","related":"http:\/\/:\/tracks\/2008\/playlists"}}}},{"id":"2013","type":"tracks","attributes":{"name":"On A Plain"},"relationships":{"albums":{"links":{"self":"http:\/\/:\/tracks\/2013\/relationship\/albums","related":"http:\/\/:\/tracks\/2013\/albums"}},"media-types":{"links":{"self":"http:\/\/:\/tracks\/2013\/relationship\/media-types","related":"http:\/\/:\/tracks\/2013\/media-types"}},"genres":{"links":{"self":"http:\/\/:\/tracks\/2013\/relationship\/genres","related":"http:\/\/:\/tracks\/2013\/genres"}},"playlists":{"links":{"self":"http:\/\/:\/tracks\/2013\/relationship\/playlists","related":"http:\/\/:\/tracks\/2013\/playlists"}}}},{"id":"2014","type":"tracks","attributes":{"name":"Something In The Way"},"relationships":{"albums":{"links":{"self":"http:\/\/:\/tracks\/2014\/relationship\/albums","related":"http:\/\/:\/tracks\/2014\/albums"}},"media-types":{"links":{"self":"http:\/\/:\/tracks\/2014\/relationship\/media-types","related":"http:\/\/:\/tracks\/2014\/media-types"}},"genres":{"links":{"self":"http:\/\/:\/tracks\/2014\/relationship\/genres","related":"http:\/\/:\/tracks\/2014\/genres"}},"playlists":{"links":{"self":"http:\/\/:\/tracks\/2014\/relationship\/playlists","related":"http:\/\/:\/tracks\/2014\/playlists"}}}},{"id":"2333","type":"tracks","attributes":{"name":"It\'s The End Of The World As We Know It (And I Feel Fine)"},"relationships":{"albums":{"links":{"self":"http:\/\/:\/tracks\/2333\/relationship\/albums","related":"http:\/\/:\/tracks\/2333\/albums"}},"media-types":{"links":{"self":"http:\/\/:\/tracks\/2333\/relationship\/media-types","related":"http:\/\/:\/tracks\/2333\/media-types"}},"genres":{"links":{"self":"http:\/\/:\/tracks\/2333\/relationship\/genres","related":"http:\/\/:\/tracks\/2333\/genres"}},"playlists":{"links":{"self":"http:\/\/:\/tracks\/2333\/relationship\/playlists","related":"http:\/\/:\/tracks\/2333\/playlists"}}}},{"id":"2396","type":"tracks","attributes":{"name":"Californication"},"relationships":{"albums":{"links":{"self":"http:\/\/:\/tracks\/2396\/relationship\/albums","related":"http:\/\/:\/tracks\/2396\/albums"}},"media-types":{"links":{"self":"http:\/\/:\/tracks\/2396\/relationship\/media-types","related":"http:\/\/:\/tracks\/2396\/media-types"}},"genres":{"links":{"self":"http:\/\/:\/tracks\/2396\/relationship\/genres","related":"http:\/\/:\/tracks\/2396\/genres"}},"playlists":{"links":{"self":"http:\/\/:\/tracks\/2396\/relationship\/playlists","related":"http:\/\/:\/tracks\/2396\/playlists"}}}},{"id":"2957","type":"tracks","attributes":{"name":"Walk To The Water"},"relationships":{"albums":{"links":{"self":"http:\/\/:\/tracks\/2957\/relationship\/albums","related":"http:\/\/:\/tracks\/2957\/albums"}},"media-types":{"links":{"self":"http:\/\/:\/tracks\/2957\/relationship\/media-types","related":"http:\/\/:\/tracks\/2957\/media-types"}},"genres":{"links":{"self":"http:\/\/:\/tracks\/2957\/relationship\/genres","related":"http:\/\/:\/tracks\/2957\/genres"}},"playlists":{"links":{"self":"http:\/\/:\/tracks\/2957\/relationship\/playlists","related":"http:\/\/:\/tracks\/2957\/playlists"}}}},{"id":"3065","type":"tracks","attributes":{"name":"Ain\'t Talkin\' \'bout Love"},"relationships":{"albums":{"links":{"self":"http:\/\/:\/tracks\/3065\/relationship\/albums","related":"http:\/\/:\/tracks\/3065\/albums"}},"media-types":{"links":{"self":"http:\/\/:\/tracks\/3065\/relationship\/media-types","related":"http:\/\/:\/tracks\/3065\/media-types"}},"genres":{"links":{"self":"http:\/\/:\/tracks\/3065\/relationship\/genres","related":"http:\/\/:\/tracks\/3065\/genres"}},"playlists":{"links":{"self":"http:\/\/:\/tracks\/3065\/relationship\/playlists","related":"http:\/\/:\/tracks\/3065\/playlists"}}}}],"links":{"self":"http:\/\/:\/tracks?page[size]=25&page[number]=1","first":"http:\/\/:\/tracks?page[size]=25&page[number]=1","last":"http:\/\/:\/tracks?page[size]=25&page[number]=2","next":"http:\/\/:\/tracks?page[size]=25&page[number]=2"},"meta":{"count":26,"pages":2}}';

        $jad->jsonApiResult();
        $this->expectOutputString($expected);
    }

    public function testGetTracks()
    {
        $_SERVER['REQUEST_URI']  = '/tracks/15';

        $mapper = new AnnotationsMapper(Manager::getInstance()->getEm());
        $jad = new Jad($mapper);
        $expected = '{"data":{"id":"15","type":"tracks","attributes":{"name":"Go Down","composer":"AC\/DC","price":"0.99"},"relationships":{"albums":{"links":{"self":"http:\/\/:\/tracks\/15\/relationship\/albums","related":"http:\/\/:\/tracks\/15\/albums"}},"media-types":{"links":{"self":"http:\/\/:\/tracks\/15\/relationship\/media-types","related":"http:\/\/:\/tracks\/15\/media-types"}},"genres":{"links":{"self":"http:\/\/:\/tracks\/15\/relationship\/genres","related":"http:\/\/:\/tracks\/15\/genres"}},"playlists":{"links":{"self":"http:\/\/:\/tracks\/15\/relationship\/playlists","related":"http:\/\/:\/tracks\/15\/playlists"}}}},"links":{"self":"http:\/\/:\/tracks\/15"}}';

        $jad->jsonApiResult();
        $this->expectOutputString($expected);
    }

    public function testGetRelationshipFull()
    {
        $_SERVER['REQUEST_URI']  = '/tracks/15/playlists';

        $mapper = new AnnotationsMapper(Manager::getInstance()->getEm());
        $jad = new Jad($mapper);

        $expected = '{"data":[{"id":"1","type":"playlists","attributes":{"name":"All tracks"}},{"id":"2","type":"playlists","attributes":{"name":"Some tracks"}}],"links":{"self":"http:\/\/:\/tracks\/15\/playlists"}}';
        $jad->jsonApiResult();
        $this->expectOutputString($expected);
    }

    public function testGetRelationshipList()
    {
        $_SERVER['REQUEST_URI']  = '/tracks/15/relationships/playlists';

        $mapper = new AnnotationsMapper(Manager::getInstance()->getEm());
        $jad = new Jad($mapper);

        $expected = '{"data":[{"id":"1","type":"playlists"},{"id":"2","type":"playlists"}],"links":{"self":"http:\/\/:\/tracks\/15\/relationships\/playlists"}}';
        $jad->jsonApiResult();
        $this->expectOutputString($expected);
    }

    public function testCreateRelationship()
    {
        Configure::getInstance()->setConfig('testMode', true);
        $_SERVER['REQUEST_URI']  = '/playlists';
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

        $expected = '{"data":{"id":"4","type":"playlists","attributes":{"name":"New Playlist"},"relationships":{"tracks":{"links":{"self":"http:\/\/:\/playlists\/4\/relationship\/tracks","related":"http:\/\/:\/playlists\/4\/tracks"}}}},"links":{"self":"http:\/\/:\/playlists"}}';
        $jad->jsonApiResult();
        $this->expectOutputString($expected);
    }

    /**
     * @depends testCreateRelationship
     */
    public function testCreateRelationshipVerify()
    {
        $_SERVER['REQUEST_URI']  = '/playlists/4/relationships/tracks';
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $mapper = new AnnotationsMapper(Manager::getInstance()->getEm());
        $jad = new Jad($mapper);

        $expected = '{"data":[{"id":"15","type":"tracks"},{"id":"43","type":"tracks"},{"id":"77","type":"tracks"},{"id":"117","type":"tracks"},{"id":"351","type":"tracks"}],"links":{"self":"http:\/\/:\/playlists\/4\/relationships\/tracks"}}';
        $jad->jsonApiResult();
        $this->expectOutputString($expected);
    }

    /**
     * @depends testCreateRelationship
     */
    public function testUpdateAddRelationship()
    {
        Configure::getInstance()->setConfig('testMode', true);
        $_SERVER['REQUEST_URI']  = '/playlists/2';
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

        $expected = '{"data":{"id":"2","type":"playlists","attributes":{"name":"Some tracks"},"relationships":{"tracks":{"links":{"self":"http:\/\/:\/playlists\/2\/relationship\/tracks","related":"http:\/\/:\/playlists\/2\/tracks"}}}},"links":{"self":"http:\/\/:\/playlists\/2"}}';
        $jad->jsonApiResult();
        $this->expectOutputString($expected);
    }
}