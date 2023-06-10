<?php

namespace ZazaScandiweb\ProductClasses;

class Book extends \ZazaScandiweb\ProductClasses\MainProduct\Product
{
    public function setSpecialProduct(array $specialProduct): void
    {
        $this->setSpecialAttributeColumnName($specialProduct);
        $weight = $specialProduct[0]["weight"];
        $this->specialProductValue = $weight . "KG";
    }
}
