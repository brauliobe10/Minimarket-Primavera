<?php
require_once __DIR__ . '/../AuthController.php';
require_once __DIR__ . '/../../config/Database.php';
use Config\Database;
header('Content-Type: application/json');
requireAdmin();
$conn = (new Database())->conectar();

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $d = json_decode(file_get_contents('php://input'), true);
        if (!is_array($d)) {
            echo json_encode(['ok' => false, 'mensaje' => 'Datos inválidos.']);
            exit;
        }
        if (($d['accion'] ?? '') === 'cambiar_estado') {
            $estado = $d['estado'];
            $id = (int)$d['id_pedido'];

            $estadosValidos = ['Pendiente', 'En preparación', 'Entregado', 'Cancelado', 'En camino', 'Asignado'];
            if (!in_array($estado, $estadosValidos, true)) {
                echo json_encode(['ok' => false, 'mensaje' => 'Estado de pedido no válido.']);
                exit;
            }

            // 1. Actualizar estado del pedido
            $conn->prepare("UPDATE pedido SET estado_pedido=? WHERE id_pedido=?")
                 ->execute([$estado, $id]);
                 
            // 2. Sincronizar estado en delivery (solo estados compatibles; el resto los gestiona el repartidor)
            if (in_array($estado, ['Entregado', 'Cancelado', 'Pendiente'], true)) {
                if ($estado === 'Entregado') {
                    $conn->prepare("UPDATE delivery SET estado_delivery=?, hora_entrega=NOW() WHERE id_pedido=?")
                         ->execute([$estado, $id]);
                } else {
                    $conn->prepare("UPDATE delivery SET estado_delivery=? WHERE id_pedido=?")
                         ->execute([$estado, $id]);
                }
            }

            echo json_encode(['ok'=>true]);
        }
        exit;
    }

    if (isset($_GET['id'])) {
        $id = (int)$_GET['id'];
        $s = $conn->prepare("SELECT p.*, COALESCE(CONCAT(u.nombres,' ',u.apellidos), p.nombre_cliente) AS cliente,
            mp.nombre_metodo AS metodo_pago
            FROM pedido p LEFT JOIN usuario u ON p.id_usuario=u.id_usuario
            LEFT JOIN pago pa ON p.id_pago=pa.id_pago
            LEFT JOIN metodo_pago mp ON pa.id_metodo_pago=mp.id_metodo_pago
            WHERE p.id_pedido=?");
        $s->execute([$id]);
        $ped = $s->fetch(\PDO::FETCH_ASSOC);
        $sd = $conn->prepare("SELECT dp.*,pr.nombre,pr.imagen FROM detalle_pedido dp JOIN Producto pr ON dp.id_producto=pr.id_producto WHERE dp.id_pedido=?");
        $sd->execute([$id]);
        $ped['detalle'] = $sd->fetchAll(\PDO::FETCH_ASSOC);
        echo json_encode(['ok'=>true,'pedido'=>$ped]);
        exit;
    }

    $pedidos = $conn->query("
        SELECT p.id_pedido, COALESCE(CONCAT(u.nombres,' ',u.apellidos), p.nombre_cliente) AS cliente,
               p.fecha_pedido, p.total, p.estado_pedido, p.tipo_entrega, p.descuento,
               mp.nombre_metodo AS metodo_pago
        FROM pedido p LEFT JOIN usuario u ON p.id_usuario=u.id_usuario
        LEFT JOIN pago pa ON p.id_pago=pa.id_pago
        LEFT JOIN metodo_pago mp ON pa.id_metodo_pago=mp.id_metodo_pago
        ORDER BY p.id_pedido ASC")->fetchAll(\PDO::FETCH_ASSOC);
    echo json_encode(['ok'=>true,'pedidos'=>$pedidos]);

} catch (\Exception $e) {
    error_log('[PedidosController] ' . $e->getMessage());
    echo json_encode(['ok' => false, 'mensaje' => 'Ocurrió un error en el servidor.']);
}
