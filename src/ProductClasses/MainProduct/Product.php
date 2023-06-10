<?php

namespace ZazaScandiweb\ProductClasses\MainProduct;

use ZazaScandiweb\RestClass\Database;

abstract class Product
{
    protected string $tableName;
    protected string $sku;
    protected string $name;
    protected string $price;
    protected string $specialAttributeColumnName;
    protected string $specialProductValue;

    public function __construct()
    {
        $childClassName = get_class($this);
        $this->tableName = explode("\\", strtolower($childClassName))[2];
    }
    // database manipulation functions
    public function insertIntoDatabase(Database $database): bool
    {
        $tableName =  $this->tableName;
        $spectialAttributeColumnName = $this->getSpecialAttributeColumnName();
        $query = "INSERT INTO $tableName (sku, name, price, {$spectialAttributeColumnName})";
        $query .= "VALUES (:sku, :name, :price, :{$spectialAttributeColumnName})";
        $statement = $database->getConnection()->prepare($query);
        $statement->bindValue(':sku', $this->getSku());
        $statement->bindValue(':name', $this->getName());
        $statement->bindValue(':price', $this->getPrice());
        $statement->bindValue(':' . $spectialAttributeColumnName, $this->getSpecialProductValue());

        return $statement->execute();
    }

    public function deleteFromTable(Database $database, array $skuData): void
    {
        $tableName =  $this->tableName;
        $placeholders = rtrim(str_repeat('?,', count($skuData)), ',');
        $query = "DELETE FROM $tableName WHERE sku IN ($placeholders)";
        $statement = $database->getConnection()->prepare($query);
        $statement->execute($skuData);
    }
    public function getAllProduct(Database $database): array
    {
        $tableName =  $this->tableName;
        $query = "select * from $tableName";
        $statement = $database->getConnection()->prepare($query);
        $statement->execute();
        $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
        return $result;
    }
    //

    // setters and getters
    abstract public function setSpecialProduct(array $specialProduct): void;
    public function getSpecialProductValue(): string
    {
        return $this->specialProductValue;
    }



    public function setSpecialAttributeColumnName(array $specialProduct): void
    {
        $keys = [];
        foreach ($specialProduct as $product) {
            $keys[] = key($product);
        }

        $result = implode('x', $keys);
        $this->specialAttributeColumnName = $result;
    }
    public function getSpecialAttributeColumnName(): string
    {
        return $this->specialAttributeColumnName;
    }



    public function getSku(): string
    {
        return $this->sku;
    }

    public function setSku(string $sku): void
    {
        $this->sku = $sku;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getPrice(): string
    {
        return $this->price;
    }

    public function setPrice($price): void
    {
        $this->price = number_format(floatval($price), 2) . " $";
    }
    public function setFormData(array $data): void
    {
        $this->setSpecialProduct($data["specialAttribute"]);
        $this->setSku($data["sku"]);
        $this->setPrice($data["price"]);
        $this->setName($data["name"]);
    }
    //
    // validation
    public function skuChecker(Database $database, string $sku): void
    {
        $data = $this->getAllProduct($database);
        foreach ($data as $product) {
            if ($product['sku'] === $sku) {
                http_response_code(409);
                $data = [
                    "status" => 409,
                    "message" => "Inserted sku already exists in the database",
                ];
                echo json_encode($data);
                exit;
            }
        }
    }
    //
}
