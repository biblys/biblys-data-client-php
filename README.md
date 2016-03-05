# Biblys Data PHP client library

A PHP library to fetch data from or push data to [Biblys Data](http://data.biblys.fr/).


## Install

With composer:

Install with composer:

`composer require biblys/isbn:~1.0`


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

#### $client->push(Book $book)

Send the `Book` object to server. Will test if there is already a book with 
this ISBN with `getBook()`. If there is, will use the `updateBook()` method
to update it. Else, will use the `createBook()` method to create it.

#### $client->createBook(Book $book)

Will try create the book on the server. Throws an exception if there is already
a book with this ISBN.

#### $client->updateBook(Book $book)

Not yet implemented on the server (will fail silently).


### Book

### $book = new Book()

Create a new `Book` object

### $book->setEan(string $isbn)

Set the book's ISBN. Throws an exception `$isbn` is not a valid ISBN.

### $book->getEan($string $isbn)

Get the book's ISBN.

### $book->setTitle(string $title)

Set the book's title

### $book->getTitle(string $title)

Get the book's title

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

$client = new Client([
  "apiKey" => "YOUR_API_KEY" // required when pushing data
]);

$book = new Book();
$book->setEan('9791091146134');
$book->setTitle('Chants du cauchemar et de la nuit');

try {
  $result = $client->push($book);  
} catch (Exception $e) {
  // Something went wrong
}

```


## Test

Run tests with PHPUnit:

* `composer install`
* `vendor/bin/phpunit tests/`


## Changelog

0.1.0 (2016-03-05)
* First release
* Basic get and push methods
