<?php

namespace Jad\E2E;

use Jad\Jad;
use Jad\Configure;
use Jad\Tests\DBTestCase;
use Jad\Database\Manager;
use Jad\Map\AnnotationsMapper;
use PHPUnit\DbUnit\DataSet\CsvDataSet;
use Spatie\Snapshots\MatchesSnapshots;

class TracksTest extends DBTestCase
{
    use MatchesSnapshots;

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

        ob_start();
        $jad->jsonApiResult();
        $output = ob_get_clean();

        $this->assertMatchesJsonSnapshot($output);
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

       ob_start();
       $jad->jsonApiResult();
       $output = ob_get_clean();

       $this->assertRegExp('/tracks.+?name.:.New\sTrack/', $output);
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

        ob_start();
        $jad->jsonApiResult();
        $output = ob_get_clean();

        $this->assertMatchesJsonSnapshot($output);
    }
}