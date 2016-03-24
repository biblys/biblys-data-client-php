<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Biblys\Data\Publisher;

class testPublisher extends PHPUnit_Framework_TestCase
{
    private $publisher;

    /**
     * Test setting an EAN
     */
    public function testSetId()
    {
        $publisher = new Publisher();
        $id = '1234';

        $publisher->setId($id);

        $this->assertEquals($id, $publisher->getId(), "setId should set an id");
    }

    /**
     * Test setting a title
     */
    public function testSetName()
    {
        $publisher = new Publisher();
        $name = 'Chants du cauchemar et de la nuit';

        $publisher->setName($name);

        $this->assertEquals($name, $publisher->getName(), "setName should set a name");
    }
}
