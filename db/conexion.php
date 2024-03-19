<?php

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

$hostname = $_ENV['DB_HOST'];
$username = $_ENV['DB_USER'];
$passname = $_ENV['DB_PASS'];
$dbname   = $_ENV['DB_NAME'];

$conexion = new mysqli($hostname, $username, $passname, $dbname);

if ($conexion->connect_error) {
    die("Error en la conexion". $conexion->connect_error);
} else {
    //echo "Conexion establecida";
}

?>