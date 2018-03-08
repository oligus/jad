<?php

namespace Jad\E2E;

use Jad\Jad;
use Jad\Tests\DBTestCase;
use Jad\Database\Manager;
use Jad\Map\AnnotationsMapper;
use PHPUnit\DbUnit\DataSet\CsvDataSet;
use Spatie\Snapshots\MatchesSnapshots;

class PlaylistTest extends DBTestCase
{
    use MatchesSnapshots;

    public function testFetchCollection()
    {
        $_SERVER['REQUEST_URI']  = '/playlists';

        $mapper = new AnnotationsMapper(Manager::getInstance()->getEm());
        $jad = new Jad($mapper);

        ob_start();
        $jad->jsonApiResult();
        $output = ob_get_clean();

        $this->assertMatchesJsonSnapshot($output);
    }

    public function testFetchCollectionFields()
    {
        $_SERVER['REQUEST_URI']  = '/tracks';
        $_GET = ['page' => ['offset' => 0, 'limit' => 5], 'fields' => [ 'tracks' => 'name']];

        $mapper = new AnnotationsMapper(Manager::getInstance()->getEm());
        $jad = new Jad($mapper);

        ob_start();
        $jad->jsonApiResult();
        $output = ob_get_clean();

        $this->assertMatchesJsonSnapshot($output);
    }

    public function testGetTracks()
    {
        $_SERVER['REQUEST_URI']  = '/tracks/15';

        $mapper = new AnnotationsMapper(Manager::getInstance()->getEm());
        $jad = new Jad($mapper);

        ob_start();
        $jad->jsonApiResult();
        $output = ob_get_clean();

        $this->assertMatchesJsonSnapshot($output);
    }

    public function testGetRelationshipFull()
    {
        $_SERVER['REQUEST_URI']  = '/tracks/15/playlists';

        $mapper = new AnnotationsMapper(Manager::getInstance()->getEm());
        $jad = new Jad($mapper);

        ob_start();
        $jad->jsonApiResult();
        $output = ob_get_clean();

        $this->assertMatchesJsonSnapshot($output);
    }

    public function testGetRelationshipList()
    {
        $_SERVER['REQUEST_URI']  = '/tracks/15/relationships/playlists';

        $mapper = new AnnotationsMapper(Manager::getInstance()->getEm());
        $jad = new Jad($mapper);

        ob_start();
        $jad->jsonApiResult();
        $output = ob_get_clean();

        $this->assertMatchesJsonSnapshot($output);
    }

    public function testCreateSingleRelationship()
    {
        $_SERVER['REQUEST_URI']  = '/playlists';
        $_SERVER['REQUEST_METHOD'] = 'POST';

        $input = new \stdClass();
        $input->data = new \stdClass();
        $input->data->type = 'playlists';
        $input->data->attributes = new \stdClass();
        $input->data->attributes->name = 'New Playlist';
        $input->data->relationships = new \stdClass();
        $input->data->relationships->tracks = new \stdClass();
        $input->data->relationships->tracks->data = new \stdClass();
        $input->data->relationships->tracks->data->type = 'tracks';
        $input->data->relationships->tracks->data->id = 15;

        $_POST = ['input' => json_encode($input)];

        $mapper = new AnnotationsMapper(Manager::getInstance()->getEm());
        $jad = new Jad($mapper);

        ob_start();
        $jad->jsonApiResult();
        $output = ob_get_clean();

        $this->assertMatchesJsonSnapshot($output);
    }

    public function testCreateRelationship()
    {
        $_SERVER['REQUEST_URI']  = '/playlists';
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_GET = ['include' => 'tracks'];

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

        ob_start();
        $jad->jsonApiResult();
        $output = ob_get_clean();
        $this->assertMatchesJsonSnapshot($output);
    }

    /**
     * @depends testCreateRelationship
     */
    public function testUpdateAddRelationship()
    {
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

        ob_start();
        $jad->jsonApiResult();
        $output = ob_get_clean();

        $this->assertMatchesJsonSnapshot($output);
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
}