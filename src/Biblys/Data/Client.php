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
    }

    public function push(Book $book)
    {
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
        $response = $this->http->request('POST', '/api/v0/books/', [
            'form_params' => [
                'ean' => $book->getEan(),
                'title' => $book->getTitle()
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
            return $book;
        }

        // Else, throw an exception
        throw new \Exception("Server answered $status");
    }
}
