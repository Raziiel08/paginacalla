<?php
$host     = "localhost";   
$usuario  = "root";        
$password = "";            
$base     = "gamervault";  

$conexion = new mysqli($host, $usuario, $password, $base);

if ($conexion->connect_error) {
    die("Error al conectar con la base de datos: " . $conexion->connect_error);
}

$conexion->set_charset("utf8");
?>