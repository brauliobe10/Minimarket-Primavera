<?php
require_once __DIR__ . '/../AuthController.php';
require_once __DIR__ . '/../../config/Database.php';
use Config\Database;

// Sin caché nunca
header('Content-Type: application/json');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

requireAdmin();

$conn = (new Database())->conectar();

// ── Leer parámetros (acepta tanto GET como POST-JSON) ───────────────
$body = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $raw  = file_get_contents('php://input');
    $body = json_decode($raw, true) ?: [];
}

$desde  = trim($body['desde']  ?? ($_GET['desde']  ?? date('Y-m-01')));
$hasta  = trim($body['hasta']  ?? ($_GET['hasta']  ?? date('Y-m-d')));
$estado = trim($body['estado'] ?? ($_GET['estado'] ?? ''));

// Validar fechas
$desde = preg_match('/^\d{4}-\d{2}-\d{2}$/', $desde) ? $desde : date('Y-m-01');
$hasta = preg_match('/^\d{4}-\d{2}-\d{2}$/', $hasta) ? $hasta : date('Y-m-d');

// Estados válidos permitidos
$estadosValidos = ['Entregado', 'En camino', 'Pendiente', 'En preparacion', 'Cancelado'];
if ($estado !== '' && !in_array($estado, $estadosValidos, true)) {
    $estado = '';
}

// ── Construir WHERE ─────────────────────────────────────────────────
$whereEstado = '';
$params      = [$desde, $hasta];
if ($estado !== '') {
    $whereEstado = ' AND p.estado_pedido = ?';
    $params[]    = $estado;
}

// ── Ventas agrupadas por día ────────────────────────────────────────
$sql = "
    SELECT
        DATE(p.fecha_pedido)          AS dia,
        COUNT(p.id_pedido)            AS total_pedidos,
        SUM(p.total)                  AS ingresos,
        SUM(CASE WHEN p.estado_pedido='Entregado'      THEN 1 ELSE 0 END) AS entregados,
        SUM(CASE WHEN p.estado_pedido='Cancelado'      THEN 1 ELSE 0 END) AS cancelados,
        SUM(CASE WHEN p.estado_pedido='Pendiente'      THEN 1 ELSE 0 END) AS pendientes,
        SUM(CASE WHEN p.estado_pedido='En preparación' THEN 1 ELSE 0 END) AS en_preparacion,
        SUM(CASE WHEN p.estado_pedido='En camino'      THEN 1 ELSE 0 END) AS en_camino,
        SUM(CASE WHEN p.estado_pedido='Entregado'      THEN p.total ELSE 0 END) AS ingresos_reales
    FROM pedido p
    WHERE DATE(p.fecha_pedido) BETWEEN ? AND ?
    {$whereEstado}
    GROUP BY DATE(p.fecha_pedido)
    ORDER BY dia DESC
";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$filas = $stmt->fetchAll(\PDO::FETCH_ASSOC);

// ── Totales del período ──────────────────────────────────────────────
$totalPeriodo = [
    'pedidos'         => 0,
    'ingresos'        => 0,
    'ingresos_reales' => 0,
    'entregados'      => 0,
    'cancelados'      => 0,
    'pendientes'      => 0,
    'en_camino'       => 0,
    'en_preparacion'  => 0,
];
foreach ($filas as $f) {
    $totalPeriodo['pedidos']         += (int)$f['total_pedidos'];
    $totalPeriodo['ingresos']        += (float)$f['ingresos'];
    $totalPeriodo['ingresos_reales'] += (float)$f['ingresos_reales'];
    $totalPeriodo['entregados']      += (int)$f['entregados'];
    $totalPeriodo['cancelados']      += (int)$f['cancelados'];
    $totalPeriodo['pendientes']      += (int)$f['pendientes'];
    $totalPeriodo['en_camino']       += (int)$f['en_camino'];
    $totalPeriodo['en_preparacion']  += (int)$f['en_preparacion'];
}

// ── Mejor día del período ────────────────────────────────────────────
$mejorDia = null;
if (!empty($filas)) {
    $mejorDia = array_reduce($filas, function ($carry, $item) {
        return (!$carry || $item['ingresos'] > $carry['ingresos']) ? $item : $carry;
    });
}

echo json_encode([
    'ok'        => true,
    'filas'     => $filas,
    'totales'   => $totalPeriodo,
    'mejor_dia' => $mejorDia,
    'filtros'   => compact('desde', 'hasta', 'estado'),
]);
