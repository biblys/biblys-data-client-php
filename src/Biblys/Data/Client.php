<?php

namespace Biblys\Data;

use GuzzleHttp\Client as Http;
use Biblys\Isbn\Isbn as Isbn;

class Client
{
    public function __construct($options = [])
    {
        $this->server = "https://data.biblys.fr";
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

    /** BOOKS **/

    public function push(Book $book)
    {
        return $this->pushBook($book);
    }

    public function pushBook(Book $book)
    {
        // Push book's publisher
        $publisher = $book->getPublisher();
        if (!$publisher) {
            throw new \Exception("Book's publisher must a Publisher object");
        }
        $publisher = $this->pushPublisher($publisher);
        $book->setPublisher($publisher);

        // Push book's authors
        $authors = $book->getAuthors();
        $book->setAuthors([]);
        if (count($authors) == 0) {
            throw new \Exception("Pushed books must have at least one author");
        }
        foreach ($authors as $author) {
            $author = $this->pushContributor($author);
            $book->addAuthor($author);
        }

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
        if (empty($ean)) {
            return false;
        }

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
            return Book::createFromResponse($response);
        }

        // Else, throw an exception
        throw new \Exception("Server answered $status");
    }

    /** PUBLISHERS **/

    public function pushPublisher(Publisher $publisher)
    {

        // Try to fetch the publisher from the server
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

        // If Book was created or already exist
        if ($status === 201 || $status === 409) {
            $publisher = Publisher::createFromResponse($response);
            return $publisher;
        }

        throw new \Exception("Server answered $status");
    }

    public function getPublisher($id)
    {
        if (empty($id)) {
            return false;
        }

        // Fetch contributor from server with this id
        $response = $this->http->request('GET', "/api/v0/books/$id");
        $status = $response->getStatusCode();

        // Return false if the contributor does not exist
        if ($status === 404) {
            return false;
        }

        // Return the contributor if it exists
        if ($status === 200) {
            return Publisher::createFromResponse($response);
        }

        // Else, throw an exception
        throw new \Exception("Server answered $status");
    }

    /** CONTRIBUTORS **/

    public function pushContributor(Contributor $contributor)
    {
        // Try to fetch the book from the server
        $fetch = $this->getContributor($contributor->getId());

        // If it doesn't exist, create it
        if (!$fetch) {
            return $this->createContributor($contributor);
        }

        // Else, update it (to be implemented server-side)
        return $fetch;
    }

    public function createContributor(Contributor $contributor)
    {
        $response = $this->http->request('POST', '/api/v0/contributors/', [
            'form_params' => [
                'name' => $contributor->getName()
            ]
        ]);
        $status = $response->getStatusCode();

        // If Book was created or already exist
        if ($status === 201 || $status === 409) {
            $contributor = Contributor::createFromResponse($response);
            return $contributor;
        }

        throw new \Exception("Server answered $status");
    }

    public function getContributor($id)
    {
        if (empty($id)) {
            return false;
        }

        // Fetch publisher from server with this id
        $response = $this->http->request('GET', "/api/v0/contributors/$id");
        $status = $response->getStatusCode();

        // Return false if the publisher does not exist
        if ($status === 404) {
            return false;
        }

        // Return the publisher if it exists
        if ($status === 200) {
            return Contributor::createFromResponse($response);
        }

        // Else, throw an exception
        throw new \Exception("Server answered $status");
    }
}
