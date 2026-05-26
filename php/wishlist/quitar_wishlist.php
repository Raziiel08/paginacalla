<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['ok' => false]);
    exit();
}
require_once '../conexion.php';

$game_id = trim($_POST['game_id']);

$consulta = $conexion->prepare(
    "DELETE FROM wishlist WHERE usuario_id = ? AND game_id = ?"
);
$consulta->bind_param("is", $_SESSION['usuario_id'], $game_id);
$consulta->execute();

echo json_encode(['ok' => true]);
exit();
?>