<?php

use Jad\Jad;
use Jad\Map\EntityMap;

class JadTest extends TestCase
{
    public function testConstruct()
    {
        $_SERVER = ['REQUEST_URI' => '/api/jad/posts'];

        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->setMethods(['getFieldNames'])
            ->getMock();

        $jad = new Jad($em);
        $jad->setPathPrefix('/api/jad');
        $jad->setEntityMap(new EntityMap([
            'awesome' => 'Awesome\Class',
            'another' => [
                'entityClass' => 'AnotherClass',
                'idField' => 'anotherId'
            ]
        ]));


        $jad->jsonApiResult();

        $this->assertTrue(true);
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
            'SCRIPT_FILENAME' => '/media/sf_share/markviss-api/public/index.php',
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
            'REQUEST_URI' => '/api/jad/accounts/1?username=demo&password=goal14',
            'SCRIPT_NAME' => '/index.php',
            'CONTENT_LENGTH' => '',
            'CONTENT_TYPE' => '',
            'REQUEST_METHOD' => 'GET',
            'QUERY_STRING' => 'username=demo&password=goal14',
            'FCGI_ROLE' => 'RESPONDER',
            'PHP_SELF' => '/index.php',
            'REQUEST_TIME_FLOAT' => 1494043365.8456409,
            'REQUEST_TIME' => 1494043365,
        ];
    }
}