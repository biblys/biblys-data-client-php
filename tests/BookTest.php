<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../vendor/autoload.php';

use Biblys\Data\Book;

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
}
