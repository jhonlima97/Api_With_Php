<?php

$hostname   ="localhost";
$username   ="root";
$passname   ="ADMINPHP";
$dbname     ="api_php";

$conexion = new mysqli($hostname,$username,$passname,$dbname);

if($conexion->connect_error){
    die("Error en la conexion". $conexion->connect_error);
} else {
    //echo "Conexion establecida";
}

?>