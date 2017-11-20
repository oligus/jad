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

        $expected = '{"data":[{"id":"1","type":"playlists","attributes":{"name":"All tracks"},"relationships":{"tracks":{"links":{"self":"http:\/\/:\/playlists\/1\/relationship\/tracks","related":"http:\/\/:\/playlists\/1\/tracks"}}}},{"id":"2","type":"playlists","attributes":{"name":"Some tracks"},"relationships":{"tracks":{"links":{"self":"http:\/\/:\/playlists\/2\/relationship\/tracks","related":"http:\/\/:\/playlists\/2\/tracks"}}}},{"id":"3","type":"playlists","attributes":{"name":"No tracks"},"relationships":{"tracks":{"links":{"self":"http:\/\/:\/playlists\/3\/relationship\/tracks","related":"http:\/\/:\/playlists\/3\/tracks"}}}}],"links":{"self":"http:\/\/:\/playlists"}}';
        $jad->jsonApiResult();
        $this->expectOutputString($expected);
    }

    public function testFetchCollectionFields()
    {
        $_SERVER = ['REQUEST_URI' => '/tracks'];
        $_GET = ['page' => ['offset' => 0, 'limit' => 5], 'fields' => [ 'tracks' => 'name']];

        $mapper = new AnnotationsMapper(Manager::getInstance()->getEm());
        $jad = new Jad($mapper);
        $expected = '{"data":[{"id":"15","type":"tracks","attributes":{"name":"Go Down"},"relationships":{"album":{"links":{"self":"http:\/\/:\/tracks\/15\/relationship\/album","related":"http:\/\/:\/tracks\/15\/album"}},"media-type":{"links":{"self":"http:\/\/:\/tracks\/15\/relationship\/media-type","related":"http:\/\/:\/tracks\/15\/media-type"}},"genre":{"links":{"self":"http:\/\/:\/tracks\/15\/relationship\/genre","related":"http:\/\/:\/tracks\/15\/genre"}},"playlists":{"links":{"self":"http:\/\/:\/tracks\/15\/relationship\/playlists","related":"http:\/\/:\/tracks\/15\/playlists"}}}},{"id":"43","type":"tracks","attributes":{"name":"Forgiven"},"relationships":{"album":{"links":{"self":"http:\/\/:\/tracks\/43\/relationship\/album","related":"http:\/\/:\/tracks\/43\/album"}},"media-type":{"links":{"self":"http:\/\/:\/tracks\/43\/relationship\/media-type","related":"http:\/\/:\/tracks\/43\/media-type"}},"genre":{"links":{"self":"http:\/\/:\/tracks\/43\/relationship\/genre","related":"http:\/\/:\/tracks\/43\/genre"}},"playlists":{"links":{"self":"http:\/\/:\/tracks\/43\/relationship\/playlists","related":"http:\/\/:\/tracks\/43\/playlists"}}}},{"id":"77","type":"tracks","attributes":{"name":"Enter Sandman"},"relationships":{"album":{"links":{"self":"http:\/\/:\/tracks\/77\/relationship\/album","related":"http:\/\/:\/tracks\/77\/album"}},"media-type":{"links":{"self":"http:\/\/:\/tracks\/77\/relationship\/media-type","related":"http:\/\/:\/tracks\/77\/media-type"}},"genre":{"links":{"self":"http:\/\/:\/tracks\/77\/relationship\/genre","related":"http:\/\/:\/tracks\/77\/genre"}},"playlists":{"links":{"self":"http:\/\/:\/tracks\/77\/relationship\/playlists","related":"http:\/\/:\/tracks\/77\/playlists"}}}},{"id":"117","type":"tracks","attributes":{"name":"Rock \'N\' Roll Music"},"relationships":{"album":{"links":{"self":"http:\/\/:\/tracks\/117\/relationship\/album","related":"http:\/\/:\/tracks\/117\/album"}},"media-type":{"links":{"self":"http:\/\/:\/tracks\/117\/relationship\/media-type","related":"http:\/\/:\/tracks\/117\/media-type"}},"genre":{"links":{"self":"http:\/\/:\/tracks\/117\/relationship\/genre","related":"http:\/\/:\/tracks\/117\/genre"}},"playlists":{"links":{"self":"http:\/\/:\/tracks\/117\/relationship\/playlists","related":"http:\/\/:\/tracks\/117\/playlists"}}}},{"id":"351","type":"tracks","attributes":{"name":"Debra Kadabra"},"relationships":{"album":{"links":{"self":"http:\/\/:\/tracks\/351\/relationship\/album","related":"http:\/\/:\/tracks\/351\/album"}},"media-type":{"links":{"self":"http:\/\/:\/tracks\/351\/relationship\/media-type","related":"http:\/\/:\/tracks\/351\/media-type"}},"genre":{"links":{"self":"http:\/\/:\/tracks\/351\/relationship\/genre","related":"http:\/\/:\/tracks\/351\/genre"}},"playlists":{"links":{"self":"http:\/\/:\/tracks\/351\/relationship\/playlists","related":"http:\/\/:\/tracks\/351\/playlists"}}}},{"id":"422","type":"tracks","attributes":{"name":"I Want It All"},"relationships":{"album":{"links":{"self":"http:\/\/:\/tracks\/422\/relationship\/album","related":"http:\/\/:\/tracks\/422\/album"}},"media-type":{"links":{"self":"http:\/\/:\/tracks\/422\/relationship\/media-type","related":"http:\/\/:\/tracks\/422\/media-type"}},"genre":{"links":{"self":"http:\/\/:\/tracks\/422\/relationship\/genre","related":"http:\/\/:\/tracks\/422\/genre"}},"playlists":{"links":{"self":"http:\/\/:\/tracks\/422\/relationship\/playlists","related":"http:\/\/:\/tracks\/422\/playlists"}}}},{"id":"603","type":"tracks","attributes":{"name":"Bye Bye Blackbird"},"relationships":{"album":{"links":{"self":"http:\/\/:\/tracks\/603\/relationship\/album","related":"http:\/\/:\/tracks\/603\/album"}},"media-type":{"links":{"self":"http:\/\/:\/tracks\/603\/relationship\/media-type","related":"http:\/\/:\/tracks\/603\/media-type"}},"genre":{"links":{"self":"http:\/\/:\/tracks\/603\/relationship\/genre","related":"http:\/\/:\/tracks\/603\/genre"}},"playlists":{"links":{"self":"http:\/\/:\/tracks\/603\/relationship\/playlists","related":"http:\/\/:\/tracks\/603\/playlists"}}}},{"id":"645","type":"tracks","attributes":{"name":"Swedish Schnapps"},"relationships":{"album":{"links":{"self":"http:\/\/:\/tracks\/645\/relationship\/album","related":"http:\/\/:\/tracks\/645\/album"}},"media-type":{"links":{"self":"http:\/\/:\/tracks\/645\/relationship\/media-type","related":"http:\/\/:\/tracks\/645\/media-type"}},"genre":{"links":{"self":"http:\/\/:\/tracks\/645\/relationship\/genre","related":"http:\/\/:\/tracks\/645\/genre"}},"playlists":{"links":{"self":"http:\/\/:\/tracks\/645\/relationship\/playlists","related":"http:\/\/:\/tracks\/645\/playlists"}}}},{"id":"678","type":"tracks","attributes":{"name":"Bad Moon Rising"},"relationships":{"album":{"links":{"self":"http:\/\/:\/tracks\/678\/relationship\/album","related":"http:\/\/:\/tracks\/678\/album"}},"media-type":{"links":{"self":"http:\/\/:\/tracks\/678\/relationship\/media-type","related":"http:\/\/:\/tracks\/678\/media-type"}},"genre":{"links":{"self":"http:\/\/:\/tracks\/678\/relationship\/genre","related":"http:\/\/:\/tracks\/678\/genre"}},"playlists":{"links":{"self":"http:\/\/:\/tracks\/678\/relationship\/playlists","related":"http:\/\/:\/tracks\/678\/playlists"}}}},{"id":"1139","type":"tracks","attributes":{"name":"Give Me Novacaine"},"relationships":{"album":{"links":{"self":"http:\/\/:\/tracks\/1139\/relationship\/album","related":"http:\/\/:\/tracks\/1139\/album"}},"media-type":{"links":{"self":"http:\/\/:\/tracks\/1139\/relationship\/media-type","related":"http:\/\/:\/tracks\/1139\/media-type"}},"genre":{"links":{"self":"http:\/\/:\/tracks\/1139\/relationship\/genre","related":"http:\/\/:\/tracks\/1139\/genre"}},"playlists":{"links":{"self":"http:\/\/:\/tracks\/1139\/relationship\/playlists","related":"http:\/\/:\/tracks\/1139\/playlists"}}}},{"id":"1246","type":"tracks","attributes":{"name":"Rainmaker"},"relationships":{"album":{"links":{"self":"http:\/\/:\/tracks\/1246\/relationship\/album","related":"http:\/\/:\/tracks\/1246\/album"}},"media-type":{"links":{"self":"http:\/\/:\/tracks\/1246\/relationship\/media-type","related":"http:\/\/:\/tracks\/1246\/media-type"}},"genre":{"links":{"self":"http:\/\/:\/tracks\/1246\/relationship\/genre","related":"http:\/\/:\/tracks\/1246\/genre"}},"playlists":{"links":{"self":"http:\/\/:\/tracks\/1246\/relationship\/playlists","related":"http:\/\/:\/tracks\/1246\/playlists"}}}},{"id":"1490","type":"tracks","attributes":{"name":"Hey Joe"},"relationships":{"album":{"links":{"self":"http:\/\/:\/tracks\/1490\/relationship\/album","related":"http:\/\/:\/tracks\/1490\/album"}},"media-type":{"links":{"self":"http:\/\/:\/tracks\/1490\/relationship\/media-type","related":"http:\/\/:\/tracks\/1490\/media-type"}},"genre":{"links":{"self":"http:\/\/:\/tracks\/1490\/relationship\/genre","related":"http:\/\/:\/tracks\/1490\/genre"}},"playlists":{"links":{"self":"http:\/\/:\/tracks\/1490\/relationship\/playlists","related":"http:\/\/:\/tracks\/1490\/playlists"}}}},{"id":"1492","type":"tracks","attributes":{"name":"Purple Haze"},"relationships":{"album":{"links":{"self":"http:\/\/:\/tracks\/1492\/relationship\/album","related":"http:\/\/:\/tracks\/1492\/album"}},"media-type":{"links":{"self":"http:\/\/:\/tracks\/1492\/relationship\/media-type","related":"http:\/\/:\/tracks\/1492\/media-type"}},"genre":{"links":{"self":"http:\/\/:\/tracks\/1492\/relationship\/genre","related":"http:\/\/:\/tracks\/1492\/genre"}},"playlists":{"links":{"self":"http:\/\/:\/tracks\/1492\/relationship\/playlists","related":"http:\/\/:\/tracks\/1492\/playlists"}}}},{"id":"1874","type":"tracks","attributes":{"name":"Fight Fire With Fire"},"relationships":{"album":{"links":{"self":"http:\/\/:\/tracks\/1874\/relationship\/album","related":"http:\/\/:\/tracks\/1874\/album"}},"media-type":{"links":{"self":"http:\/\/:\/tracks\/1874\/relationship\/media-type","related":"http:\/\/:\/tracks\/1874\/media-type"}},"genre":{"links":{"self":"http:\/\/:\/tracks\/1874\/relationship\/genre","related":"http:\/\/:\/tracks\/1874\/genre"}},"playlists":{"links":{"self":"http:\/\/:\/tracks\/1874\/relationship\/playlists","related":"http:\/\/:\/tracks\/1874\/playlists"}}}},{"id":"1876","type":"tracks","attributes":{"name":"For Whom The Bell Tolls"},"relationships":{"album":{"links":{"self":"http:\/\/:\/tracks\/1876\/relationship\/album","related":"http:\/\/:\/tracks\/1876\/album"}},"media-type":{"links":{"self":"http:\/\/:\/tracks\/1876\/relationship\/media-type","related":"http:\/\/:\/tracks\/1876\/media-type"}},"genre":{"links":{"self":"http:\/\/:\/tracks\/1876\/relationship\/genre","related":"http:\/\/:\/tracks\/1876\/genre"}},"playlists":{"links":{"self":"http:\/\/:\/tracks\/1876\/relationship\/playlists","related":"http:\/\/:\/tracks\/1876\/playlists"}}}},{"id":"1877","type":"tracks","attributes":{"name":"Fade To Black"},"relationships":{"album":{"links":{"self":"http:\/\/:\/tracks\/1877\/relationship\/album","related":"http:\/\/:\/tracks\/1877\/album"}},"media-type":{"links":{"self":"http:\/\/:\/tracks\/1877\/relationship\/media-type","related":"http:\/\/:\/tracks\/1877\/media-type"}},"genre":{"links":{"self":"http:\/\/:\/tracks\/1877\/relationship\/genre","related":"http:\/\/:\/tracks\/1877\/genre"}},"playlists":{"links":{"self":"http:\/\/:\/tracks\/1877\/relationship\/playlists","related":"http:\/\/:\/tracks\/1877\/playlists"}}}},{"id":"2003","type":"tracks","attributes":{"name":"Smells Like Teen Spirit"},"relationships":{"album":{"links":{"self":"http:\/\/:\/tracks\/2003\/relationship\/album","related":"http:\/\/:\/tracks\/2003\/album"}},"media-type":{"links":{"self":"http:\/\/:\/tracks\/2003\/relationship\/media-type","related":"http:\/\/:\/tracks\/2003\/media-type"}},"genre":{"links":{"self":"http:\/\/:\/tracks\/2003\/relationship\/genre","related":"http:\/\/:\/tracks\/2003\/genre"}},"playlists":{"links":{"self":"http:\/\/:\/tracks\/2003\/relationship\/playlists","related":"http:\/\/:\/tracks\/2003\/playlists"}}}},{"id":"2005","type":"tracks","attributes":{"name":"Come As You Are"},"relationships":{"album":{"links":{"self":"http:\/\/:\/tracks\/2005\/relationship\/album","related":"http:\/\/:\/tracks\/2005\/album"}},"media-type":{"links":{"self":"http:\/\/:\/tracks\/2005\/relationship\/media-type","related":"http:\/\/:\/tracks\/2005\/media-type"}},"genre":{"links":{"self":"http:\/\/:\/tracks\/2005\/relationship\/genre","related":"http:\/\/:\/tracks\/2005\/genre"}},"playlists":{"links":{"self":"http:\/\/:\/tracks\/2005\/relationship\/playlists","related":"http:\/\/:\/tracks\/2005\/playlists"}}}},{"id":"2008","type":"tracks","attributes":{"name":"Polly"},"relationships":{"album":{"links":{"self":"http:\/\/:\/tracks\/2008\/relationship\/album","related":"http:\/\/:\/tracks\/2008\/album"}},"media-type":{"links":{"self":"http:\/\/:\/tracks\/2008\/relationship\/media-type","related":"http:\/\/:\/tracks\/2008\/media-type"}},"genre":{"links":{"self":"http:\/\/:\/tracks\/2008\/relationship\/genre","related":"http:\/\/:\/tracks\/2008\/genre"}},"playlists":{"links":{"self":"http:\/\/:\/tracks\/2008\/relationship\/playlists","related":"http:\/\/:\/tracks\/2008\/playlists"}}}},{"id":"2013","type":"tracks","attributes":{"name":"On A Plain"},"relationships":{"album":{"links":{"self":"http:\/\/:\/tracks\/2013\/relationship\/album","related":"http:\/\/:\/tracks\/2013\/album"}},"media-type":{"links":{"self":"http:\/\/:\/tracks\/2013\/relationship\/media-type","related":"http:\/\/:\/tracks\/2013\/media-type"}},"genre":{"links":{"self":"http:\/\/:\/tracks\/2013\/relationship\/genre","related":"http:\/\/:\/tracks\/2013\/genre"}},"playlists":{"links":{"self":"http:\/\/:\/tracks\/2013\/relationship\/playlists","related":"http:\/\/:\/tracks\/2013\/playlists"}}}},{"id":"2014","type":"tracks","attributes":{"name":"Something In The Way"},"relationships":{"album":{"links":{"self":"http:\/\/:\/tracks\/2014\/relationship\/album","related":"http:\/\/:\/tracks\/2014\/album"}},"media-type":{"links":{"self":"http:\/\/:\/tracks\/2014\/relationship\/media-type","related":"http:\/\/:\/tracks\/2014\/media-type"}},"genre":{"links":{"self":"http:\/\/:\/tracks\/2014\/relationship\/genre","related":"http:\/\/:\/tracks\/2014\/genre"}},"playlists":{"links":{"self":"http:\/\/:\/tracks\/2014\/relationship\/playlists","related":"http:\/\/:\/tracks\/2014\/playlists"}}}},{"id":"2333","type":"tracks","attributes":{"name":"It\'s The End Of The World As We Know It (And I Feel Fine)"},"relationships":{"album":{"links":{"self":"http:\/\/:\/tracks\/2333\/relationship\/album","related":"http:\/\/:\/tracks\/2333\/album"}},"media-type":{"links":{"self":"http:\/\/:\/tracks\/2333\/relationship\/media-type","related":"http:\/\/:\/tracks\/2333\/media-type"}},"genre":{"links":{"self":"http:\/\/:\/tracks\/2333\/relationship\/genre","related":"http:\/\/:\/tracks\/2333\/genre"}},"playlists":{"links":{"self":"http:\/\/:\/tracks\/2333\/relationship\/playlists","related":"http:\/\/:\/tracks\/2333\/playlists"}}}},{"id":"2396","type":"tracks","attributes":{"name":"Californication"},"relationships":{"album":{"links":{"self":"http:\/\/:\/tracks\/2396\/relationship\/album","related":"http:\/\/:\/tracks\/2396\/album"}},"media-type":{"links":{"self":"http:\/\/:\/tracks\/2396\/relationship\/media-type","related":"http:\/\/:\/tracks\/2396\/media-type"}},"genre":{"links":{"self":"http:\/\/:\/tracks\/2396\/relationship\/genre","related":"http:\/\/:\/tracks\/2396\/genre"}},"playlists":{"links":{"self":"http:\/\/:\/tracks\/2396\/relationship\/playlists","related":"http:\/\/:\/tracks\/2396\/playlists"}}}},{"id":"2957","type":"tracks","attributes":{"name":"Walk To The Water"},"relationships":{"album":{"links":{"self":"http:\/\/:\/tracks\/2957\/relationship\/album","related":"http:\/\/:\/tracks\/2957\/album"}},"media-type":{"links":{"self":"http:\/\/:\/tracks\/2957\/relationship\/media-type","related":"http:\/\/:\/tracks\/2957\/media-type"}},"genre":{"links":{"self":"http:\/\/:\/tracks\/2957\/relationship\/genre","related":"http:\/\/:\/tracks\/2957\/genre"}},"playlists":{"links":{"self":"http:\/\/:\/tracks\/2957\/relationship\/playlists","related":"http:\/\/:\/tracks\/2957\/playlists"}}}},{"id":"3065","type":"tracks","attributes":{"name":"Ain\'t Talkin\' \'bout Love"},"relationships":{"album":{"links":{"self":"http:\/\/:\/tracks\/3065\/relationship\/album","related":"http:\/\/:\/tracks\/3065\/album"}},"media-type":{"links":{"self":"http:\/\/:\/tracks\/3065\/relationship\/media-type","related":"http:\/\/:\/tracks\/3065\/media-type"}},"genre":{"links":{"self":"http:\/\/:\/tracks\/3065\/relationship\/genre","related":"http:\/\/:\/tracks\/3065\/genre"}},"playlists":{"links":{"self":"http:\/\/:\/tracks\/3065\/relationship\/playlists","related":"http:\/\/:\/tracks\/3065\/playlists"}}}}],"links":{"self":"http:\/\/:\/tracks?page[size]=25&page[number]=1","first":"http:\/\/:\/tracks?page[size]=25&page[number]=1","last":"http:\/\/:\/tracks?page[size]=25&page[number]=2","next":"http:\/\/:\/tracks?page[size]=25&page[number]=2"}}';

        $jad->jsonApiResult();
        $this->expectOutputString($expected);
    }

    public function testGetTracks()
    {
        $_SERVER = ['REQUEST_URI' => '/tracks/15'];

        $mapper = new AnnotationsMapper(Manager::getInstance()->getEm());
        $jad = new Jad($mapper);
        $expected = '{"data":[{"id":"15","type":"tracks","attributes":{"name":"Go Down","composer":"AC\/DC","price":"0.99"},"relationships":{"album":{"links":{"self":"http:\/\/:\/tracks\/15\/relationship\/album","related":"http:\/\/:\/tracks\/15\/album"}},"media-type":{"links":{"self":"http:\/\/:\/tracks\/15\/relationship\/media-type","related":"http:\/\/:\/tracks\/15\/media-type"}},"genre":{"links":{"self":"http:\/\/:\/tracks\/15\/relationship\/genre","related":"http:\/\/:\/tracks\/15\/genre"}},"playlists":{"links":{"self":"http:\/\/:\/tracks\/15\/relationship\/playlists","related":"http:\/\/:\/tracks\/15\/playlists"}}}}],"links":{"self":"http:\/\/:\/tracks\/15"}}';

        $jad->jsonApiResult();
        $this->expectOutputString($expected);
    }

    public function testGetRelationshipFull()
    {
        $_SERVER = ['REQUEST_URI' => '/tracks/15/playlists'];

        $mapper = new AnnotationsMapper(Manager::getInstance()->getEm());
        $jad = new Jad($mapper);

        $expected = '{"data":[{"id":"1","type":"playlists","attributes":{"name":"All tracks"}},{"id":"2","type":"playlists","attributes":{"name":"Some tracks"}}],"links":{"self":"http:\/\/:\/tracks\/15\/playlists"}}';
        $jad->jsonApiResult();
        $this->expectOutputString($expected);
    }

    public function testGetRelationshipList()
    {
        $_SERVER = ['REQUEST_URI' => '/tracks/15/relationships/playlists'];

        $mapper = new AnnotationsMapper(Manager::getInstance()->getEm());
        $jad = new Jad($mapper);

        $expected = '{"data":[{"id":"1","type":"playlists"},{"id":"2","type":"playlists"}],"links":{"self":"http:\/\/:\/tracks\/15\/relationships\/playlists"}}';
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

        $expected = '{"data":[{"id":"4","type":"playlists","attributes":{"name":"New Playlist"},"relationships":{"tracks":{"links":{"self":"http:\/\/:\/playlists\/4\/relationship\/tracks","related":"http:\/\/:\/playlists\/4\/tracks"}}}}],"links":{"self":"http:\/\/:\/playlists"}}';
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

        $expected = '{"data":[{"id":"4","type":"playlists","attributes":{"name":"New Playlist"},"relationships":{"tracks":{"links":{"self":"http:\/\/:\/playlists\/4\/relationship\/tracks","related":"http:\/\/:\/playlists\/4\/tracks"}}}}],"links":{"self":"http:\/\/:\/playlists\/4"}}';
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

        $expected = '{"data":[{"id":"15","type":"tracks"},{"id":"43","type":"tracks"},{"id":"77","type":"tracks"},{"id":"117","type":"tracks"},{"id":"351","type":"tracks"},{"id":"422","type":"tracks"}],"links":{"self":"http:\/\/:\/playlists\/4\/relationships\/tracks"}}';
        $jad->jsonApiResult();
        $this->expectOutputString($expected);
    }
}