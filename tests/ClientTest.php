<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../vendor/autoload.php';

use Biblys\Data\Client;
use Biblys\Data\Book;
use Biblys\Data\Contributor;
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
        $bookResponse = '{"ean":"9791091146135","title":"Chants du cauchemar et de la nuit","publisher":{"id":"1234","name":"Dystopia"}}';
        $contributorResponse = '{"id":"12345","name":"Thomas Ligotti"}';
        $publisherResponse = '{"id":"1234","name":"Dystopia"}';
        $mock = new MockHandler([
            new Response(200, [], $bookResponse),
            new Response(404, [], '{"error":"Cannot find a book with EAN 9791091146134"}'),
            new Response(201, [], $bookResponse),
            new Response(409, [], $bookResponse),
            new Response(200, [], $publisherResponse),
            new Response(404, [], '{"error": "Cannot find a publisher with id 1234"}'),
            new Response(201, [], $publisherResponse),
            new Response(409, [], $publisherResponse),
            new Response(200, [], $contributorResponse),
            new Response(404, [], '{"error": "Cannot find a contributor with id 12345"}'),
            new Response(201, [], $contributorResponse),
            new Response(409, [], $contributorResponse)
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
     * Test creating a book without a publisher
     * @expectedException Exception
     * @expectedExceptionMessage Cannot create a Book without a Publisher
     */
    public function testCreateBookWithoutPublisher()
    {
        $book = new Book();
        $book->setEan('9791091146134');
        $book->setTitle('Chants du cauchemar et de la nuit');

        $result = self::$client->createBook($book);

        $this->assertTrue($result);
    }

    /**
     * Test creating a book without a publisher
     * @expectedException Exception
     * @expectedExceptionMessage Book's Publisher has no id
     */
    public function testCreateBookWithPublisherButNoId()
    {
        $book = new Book();
        $book->setEan('9791091146134');
        $book->setTitle('Chants du cauchemar et de la nuit');

        $publisher = new Publisher();
        $publisher->setName('Dystopia');
        $book->setPublisher($publisher);

        $result = self::$client->createBook($book);

        $this->assertTrue($result);
    }

    /**
     * Test getting a publisher
     */
    public function testGetPublisher()
    {
        $publisher = self::$client->getPublisher('1234');

        $this->assertInstanceOf('Biblys\Data\Publisher', $publisher,
            'getPublisher result must be an instance of Publisher');
        $this->assertEquals('1234', $publisher->getId());
        $this->assertEquals('Dystopia', $publisher->getName());
    }

    /**
     * Test getting a publisher with response 404
     */
    public function testGetPublisherNotFound()
    {
        $publisher = self::$client->getPublisher('1018');

        $this->assertFalse($publisher);
    }

    /**
     * Test creating a publisher
     */
    public function testCreatePublisher()
    {
        $publisher = new Publisher();
        $publisher->setName('Dystopia');

        $result = self::$client->createPublisher($publisher);

        $this->assertInstanceOf('\Biblys\Data\Publisher', $result);
        $this->assertEquals('1234', $result->getId());
        $this->assertEquals('Dystopia', $result->getName());
    }

    /**
     * Test creating a publisher that exists
     */
    public function testCreatePublisherThatExists()
    {
        $publisher = new Publisher();
        $publisher->setName('Dystopia');

        $result = self::$client->createPublisher($publisher);

        $this->assertInstanceOf('\Biblys\Data\Publisher', $result);
        $this->assertEquals('1234', $result->getId());
        $this->assertEquals('Dystopia', $result->getName());
    }

    /**
     * Test getting a contributor
     */
    public function testGetContributor()
    {
        $contributor = self::$client->getContributor('12345');

        $this->assertInstanceOf('Biblys\Data\Contributor', $contributor,
            'getContributor result must be an instance of Contributor');
        $this->assertEquals('12345', $contributor->getId());
        $this->assertEquals('Thomas Ligotti', $contributor->getName());
    }

    /**
     * Test getting a contributor with response 404
     */
    public function testGetContributorNotFound()
    {
        $contributor = self::$client->getContributor('12344');

        $this->assertFalse($contributor);
    }

    /**
     * Test creating a contributor
     */
    public function testCreateContributor()
    {
        $contributor = new Contributor();
        $contributor->setName('Thomas Ligotti');

        $result = self::$client->createContributor($contributor);

        $this->assertInstanceOf('\Biblys\Data\Contributor', $result);
        $this->assertEquals('12345', $result->getId());
        $this->assertEquals('Thomas Ligotti', $result->getName());
    }

    /**
     * Test creating a contributor that exists
     */
    public function testCreateContributorThatExists()
    {
        $contributor = new Contributor();
        $contributor->setName('Thomas Ligotti');

        $result = self::$client->createContributor($contributor);

        $this->assertInstanceOf('\Biblys\Data\Contributor', $result);
        $this->assertEquals('12345', $result->getId());
        $this->assertEquals('Thomas Ligotti', $result->getName());
    }
}
