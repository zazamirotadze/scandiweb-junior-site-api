<?php

header("Access-Control-Allow-Origin:*");
header("Access-Control-Allow-Methods: POST, GET, DELETE");
header("Access-Control-Allow-Headers: Content-Type");
header("content-type:application/json; charset=UTF-8");

require_once realpath("vendor/autoload.php");

set_error_handler("\ZazaScandiweb\RestClass\ErrorHandler::handleError");
set_exception_handler("\ZazaScandiweb\RestClass\ErrorHandler::handleException");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(204);
    exit();
}

$parts = explode("/", $_SERVER["REQUEST_URI"]);
$length = count($parts);
if ($parts[1] != "products" || $length !== 2) {
    http_response_code(404);
    exit;
}

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$hostname = getenv("DB_HOSTNAME");
$databaseName = getenv("DB_DATABASE");
$username = getenv("DB_USERNAME");
$password = getenv("DB_PASSWORD");

$database = new \ZazaScandiweb\RestClass\Database(
    $hostname,
    $databaseName,
    $username,
    $password
);
$controller = new \ZazaScandiweb\Controller\ProductController($database);
$controller->processRequest($_SERVER["REQUEST_METHOD"]);
