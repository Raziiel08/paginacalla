<?php
require_once 'conexion.php';

$nombre   = $_POST['nombre'];
$email    = $_POST['email'];
$password = $_POST['password'];

$consulta = $conexion->prepare("SELECT id FROM usuarios WHERE email = ?");

$consulta->bind_param("s", $email);

$consulta->execute(); 
$consulta->store_result(); 

if ($consulta->num_rows > 0) {

    header('Location: ../auth.php?error=email_duplicado');
    exit();
}

$hash = password_hash($password, PASSWORD_BCRYPT);

$insertar = $conexion->prepare(
    "INSERT INTO usuarios (nombre, email, contrasena) VALUES (?, ?, ?)"
);

$insertar->bind_param("sss", $nombre, $email, $hash);
$insertar->execute();

header('Location: ../auth.php?registro=ok');
exit();
?>