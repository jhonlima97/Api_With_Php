<?php

require('../db/conexion.php');

header("Content-Type: application/json");
$metodo =  $_SERVER['REQUEST_METHOD'];
//print_r($metodo);

// Capturar el id que esta llegando
$path = isset($_SERVER['PATH_INFO'])?$_SERVER['PATH_INFO']:'/';
$buscarId= explode('/',$path);
$id=($path!=='/') ? end($buscarId) : null;

switch ($metodo){
    case 'GET':
        select($conexion, $id);
        break;
    case 'POST':
        insert($conexion);
        break;
    case 'PUT':
        update($conexion, $id);
        break;
    case 'DELETE':
        borrar($conexion, $id);
        break;
    default:
        echo "Método no permitido";
        break;

}

// Validaciones de los campos
function validations($dni, $name, $phone, $email) {
    // Validación del DNI
    if(!is_numeric($dni) || strlen($dni) != 8 || $dni == '00000000'){
        http_response_code(400);
        echo json_encode(array("message" => "DNI debe ser números de 8 dígitos y no solo ceros"));
        return false;
    }


    // Validación del nombre
    if(!ctype_alpha(str_replace(' ', '', $name))){
        http_response_code(400);
        echo json_encode(array("message" => "El nombre solo debe contener letras"));
        return false;
    }

    // Validación del teléfono
    if(!is_numeric($phone) || strlen($phone) != 9){
        http_response_code(400);
        echo json_encode(array("message" => "El teléfono debe ser un número de 9 dígitos"));
        return false;
    }

    // Validación del email
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        http_response_code(400);
        echo json_encode(array("message" => "El email debe ser válido"));
        return false;
    }
    return true;
}

// Method GET request
function select($conexion, $id) {
    if($id === null){
        $sql = "SELECT * FROM users";
    } else if(!is_numeric($id) || intval($id) != $id){
        http_response_code(406);
        echo json_encode(array("message" => "El id debe ser Entero"));
        return;
    } else {
        $sql = "SELECT * FROM users WHERE id = $id";
    }

    $resultado = $conexion->query($sql);

    if($resultado){
        $datos = array();
        while($fila = $resultado->fetch_assoc()){
            $datos[] = $fila;
        }
        if(count($datos) > 0){
            echo json_encode($datos);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "El Usuario no existe"));
        }
    }
}

// Method POST request
function insert($conexion) {
    $dato =  json_decode(file_get_contents('php://input'),true);
    $dni = $dato['dni'];
    $name = $dato['name'];
    $phone = $dato['phone'];
    $email = $dato['email'];

    // Validar si el DNI ya existe
    $sql = "SELECT * FROM users WHERE dni = '$dni'";
    $resultado = $conexion->query($sql);
    if($resultado->num_rows > 0){
        http_response_code(400);
        echo json_encode(array("message" => "El DNI ya existe"));
        return;
    }

    if(!validations($dni, $name, $phone, $email)){
        return;
    }

    $sql = "INSERT INTO users(dni, name,phone, email) 
            VALUES ('$dni','$name', '$phone', '$email')";

    $resultado = $conexion->query($sql);

    if ($resultado){
        //Devolver el objeto registrado
        //$dato['id'] = $conexion->insert_id;
        //echo json_encode($dato);
        //Devolver un mensaje de registro
        echo json_encode(array('message' => 'User registered successfully'));
    }else{
        echo json_encode(array('error' => 'Error al insertar el user'));
    }
    //print_r($dni, $name, $phone, $email);
}

// Method PUT request
function update($conexion, $id) {
    if(!is_numeric($id) || intval($id) != $id){
        http_response_code(406);
        echo json_encode(array("message" => "El id debe ser Entero"));
        return;
    }
    
    // Buscar el ID
    $sql = "SELECT * FROM users WHERE id = $id";
    $resultado = $conexion->query($sql);
    if($resultado->num_rows == 0){
        http_response_code(404);
        echo json_encode(array("message" => "El ID no existe"));
        return;
    }
    //echo "El id a actualizar es: ". $id. " con el dato ".$name;
    $dato =  json_decode(file_get_contents('php://input'),true);
    $dni = $dato['dni'];
    $name = $dato['name'];
    $phone = $dato['phone'];
    $email = $dato['email'];

    if(!validations($dni, $name, $phone, $email)){
        return;
    }
    //El dni se edite o no se gurda normal, no se controla
    $sql = "UPDATE users SET dni= '$dni',name= '$name',phone= '$phone',email= '$email' 
            WHERE id = $id";

    $resultado = $conexion->query($sql);

    if ($resultado){
        echo json_encode(array('mensaje' => 'User Updated successfully'));
    }else{
        echo json_encode(array('mensaje' => 'Error al actualizar user'));
    }
}

// Method DELET request
function borrar($conexion, $id) {
    if(!is_numeric($id) || intval($id) != $id){
        http_response_code(406);
        echo json_encode(array("message" => "El id debe ser Entero"));
        return;
    }
    
    // Buscar el ID
    $sql = "SELECT * FROM users WHERE id = $id";
    $resultado = $conexion->query($sql);
    if($resultado->num_rows == 0){
        http_response_code(404);
        echo json_encode(array("message" => "El ID no existe"));
        return;
    }

    $sql = "DELETE FROM users WHERE id = $id";
    $resultado = $conexion->query($sql);

    if ($resultado){
        echo json_encode(array('mensaje' => 'User deleted'));
    }else{
        echo json_encode(array('mensaje' => 'Error al eliminar user'));
    }
}

?>