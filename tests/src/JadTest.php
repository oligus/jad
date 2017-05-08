<?php

use Jad\Jad;
use Jad\Map\EntityMap;

require_once 'Mocks.php';

class JadTest extends TestCase
{


    public function testMoo() {
        $this->assertTrue(true);
    }

    public function testConstruct()
    {
        $_SERVER = ['REQUEST_URI' => '/api/jad/articles/1'];

        $_GET = [
            'include' => 'author',
            'fields' => [
                'articles' => 'title,body,author'
            ]
        ];

        $articleEntity = Mocks::getInstance()->getArticleEntity();
        $repo = Mocks::getInstance()->getRepo($articleEntity);
        $classMeta = Mocks::getInstance()->getClassMeta();

        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->setMethods(['getRepository', 'getClassMetadata'])
            ->getMock();

        $em
            ->expects($this->any())
            ->method('getRepository')
            ->with('ArticleEntity')
            ->willReturn($repo);

        $em
            ->expects($this->any())
            ->method('getClassMetadata')
            ->with('ArticleEntity')
            ->willReturn($classMeta);

        $entityMap = new EntityMap([
            'articles' => [
                'entityClass' => 'ArticleEntity',
                'id' => 'id'
            ]
        ]);

        $jad = new Jad($em, $entityMap);
        $jad->setPathPrefix('/api/jad');

        $expected = '{"data":{"type":"articles","id":"5","attributes":{"title":"Article Title","body":"Article Body","author":"author Entity"}}}';
        $this->assertEquals($expected, $jad->jsonApiResult());
    }

    private function getServer()
    {
        return [
            'USER' => 'www-data',
            'HOME' => '/var/www',
            'HTTP_ACCEPT_ENCODING' => 'gzip,deflate',
            'HTTP_USER_AGENT' => 'Apache-HttpClient/4.5.2 (Java/1.8.0_112-release)',
            'HTTP_CONNECTION' => 'Keep-Alive',
            'HTTP_HOST' => 'jad.localhost.dev',
            'HTTP_CACHE_CONTROL' => 'no-cache',
            'HTTP_ACCEPT' => '*/*',
            'SCRIPT_FILENAME' => '/media/sf_share/jad/public/index.php',
            'APPLICATION_ENV' => 'development',
            'REDIRECT_STATUS' => '200',
            'SERVER_NAME' => 'jad.localhost.dev',
            'SERVER_PORT' => '80',
            'SERVER_ADDR' => '192.168.0.1',
            'REMOTE_PORT' => '64447',
            'REMOTE_ADDR' => '192.168.56.1',
            'SERVER_SOFTWARE' => 'nginx/1.10.3',
            'GATEWAY_INTERFACE' => 'CGI/1.1',
            'REQUEST_SCHEME' => 'http',
            'SERVER_PROTOCOL' => 'HTTP/1.1',
            'DOCUMENT_ROOT' => '/media/sf_share/markviss-api/public',
            'DOCUMENT_URI' => '/index.php',
            'REQUEST_URI' => '/api/jad/articles/1',
            'SCRIPT_NAME' => '/index.php',
            'CONTENT_LENGTH' => '',
            'CONTENT_TYPE' => '',
            'REQUEST_METHOD' => 'GET',
            'QUERY_STRING' => '',
            'FCGI_ROLE' => 'RESPONDER',
            'PHP_SELF' => '/index.php',
            'REQUEST_TIME_FLOAT' => 1494043365.8456409,
            'REQUEST_TIME' => 1494043365,
        ];
    }
}