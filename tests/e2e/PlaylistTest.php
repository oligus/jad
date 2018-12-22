<?php declare(strict_types=1);

namespace Jad\E2E;

use Jad\Jad;
use Jad\Database\Manager;
use Jad\Map\AnnotationsMapper;
use Jad\Tests\TestCase;
use Spatie\Snapshots\MatchesSnapshots;

/**
 * Class PlaylistTest
 * @package Jad\E2E
 */
class PlaylistTest extends TestCase
{
    use MatchesSnapshots;

    /**
     * @throws \Doctrine\Common\Annotations\AnnotationException
     */
    public function testFetchCollection()
    {
        $_SERVER['REQUEST_URI'] = '/playlists';

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
    public function testFetchCollectionFields()
    {
        $_SERVER['REQUEST_URI'] = '/tracks';
        $_GET = ['page' => ['offset' => 0, 'limit' => 5], 'fields' => ['tracks' => 'name']];

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
    public function testGetTracks()
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
    public function testGetRelationshipFull()
    {
        $_SERVER['REQUEST_URI'] = '/tracks/15/playlists';

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
    public function testGetRelationshipList()
    {
        $_SERVER['REQUEST_URI'] = '/tracks/15/relationships/playlists';

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
    public function testCreateSingleRelationship()
    {
        $_SERVER['REQUEST_URI'] = '/playlists';
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

    /**
     * @throws \Doctrine\Common\Annotations\AnnotationException
     */
    public function testCreateRelationship()
    {
        $_SERVER['REQUEST_URI'] = '/playlists';
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
        $input->data->relationships->tracks->data[] = ['type' => 'tracks', 'id' => 15];
        $input->data->relationships->tracks->data[] = ['type' => 'tracks', 'id' => 43];
        $input->data->relationships->tracks->data[] = ['type' => 'tracks', 'id' => 77];
        $input->data->relationships->tracks->data[] = ['type' => 'tracks', 'id' => 117];
        $input->data->relationships->tracks->data[] = ['type' => 'tracks', 'id' => 351];

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
     * @throws \Doctrine\Common\Annotations\AnnotationException
     */
    public function testUpdateAddRelationship()
    {
        $_SERVER['REQUEST_URI'] = '/playlists/2';
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

    /**
     * @throws \Doctrine\Common\Annotations\AnnotationException
     */
    public function testUpdateRelationships()
    {
        $_SERVER['REQUEST_URI'] = '/playlists/2';
        $_SERVER['REQUEST_METHOD'] = 'PATCH';

        $input = new \stdClass();
        $input->data = new \stdClass();
        $input->data->type = 'playlists';
        $input->data->relationships = new \stdClass();
        $input->data->relationships->tracks = new \stdClass();
        $input->data->relationships->tracks->data = [];

        $track = new \stdClass();
        $track->type = 'tracks';
        $track->id = 422;

        $input->data->relationships->tracks->data[] = $track;

        $track = new \stdClass();
        $track->type = 'tracks';
        $track->id = 1;

        $input->data->relationships->tracks->data[] = $track;

        $_POST = ['input' => json_encode($input)];

        $mapper = new AnnotationsMapper(Manager::getInstance()->getEm());
        $jad = new Jad($mapper);

        ob_start();
        $jad->jsonApiResult();
        $output = ob_get_clean();

        $this->assertMatchesJsonSnapshot($output);

        $_SERVER['REQUEST_URI'] = '/playlists/2/relationships/tracks';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $mapper = new AnnotationsMapper(Manager::getInstance()->getEm());
        $jad = new Jad($mapper);

        ob_start();
        $jad->jsonApiResult();
        $output = ob_get_clean();

        $this->assertMatchesJsonSnapshot($output);
    }
}
