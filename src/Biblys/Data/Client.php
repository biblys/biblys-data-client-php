<?php

namespace Biblys\Data;

use GuzzleHttp\Client as Http;
use Biblys\Isbn\Isbn as Isbn;

class Client
{
    public function __construct($options = [])
    {
        $this->server = "http://data.biblys.fr";
        if (isset($options["server"])) {
            $this->server = $options["server"];
        }

        $this->apiKey = "";
        if (isset($options["apiKey"])) {
            $this->apiKey = $options["apiKey"];
        }

        $this->http = new Http([
            'base_uri' => $this->server,
            'headers' => [
                'Authorization' => $this->apiKey
            ],
            'http_errors' => false
        ]);
        if (isset($options["http"])) {
            $this->http = $options["http"];
        }
    }

    public function push(Book $book)
    {
        return $this->pushBook($book);
    }

    public function pushBook(Book $book)
    {
        // Push related publisher
        $publisher = $book->getPublisher();
        $publisher = $this->pushPublisher($publisher);
        $book->setPublisher($publisher);

        // Try to fetch the book from the server
        $fetch = $this->getBook($book->getEan());

        // If it doesn't exist, create it
        if (!$fetch) {
            $this->createBook($book);
            return;
        }

        // Else, update it (to be implemented server-side)
        return;
    }

    public function createBook(Book $book)
    {
        $publisher = $book->getPublisher();
        if (!$publisher) {
            throw new \Exception("Cannot create a Book without a Publisher");
        }

        $publisher_id = $publisher->getId();
        if (!$publisher_id) {
            throw new \Exception("Book's Publisher has no id");
        }

        $response = $this->http->request('POST', '/api/v0/books/', [
            'form_params' => [
                'ean' => $book->getEan(),
                'title' => $book->getTitle(),
                'publisher' => $publisher->getId()
            ]
        ]);
        $status = $response->getStatusCode();

        if ($status !== 201) {
            throw new \Exception("Server answered $status");
        }

        return true;
    }

    public function getBook($ean)
    {
        // Check that EAN is valid
        $isbn = new Isbn($ean);
        if (!$isbn->isValid()) {
            throw new \Exception("$ean is not a valid ISBN");
        }
        $ean = $isbn->format('EAN');

        // Fetch book from server with this ISBN
        $response = $this->http->request('GET', "/api/v0/books/$ean");
        $status = $response->getStatusCode();

        // Return false if the book does not exist
        if ($status === 404) {
            return false;
        }

        // Return the book if it exists
        if ($status === 200) {
            $body = (string) $response->getBody();
            $bookData = json_decode($body);

            $book = new Book();
            $book->setEan($bookData->ean);
            $book->setTitle($bookData->title);

            $publisher = new Publisher();
            $publisher->setId($bookData->publisher->id);
            $publisher->setName($bookData->publisher->name);
            $book->setPublisher($publisher);

            return $book;
        }

        // Else, throw an exception
        throw new \Exception("Server answered $status");
    }

    public function pushPublisher(Publisher $publisher)
    {
        // Try to fetch the book from the server
        $fetch = $this->getPublisher($publisher->getId());

        // If it doesn't exist, create it
        if (!$fetch) {
            return $this->createPublisher($publisher);
        }

        // Else, update it (to be implemented server-side)
        return $fetch;
    }

    public function createPublisher(Publisher $publisher)
    {
        $response = $this->http->request('POST', '/api/v0/publishers/', [
            'form_params' => [
                'name' => $publisher->getName()
            ]
        ]);
        $status = $response->getStatusCode();

        if ($status !== 201) {
            throw new \Exception("Server answered $status");
        }

        // Update publisher with response
        $body = (string) $response->getBody();
        $publisherData = json_decode($body);
        $publisher->setId($publisherData->id);
        $publisher->setName($publisherData->name);

        return $publisher;
    }

    public function getPublisher($id)
    {
        // Fetch publisher from server with this id
        $response = $this->http->request('GET', "/api/v0/books/$id");
        $status = $response->getStatusCode();

        // Return false if the publisher does not exist
        if ($status === 404) {
            return false;
        }

        // Return the publisher if it exists
        if ($status === 200) {
            $body = (string) $response->getBody();
            $publisherData = json_decode($body);

            $publisher = new Publisher();
            $publisher->setId($publisherData->id);
            $publisher->setName($publisherData->name);
            return $publisher;
        }

        // Else, throw an exception
        throw new \Exception("Server answered $status");
    }
}
