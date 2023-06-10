<?php

namespace ZazaScandiweb\ProductClasses;

class Furniture extends \ZazaScandiweb\ProductClasses\MainProduct\Product
{
    public function setSpecialProduct(array $specialProduct): void
    {
        $this->setSpecialAttributeColumnName($specialProduct);
        $values = [];
        foreach ($specialProduct as $product) {
            $values[] = reset($product);
        }
        $dimensionsString = implode('x', $values);
        $json_dimensions = json_encode($dimensionsString);
        $this->specialProductValue = $json_dimensions;
    }
}
