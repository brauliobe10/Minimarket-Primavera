<?php
require_once __DIR__ . '/../AuthController.php';
require_once __DIR__ . '/../../config/Database.php';
use Config\Database;
header('Content-Type: application/json');
requireAdmin();
$conn = (new Database())->conectar();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $d = json_decode(file_get_contents('php://input'), true);
    if (!is_array($d)) {
        echo json_encode(['ok' => false, 'mensaje' => 'Datos inválidos.']);
        exit;
    }
    $accion = trim($d['accion'] ?? '');

    if ($accion === 'toggle_estado') {
        $id     = (int)($d['id_usuario'] ?? 0);
        $estado = (int)($d['estado']     ?? 0);
        if ($id <= 0) {
            echo json_encode(['ok' => false, 'mensaje' => 'ID de usuario inválido.']);
            exit;
        }
        try {
            $conn->prepare("UPDATE usuario SET estado=? WHERE id_usuario=?")->execute([$estado, $id]);
            echo json_encode(['ok' => true]);
        } catch (\PDOException $e) {
            error_log('[UsuariosController] ' . $e->getMessage());
            echo json_encode(['ok' => false, 'mensaje' => 'Error al cambiar estado del usuario.']);
        }

    } elseif ($accion === 'asignar_roles') {
        $id_usuario = (int)($d['id_usuario'] ?? 0);
        $roles      = is_array($d['roles'] ?? null) ? array_map('intval', $d['roles']) : [];

        if ($id_usuario <= 0) {
            echo json_encode(['ok' => false, 'mensaje' => 'ID de usuario inválido.']);
            exit;
        }

        $conn->beginTransaction();
        try {
            $conn->prepare("DELETE FROM usuario_rol WHERE id_usuario=?")->execute([$id_usuario]);
            $stmt = $conn->prepare("INSERT INTO usuario_rol (id_usuario, id_rol) VALUES (?, ?)");
            foreach ($roles as $r) {
                $stmt->execute([$id_usuario, $r]);
            }

            // Si tiene rol de repartidor (3), asegurar que exista en la tabla repartidor
            if (in_array(3, $roles, true)) {
                $existe = $conn->prepare("SELECT id_repartidor FROM repartidor WHERE id_usuario=?");
                $existe->execute([$id_usuario]);
                if (!$existe->fetch()) {
                    $u = $conn->prepare("SELECT nombres, apellidos, telefono FROM usuario WHERE id_usuario=?");
                    $u->execute([$id_usuario]);
                    $usr = $u->fetch(\PDO::FETCH_ASSOC);
                    $conn->prepare("INSERT INTO repartidor (id_usuario, nombres, telefono, estado) VALUES (?,?,?,0)")
                         ->execute([
                             $id_usuario,
                             trim(($usr['nombres'] ?? '') . ' ' . ($usr['apellidos'] ?? '')),
                             $usr['telefono'] ?? null,
                         ]);
                }
            } else {
                // Si ya no tiene el rol, eliminarlo de la tabla repartidor
                $conn->prepare("DELETE FROM repartidor WHERE id_usuario=?")->execute([$id_usuario]);
            }

            // Sincronizar automáticamente con tabla trabajador si tiene cargo operativo
            $rolesOperativos = [
                3 => 'Repartidor',
                4 => 'Almacenero',
                5 => 'Despachador',
                6 => 'Cajero'
            ];
            $rolAsignado = null;
            foreach ($rolesOperativos as $rId => $rCargo) {
                if (in_array($rId, $roles, true)) {
                    $rolAsignado = $rCargo;
                    break;
                }
            }

            if ($rolAsignado) {
                $checkTrab = $conn->prepare("SELECT id_trabajador FROM trabajador WHERE id_usuario=?");
                $checkTrab->execute([$id_usuario]);
                if (!$checkTrab->fetch()) {
                    $conn->prepare("INSERT INTO trabajador (id_usuario, cargo, turno, estado) VALUES (?, ?, 'Mañana', 1)")
                         ->execute([$id_usuario, $rolAsignado]);
                } else {
                    $conn->prepare("UPDATE trabajador SET cargo=?, estado=1 WHERE id_usuario=?")
                         ->execute([$rolAsignado, $id_usuario]);
                }
            }

            $conn->commit();
            echo json_encode(['ok' => true]);
        } catch (\Exception $e) {
            $conn->rollBack();
            error_log('[UsuariosController] ' . $e->getMessage());
            echo json_encode(['ok' => false, 'mensaje' => 'Error al asignar roles.']);
        }
    } else {
        echo json_encode(['ok' => false, 'mensaje' => 'Acción no reconocida.']);
    }
    exit;
}

$usuarios = $conn->query("
    SELECT u.id_usuario, u.nombres, u.apellidos, u.correo, u.telefono,
           u.dni, u.fecha_registro, u.estado, GROUP_CONCAT(r.nombre_rol) AS roles
    FROM usuario u
    LEFT JOIN usuario_rol ur ON u.id_usuario=ur.id_usuario
    LEFT JOIN rol r ON ur.id_rol=r.id_rol
    GROUP BY u.id_usuario ORDER BY u.id_usuario ASC")->fetchAll(\PDO::FETCH_ASSOC);
echo json_encode(['ok'=>true,'usuarios'=>$usuarios]);
