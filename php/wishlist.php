<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['ok' => false, 'mensaje' => 'Tenés que iniciar sesión']);
    exit();
}

require_once 'conexion.php';

$game_id     = trim($_POST['game_id']);
$game_nombre = trim($_POST['game_nombre']);

if (empty($game_id) || empty($game_nombre)) {
    echo json_encode(['ok' => false, 'mensaje' => 'Faltan datos']);
    exit();
}

$check = $conexion->prepare(
    "SELECT id FROM wishlist WHERE usuario_id = ? AND game_id = ?"
);
$check->bind_param("is", $_SESSION['usuario_id'], $game_id);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    echo json_encode(['ok' => false, 'mensaje' => 'El juego ya está en tu wishlist']);
    exit();
}

$insertar = $conexion->prepare(
    "INSERT INTO wishlist (usuario_id, game_id, game_nombre) VALUES (?, ?, ?)"
);
$insertar->bind_param("iss", $_SESSION['usuario_id'], $game_id, $game_nombre);
$insertar->execute();

echo json_encode(['ok' => true]);
exit();
?>