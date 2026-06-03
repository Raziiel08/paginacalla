<?php
// Iniciar sesión para identificar al usuario
session_start();

// Si no hay sesión activa, devolvemos una lista vacía
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode([]);
    exit();
}

// Conectar a la base de datos MySQL
require_once '../conexion.php';

$usuario_id = $_SESSION['usuario_id'];

// Obtener todas las alertas del usuario logueado ordenadas por fecha
$consulta = $conexion->prepare(
    "SELECT game_id, game_nombre, precio_objetivo, email_notificado, fecha_creacion FROM alertas WHERE usuario_id = ? ORDER BY fecha_creacion DESC"
);
$consulta->bind_param("i", $usuario_id);
$consulta->execute();
$resultado = $consulta->get_result();

// Almacenar el resultado en un array con los tipos de datos casteados
$alertas = [];
while ($fila = $resultado->fetch_assoc()) {
    $alertas[] = [
        'game_id'          => $fila['game_id'],
        'game_nombre'      => $fila['game_nombre'],
        'precio_objetivo'  => floatval($fila['precio_objetivo']),
        'email_notificado' => intval($fila['email_notificado']),
        'fecha_creacion'   => $fila['fecha_creacion']
    ];
}

// Cerrar conexión y retornar el JSON al cliente
$conexion->close();
echo json_encode($alertas);
exit();
?>
