<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Biblys\Data\Publisher;

use GuzzleHttp\Psr7\Response;

class testPublisher extends PHPUnit_Framework_TestCase
{
    private $publisher;

    /**
     * Test setting an id
     */
    public function testSetId()
    {
        $publisher = new Publisher();
        $id = '1234';

        $publisher->setId($id);

        $this->assertEquals($id, $publisher->getId(), "setId should set an id");
    }

    /**
     * Test setting a name
     */
    public function testSetName()
    {
        $publisher = new Publisher();
        $name = 'Chants du cauchemar et de la nuit';

        $publisher->setName($name);

        $this->assertEquals($name, $publisher->getName(), "setName should set a name");
    }

    /**
     * Test creating a Publisher from response
     */
    public function testCreateFromResponse()
    {
        $response = new Response(201, [], '{"id":"1234","name":"Dystopia"}');

        $publisher = Publisher::createFromResponse($response);

        $this->assertEquals('1234', $publisher->getId());
        $this->assertEquals('Dystopia', $publisher->getName());
    }
}
