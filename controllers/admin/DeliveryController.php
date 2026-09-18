<?php
require_once __DIR__ . '/../AuthController.php';
require_once __DIR__ . '/../../config/Database.php';
use Config\Database;
header('Content-Type: application/json');
requireAdmin();
$conn = (new Database())->conectar();

require_once __DIR__ . '/../../config/MailHelper.php';
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
        error_log("Error notificando: " . $e->getMessage());
    }
}

/* Helper: valida que el repartidor tenga DNI de 8 dígitos y Placa registrada */
function repartidorValido(\PDO $conn, int $idRepartidor): array {
    $stmt = $conn->prepare("
        SELECT u.dni, r.placa_vehiculo
        FROM repartidor r
        JOIN usuario u ON u.id_usuario = r.id_usuario
        WHERE r.id_repartidor = ?
    ");
    $stmt->execute([$idRepartidor]);
    $row = $stmt->fetch(\PDO::FETCH_ASSOC);

    if (!$row) return ['valido' => false, 'motivo' => 'Repartidor no encontrado'];

    $dniValido   = !empty($row['dni']) && preg_match('/^\d{8}$/', $row['dni']);
    $placaValida = !empty(trim($row['placa_vehiculo'] ?? '')) && trim($row['placa_vehiculo']) !== 'Sin placa';

    if (!$dniValido) {
        return ['valido' => false, 'motivo' => 'El repartidor no tiene un DNI válido (8 dígitos).'];
    }
    if (!$placaValida) {
        return ['valido' => false, 'motivo' => 'El repartidor no tiene una placa de vehículo registrada. Debe completar su perfil.'];
    }

    return ['valido' => true];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $d  = json_decode(file_get_contents('php://input'), true);
    $ac = $d['accion'] ?? '';

    /* ── Asignar repartidor a un delivery ── */
    if ($ac === 'asignar_repartidor') {
        $idDelivery   = (int) $d['id_delivery'];
        $idRepartidor = (int) $d['id_repartidor'];

        // Validar DNI y Placa del repartidor antes de permitir la asignación
        $check = repartidorValido($conn, $idRepartidor);
        if (!$check['valido']) {
            echo json_encode(['ok' => false, 'mensaje' => $check['motivo']]);
            exit;
        }

        // Solo se puede asignar/reasignar si aún no fue aceptado por otro repartidor
        $stEstado = $conn->prepare("SELECT estado_delivery FROM delivery WHERE id_delivery=?");
        $stEstado->execute([$idDelivery]);
        $estadoActual = $stEstado->fetchColumn();

        if (in_array($estadoActual, ['En camino', 'Entregado'], true)) {
            echo json_encode(['ok' => false, 'mensaje' => 'Este pedido ya fue aceptado por un repartidor y está en curso. No se puede reasignar desde aquí.']);
            exit;
        }

        try {
            $conn->prepare("UPDATE delivery SET id_repartidor=?, estado_delivery='Asignado', hora_salida=NULL, motivo_rechazo=NULL
                            WHERE id_delivery=?")->execute([$idRepartidor, $idDelivery]);
        } catch (\PDOException $e) {
$conn->prepare("UPDATE delivery SET id_repartidor=?, estado_delivery='Asignado', hora_salida=NULL
                        WHERE id_delivery=?")->execute([$idRepartidor, $idDelivery]);
        }

        echo json_encode(['ok' => true]);

    /* ── Quitar asignación (vuelve al pool, mientras no haya sido aceptado) ── */
    } elseif ($ac === 'cancelar_asignacion') {
        $idDelivery = (int) $d['id_delivery'];

        $stEstado = $conn->prepare("SELECT estado_delivery FROM delivery WHERE id_delivery=?");
        $stEstado->execute([$idDelivery]);
        $estadoActual = $stEstado->fetchColumn();

        if (in_array($estadoActual, ['En camino', 'Entregado'], true)) {
            echo json_encode(['ok' => false, 'mensaje' => 'Este pedido ya fue aceptado y está en curso, no se puede quitar la asignación.']);
            exit;
        }

        $conn->prepare("UPDATE delivery SET id_repartidor=NULL, estado_delivery='Pendiente', hora_salida=NULL
                        WHERE id_delivery=?")->execute([$idDelivery]);
        $conn->prepare("UPDATE pedido SET estado_pedido='Pendiente'
                        WHERE id_pedido=(SELECT id_pedido FROM delivery WHERE id_delivery=?)")
             ->execute([$idDelivery]);

        echo json_encode(['ok' => true]);

    /* ── Cambiar estado del delivery ── */
    } elseif ($ac === 'cambiar_estado') {
        $extra = '';
        $vals  = [$d['estado'], $d['id_delivery']];
        if ($d['estado'] === 'Entregado') {
            $extra = ', hora_entrega=NOW()';
            // Actualizar pedido
            $conn->prepare("UPDATE pedido SET estado_pedido='Entregado'
                            WHERE id_pedido=(SELECT id_pedido FROM delivery WHERE id_delivery=?)")
                 ->execute([$d['id_delivery']]);
        }
        $conn->prepare("UPDATE delivery SET estado_delivery=?{$extra} WHERE id_delivery=?")
             ->execute($vals);
        
        notificarCambioEstado($conn, (int)$d['id_delivery'], $d['estado']);
        
        echo json_encode(['ok'=>true]);

    /* ── Crear delivery para un pedido ── */
    } elseif ($ac === 'crear_delivery') {
        // Obtener dirección del pedido
        $stmt = $conn->prepare("
            SELECT d.direccion, d.distrito, d.provincia, d.referencia
            FROM pedido p
            LEFT JOIN direccion d ON d.id_usuario = p.id_usuario AND d.predeterminada = 1
            WHERE p.id_pedido = ?
        ");
        $stmt->execute([$d['id_pedido']]);
        $dir = $stmt->fetch(\PDO::FETCH_ASSOC);

        $direccion = $dir
            ? trim(($dir['direccion'] ?? '') . ', ' . ($dir['distrito'] ?? ''))
            : ($d['direccion_manual'] ?? 'Sin dirección');
        $referencia = $dir['referencia'] ?? null;

        $conn->prepare("INSERT INTO delivery (direccion_entrega, referencia, costo_delivery, estado_delivery, id_pedido)
                        VALUES (?, ?, 5.00, 'Pendiente', ?)")
             ->execute([$direccion, $referencia, $d['id_pedido']]);

        $conn->prepare("UPDATE pedido SET estado_pedido='Pendiente' WHERE id_pedido=?")
             ->execute([$d['id_pedido']]);

        echo json_encode(['ok'=>true]);
    }
    exit;
}

/* ── GET: detalle de un delivery individual ── */
if (isset($_GET['id'])) {
    $stmt = $conn->prepare("
        SELECT dv.*, p.total AS total_pedido,
               CONCAT(u.nombres,' ',u.apellidos) AS cliente,
               CONCAT(r.nombres) AS repartidor_nombre, r.telefono AS rep_tel
        FROM delivery dv
        JOIN pedido p ON dv.id_pedido = p.id_pedido
        LEFT JOIN usuario u ON p.id_usuario = u.id_usuario
        LEFT JOIN repartidor r ON dv.id_repartidor = r.id_repartidor
        WHERE dv.id_delivery = ?
    ");
    $stmt->execute([(int)$_GET['id']]);
    echo json_encode(['ok'=>true,'delivery'=>$stmt->fetch(\PDO::FETCH_ASSOC)]);
    exit;
}

/* ── GET: preview de un pedido para crear delivery ── */
if (isset($_GET['pedido_preview'])) {
    $idPed = (int)$_GET['pedido_preview'];
    $stmt = $conn->prepare("
        SELECT p.id_pedido, p.estado_pedido,
               COALESCE(CONCAT(u.nombres,' ',u.apellidos), p.nombre_cliente) AS cliente,
               COALESCE(
                   TRIM(CONCAT_WS(', ', d.direccion, d.distrito)),
                   'Sin dirección'
               ) AS direccion,
               d.referencia
        FROM pedido p
        LEFT JOIN usuario u ON p.id_usuario = u.id_usuario
        LEFT JOIN direccion d ON d.id_usuario = p.id_usuario AND d.predeterminada = 1
        WHERE p.id_pedido = ?
    ");
    $stmt->execute([$idPed]);
    $row = $stmt->fetch(\PDO::FETCH_ASSOC);
    if ($row) {
        echo json_encode(['ok'=>true, 'pedido'=>$row]);
    } else {
        echo json_encode(['ok'=>false, 'mensaje'=>'Pedido no encontrado']);
    }
    exit;
}

/* ── GET: pedidos elegibles para crear delivery (Delivery, no entregado/cancelado, sin delivery) ── */
if (isset($_GET['pedidos_elegibles'])) {
    $rows = $conn->query("
        SELECT p.id_pedido, p.estado_pedido, p.total,
               p.fecha_pedido,
               COALESCE(CONCAT(u.nombres,' ',u.apellidos), p.nombre_cliente) AS cliente,
               COALESCE(
                   TRIM(CONCAT_WS(', ', d.direccion, d.distrito)),
                   'Sin dirección'
               ) AS direccion
        FROM pedido p
        LEFT JOIN usuario u ON p.id_usuario = u.id_usuario
        LEFT JOIN direccion d ON d.id_usuario = p.id_usuario AND d.predeterminada = 1
        LEFT JOIN delivery dv ON dv.id_pedido = p.id_pedido
        WHERE p.tipo_entrega = 'Delivery'
          AND p.estado_pedido NOT IN ('Entregado','Cancelado')
          AND dv.id_delivery IS NULL
        ORDER BY p.id_pedido ASC
    ")->fetchAll(\PDO::FETCH_ASSOC);
    echo json_encode(['ok'=>true, 'pedidos'=>$rows]);
    exit;
}

function columnaExiste(\PDO $conn, string $tabla, string $columna): bool {
    $columnaSegura = preg_replace('/[^a-zA-Z0-9_]/', '', $columna);
    $stmt = $conn->prepare("SHOW COLUMNS FROM `{$tabla}` LIKE '{$columnaSegura}'");
    $stmt->execute();
    return (bool) $stmt->fetch();
}

$tieneMotivoRechazo = columnaExiste($conn, 'delivery', 'motivo_rechazo');
$tienePagoEfectivo  = columnaExiste($conn, 'pago', 'monto_recibido');

$colMotivo = $tieneMotivoRechazo ? 'dv.motivo_rechazo' : 'NULL AS motivo_rechazo';
$colPago   = $tienePagoEfectivo
    ? 'mp.nombre_metodo AS metodo_pago, pg.monto_recibido, pg.vuelto'
    : 'mp.nombre_metodo AS metodo_pago, NULL AS monto_recibido, NULL AS vuelto';

$deliveries = $conn->query("
    SELECT dv.id_delivery, dv.direccion_entrega, dv.estado_delivery, {$colMotivo},
           dv.costo_delivery, dv.hora_salida, dv.hora_entrega,
           dv.id_pedido, dv.id_repartidor, p.id_usuario AS id_usuario_pedido,
           COALESCE(CONCAT(u.nombres,' ',u.apellidos), p.nombre_cliente) AS cliente,
           p.total AS total_pedido, r.nombres AS repartidor_nombre, {$colPago}
    FROM delivery dv
    JOIN pedido p ON dv.id_pedido = p.id_pedido
    LEFT JOIN usuario u ON p.id_usuario = u.id_usuario
    LEFT JOIN repartidor r ON dv.id_repartidor = r.id_repartidor
    LEFT JOIN pago pg ON p.id_pago = pg.id_pago
    LEFT JOIN metodo_pago mp ON pg.id_metodo_pago = mp.id_metodo_pago
    ORDER BY dv.id_delivery ASC
")->fetchAll(\PDO::FETCH_ASSOC);

$repartidores = $conn->query("
    SELECT r.id_repartidor, r.nombres, r.telefono, r.placa_vehiculo, r.estado,
           u.dni,
           (u.dni IS NOT NULL AND u.dni REGEXP '^[0-9]{8}$') AS dni_valido,
           (r.placa_vehiculo IS NOT NULL AND TRIM(r.placa_vehiculo) != '' AND TRIM(r.placa_vehiculo) != 'Sin placa') AS placa_valida,
           (u.dni IS NOT NULL AND u.dni REGEXP '^[0-9]{8}$' AND r.placa_vehiculo IS NOT NULL AND TRIM(r.placa_vehiculo) != '' AND TRIM(r.placa_vehiculo) != 'Sin placa') AS perfil_completo
    FROM repartidor r
    LEFT JOIN usuario u ON u.id_usuario = r.id_usuario
    WHERE r.estado = 1
      AND r.id_repartidor NOT IN (
          SELECT id_repartidor FROM delivery
          WHERE estado_delivery IN ('Asignado', 'En camino')
            AND id_repartidor IS NOT NULL
      )
")->fetchAll(\PDO::FETCH_ASSOC);

echo json_encode(['ok'=>true,'deliveries'=>$deliveries,'repartidores'=>$repartidores]);
