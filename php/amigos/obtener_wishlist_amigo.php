<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['ok' => false, 'mensaje' => 'Sesión no iniciada.']);
    exit();
}

require_once '../conexion.php';

$usuario_id = $_SESSION['usuario_id'];
$amigo_id = intval($_GET['amigo_id'] ?? 0);

if ($amigo_id <= 0) {
    echo json_encode(['ok' => false, 'mensaje' => 'ID de amigo inválido.']);
    exit();
}

// 1. Verificar si hay relación de amistad aceptada
$sql_rel = "SELECT id FROM amigos WHERE ((usuario_id = ? AND amigo_id = ?) OR (usuario_id = ? AND amigo_id = ?)) AND estado = 'aceptado'";
$stmt_rel = $conexion->prepare($sql_rel);
$stmt_rel->bind_param('iiii', $usuario_id, $amigo_id, $amigo_id, $usuario_id);
$stmt_rel->execute();
$res_rel = $stmt_rel->get_result();

if ($res_rel->num_rows === 0) {
    echo json_encode(['ok' => false, 'mensaje' => 'No tenés permiso para ver esta wishlist (no son amigos).']);
    $stmt_rel->close();
    $conexion->close();
    exit();
}
$stmt_rel->close();

// 2. Verificar si la wishlist del amigo es pública
$sql_pub = "SELECT nombre, wishlist_publica FROM usuarios WHERE id = ?";
$stmt_pub = $conexion->prepare($sql_pub);
$stmt_pub->bind_param('i', $amigo_id);
$stmt_pub->execute();
$res_pub = $stmt_pub->get_result();
$amigo_row = $res_pub->fetch_assoc();
$stmt_pub->close();

if (intval($amigo_row['wishlist_publica']) !== 1) {
    echo json_encode(['ok' => false, 'privada' => true, 'mensaje' => 'La wishlist de ' . htmlspecialchars($amigo_row['nombre']) . ' es privada.']);
    $conexion->close();
    exit();
}

// 3. Obtener wishlist
$sql_wish = "SELECT game_id, game_nombre, fecha_agregado FROM wishlist WHERE usuario_id = ? ORDER BY fecha_agregado DESC";
$stmt_wish = $conexion->prepare($sql_wish);
$stmt_wish->bind_param('i', $amigo_id);
$stmt_wish->execute();
$res_wish = $stmt_wish->get_result();

$juegos = [];
while ($row = $res_wish->fetch_assoc()) {
    $juegos[] = $row;
}
$stmt_wish->close();
$conexion->close();

echo json_encode([
    'ok' => true,
    'amigo_nombre' => $amigo_row['nombre'],
    'wishlist' => $juegos
]);
exit();
?>
