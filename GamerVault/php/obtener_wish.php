<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode([]);
    exit();
}
require_once 'conexion.php';

$consulta = $conexion->prepare(
    "SELECT game_id, game_nombre, fecha_agregado FROM wishlist WHERE usuario_id = ? ORDER BY fecha_agregado DESC"
);
$consulta->bind_param("i", $_SESSION['usuario_id']);
$consulta->execute();
$resultado = $consulta->get_result();

$juegos = [];
while ($fila = $resultado->fetch_assoc()) {
    $juegos[] = $fila;
}

echo json_encode($juegos);
exit();
?>