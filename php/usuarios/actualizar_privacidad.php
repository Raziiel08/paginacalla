<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['ok' => false, 'mensaje' => 'Sesión no iniciada.']);
    exit();
}

if (!isset($_POST['wishlist_publica'])) {
    echo json_encode(['ok' => false, 'mensaje' => 'Datos insuficientes.']);
    exit();
}

require_once '../conexion.php';

$usuario_id = $_SESSION['usuario_id'];
$wishlist_publica = intval($_POST['wishlist_publica']) === 1 ? 1 : 0;

$sql = "UPDATE usuarios SET wishlist_publica = ? WHERE id = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param('ii', $wishlist_publica, $usuario_id);

if ($stmt->execute()) {
    echo json_encode(['ok' => true, 'mensaje' => 'Privacidad de wishlist actualizada.']);
} else {
    echo json_encode(['ok' => false, 'mensaje' => 'Error al actualizar la privacidad.']);
}

$stmt->close();
$conexion->close();
exit();
?>
