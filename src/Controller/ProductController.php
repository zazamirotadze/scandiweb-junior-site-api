<?php

namespace ZazaScandiweb\Controller;

use ZazaScandiweb\ProductClasses\{
    Book,
    DVD,
    Furniture
};
use ZazaScandiweb\RestClass\{
    Database,
    Validation,
};

class ProductController
{
    public function __construct(private Database $database)
    {
    }
    public function processRequest($method)
    {
        $data = (array) json_decode(file_get_contents("php://input"), true);
        $book = new Book();
        $dvd = new DVD();
        $furniture = new Furniture();
        switch ($method) {
            case 'GET':
                $allProducts = array_merge(
                    $furniture->getAllProduct($this->database),
                    $dvd->getAllProduct($this->database),
                    $book->getAllProduct($this->database)
                );
                usort($allProducts, function ($a, $b) {
                    return $a['id'] - $b['id'];
                });
                echo json_encode($allProducts);
                break;
            case "POST":
                $validatedData = Validation::validateFormData($data);
                $className = "\ZazaScandiweb\ProductClasses\\" . $validatedData["productType"];
                //check if there is same sku in db
                $book->skuChecker($this->database, $validatedData["sku"]);
                $dvd->skuChecker($this->database, $validatedData["sku"]);
                $furniture->skuChecker($this->database, $validatedData["sku"]);
                //
                $object = new $className();
                $object->setFormData($validatedData);
                $object->insertIntoDatabase($this->database);
                http_response_code(201);
                $data = [
                    "status" => 201,
                    "message" => "Insertion is successful",
                ];
                echo json_encode($data);
                break;
            case "DELETE":
                Validation::validateMassDeleteFunction($data);
                $book->deleteFromTable($this->database, $data);
                $dvd->deleteFromTable($this->database, $data);
                $furniture->deleteFromTable($this->database, $data);
                http_response_code(204);
                break;
            default:
                http_response_code(405);
                header("Allow: GET, POST, DELETE");
                break;
        }
    }
}
