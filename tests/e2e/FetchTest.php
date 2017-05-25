<?php

namespace Jad\E2E;

use Jad\Tests\TestCase;
use Jad\Database\Manager;
use Jad\Map\AnnotationsMapper;
use Jad\Jad;

use PHPUnit\DbUnit\TestCaseTrait;
use PHPUnit\DbUnit\DataSet\CsvDataSet;

class FetchTest extends TestCase
{
    use TestCaseTrait;

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

    public function testItemsNoRelation()
    {
        $_SERVER = ['REQUEST_URI' => '/tracks'];

        $mapper = new AnnotationsMapper(Manager::getInstance()->getEm());
        $jad = new Jad($mapper);

        $expected = '{"links":{"self":"http:\/\/:\/tracks"},"data":[{"type":"tracks","id":"15","attributes":{"name":"Go Down","composer":"AC\/DC","price":"0.99"}},{"type":"tracks","id":"43","attributes":{"name":"Forgiven","composer":"Alanis Morissette & Glenn Ballard","price":"0.99"}},{"type":"tracks","id":"77","attributes":{"name":"Enter Sandman","composer":"Apocalyptica","price":"0.99"}},{"type":"tracks","id":"117","attributes":{"name":"Rock \'N\' Roll Music","composer":"Chuck Berry","price":"0.99"}},{"type":"tracks","id":"351","attributes":{"name":"Debra Kadabra","composer":"Frank Zappa","price":"0.99"}},{"type":"tracks","id":"422","attributes":{"name":"I Want It All","composer":"Queen","price":"0.99"}},{"type":"tracks","id":"603","attributes":{"name":"Bye Bye Blackbird","composer":"Miles Davis","price":"0.99"}},{"type":"tracks","id":"645","attributes":{"name":"Swedish Schnapps","composer":"","price":"0.99"}},{"type":"tracks","id":"678","attributes":{"name":"Bad Moon Rising","composer":"J. C. Fogerty","price":"0.99"}},{"type":"tracks","id":"1139","attributes":{"name":"Give Me Novacaine","composer":"Green Day","price":"0.99"}},{"type":"tracks","id":"1246","attributes":{"name":"Rainmaker","composer":"Bruce Dickinson\/David Murray\/Steve Harris","price":"0.99"}},{"type":"tracks","id":"1490","attributes":{"name":"Hey Joe","composer":"Billy Roberts","price":"0.99"}},{"type":"tracks","id":"1492","attributes":{"name":"Purple Haze","composer":"Jimi Hendrix","price":"0.99"}},{"type":"tracks","id":"1874","attributes":{"name":"Fight Fire With Fire","composer":"Metallica","price":"0.99"}},{"type":"tracks","id":"1876","attributes":{"name":"For Whom The Bell Tolls","composer":"Metallica","price":"0.99"}},{"type":"tracks","id":"1877","attributes":{"name":"Fade To Black","composer":"Metallica","price":"0.99"}},{"type":"tracks","id":"2003","attributes":{"name":"Smells Like Teen Spirit","composer":"Kurt Cobain","price":"0.99"}},{"type":"tracks","id":"2005","attributes":{"name":"Come As You Are","composer":"Kurt Cobain","price":"0.99"}},{"type":"tracks","id":"2008","attributes":{"name":"Polly","composer":"Kurt Cobain","price":"0.99"}},{"type":"tracks","id":"2013","attributes":{"name":"On A Plain","composer":"Kurt Cobain","price":"0.99"}},{"type":"tracks","id":"2014","attributes":{"name":"Something In The Way","composer":"Kurt Cobain","price":"0.99"}},{"type":"tracks","id":"2333","attributes":{"name":"It\'s The End Of The World As We Know It (And I Feel Fine)","composer":"R.E.M.","price":"0.99"}},{"type":"tracks","id":"2396","attributes":{"name":"Californication","composer":"Red Hot Chili Peppers","price":"0.99"}},{"type":"tracks","id":"2957","attributes":{"name":"Walk To The Water","composer":"U2","price":"0.99"}},{"type":"tracks","id":"3065","attributes":{"name":"Ain\'t Talkin\' \'bout Love","composer":"Edward Van Halen, Alex Van Halen, David Lee Roth, Michael Anthony","price":"0.99"}},{"type":"tracks","id":"3479","attributes":{"name":"Prometheus Overture, Op. 43","composer":"Ludwig van Beethoven","price":"0.99"}}]}';
        $this->assertEquals($expected, $jad->jsonApiResult());

        $_GET = [
            'page' => [
                'offset' => 0,
                'limit' => 5
            ],

            'sort' => '-name',
            'fields' => [
                'tracks' => 'name, composer'
            ]

        ];
        $jad = new Jad($mapper);

        $expected = '{"links":{"self":"http:\/\/:\/tracks"},"data":[{"type":"tracks","id":"2957","attributes":{"name":"Walk To The Water"}},{"type":"tracks","id":"645","attributes":{"name":"Swedish Schnapps"}},{"type":"tracks","id":"2014","attributes":{"name":"Something In The Way"}},{"type":"tracks","id":"2003","attributes":{"name":"Smells Like Teen Spirit"}},{"type":"tracks","id":"117","attributes":{"name":"Rock \'N\' Roll Music"}}]}';
        $this->assertEquals($expected, $jad->jsonApiResult());
    }

    public function testResourceNotFoundException()
    {
        $_SERVER = ['REQUEST_URI' => '/notfound'];

        $mapper = new AnnotationsMapper(Manager::getInstance()->getEm());
        $jad = new Jad($mapper);

        $expected = '{"errors":[{"status":404,"title":"Resource Not Found","detail":"Resource type not found (notfound)"}]}';
        $this->assertEquals($expected, $jad->jsonApiResult());
    }
}