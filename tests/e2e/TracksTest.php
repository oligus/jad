<?php

namespace Jad\E2E;

use Jad\Jad;
use Jad\Configure;
use Jad\Tests\TestCase;
use Jad\Database\Manager;
use Jad\Map\AnnotationsMapper;

use PHPUnit\DbUnit\TestCaseTrait;
use PHPUnit\DbUnit\DataSet\CsvDataSet;

class TracksTest extends TestCase
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
        $dataSet->addTable('tracks', dirname(__DIR__ ) . '/fixtures/tracks.csv');
        return $dataSet;
    }

    public function xtestFetchTrack()
    {
        $_SERVER['REQUEST_URI']  = '/tracks/1874';

        $mapper = new AnnotationsMapper(Manager::getInstance()->getEm());
        $jad = new Jad($mapper);

        $expected = '{"data":{"id":"1874","type":"tracks","attributes":{"name":"Fight Fire With Fire","composer":"Metallica","milliseconds":285753,"price":"0.99"},"relationships":{"albums":{"links":{"self":"http:\/\/:\/tracks\/1874\/relationship\/albums","related":"http:\/\/:\/tracks\/1874\/albums"}},"media-types":{"links":{"self":"http:\/\/:\/tracks\/1874\/relationship\/media-types","related":"http:\/\/:\/tracks\/1874\/media-types"}},"genres":{"links":{"self":"http:\/\/:\/tracks\/1874\/relationship\/genres","related":"http:\/\/:\/tracks\/1874\/genres"}},"playlists":{"links":{"self":"http:\/\/:\/tracks\/1874\/relationship\/playlists","related":"http:\/\/:\/tracks\/1874\/playlists"}}}},"links":{"self":"http:\/\/:\/tracks\/1874"}}';
        $jad->jsonApiResult();
        $this->expectOutputString($expected);
    }

   public function testCreateTrack()
   {
       Configure::getInstance()->setConfig('test_mode', true);
       $_SERVER['REQUEST_URI']  = '/tracks';
       $_SERVER['REQUEST_METHOD'] = 'POST';

       $input = new \stdClass();
       $input->data = new \stdClass();
       $input->data->type = 'tracks';
       $input->data->attributes = new \stdClass();
       $input->data->attributes->name = 'New Track';
       $input->data->attributes->composer = 'My Composer';
       $input->data->attributes->price = 1.99;
       $input->data->attributes->milliseconds = 331180;
       $input->data->relationships = new \stdClass();

       $input->data->relationships->albums = new \stdClass();
       $input->data->relationships->albums->data = new \stdClass();
       $input->data->relationships->albums->data->type = 'albums';
       $input->data->relationships->albums->data->id = 2;

       $input->data->relationships->genres = new \stdClass();
       $input->data->relationships->genres->data = new \stdClass();
       $input->data->relationships->genres->data->type = 'genres';
       $input->data->relationships->genres->data->id = 2;

       $input->data->relationships->mediaTypes = new \stdClass();
       $input->data->relationships->mediaTypes->data = new \stdClass();
       $input->data->relationships->mediaTypes->data->type = 'media-types';
       $input->data->relationships->mediaTypes->data->id = 2;

       $_POST = ['input' => json_encode($input)];

       $mapper = new AnnotationsMapper(Manager::getInstance()->getEm());
       $jad = new Jad($mapper);

       $jad->jsonApiResult();
       $this->expectOutputRegex('/tracks.+?name.:.New\sTrack/');
   }

    public function testUpdateTrack()
    {
        Configure::getInstance()->setConfig('test_mode', true);
        $_SERVER['REQUEST_URI'] = '/tracks/43';
        $_SERVER['REQUEST_METHOD'] = 'PATCH';

        $input = new \stdClass();
        $input->data = new \stdClass();
        $input->data->type = 'tracks';
        $input->data->relationships = new \stdClass();
        $input->data->relationships->mediaTypes = new \stdClass();
        $input->data->relationships->mediaTypes->data = new \stdClass();
        $input->data->relationships->mediaTypes->data->type = 'media-types';
        $input->data->relationships->mediaTypes->data->id = 2;

        $_POST = ['input' => json_encode($input)];

        $mapper = new AnnotationsMapper(Manager::getInstance()->getEm());
        $jad = new Jad($mapper);

        $expected = '{"data":{"id":"43","type":"tracks","attributes":{"name":"Forgiven","composer":"Alanis Morissette & Glenn Ballard","milliseconds":300355,"price":"0.99"},"relationships":{"albums":{"links":{"self":"http:\/\/:\/tracks\/43\/relationship\/albums","related":"http:\/\/:\/tracks\/43\/albums"}},"media-types":{"links":{"self":"http:\/\/:\/tracks\/43\/relationship\/media-types","related":"http:\/\/:\/tracks\/43\/media-types"}},"genres":{"links":{"self":"http:\/\/:\/tracks\/43\/relationship\/genres","related":"http:\/\/:\/tracks\/43\/genres"}},"playlists":{"links":{"self":"http:\/\/:\/tracks\/43\/relationship\/playlists","related":"http:\/\/:\/tracks\/43\/playlists"}}}},"links":{"self":"http:\/\/:\/tracks\/43"}}';
        $jad->jsonApiResult();
        $this->expectOutputString($expected);
    }
}