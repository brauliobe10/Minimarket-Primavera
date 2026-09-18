<?php
require_once __DIR__ . '/AuthController.php';
require_once __DIR__ . '/../config/Database.php';
use Config\Database;
header('Content-Type: application/json');

if (!estaLogueado()) {
    echo json_encode(['ok'=>false,'mensaje'=>'No autenticado']);
    exit;
}

require_once __DIR__ . '/../config/MailHelper.php';
$conn = (new \Config\Database())->conectar();

// Helper para notificar por correo
function notificarCambioEstado($conn, $idDelivery, $estado) {
    try {
        $stmt = $conn->prepare("
            SELECT p.id_pedido, p.nombre_cliente, p.correo_cliente, u.correo, u.nombres, u.apellidos
            FROM pedido p
            JOIN delivery d ON p.id_pedido = d.id_pedido
            LEFT JOIN usuario u ON p.id_usuario = u.id_usuario
            WHERE d.id_delivery = ?
        ");
        $stmt->execute([$idDelivery]);
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $email = $row['correo'] ?: $row['correo_cliente'];
            $nombre = trim("{$row['nombres']} {$row['apellidos']}") ?: $row['nombre_cliente'];
            if ($email) {
                $mailHelper = new \Config\MailHelper();
                $mailHelper->enviarActualizacionEstado($email, $nombre, $row['id_pedido'], $estado);
            }
        }
    } catch (\Exception $e) {
        error_log("Error notificando cambio de estado: " . $e->getMessage());
    }
}

if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['es_repartidor']) || !$_SESSION['es_repartidor']) {
    echo json_encode(['ok'=>false,'mensaje'=>'No autorizado']);
    exit;
}

// Refrescar roles desde BD
sincronizarRoles();

$idUsuario = (int) $_SESSION['usuario_id'];

// Buscar repartidor (sin filtrar por estado, para no crear duplicados si está desactivado)
$check = $conn->prepare("
    SELECT r.id_repartidor, r.nombres, r.estado
    FROM repartidor r
    WHERE r.id_usuario = ?
    LIMIT 1
");
$check->execute([$idUsuario]);
$repartidor = $check->fetch(\PDO::FETCH_ASSOC);

// Fallback: si no tiene id_usuario enlazado, buscar por usuario_rol
if (!$repartidor) {
    // Verificar que el usuario tiene rol repartidor
    $rolCheck = $conn->prepare("SELECT 1 FROM usuario_rol WHERE id_usuario=? AND id_rol=3");
    $rolCheck->execute([$idUsuario]);
    if ($rolCheck->fetch()) {
        // Buscar repartidor por nombre (usando datos del usuario), sin filtrar por estado
        $uStmt = $conn->prepare("SELECT nombres, apellidos FROM usuario WHERE id_usuario=?");
        $uStmt->execute([$idUsuario]);
        $uDatos = $uStmt->fetch(\PDO::FETCH_ASSOC);
        $nombreCompleto = trim(($uDatos['nombres'] ?? '') . ' ' . ($uDatos['apellidos'] ?? ''));

        $rCheck = $conn->prepare("SELECT id_repartidor, nombres, estado FROM repartidor WHERE nombres LIKE ? LIMIT 1");
        $rCheck->execute(["%{$nombreCompleto}%"]);
        $repartidor = $rCheck->fetch(\PDO::FETCH_ASSOC);

        // Si encontró, vincular id_usuario automáticamente
        if ($repartidor) {
            try {
                $conn->prepare("UPDATE repartidor SET id_usuario=? WHERE id_repartidor=?")
                     ->execute([$idUsuario, $repartidor['id_repartidor']]);
            } catch (\Exception $e) { /* columna puede no existir aún */ }
        } else {
            // Crear entrada repartidor automáticamente (solo si de verdad no existe ninguna)
            try {
                $uStmt2 = $conn->prepare("SELECT telefono FROM usuario WHERE id_usuario=?");
                $uStmt2->execute([$idUsuario]);
                $tel = $uStmt2->fetchColumn();
                $conn->prepare("INSERT INTO repartidor (id_usuario, nombres, telefono, estado) VALUES (?,?,?,1)")
                     ->execute([$idUsuario, $nombreCompleto, $tel ?: null]);
                $nuevoId = (int)$conn->lastInsertId();
                $repartidor = ['id_repartidor' => $nuevoId, 'nombres' => $nombreCompleto, 'estado' => 1];
            } catch (\Exception $e) {
                echo json_encode(['ok'=>false,'mensaje'=>'No se pudo crear el perfil de repartidor: ' . $e->getMessage()]);
                exit;
            }
        }
    }
}

// Si el repartidor existe pero está desactivado por el admin, no dejar entrar ni crear duplicados
if ($repartidor && (int)$repartidor['estado'] === 0) {
    echo json_encode(['ok'=>false,'mensaje'=>'Tu cuenta de repartidor está desactivada por el administrador. Contáctalo si crees que es un error.']);
    exit;
}

if (!$repartidor) {
    echo json_encode(['ok'=>false,'mensaje'=>'No eres repartidor activo']);
    exit;
}

$idRepartidor = (int) $repartidor['id_repartidor'];

// ── Validar DNI del repartidor (usuario.dni debe existir y tener 8 dígitos) ──
$dniStmt = $conn->prepare("SELECT dni FROM usuario WHERE id_usuario = ?");
$dniStmt->execute([$idUsuario]);
$dniActual  = $dniStmt->fetchColumn();
$dniValido  = (bool) ($dniActual && preg_match('/^\d{8}$/', $dniActual));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $d  = json_decode(file_get_contents('php://input'), true);
    $ac = $d['accion'] ?? '';

    // Todas las acciones que implican tomar/aceptar un pedido requieren DNI válido
    if (in_array($ac, ['aceptar_delivery', 'tomar_delivery'], true) && !$dniValido) {
        echo json_encode(['ok'=>false,'mensaje'=>'Debes completar tu DNI en tu perfil antes de poder aceptar pedidos.']);
        exit;
    }

    // Solo se permite tener UNA entrega activa ("En camino") a la vez
    if (in_array($ac, ['aceptar_delivery', 'tomar_delivery'], true)) {
        $activo = $conn->prepare("SELECT id_delivery FROM delivery WHERE id_repartidor=? AND estado_delivery='En camino' LIMIT 1");
        $activo->execute([$idRepartidor]);
        $entregaActiva = $activo->fetchColumn();
        if ($entregaActiva) {
            echo json_encode(['ok'=>false,'mensaje'=>"Ya tienes una entrega en camino (Delivery #{$entregaActiva}). Debes marcarla como entregada antes de aceptar otra."]);
            exit;
        }
    }

    if ($ac === 'aceptar_delivery') {
        // Aceptar un pedido que el admin le asignó (estado 'Asignado')
        try {
            $upd = $conn->prepare("UPDATE delivery SET estado_delivery='En camino', hora_salida=NOW(), motivo_rechazo=NULL
                                   WHERE id_delivery=? AND id_repartidor=? AND estado_delivery='Asignado'");
            $upd->execute([(int)$d['id_delivery'], $idRepartidor]);
        } catch (\PDOException $e) {
            $upd = $conn->prepare("UPDATE delivery SET estado_delivery='En camino', hora_salida=NOW()
                                   WHERE id_delivery=? AND id_repartidor=? AND estado_delivery='Asignado'");
            $upd->execute([(int)$d['id_delivery'], $idRepartidor]);
        }
        if ($upd->rowCount() > 0) {
            $conn->prepare("UPDATE pedido SET estado_pedido='En camino'
                            WHERE id_pedido=(SELECT id_pedido FROM delivery WHERE id_delivery=?)")
                 ->execute([(int)$d['id_delivery']]);
            notificarCambioEstado($conn, (int)$d['id_delivery'], 'En camino');
            echo json_encode(['ok'=>true]);
        } else {
            echo json_encode(['ok'=>false,'mensaje'=>'Este pedido ya no está disponible para aceptar']);
        }

    } elseif ($ac === 'rechazar_delivery') {
        // Rechazar un pedido asignado: vuelve al pool disponible, con motivo visible para el admin
        $motivo = trim((string)($d['motivo'] ?? '')) ?: 'Ocupado con otro pedido';
        $motivoTexto = $repartidor['nombres'] . ': ' . $motivo;

        try {
            $upd = $conn->prepare("UPDATE delivery SET id_repartidor=NULL, estado_delivery='Pendiente', hora_salida=NULL, motivo_rechazo=?
                                   WHERE id_delivery=? AND id_repartidor=? AND estado_delivery='Asignado'");
            $upd->execute([$motivoTexto, (int)$d['id_delivery'], $idRepartidor]);
        } catch (\PDOException $e) {
            $upd = $conn->prepare("UPDATE delivery SET id_repartidor=NULL, estado_delivery='Pendiente', hora_salida=NULL
                                   WHERE id_delivery=? AND id_repartidor=? AND estado_delivery='Asignado'");
            $upd->execute([(int)$d['id_delivery'], $idRepartidor]);
        }
        if ($upd->rowCount() > 0) {
            $conn->prepare("UPDATE pedido SET estado_pedido='Pendiente'
                            WHERE id_pedido=(SELECT id_pedido FROM delivery WHERE id_delivery=?)")
                 ->execute([(int)$d['id_delivery']]);
            echo json_encode(['ok'=>true]);
        } else {
            echo json_encode(['ok'=>false,'mensaje'=>'Este pedido ya no está disponible para rechazar']);
        }

    } elseif ($ac === 'tomar_delivery') {
        // Repartidor toma directamente un delivery sin asignar (pool)
        try {
            $upd = $conn->prepare("UPDATE delivery SET id_repartidor=?, estado_delivery='En camino', hora_salida=NOW(), motivo_rechazo=NULL
                                   WHERE id_delivery=? AND id_repartidor IS NULL AND estado_delivery='Pendiente'");
            $upd->execute([$idRepartidor, (int)$d['id_delivery']]);
        } catch (\PDOException $e) {
            $upd = $conn->prepare("UPDATE delivery SET id_repartidor=?, estado_delivery='En camino', hora_salida=NOW()
                                   WHERE id_delivery=? AND id_repartidor IS NULL AND estado_delivery='Pendiente'");
            $upd->execute([$idRepartidor, (int)$d['id_delivery']]);
        }
        if ($upd->rowCount() > 0) {
            $conn->prepare("UPDATE pedido SET estado_pedido='En camino'
                            WHERE id_pedido=(SELECT id_pedido FROM delivery WHERE id_delivery=?)")
                 ->execute([(int)$d['id_delivery']]);
            notificarCambioEstado($conn, (int)$d['id_delivery'], 'En camino');
            echo json_encode(['ok'=>true]);
        } else {
            echo json_encode(['ok'=>false,'mensaje'=>'Este delivery ya fue tomado por otro repartidor']);
        }

    } elseif ($ac === 'entregar') {
        $conn->prepare("UPDATE delivery SET estado_delivery='Entregado', hora_entrega=NOW()
                        WHERE id_delivery=? AND id_repartidor=?")
             ->execute([(int)$d['id_delivery'], $idRepartidor]);
        $conn->prepare("UPDATE pedido SET estado_pedido='Entregado'
                        WHERE id_pedido=(SELECT id_pedido FROM delivery WHERE id_delivery=?)")
             ->execute([(int)$d['id_delivery']]);
        notificarCambioEstado($conn, (int)$d['id_delivery'], 'Entregado');
        echo json_encode(['ok'=>true]);
    } elseif ($ac === 'cancelar_entrega') {
        // Repartidor cancela una entrega en camino: vuelve a estar disponible para el pool
        try {
            $upd = $conn->prepare("UPDATE delivery SET id_repartidor=NULL, estado_delivery='Pendiente', hora_salida=NULL 
                                   WHERE id_delivery=? AND id_repartidor=?");
            $upd->execute([(int)$d['id_delivery'], $idRepartidor]);
            if ($upd->rowCount() > 0) {
                $conn->prepare("UPDATE pedido SET estado_pedido='Pendiente'
                                WHERE id_pedido=(SELECT id_pedido FROM delivery WHERE id_delivery=?)")
                     ->execute([(int)$d['id_delivery']]);
                notificarCambioEstado($conn, (int)$d['id_delivery'], 'Pendiente');
                echo json_encode(['ok'=>true]);
            } else {
                echo json_encode(['ok'=>false,'mensaje'=>'No se pudo liberar el pedido o ya no tienes esta asignación activa']);
            }
        } catch (\PDOException $e) {
            echo json_encode(['ok'=>false,'mensaje'=>'Error al cancelar la entrega: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['ok'=>false,'mensaje'=>'Acción no reconocida']);
    }
    exit;
}

function columnaExisteRep(\PDO $conn, string $tabla, string $columna): bool {
    $columnaSegura = preg_replace('/[^a-zA-Z0-9_]/', '', $columna);
    $stmt = $conn->prepare("SHOW COLUMNS FROM `{$tabla}` LIKE '{$columnaSegura}'");
    $stmt->execute();
    return (bool) $stmt->fetch();
}
$tienePagoEfectivo = columnaExisteRep($conn, 'pago', 'monto_recibido');
$colPagoRep = $tienePagoEfectivo
    ? 'mp.nombre_metodo AS metodo_pago, pg.monto_recibido, pg.vuelto'
    : 'mp.nombre_metodo AS metodo_pago, NULL AS monto_recibido, NULL AS vuelto';

// GET: entregas asignadas (incluye 'Asignado' esperando respuesta y 'En camino')
$deliveries = $conn->prepare("
    SELECT dv.id_delivery, dv.direccion_entrega, dv.referencia,
           dv.estado_delivery, dv.costo_delivery,
           dv.hora_salida, dv.hora_entrega,
           dv.id_pedido, p.id_usuario AS id_usuario_pedido,
           COALESCE(CONCAT(u.nombres,' ',u.apellidos), p.nombre_cliente) AS cliente,
           COALESCE(u.telefono, p.telefono_cliente) AS tel_cliente,
           p.total AS total_pedido,
           p.tipo_entrega, {$colPagoRep}
    FROM delivery dv
    JOIN pedido p ON dv.id_pedido = p.id_pedido
    LEFT JOIN usuario u ON p.id_usuario = u.id_usuario
    LEFT JOIN pago pg ON p.id_pago = pg.id_pago
    LEFT JOIN metodo_pago mp ON pg.id_metodo_pago = mp.id_metodo_pago
    WHERE dv.id_repartidor = ?
    ORDER BY
        FIELD(dv.estado_delivery,'Asignado','En camino','Pendiente','Entregado') ASC,
        dv.id_delivery DESC
");
$deliveries->execute([$idRepartidor]);

// Pedidos sin repartidor asignado (disponibles para tomar directamente del pool)
$disponibles = $conn->query("
    SELECT dv.id_delivery, dv.direccion_entrega, dv.referencia,
           dv.costo_delivery, dv.id_pedido, p.id_usuario AS id_usuario_pedido,
           COALESCE(CONCAT(u.nombres,' ',u.apellidos), p.nombre_cliente) AS cliente,
           p.total AS total_pedido, {$colPagoRep}
    FROM delivery dv
    JOIN pedido p ON dv.id_pedido = p.id_pedido
    LEFT JOIN usuario u ON p.id_usuario = u.id_usuario
    LEFT JOIN pago pg ON p.id_pago = pg.id_pago
    LEFT JOIN metodo_pago mp ON pg.id_metodo_pago = mp.id_metodo_pago
    WHERE dv.id_repartidor IS NULL
    AND dv.estado_delivery = 'Pendiente'
    ORDER BY dv.id_delivery DESC
")->fetchAll(\PDO::FETCH_ASSOC);

$stats = $conn->prepare("
    SELECT
        COUNT(*) AS total,
        SUM(estado_delivery='Entregado') AS entregados,
        SUM(estado_delivery='En camino') AS en_camino,
        SUM(estado_delivery='Asignado')  AS pendientes,
        SUM(CASE WHEN estado_delivery='Entregado' THEN costo_delivery ELSE 0 END) AS ganancias
    FROM delivery WHERE id_repartidor=?
");
$stats->execute([$idRepartidor]);

echo json_encode([
    'ok'          => true,
    'repartidor'  => $repartidor,
    'dni_valido'  => $dniValido,
    'deliveries'  => $deliveries->fetchAll(\PDO::FETCH_ASSOC),
    'disponibles' => $disponibles,
    'stats'       => $stats->fetch(\PDO::FETCH_ASSOC)
]);
