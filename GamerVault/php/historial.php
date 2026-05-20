<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['ok' => false, 'mensaje' => 'No logueado']);
    exit();
}

require_once 'conexion.php';

$game_nombre = trim($_POST['game_nombre']);

if (empty($game_nombre)) {
    echo json_encode(['ok' => false, 'mensaje' => 'Falta el nombre']);
    exit();
}

$consulta = $conexion->prepare(
    "INSERT INTO historial_busquedas (usuario_id, game_nombre) VALUES (?, ?)"
);
$consulta->bind_param("is", $_SESSION['usuario_id'], $game_nombre);
$consulta->execute();

echo json_encode(['ok' => true]);
exit();
?>