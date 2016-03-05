<?php

namespace Biblys\Data;

use Biblys\Isbn\Isbn as Isbn;

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
}
