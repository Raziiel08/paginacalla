<?php
$host     = "localhost";   // Servidor de la base de datos 
$usuario  = "root";        // Usuario de MySQL 
$password = "";            // Contraseña de MySQL 
$base     = "gamervault";  // Nombre de la base de datos

$conexion = new mysqli($host, $usuario, $password, $base);

if ($conexion->connect_error) {
    die("Error al conectar con la base de datos: " . $conexion->connect_error);
}

$conexion->set_charset("utf8");
//para que hacepte los caracteres como ñ
?>