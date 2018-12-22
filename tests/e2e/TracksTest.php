<?php

namespace Jad\E2E;

use Jad\Jad;
use Jad\Database\Manager;
use Jad\Map\AnnotationsMapper;
use Jad\Tests\TestCase;
use Spatie\Snapshots\MatchesSnapshots;

/**
 * Class TracksTest
 * @package Jad\E2E
 */
class TracksTest extends TestCase
{
    use MatchesSnapshots;

    /**
     * @throws \Doctrine\Common\Annotations\AnnotationException
     */
    public function testFetchTrack()
    {
        $_SERVER['REQUEST_URI'] = '/tracks/15';

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
    public function testCreateTrack()
    {
        $_SERVER['REQUEST_URI'] = '/tracks';
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

    /**
     * @throws \Doctrine\Common\Annotations\AnnotationException
     */
    public function testUpdateTrack()
    {
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
