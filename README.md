# Biblys Data PHP client library

[![Build Status](https://travis-ci.org/biblys/biblys-data-client-php.svg?branch=master)](https://travis-ci.org/biblys/biblys-data-client-php)

A PHP library to fetch data from or push data to [Biblys Data](https://data.biblys.fr/).


## Install

With composer:

Install with composer:

`composer require biblys/data-client-php:~0`


## API

### Client

#### $client = new Client(array options)

Initialize a new Biblys Data client.

Options:
* `apiKey`: optional when reading data from server,
only required when pushing data to server
* `server`: optional, defaults to `http://data.biblys.fr`


#### $client->getBook(string $isbn)

Get book infos from Biblys Data server for this ISBN.

Returns a `Book` object if an object was found for this ISBN, returns false
otherwise.

#### $client->pushBook(Book $book)

Send the `Book` object to server. Will test if there is already a book with 
this ISBN with `getBook()`. If there is, will use the `updateBook()` method
to update it. Else, will use the `createBook()` method to create it.

#### $client->createBook(Book $book)

Will try create the book on the server. Throws an exception if there is already
a book with this ISBN.

#### $client->updateBook(Book $book)

Not yet implemented on the server (will fail silently).

#### $client->pushPublisher(Publisher $publisher)

Send the `Publisher` object to server. Will test if there is already a book with 
this id with `getPublisher()`. If there is, will use the `updatePublisher()` method
to update it. Else, will use the `createPublisher()` method to create it.

#### $client->createPublisher(Publisher $publisher)

Will try create the book on the server. Throws an exception if there is already
a book with this ISBN.

#### $client->updatePublisher(Publisher $publisher)

Not yet implemented on the server (will fail silently).


### Book

#### $book = new Book()

Create a new `Book` object

#### $book->setEan(string $isbn)

Set the book's ISBN. Throws an exception `$isbn` is not a valid ISBN.

#### $book->getEan()

Get the book's ISBN.

#### $book->setTitle(string $title)

Set the book's title

#### $book->getTitle()

Get the book's title


### Publisher

#### $publisher = new Publisher()

Create a new `Publisher` object

#### $publisher->setId(string $id)

Set the publisher's id.

#### $publisher->getId()

Get the publisher's id.

#### $publisher->setName(string $name)

Set the publisher's name

#### $book->getName()

Get the publisher's name


## Examples

Get a book's infos from an ISBN

```php
use Biblys\Data\Client;

$client = new Client();

$result = $client->getBook('9791091146134');

if (!$result) {
  // Result if false, no book was found for this ISBN
} else {
  echo $result->getTitle();
}

```


Push a book's infos to server

```php
use Biblys\Data\Client;
use Biblys\Data\Book;
use Biblys\Data\Publisher;

$client = new Client([
  "apiKey" => "YOUR_API_KEY" // required when pushing data
]);

$book = new Book();
$book->setEan('9791091146134');
$book->setTitle('Chants du cauchemar et de la nuit');

$publisher = new Publisher();
$publisher->setName('Dystopia');
$book->setPublisher($publisher);

try {
  $result = $client->push($book);  
} catch (Exception $e) {
  // Something went wrong
}

```


## Test

Run tests with PHPUnit:

* `composer install`
* `composer test`


## Changelog

0.3.0 (2016-04-05)
* Contributor push, create and get methods
* Books must be pushed with at least one Contributor as an author

0.2.1 (2016-03-25)
* Fixed getting Publisher with Book
* Require Publisher property when creating a Book 

0.2.0 (2016-03-24)
* Publisher push, create and get methods
* Book must be pushed with a Publisher

0.1.0 (2016-03-05)
* First release
* Book push, create and get methods
