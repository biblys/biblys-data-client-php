<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Biblys\Data\Book;
use Biblys\Data\Contributor;
use Biblys\Data\Publisher;

use GuzzleHttp\Psr7\Response;

class testBook extends PHPUnit_Framework_TestCase
{
    private $book;

    /**
     * Test setting an EAN
     */
    public function testSetEan()
    {
        $book = new Book();
        $ean = '9791091146135';

        $book->setEan($ean);

        $this->assertEquals($ean, $book->getEan(), "setEan should set an EAN");
    }

    /**
     * Test setting an invalid EAN
     * @expectedException Exception
     */
    public function testSetInvalidEan()
    {
        $book = new Book();
        $ean = '00777289';

        $book->setEan($ean);
    }

    /**
     * Test setting a title
     */
    public function testSetTitle()
    {
        $book = new Book();
        $title = 'Chants du cauchemar et de la nuit';

        $book->setTitle($title);

        $this->assertEquals($title, $book->getTitle(), "setTitle should set a title");
    }

    /**
     * Test setting a Publisher
     */
    public function testSetPublisher()
    {
        $book = new Book();

        $publisher = new Publisher();
        $publisher->setName('Dystopia');

        $book->setPublisher($publisher);

        $this->assertInstanceOf('\Biblys\Data\Publisher', $book->getPublisher());
        $this->assertEquals('Dystopia', $book->getPublisher()->getName());
    }

    /**
     * Test adding an author
     */
    public function testAddAuthor()
    {
        $book = new Book();

        $author = new Contributor();
        $author->setFirstName('Thomas');
        $author->setLastName('Ligotti');

        $book->addAuthor($author);

        $this->assertInstanceOf('\Biblys\Data\Contributor', $book->getAuthors()[0]);
        $this->assertEquals('Ligotti', $book->getAuthors()[0]->getLastName());
    }

    /**
     * Test creating a Book from response
     */
    public function testCreateFromResponse()
    {
        $response = new Response(201, [], '{
            "ean": "9791091146135",
            "title": "Chants du cauchemar et de la nuit",
            "publisher": {
                "id": "1234",
                "name": "Dystopia"
            },
            "authors":[{
                "id": "12345",
                "name": "Thomas Ligotti"
            }]
        }');

        $book = Book::createFromResponse($response);
        $publisher = $book->getPublisher();
        $author = $book->getAuthors()[0];

        $this->assertEquals('9791091146135', $book->getEan());
        $this->assertEquals('Chants du cauchemar et de la nuit', $book->getTitle());
        $this->assertEquals('1234', $publisher->getId());
        $this->assertEquals('Dystopia', $publisher->getName());
        $this->assertEquals('Thomas Ligotti', $author->getName());
    }
}
