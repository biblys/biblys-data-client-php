<?php

namespace Biblys\Data;

use Biblys\Isbn\Isbn as Isbn;

class Publisher
{
    private $id, $name;

    public static function createFromResponse($response)
    {
        $body = (string) $response->getBody();
        $publisherData = json_decode($body);

        $publisher = new Publisher();
        $publisher->setId($publisherData->id);
        $publisher->setName($publisherData->name);

        return $publisher;
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function getName()
    {
        return $this->name;
    }
}
