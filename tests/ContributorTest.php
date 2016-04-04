<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Biblys\Data\Contributor;

use GuzzleHttp\Psr7\Response;

class testContributor extends PHPUnit_Framework_TestCase
{
    private $contributor;

    /**
     * Test setting an id
     */
    public function testSetId()
    {
        $contributor = new Contributor();
        $id = '1234';

        $contributor->setId($id);

        $this->assertEquals($id, $contributor->getId(), "setId should set an id");
    }

    /**
     * Test setting a first name
     */
    public function testSetFirstName()
    {
        $contributor = new Contributor();

        $contributor->setFirstName('Thomas');

        $this->assertEquals('Thomas', $contributor->getFirstName(), "setFirstName should set a first name");
    }

    /**
     * Test setting a last name
     */
    public function testSetLastName()
    {
        $contributor = new Contributor();

        $contributor->setLastName('Ligotti');

        $this->assertEquals('Ligotti', $contributor->getLastName(), "setLastName should set a last name");
    }

    /**
     * Test creating a Contributor from response
     */
    public function testCreateFromResponse()
    {
        $response = new Response(201, [], '{"id":"1234","firstName":"Thomas","lastName":"Ligotti"}');

        $contributor = Contributor::createFromResponse($response);

        $this->assertEquals('1234', $contributor->getId());
        $this->assertEquals('Thomas', $contributor->getFirstName());
        $this->assertEquals('Ligotti', $contributor->getLastName());
    }
}
