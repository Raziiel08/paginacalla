<?php
$host     = "sql208.infinityfree.com";   
$usuario  = "if0_42338178";        
$password = "mebdejdmVfbVr";            
$base     = "if0_42338178_gamervault";  

$conexion = new mysqli($host, $usuario, $password, $base);

if ($conexion->connect_error) {
    die("Error al conectar con la base de datos: " . $conexion->connect_error);
}

$conexion->set_charset("utf8");
?>