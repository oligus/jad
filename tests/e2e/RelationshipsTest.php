<?php

namespace Jad\E2E;

use Jad\Jad;
use Jad\Tests\DBTestCase;
use Jad\Database\Manager;
use Jad\Map\AnnotationsMapper;
use PHPUnit\DbUnit\DataSet\CsvDataSet;
use Spatie\Snapshots\MatchesSnapshots;

class RelationshipsTest extends DBTestCase
{
    use MatchesSnapshots;

    public function getDataSet()
    {
        $dataSet = new CsvDataSet();
        $dataSet->addTable('playlists', dirname(__DIR__) . '/fixtures/playlists.csv');
        $dataSet->addTable('tracks', dirname(__DIR__) . '/fixtures/tracks.csv');
        $dataSet->addTable('playlist_track', dirname(__DIR__) . '/fixtures/playlist_track.csv');
        $dataSet->addTable('customers', dirname(__DIR__) . '/fixtures/customers.csv');
        return $dataSet;
    }

    /**
     * @throws \Doctrine\Common\Annotations\AnnotationException
     */
    public function testCreateRecordOneRelationship()
    {
        $_SERVER['REQUEST_URI']  = '/playlists';
        $_GET = ['include' => 'tracks'];
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
        $input->data->relationships->tracks->data->id = 77;

        $_POST = ['input' => json_encode($input)];

        $mapper = new AnnotationsMapper(Manager::getInstance()->getEm());
        $jad = new Jad($mapper);

        ob_start();
        $jad->jsonApiResult();
        $output = ob_get_clean();

        $this->assertMatchesJsonSnapshot($output);
    }

    /**
     * @throws \Doctrine\Common\Annotations\AnnotationException
     */
    public function testCreateRecordManyRelationships()
    {
        $_SERVER['REQUEST_URI']  = '/playlists';
        $_GET = ['include' => 'tracks'];
        $_SERVER['REQUEST_METHOD'] = 'POST';

        $input = new \stdClass();
        $input->data = new \stdClass();
        $input->data->type = 'playlists';
        $input->data->attributes = new \stdClass();
        $input->data->attributes->name = 'New Playlist';

        $input->data->relationships = new \stdClass();
        $input->data->relationships->tracks = new \stdClass();

        $data = [];
        $track = new \stdClass();
        $track->id = 77;
        $track->type = 'tracks';
        $data[] = $track;

        $track = new \stdClass();
        $track->id = 422;
        $track->type = 'tracks';
        $data[] = $track;

        $track = new \stdClass();
        $track->id = 2014;
        $track->type = 'tracks';
        $data[] = $track;

        $input->data->relationships->tracks->data = $data;

        $_POST = ['input' => json_encode($input)];

        $mapper = new AnnotationsMapper(Manager::getInstance()->getEm());
        $jad = new Jad($mapper);

        ob_start();
        $jad->jsonApiResult();
        $output = ob_get_clean();

        $this->assertMatchesJsonSnapshot($output);
    }
}