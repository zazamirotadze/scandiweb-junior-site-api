<?php

namespace ZazaScandiweb\ProductClasses;

class DVD extends \ZazaScandiweb\ProductClasses\MainProduct\Product
{
    public function setSpecialProduct(array $specialProduct): void
    {
        $this->setSpecialAttributeColumnName($specialProduct);
        $size = $specialProduct[0]["size"];
        $this->specialProductValue = $size . " " . "MB";
    }
}
