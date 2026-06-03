<?php
// Iniciar sesión para identificar al usuario
session_start();

// Validar que el usuario haya iniciado sesión
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['ok' => false, 'mensaje' => 'Tenés que iniciar sesión']);
    exit();
}

// Conectar con la base de datos MySQL
require_once '../conexion.php';

// Limpiar y recibir los datos enviados desde el cliente
$game_id         = trim($_POST['game_id'] ?? '');
$game_nombre     = trim($_POST['game_nombre'] ?? '');
$precio_objetivo = trim($_POST['precio_objetivo'] ?? '');

// Validar que todos los campos requeridos tengan valor
if (empty($game_id) || empty($game_nombre) || $precio_objetivo === '') {
    echo json_encode(['ok' => false, 'mensaje' => 'Faltan datos requeridos']);
    exit();
}

// Validar que el precio ingresado sea un número decimal mayor a cero
$precio_objetivo = floatval($precio_objetivo);
if ($precio_objetivo <= 0) {
    echo json_encode(['ok' => false, 'mensaje' => 'El precio debe ser mayor a 0']);
    exit();
}

$usuario_id = $_SESSION['usuario_id'];

// Consultar si el usuario ya configuró una alerta para este mismo videojuego
$consulta = $conexion->prepare("SELECT id FROM alertas WHERE usuario_id = ? AND game_id = ?");
$consulta->bind_param("is", $usuario_id, $game_id);
$consulta->execute();
$resultado = $consulta->get_result();

if ($resultado->num_rows > 0) {
    // Si ya existe la alerta, actualizamos el precio objetivo y reseteamos el estado de email_notificado
    $actualizar = $conexion->prepare("UPDATE alertas SET precio_objetivo = ?, email_notificado = 0 WHERE usuario_id = ? AND game_id = ?");
    $actualizar->bind_param("dis", $precio_objetivo, $usuario_id, $game_id);
    $actualizar->execute();
    
    echo json_encode(['ok' => true, 'mensaje' => 'Alerta de precio actualizada correctamente']);
} else {
    // Si no existe, insertamos un nuevo registro en la tabla de alertas
    $insertar = $conexion->prepare("INSERT INTO alertas (usuario_id, game_id, game_nombre, precio_objetivo, email_notificado) VALUES (?, ?, ?, ?, 0)");
    $insertar->bind_param("issd", $usuario_id, $game_id, $game_nombre, $precio_objetivo);
    $insertar->execute();
    
    echo json_encode(['ok' => true, 'mensaje' => 'Alerta de precio creada correctamente']);
}

// Cerrar conexión
$conexion->close();
exit();
?>
