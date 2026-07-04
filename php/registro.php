<?php
require_once 'conexion.php';

$nombre   = trim($_POST['nombre'] ?? '');
$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

$dominiosPermitidos = ['gmail.com', 'hotmail.com', 'outlook.com', 'live.com', 'yahoo.com', 'yahoo.es', 'icloud.com', 'protonmail.com', 'mail.com', 'aol.com', 'msn.com', 'outlook.es'];

$esEmailValido = preg_match('/^[a-z0-9._%+\-]+@[a-z0-9.\-]+\.[a-z]{2,}$/i', $email) === 1;
$dominio = strtolower(substr(strrchr($email, '@'), 1));
$esDominioPermitido = $esEmailValido && in_array($dominio, $dominiosPermitidos, true);
$esPasswordValida = preg_match('/^(?=.*[A-Z])(?=.*[^A-Za-z0-9]).{8,}$/', $password) === 1;

if (empty($nombre) || empty($email) || empty($password) || !$esEmailValido || !$esDominioPermitido || !$esPasswordValida) {
    $error = !$esEmailValido || !$esDominioPermitido ? 'correo_invalido' : 'password_invalida';
    header('Location: ../auth.php?error=' . $error);
    exit();
}

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