<?php
session_start();

// Verificar que hay sesión activa
if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'No autorizado']);
    exit();
}

// Verificar que se envió un archivo
if (!isset($_FILES['foto']) || $_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(['error' => 'No se recibió archivo']);
    exit();
}

// Configuración
$usuario_id = $_SESSION['usuario_id'];
$directorio_destino = __DIR__ . '/../assets/img/perfiles/';
$archivo_temporal = $_FILES['foto']['tmp_name'];
$nombre_original = $_FILES['foto']['name'];
$tamanio_archivo = $_FILES['foto']['size'];

// Validaciones
$extensiones_permitidas = ['jpg', 'jpeg', 'png', 'gif'];
$tamanio_maximo = 2 * 1024 * 1024; // 2MB

// Validar tamaño
if ($tamanio_archivo > $tamanio_maximo) {
    http_response_code(400);
    echo json_encode(['error' => 'Archivo muy grande. Máximo 2MB']);
    exit();
}

// Validar extensión
$extension = strtolower(pathinfo($nombre_original, PATHINFO_EXTENSION));
if (!in_array($extension, $extensiones_permitidas)) {
    http_response_code(400);
    echo json_encode(['error' => 'Tipo de archivo no permitido. Solo JPG, PNG, GIF']);
    exit();
}

// Validar que es una imagen real usando getimagesize()
if (@getimagesize($archivo_temporal) === false) {
    http_response_code(400);
    echo json_encode(['error' => 'El archivo no es una imagen válida']);
    exit();
}

// Crear directorio si no existe
if (!is_dir($directorio_destino)) {
    mkdir($directorio_destino, 0755, true);
}

// Generar nombre único con el ID del usuario
$nombre_archivo = 'perfil_' . $usuario_id . '.' . $extension;
$ruta_destino = $directorio_destino . $nombre_archivo;

// Eliminar foto anterior si existe (salvo default.jpg)
if (file_exists($ruta_destino)) {
    unlink($ruta_destino);
}

// Mover archivo
if (!move_uploaded_file($archivo_temporal, $ruta_destino)) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al guardar el archivo']);
    exit();
}

// Conectar a BD y actualizar
require_once 'conexion.php';

$nombre_guardado = 'perfil_' . $usuario_id . '.' . $extension;
$sql = "UPDATE usuarios SET foto = ? WHERE id = ?";
$stmt = $conexion->prepare($sql);

if (!$stmt) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al preparar consulta: ' . $conexion->error]);
    exit();
}

$stmt->bind_param('si', $nombre_guardado, $usuario_id);

if (!$stmt->execute()) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al actualizar base de datos']);
    exit();
}

$stmt->close();
$conexion->close();

// Respuesta exitosa
http_response_code(200);
echo json_encode([
    'success' => true,
    'foto' => 'assets/img/perfiles/' . $nombre_guardado . '?' . time(),
    'mensaje' => 'Foto de perfil actualizada'
]);
?>
