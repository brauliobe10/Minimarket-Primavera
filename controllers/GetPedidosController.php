<?php
require_once __DIR__ . '/AuthController.php';
require_once __DIR__ . '/../config/Database.php';

use Config\Database;

header('Content-Type: application/json');

if (!estaLogueado()) {
    echo json_encode(['ok' => false, 'mensaje' => 'No autenticado']);
    exit;
}

$db   = new Database();
$conn = $db->conectar();

$idUsuario = (int) $_SESSION['usuario_id'];

// Pedidos con comprobante
$stmt = $conn->prepare("
    SELECT
        p.id_pedido,
        p.fecha_pedido,
        p.subtotal,
        p.descuento,
        p.total,
        p.estado_pedido,
        p.tipo_entrega,
        mp.nombre_metodo  AS metodo_pago,
        c.serie           AS comp_serie,
        c.numero          AS comp_numero,
        c.tipo_comprobante,
        c.igv             AS comp_igv,
        COALESCE(p.dni_cliente, u.dni, 'N/A') AS dni_cliente,
        COALESCE(NULLIF(p.telefono_cliente, ''), u.telefono, 'N/A') AS telefono_cliente,
        COALESCE(NULLIF(CONCAT(u.nombres, ' ', u.apellidos), ' '), p.nombre_cliente, 'N/A') AS cliente,
        del.direccion_entrega
    FROM pedido p
    LEFT JOIN usuario u     ON p.id_usuario = u.id_usuario
    LEFT JOIN pago pa       ON p.id_pago        = pa.id_pago
    LEFT JOIN metodo_pago mp ON pa.id_metodo_pago = mp.id_metodo_pago
    LEFT JOIN comprobante c  ON c.id_pedido      = p.id_pedido
    LEFT JOIN delivery del   ON del.id_pedido = p.id_pedido
    WHERE p.id_usuario = :id
    ORDER BY p.fecha_pedido DESC
");
$stmt->execute(['id' => $idUsuario]);
$pedidos = $stmt->fetchAll(\PDO::FETCH_ASSOC);

// Para cada pedido traer el detalle
$stmtDet = $conn->prepare("
    SELECT dp.cantidad, dp.precio_unitario, dp.subtotal, pr.nombre, pr.imagen
    FROM detalle_pedido dp
    JOIN Producto pr ON dp.id_producto = pr.id_producto
    WHERE dp.id_pedido = :id
");

foreach ($pedidos as &$ped) {
    $stmtDet->execute(['id' => $ped['id_pedido']]);
    $ped['detalle'] = $stmtDet->fetchAll(\PDO::FETCH_ASSOC);
}
unset($ped);

echo json_encode(['ok' => true, 'pedidos' => $pedidos]);
