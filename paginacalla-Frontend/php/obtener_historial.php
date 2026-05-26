<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode([]);
    exit();
}
require_once 'conexion.php';

if (isset($_POST['accion']) && $_POST['accion'] == 'limpiar') {
    $consulta = $conexion->prepare(
        "DELETE FROM historial_busquedas WHERE usuario_id = ?"
    );
    $consulta->bind_param("i", $_SESSION['usuario_id']);
    $consulta->execute();
    echo json_encode(['ok' => true]);
    exit();
}

$consulta = $conexion->prepare(
    "SELECT game_nombre, fecha_busqueda FROM historial_busquedas 
     WHERE usuario_id = ? 
     ORDER BY fecha_busqueda DESC 
     LIMIT 20"
);
$consulta->bind_param("i", $_SESSION['usuario_id']);
$consulta->execute();
$resultado = $consulta->get_result();

$historial = [];
while ($fila = $resultado->fetch_assoc()) {
    $historial[] = $fila;
}
echo json_encode($historial);
exit();
?>