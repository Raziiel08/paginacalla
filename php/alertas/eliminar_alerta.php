<?php
// Iniciar sesión para identificar al usuario
session_start();

// Validar inicio de sesión
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['ok' => false, 'mensaje' => 'Tenés que iniciar sesión']);
    exit();
}

// Conectar con la base de datos MySQL
require_once '../conexion.php';

// Limpiar y recibir el ID del juego enviado por POST
$game_id = trim($_POST['game_id'] ?? '');

// Validar que se haya enviado el ID del juego
if (empty($game_id)) {
    echo json_encode(['ok' => false, 'mensaje' => 'Falta el ID del juego']);
    exit();
}

$usuario_id = $_SESSION['usuario_id'];

// Eliminar el registro correspondiente en la tabla de alertas
$eliminar = $conexion->prepare(
    "DELETE FROM alertas WHERE usuario_id = ? AND game_id = ?"
);
$eliminar->bind_param("is", $usuario_id, $game_id);
$eliminar->execute();

// Retornar respuesta al cliente según el resultado de la eliminación
if ($eliminar->affected_rows > 0) {
    echo json_encode(['ok' => true, 'mensaje' => 'Alerta eliminada correctamente']);
} else {
    echo json_encode(['ok' => false, 'mensaje' => 'No se encontró la alerta especificada']);
}

// Cerrar conexión
$conexion->close();
exit();
?>
