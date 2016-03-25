<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Biblys\Data\Book;
use Biblys\Data\Publisher;

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
}
