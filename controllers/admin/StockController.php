<?php
require_once __DIR__ . '/../AuthController.php';
require_once __DIR__ . '/../../config/Database.php';
use Config\Database;
header('Content-Type: application/json');
requireAdmin();

try {
    $conn = (new Database())->conectar();

    // Whitelist de tipos permitidos (previene inyección via $tipo)
    $tiposPermitidos = ['ENTRADA', 'SALIDA'];
    $tipo  = in_array($_GET['tipo'] ?? '', $tiposPermitidos, true) ? $_GET['tipo'] : '';
    $fecha = trim($_GET['fecha'] ?? '');

    // Validar formato de fecha yyyy-mm-dd
    if ($fecha !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
        $fecha = '';
    }

    $sql    = "SELECT ms.*, pr.nombre AS producto
              FROM movimiento_stock ms
              JOIN Producto pr ON ms.id_producto = pr.id_producto
              WHERE 1=1";
    $params = [];

    if ($tipo !== '') {
        $sql     .= " AND ms.tipo_movimiento = ?";
        $params[] = $tipo;
    }

    if ($fecha !== '') {
        $sql     .= " AND DATE(ms.fecha_movimiento) = ?";
        $params[] = $fecha;
    }

    $sql .= " ORDER BY ms.fecha_movimiento DESC LIMIT 200";

    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $movs = $stmt->fetchAll(\PDO::FETCH_ASSOC);

    echo json_encode(['ok' => true, 'movimientos' => $movs]);

} catch (\Exception $e) {
    error_log('[StockController] ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['ok' => false, 'mensaje' => 'Error al cargar movimientos de stock.']);
}
