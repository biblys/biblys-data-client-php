<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../vendor/autoload.php';

use Biblys\Data\Client;
use Biblys\Data\Book;
use Biblys\Data\Publisher;

use GuzzleHttp\Client as Http;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Exception\RequestException;

class testClient extends PHPUnit_Framework_TestCase
{
    private static $client;

    public static function setUpBeforeClass()
    {
        $mock = new MockHandler([
            new Response(200, [], '{"ean":"9791091146135","title":"Chants du cauchemar et de la nuit","publisher":{"id":"1234","name":"Dystopia"}}'),
            new Response(404, [], 'Cannot find a book with EAN 9791091146134'),
            new Response(201, [], '{"ean":"9791091146135","title":"Chants du cauchemar et de la nuit","publisher":{"id":"1234","name":"Dystopia"}}'),
            new Response(409),
            new Response(200, [], '{"id":"1234","name":"Dystopia"}'),
            new Response(404, [], 'Cannot find a publisher with id 1234'),
            new Response(201, [], '{"id":"1234","name":"Dystopia"}'),
            new Response(409, [], '{"id":"1234"}')
        ]);

        $handler = HandlerStack::create($mock);
        $http = new Http([
            'handler' => $handler,
            'http_errors' => false
        ]);

        self::$client = new Client([
            'http' => $http
        ]);
    }

    /**
     * Test getting a book
     */
    public function testGetBook()
    {
        $book = self::$client->getBook('9791091146135');
        $publisher = $book->getPublisher();

        $this->assertInstanceOf('Biblys\Data\Book', $book, 'getBook result must be an instance of book');
        $this->assertEquals('Chants du cauchemar et de la nuit', $book->getTitle());
        $this->assertEquals('9791091146135', $book->getEan());
        $this->assertInstanceOf('Biblys\Data\Publisher', $publisher);
        $this->assertEquals('1234', $publisher->getId());
        $this->assertEquals('Dystopia', $publisher->getName());
    }

    /**
     * Test getting a book with response 404
     */
    public function testGetBookNotFound()
    {
        $book = self::$client->getBook('9791091146134');

        $this->assertFalse($book);
    }

    /**
     * Test creating a book
     */
    public function testCreateBook()
    {
        $book = new Book();
        $book->setEan('9791091146134');
        $book->setTitle('Chants du cauchemar et de la nuit');

        $publisher = new Publisher();
        $publisher->setId('1234');
        $publisher->setName('Dystopia');
        $book->setPublisher($publisher);

        $result = self::$client->createBook($book);

        $this->assertTrue($result);
    }

    /**
     * Test creating a book that already exists
     * @expectedException Exception
     * @expectedExceptionMessage Server answered 409
     */
    public function testCreateBookThatExists()
    {
        $book = new Book();
        $book->setEan('9791091146134');
        $book->setTitle('Chants du cauchemar et de la nuit');

        $publisher = new Publisher();
        $publisher->setId('1234');
        $publisher->setName('Dystopia');
        $book->setPublisher($publisher);

        $result = self::$client->createBook($book);
    }

    /**
     * Test getting a book
     */
    public function testGetPublisher()
    {
        $book = self::$client->getPublisher('1234');

        $this->assertInstanceOf('Biblys\Data\Publisher', $book, 'getPublisher result must be an instance of Publisher');
        $this->assertEquals('1234', $book->getId());
        $this->assertEquals('Dystopia', $book->getName());
    }

    /**
     * Test getting a book with response 404
     */
    public function testGetPublisherNotFound()
    {
        $publisher = self::$client->getPublisher('1018');

        $this->assertFalse($publisher);
    }

    /**
     * Test creating a book
     */
    public function testCreatePublisher()
    {
        $publisher = new Publisher();
        $publisher->setName('Dystopia');

        $result = self::$client->createPublisher($publisher);

        $this->assertInstanceOf('\Biblys\Data\Publisher', $result);
        $this->assertEquals($publisher->getName(), 'Dystopia');
        $this->assertNotEmpty($publisher->getId());
    }

    /**
     * Test creating a book that already exists
     * @expectedException Exception
     * @expectedExceptionMessage Server answered 409
     */
    public function testCreatePublisherThatExists()
    {
        $publisher = new Publisher();
        $publisher->setName('Dystopia');

        $result = self::$client->createPublisher($publisher);
    }
}
