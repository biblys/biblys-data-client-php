<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../vendor/autoload.php';

use Biblys\Data\Client;
use Biblys\Data\Book;

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
            new Response(200, [], '{"title":"Chants du cauchemar et de la nuit","ean":"9791091146135"}'),
            new Response(404, [], 'Cannot find a book with EAN 9791091146134'),
            new Response(201, [], '{"title":"Chants du cauchemar et de la nuit","ean":"9791091146135"}'),
            new Response(409, [])
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

        $this->assertInstanceOf('Biblys\Data\Book', $book, 'getBook result must be an instance of book');
        $this->assertEquals('Chants du cauchemar et de la nuit', $book->getTitle());
        $this->assertEquals('9791091146135', $book->getEan());
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

        $result = self::$client->createBook($book);
    }

}
