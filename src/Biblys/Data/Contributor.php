<?php

namespace Biblys\Data;

class Contributor
{
    private $id, $firstName, $lastName, $name;

    public static function createFromResponse($response)
    {
        $body = (string) $response->getBody();
        $contributorData = json_decode($body);

        $contributor = new Contributor();

        $contributor->setId($contributorData->id);
        if (isset($contributorData->firstName)) {
            $contributor->setFirstName($contributorData->firstName);
        }
        if (isset($contributorData->lastName)) {
            $contributor->setLastName($contributorData->lastName);
        }
        if (isset($contributorData->name)) {
            $contributor->setName($contributorData->name);
        }

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

    /**
     * Full name: firstName + lastName
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function getName()
    {
        if (!isset($this->name)) {
            $this->name = trim($this->getFirstName().' '.$this->getLastName());
        }

        return $this->name;
    }
}
