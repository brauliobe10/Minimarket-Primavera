<?php
require_once __DIR__ . '/../AuthController.php';
require_once __DIR__ . '/../../config/Database.php';
use Config\Database;
header('Content-Type: application/json');
requireAdmin();
$conn = (new Database())->conectar();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $d  = json_decode(file_get_contents('php://input'), true);
    if (!is_array($d)) {
        echo json_encode(['ok' => false, 'mensaje' => 'Datos inválidos.']);
        exit;
    }
    $ac = trim($d['accion'] ?? '');

    try {

    /* ── Asignar rol repartidor a un usuario ── */
    if ($ac === 'asignar_rol') {
        $idUsuario = (int) $d['id_usuario'];

        // Verificar que no tenga ya el rol
        $check = $conn->prepare("SELECT 1 FROM usuario_rol WHERE id_usuario=? AND id_rol=3");
        $check->execute([$idUsuario]);
        if (!$check->fetch()) {
            $conn->prepare("INSERT INTO usuario_rol (id_usuario, id_rol) VALUES (?,3)")
                 ->execute([$idUsuario]);
        }

        // Obtener datos del usuario
        $u = $conn->prepare("SELECT nombres, apellidos, telefono FROM usuario WHERE id_usuario=?");
        $u->execute([$idUsuario]);
        $usr = $u->fetch(\PDO::FETCH_ASSOC);

        // Crear entrada en repartidor si no existe
        $existe = $conn->prepare("SELECT id_repartidor FROM repartidor WHERE id_usuario=?");
        $existe->execute([$idUsuario]);
        if (!$existe->fetch()) {
            $conn->prepare("INSERT INTO repartidor (id_usuario, nombres, telefono, estado)
                            VALUES (?,?,?,1)")
                 ->execute([
                     $idUsuario,
                     trim(($usr['nombres'] ?? '') . ' ' . ($usr['apellidos'] ?? '')),
                     $usr['telefono'] ?? null
                 ]);
        }
        echo json_encode(['ok'=>true,'mensaje'=>'Rol de repartidor asignado']);

    /* ── Quitar rol repartidor ── */
    } elseif ($ac === 'quitar_rol') {
        $idUsuario = (int) $d['id_usuario'];
        $conn->prepare("DELETE FROM usuario_rol WHERE id_usuario=? AND id_rol=3")
             ->execute([$idUsuario]);
        $conn->prepare("UPDATE repartidor SET estado=0 WHERE id_usuario=?")
             ->execute([$idUsuario]);
        echo json_encode(['ok'=>true]);

    /* ── Actualizar datos del repartidor ── */
    } elseif ($ac === 'actualizar') {
        $conn->prepare("UPDATE repartidor SET placa_vehiculo=?, telefono=? WHERE id_repartidor=?")
             ->execute([$d['placa'] ?? null, $d['telefono'] ?? null, (int)$d['id_repartidor']]);
        echo json_encode(['ok'=>true]);

    /* ── Toggle estado repartidor ── */
    } elseif ($ac === 'toggle_estado') {
        $estadoNuevo = (int)$d['estado'];
        
        if ($estadoNuevo === 1) {
            $stmt = $conn->prepare("
                SELECT r.telefono, r.placa_vehiculo, u.dni, u.telefono AS usr_tel 
                FROM repartidor r 
                LEFT JOIN usuario u ON r.id_usuario = u.id_usuario 
                WHERE r.id_repartidor = ?
            ");
            $stmt->execute([(int)$d['id_repartidor']]);
            $rep = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            $dniOk = !empty($rep['dni']) && preg_match('/^[0-9]{8}$/', $rep['dni']);
            $telOk = !empty($rep['telefono']) || !empty($rep['usr_tel']);
            $placaOk = !empty($rep['placa_vehiculo']);
            
            // Si faltan datos, se queda en 0 (Pendiente en la UI)
            if (!$dniOk || !$telOk || !$placaOk) {
                $estadoNuevo = 0;
            }
        }

        $conn->prepare("UPDATE repartidor SET estado=? WHERE id_repartidor=?")
             ->execute([$estadoNuevo, (int)$d['id_repartidor']]);
        echo json_encode(['ok'=>true]);

    /* ── Eliminar repartidor (para limpiar duplicados) ── */
    } elseif ($ac === 'eliminar') {
        $idRepartidor = (int) $d['id_repartidor'];

        // No permitir borrar si tiene entregas en curso (evita dejar pedidos huérfanos)
        $enCurso = $conn->prepare("SELECT COUNT(*) FROM delivery WHERE id_repartidor=? AND estado_delivery IN ('Asignado','En camino')");
        $enCurso->execute([$idRepartidor]);
        if ($enCurso->fetchColumn() > 0) {
            echo json_encode(['ok'=>false,'mensaje'=>'Este repartidor tiene entregas en curso. Espera a que termine o reasígnalas antes de eliminarlo.']);
            exit;
        }

        // Liberar id_usuario para poder quitarle el rol después de borrar la fila
        $idUsr = $conn->prepare("SELECT id_usuario FROM repartidor WHERE id_repartidor=?");
        $idUsr->execute([$idRepartidor]);
        $idUsuarioRepartidor = $idUsr->fetchColumn();

        // Desvincular su historial de entregas ya completadas (se conservan, solo se quita la referencia)
        $conn->prepare("UPDATE delivery SET id_repartidor=NULL WHERE id_repartidor=?")->execute([$idRepartidor]);
        $conn->prepare("DELETE FROM repartidor WHERE id_repartidor=?")->execute([$idRepartidor]);

        // Quitarle el rol de repartidor: vuelve a ser un cliente normal
        if ($idUsuarioRepartidor) {
            $conn->prepare("DELETE FROM usuario_rol WHERE id_usuario=? AND id_rol=3")
                 ->execute([$idUsuarioRepartidor]);
        }

        echo json_encode(['ok'=>true]);
    }
    } catch (\Exception $e) {
        error_log('[RepartidorController] ' . $e->getMessage());
        echo json_encode(['ok' => false, 'mensaje' => 'Error en la operación. Inténtalo de nuevo.']);
    }
    exit;
}

/* ── GET: lista repartidores con datos de usuario ── */
$repartidores = $conn->query("
    SELECT r.id_repartidor, r.nombres, r.telefono, r.placa_vehiculo,
           r.estado, r.id_usuario,
           u.correo, u.dni,
           (u.dni IS NOT NULL AND u.dni REGEXP '^[0-9]{8}$') AS dni_valido,
           (u.dni IS NOT NULL AND u.dni REGEXP '^[0-9]{8}$'
            AND r.telefono IS NOT NULL AND r.telefono != ''
            AND r.placa_vehiculo IS NOT NULL AND r.placa_vehiculo != '') AS datos_completos,
           COUNT(DISTINCT dv.id_delivery) AS total_deliveries,
           COUNT(DISTINCT CASE WHEN dv.estado_delivery='Entregado' THEN dv.id_delivery END) AS entregados
    FROM repartidor r
    LEFT JOIN usuario u ON r.id_usuario = u.id_usuario
    LEFT JOIN delivery dv ON r.id_repartidor = dv.id_repartidor
    GROUP BY r.id_repartidor
    ORDER BY r.id_repartidor DESC
")->fetchAll(\PDO::FETCH_ASSOC);

// Usuarios que podrían ser repartidores (rol Cliente, sin rol Repartidor)
$candidatos = $conn->query("
    SELECT u.id_usuario, u.nombres, u.apellidos, u.correo, u.telefono
    FROM usuario u
    JOIN usuario_rol ur ON u.id_usuario = ur.id_usuario AND ur.id_rol = 2
    WHERE u.estado = 1
    AND u.id_usuario NOT IN (
        SELECT id_usuario FROM usuario_rol WHERE id_rol = 3
    )
    ORDER BY u.nombres
")->fetchAll(\PDO::FETCH_ASSOC);

echo json_encode([
    'ok'          => true,
    'repartidores'=> $repartidores,
    'candidatos'  => $candidatos
]);
