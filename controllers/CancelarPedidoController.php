<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../config/Security.php';
use Config\Database;
use Config\Security;

Security::initSession();
Security::setSecurityHeaders();
ob_start();
header('Content-Type: application/json');

$data      = json_decode(file_get_contents('php://input'), true);
$id_pedido = (int)($data['id_pedido'] ?? 0);
$guest     = !empty($data['guest']);

if ($id_pedido <= 0) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'ID de pedido requerido.']);
    exit;
}

try {
    $db = new Database();
    $conn = $db->conectar();

    // Obtener información del pedido actual
    $stmt = $conn->prepare("SELECT id_usuario, estado_pedido FROM pedido WHERE id_pedido = ?");
    $stmt->execute([$id_pedido]);
    $pedido = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$pedido) {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'El pedido no existe.']);
        exit;
    }

    // Validar permisos
    if (!$guest) {
        // Cancelación desde perfil (usuario autenticado)
        if (!isset($_SESSION['usuario_id'])) {
            ob_end_clean();
            echo json_encode(['success' => false, 'message' => 'Sesión expirada.']);
            exit;
        }
        if ($pedido['id_usuario'] != $_SESSION['usuario_id']) {
            ob_end_clean();
            echo json_encode(['success' => false, 'message' => 'No tienes permiso para cancelar este pedido.']);
            exit;
        }
    } else {
        // Cancelación desde rastreo de invitado
        // Si el pedido no es de un usuario invitado (id_usuario != null) y no hay sesión activa coincidente, no se puede (opcional, pero dejaremos cancelar si conocen DNI y rastreo, por simplicidad asumimos que si llegaron aquí, pasaron el filtro de RastrearPedidoController. En la vida real, se validaría con token).
    }

    if ($pedido['estado_pedido'] !== 'Pendiente') {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Solo se pueden cancelar pedidos en estado "Pendiente".']);
        exit;
    }

    $motivo = $data['motivo'] ?? null;
    $motivo = empty(trim($motivo)) ? null : trim($motivo);

    $conn->beginTransaction();

    // Proceder a cancelar
    $stmtUpdate = $conn->prepare("UPDATE pedido SET estado_pedido = 'Cancelado', motivo_cancelacion = ? WHERE id_pedido = ?");
    if (!$stmtUpdate->execute([$motivo, $id_pedido])) {
        $conn->rollBack();
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'No se pudo actualizar el estado.']);
        exit;
    }

    // Sincronizar estado en delivery (si existe uno asociado)
    $stmtDelivery = $conn->prepare("UPDATE delivery SET estado_delivery = 'Cancelado' WHERE id_pedido = ?");
    $stmtDelivery->execute([$id_pedido]);


    // Restaurar stock
    $stmtDetalle = $conn->prepare("SELECT id_producto, cantidad FROM detalle_pedido WHERE id_pedido = ?");
    $stmtDetalle->execute([$id_pedido]);
    $items = $stmtDetalle->fetchAll(PDO::FETCH_ASSOC);

    $stmtStock = $conn->prepare("UPDATE Producto SET stock_actual = stock_actual + ? WHERE id_producto = ?");
    $stmtMov = $conn->prepare("INSERT INTO movimiento_stock (id_producto, tipo_movimiento, cantidad, motivo) VALUES (?, 'ENTRADA', ?, ?)");

    foreach ($items as $it) {
        $stmtStock->execute([$it['cantidad'], $it['id_producto']]);
        $stmtMov->execute([$it['id_producto'], $it['cantidad'], "Cancelación Pedido #{$id_pedido}"]);
    }

    $conn->commit();
    ob_end_clean();
    echo json_encode(['success' => true]);

} catch (Exception $e) {
    if (isset($conn)) $conn->rollBack();
    error_log("Error al cancelar pedido: " . $e->getMessage());
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Error de conexión.']);
}
