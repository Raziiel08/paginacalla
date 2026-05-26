<?php
session_start();

require_once 'conexion.php';

$email    = $_POST['email'];
$password = $_POST['password'];

$consulta = $conexion->prepare(
    "SELECT id, nombre, contrasena, email, fecha_registro FROM usuarios WHERE email = ?"
);
$consulta->bind_param("s", $email);
$consulta->execute();
$consulta->store_result();

if ($consulta->num_rows == 0) {
    header('Location: ../auth.php?error=credenciales');
    exit();
}

$consulta->bind_result($id, $nombre, $hash, $emailDB, $fecha);
$consulta->fetch();

if (!password_verify($password, $hash)) {
    header('Location: ../auth.php?error=credenciales');
    exit();
}

$_SESSION['usuario_id']     = $id;
$_SESSION['usuario_nombre'] = $nombre;
$_SESSION['usuario_email']  = $email;
$_SESSION['usuario_fecha'] = $fecha;

header('Location: ../perfil.php');
exit();
?>