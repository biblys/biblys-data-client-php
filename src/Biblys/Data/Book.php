<?php

namespace Biblys\Data;

use Biblys\Isbn\Isbn;
use Biblys\Data\Publisher;

class Book
{
    private $ean, $title;

    public function setEan($ean)
    {
        $isbn = new Isbn($ean);

        if (!$isbn->isValid()) {
            throw new \Exception("$ean is not a valid EAN");
        }

        $this->ean = $isbn->format("EAN");
        return $this;
    }

    public function getEan()
    {
        return $this->ean;
    }

    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setPublisher(Publisher $publisher)
    {
        $this->publisher = $publisher;
        return $this;
    }

    public function getPublisher()
    {
        return $this->publisher;
    }
}
