<?php

namespace Biblys\Data;

class Contributor
{
    private $id, $firstName, $lastName;

    public static function createFromResponse($response)
    {
        $body = (string) $response->getBody();
        $contributorData = json_decode($body);

        $contributor = new Contributor();
        $contributor->setId($contributorData->id);
        $contributor->setFirstName($contributorData->firstName);
        $contributor->setLastName($contributorData->lastName);

        return $contributor;
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

    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function getFirstName()
    {
        return $this->firstName;
    }

    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
        return $this;
    }

    public function getLastName()
    {
        return $this->lastName;
    }
}
