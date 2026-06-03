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

// Obtener y limpiar datos del POST
$game_id         = trim($_POST['game_id'] ?? '');
$precio_objetivo = trim($_POST['precio_objetivo'] ?? '');

// Validar que los campos no estén vacíos
if (empty($game_id) || $precio_objetivo === '') {
    echo json_encode(['ok' => false, 'mensaje' => 'Faltan datos requeridos']);
    exit();
}

// Validar formato numérico del precio
$precio_objetivo = floatval($precio_objetivo);
if ($precio_objetivo <= 0) {
    echo json_encode(['ok' => false, 'mensaje' => 'El precio debe ser mayor a 0']);
    exit();
}

$usuario_id = $_SESSION['usuario_id'];

// Actualizar el precio objetivo y resetear el estado de la notificación por correo de la alerta correspondiente
$actualizar = $conexion->prepare(
    "UPDATE alertas SET precio_objetivo = ?, email_notificado = 0 WHERE usuario_id = ? AND game_id = ?"
);
$actualizar->bind_param("dis", $precio_objetivo, $usuario_id, $game_id);
$actualizar->execute();

// Retornar éxito si la consulta no falló
if ($actualizar->affected_rows > 0 || $actualizar->errno === 0) {
    echo json_encode(['ok' => true, 'mensaje' => 'Precio objetivo actualizado correctamente']);
} else {
    echo json_encode(['ok' => false, 'mensaje' => 'No se encontró la alerta o no hubo cambios']);
}

// Cerrar conexión
$conexion->close();
exit();
?>
