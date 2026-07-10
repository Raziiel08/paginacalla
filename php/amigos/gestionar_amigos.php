<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['ok' => false, 'mensaje' => 'Sesión no iniciada.']);
    exit();
}

require_once '../conexion.php';

$usuario_id = $_SESSION['usuario_id'];
$accion = $_POST['accion'] ?? '';

if (empty($accion)) {
    echo json_encode(['ok' => false, 'mensaje' => 'Acción no especificada.']);
    exit();
}

switch ($accion) {
    case 'enviar':
        $input = trim($_POST['amigo_busqueda'] ?? '');
        if (empty($input)) {
            echo json_encode(['ok' => false, 'mensaje' => 'Ingresá un nombre o #ID de amigo.']);
            exit();
        }

        $amigo_id = 0;
        if (strpos($input, '#') === 0) {
            $amigo_id = intval(substr($input, 1));
        } elseif (is_numeric($input)) {
            $amigo_id = intval($input);
        }

        // Buscar al amigo en la BD
        if ($amigo_id > 0) {
            $sql_user = "SELECT id, nombre FROM usuarios WHERE id = ?";
            $stmt_user = $conexion->prepare($sql_user);
            $stmt_user->bind_param('i', $amigo_id);
        } else {
            $sql_user = "SELECT id, nombre FROM usuarios WHERE nombre = ?";
            $stmt_user = $conexion->prepare($sql_user);
            $stmt_user->bind_param('s', $input);
        }

        $stmt_user->execute();
        $res_user = $stmt_user->get_result();

        if ($res_user->num_rows === 0) {
            echo json_encode(['ok' => false, 'mensaje' => 'Usuario no encontrado.']);
            $stmt_user->close();
            exit();
        }

        $amigo_row = $res_user->fetch_assoc();
        $amigo_id = $amigo_row['id'];
        $amigo_nombre = $amigo_row['nombre'];
        $stmt_user->close();

        if ($amigo_id === $usuario_id) {
            echo json_encode(['ok' => false, 'mensaje' => 'No podés agregarte a vos mismo.']);
            exit();
        }

        // Verificar si ya existe una relación
        $sql_rel = "SELECT id, usuario_id, amigo_id, estado FROM amigos WHERE (usuario_id = ? AND amigo_id = ?) OR (usuario_id = ? AND amigo_id = ?)";
        $stmt_rel = $conexion->prepare($sql_rel);
        $stmt_rel->bind_param('iiii', $usuario_id, $amigo_id, $amigo_id, $usuario_id);
        $stmt_rel->execute();
        $res_rel = $stmt_rel->get_result();

        if ($res_rel->num_rows > 0) {
            $rel = $res_rel->fetch_assoc();
            if ($rel['estado'] === 'aceptado') {
                echo json_encode(['ok' => false, 'mensaje' => 'Ya son amigos.']);
            } elseif ($rel['usuario_id'] === $usuario_id) {
                echo json_encode(['ok' => false, 'mensaje' => 'Ya enviaste una solicitud de amistad a este usuario.']);
            } else {
                echo json_encode(['ok' => false, 'mensaje' => 'Este usuario ya te envió una solicitud. ¡Revisá tus solicitudes pendientes!']);
            }
            $stmt_rel->close();
            exit();
        }
        $stmt_rel->close();

        // Crear la solicitud
        $sql_insert = "INSERT INTO amigos (usuario_id, amigo_id, estado) VALUES (?, ?, 'pendiente')";
        $stmt_insert = $conexion->prepare($sql_insert);
        $stmt_insert->bind_param('ii', $usuario_id, $amigo_id);

        if ($stmt_insert->execute()) {
            echo json_encode(['ok' => true, 'mensaje' => 'Solicitud de amistad enviada a ' . htmlspecialchars($amigo_nombre) . '.']);
        } else {
            echo json_encode(['ok' => false, 'mensaje' => 'Error al enviar la solicitud.']);
        }
        $stmt_insert->close();
        break;

    case 'aceptar':
        $solicitud_id = intval($_POST['solicitud_id'] ?? 0);
        if ($solicitud_id <= 0) {
            echo json_encode(['ok' => false, 'mensaje' => 'ID de solicitud inválido.']);
            exit();
        }

        // El amigo_id de la solicitud debe ser el usuario logueado
        $sql_accept = "UPDATE amigos SET estado = 'aceptado' WHERE id = ? AND amigo_id = ?";
        $stmt_accept = $conexion->prepare($sql_accept);
        $stmt_accept->bind_param('ii', $solicitud_id, $usuario_id);

        if ($stmt_accept->execute() && $stmt_accept->affected_rows > 0) {
            echo json_encode(['ok' => true, 'mensaje' => 'Solicitud de amistad aceptada.']);
        } else {
            echo json_encode(['ok' => false, 'mensaje' => 'No se pudo aceptar la solicitud.']);
        }
        $stmt_accept->close();
        break;

    case 'rechazar':
    case 'eliminar':
        // Si eliminamos una relación, puede ser por id de la tabla amigos o por id de usuario del amigo
        $solicitud_id = intval($_POST['solicitud_id'] ?? 0);
        $amigo_user_id = intval($_POST['amigo_user_id'] ?? 0);

        if ($solicitud_id > 0) {
            // Cancelar o rechazar solicitud por ID de fila
            // Debe involucrar al usuario logueado (como remitente o destinatario)
            $sql_del = "DELETE FROM amigos WHERE id = ? AND (usuario_id = ? OR amigo_id = ?)";
            $stmt_del = $conexion->prepare($sql_del);
            $stmt_del->bind_param('iii', $solicitud_id, $usuario_id, $usuario_id);
        } elseif ($amigo_user_id > 0) {
            // Eliminar amigo por ID de usuario
            $sql_del = "DELETE FROM amigos WHERE ((usuario_id = ? AND amigo_id = ?) OR (usuario_id = ? AND amigo_id = ?)) AND estado = 'aceptado'";
            $stmt_del = $conexion->prepare($sql_del);
            $stmt_del->bind_param('iiii', $usuario_id, $amigo_user_id, $amigo_user_id, $usuario_id);
        } else {
            echo json_encode(['ok' => false, 'mensaje' => 'Datos insuficientes para eliminar.']);
            exit();
        }

        if ($stmt_del->execute() && $stmt_del->affected_rows > 0) {
            $msg = $accion === 'rechazar' ? 'Solicitud cancelada/rechazada.' : 'Amigo eliminado de tu lista.';
            echo json_encode(['ok' => true, 'mensaje' => $msg]);
        } else {
            echo json_encode(['ok' => false, 'mensaje' => 'No se pudo realizar la operación.']);
        }
        $stmt_del->close();
        break;

    default:
        echo json_encode(['ok' => false, 'mensaje' => 'Acción no válida.']);
        break;
}

$conexion->close();
exit();
?>
