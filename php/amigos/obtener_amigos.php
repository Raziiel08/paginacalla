<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['ok' => false, 'mensaje' => 'Sesión no iniciada.']);
    exit();
}

require_once '../conexion.php';

$usuario_id = $_SESSION['usuario_id'];

// 1. Obtener amigos aceptados
$sql_amigos = "
    SELECT a.id AS relacion_id, 
           u.id AS amigo_id, 
           u.nombre AS amigo_nombre, 
           u.foto AS amigo_foto, 
           u.wishlist_publica
    FROM amigos a
    JOIN usuarios u ON (a.usuario_id = u.id OR a.amigo_id = u.id)
    WHERE (a.usuario_id = ? OR a.amigo_id = ?) 
      AND a.estado = 'aceptado'
      AND u.id != ?
    ORDER BY u.nombre ASC
";
$stmt_amigos = $conexion->prepare($sql_amigos);
$stmt_amigos->bind_param('iii', $usuario_id, $usuario_id, $usuario_id);
$stmt_amigos->execute();
$res_amigos = $stmt_amigos->get_result();

$amigos = [];
while ($row = $res_amigos->fetch_assoc()) {
    $amigos[] = $row;
}
$stmt_amigos->close();

// 2. Obtener solicitudes recibidas (pendientes)
$sql_recibidas = "
    SELECT a.id AS relacion_id, 
           u.id AS usuario_id, 
           u.nombre AS usuario_nombre, 
           u.foto AS usuario_foto
    FROM amigos a
    JOIN usuarios u ON a.usuario_id = u.id
    WHERE a.amigo_id = ? AND a.estado = 'pendiente'
    ORDER BY a.fecha_creacion DESC
";
$stmt_recibidas = $conexion->prepare($sql_recibidas);
$stmt_recibidas->bind_param('i', $usuario_id);
$stmt_recibidas->execute();
$res_recibidas = $stmt_recibidas->get_result();

$recibidas = [];
while ($row = $res_recibidas->fetch_assoc()) {
    $recibidas[] = $row;
}
$stmt_recibidas->close();

// 3. Obtener solicitudes enviadas (pendientes)
$sql_enviadas = "
    SELECT a.id AS relacion_id, 
           u.id AS amigo_id, 
           u.nombre AS amigo_nombre, 
           u.foto AS amigo_foto
    FROM amigos a
    JOIN usuarios u ON a.amigo_id = u.id
    WHERE a.usuario_id = ? AND a.estado = 'pendiente'
    ORDER BY a.fecha_creacion DESC
";
$stmt_enviadas = $conexion->prepare($sql_enviadas);
$stmt_enviadas->bind_param('i', $usuario_id);
$stmt_enviadas->execute();
$res_enviadas = $stmt_enviadas->get_result();

$enviadas = [];
while ($row = $res_enviadas->fetch_assoc()) {
    $enviadas[] = $row;
}
$stmt_enviadas->close();

$conexion->close();

echo json_encode([
    'ok' => true,
    'amigos' => $amigos,
    'recibidas' => $recibidas,
    'enviadas' => $enviadas
]);
exit();
?>
