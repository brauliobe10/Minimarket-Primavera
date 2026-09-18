<?php
require_once __DIR__ . '/../AuthController.php';
require_once __DIR__ . '/../../config/Database.php';
use Config\Database;
header('Content-Type: application/json');
requireAdmin();

try {
    $conn = (new Database())->conectar();

    $stats = $conn->query("
        SELECT
            (SELECT COUNT(*) FROM pedido WHERE estado_pedido != 'Cancelado') AS total_pedidos,
            (SELECT COALESCE(SUM(total),0) FROM pedido WHERE DATE(fecha_pedido)=CURDATE() AND estado_pedido != 'Cancelado') AS ventas_hoy,
            (SELECT COUNT(*) FROM usuario WHERE estado=1) AS total_usuarios,
            (SELECT COUNT(*) FROM Producto WHERE estado=1) AS total_productos,
            (SELECT COUNT(*) FROM pedido WHERE estado_pedido='Pendiente') AS pedidos_pendientes
    ")->fetch(\PDO::FETCH_ASSOC);

    $pedidos = $conn->query("
        SELECT p.id_pedido, COALESCE(CONCAT(u.nombres,' ',u.apellidos), p.nombre_cliente) AS cliente,
               p.fecha_pedido, p.total, p.estado_pedido
        FROM pedido p LEFT JOIN usuario u ON p.id_usuario=u.id_usuario
        ORDER BY p.fecha_pedido DESC LIMIT 8
    ")->fetchAll(\PDO::FETCH_ASSOC);

    // Datos para gráficos (ltimos 7 días)
    $ventas_7dias = $conn->query("
        SELECT DATE(fecha_pedido) as fecha, SUM(total) as total
        FROM pedido
        WHERE fecha_pedido >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
          AND estado_pedido NOT IN ('Cancelado')
        GROUP BY DATE(fecha_pedido)
        ORDER BY fecha ASC
    ")->fetchAll(\PDO::FETCH_ASSOC);

    // Datos para gráficos (Estado de pedidos)
    $pedidos_estado = $conn->query("
        SELECT estado_pedido, COUNT(*) as cantidad
        FROM pedido
        GROUP BY estado_pedido
    ")->fetchAll(\PDO::FETCH_ASSOC);

    echo json_encode([
        'ok'               => true,
        'stats'            => $stats,
        'pedidos_recientes' => $pedidos,
        'graficos'         => [
            'ventas_7dias'   => $ventas_7dias,
            'pedidos_estado' => $pedidos_estado,
        ],
    ]);

} catch (\Exception $e) {
    error_log('[DashboardController] ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['ok' => false, 'mensaje' => 'Error al cargar el dashboard.']);
}
