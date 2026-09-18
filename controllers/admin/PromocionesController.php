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

    if ($ac === 'crear') {
        $nombre  = trim($d['nombre_promocion'] ?? '');
        $pct     = (float)($d['porcentaje_descuento'] ?? 0);
        if (empty($nombre) || $pct <= 0 || $pct > 100) {
            echo json_encode(['ok' => false, 'mensaje' => 'Nombre y porcentaje (1-100) son obligatorios.']);
            exit;
        }
        try {
            $stmt = $conn->prepare("INSERT INTO promocion (nombre_promocion, descripcion, porcentaje_descuento, fecha_inicio, fecha_fin, estado) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $nombre,
                trim($d['descripcion'] ?? ''),
                $pct,
                $d['fecha_inicio'] ?? null,
                $d['fecha_fin']    ?? null,
                isset($d['estado']) ? (int)$d['estado'] : 1,
            ]);
            echo json_encode(['ok' => true, 'mensaje' => 'Promoción creada correctamente.']);
        } catch (\PDOException $e) {
            error_log('[PromocionesController] crear: ' . $e->getMessage());
            echo json_encode(['ok' => false, 'mensaje' => 'Error al crear la promoción.']);
        }
    }
    elseif ($ac === 'editar') {
        $id     = (int)($d['id_promocion'] ?? 0);
        $nombre = trim($d['nombre_promocion'] ?? '');
        $pct    = (float)($d['porcentaje_descuento'] ?? 0);
        if ($id <= 0 || empty($nombre) || $pct <= 0) {
            echo json_encode(['ok' => false, 'mensaje' => 'Datos insuficientes para editar.']);
            exit;
        }
        try {
            $stmt = $conn->prepare("UPDATE promocion SET nombre_promocion = ?, descripcion = ?, porcentaje_descuento = ?, fecha_inicio = ?, fecha_fin = ?, estado = ? WHERE id_promocion = ?");
            $stmt->execute([
                $nombre,
                trim($d['descripcion'] ?? ''),
                $pct,
                $d['fecha_inicio'] ?? null,
                $d['fecha_fin']    ?? null,
                isset($d['estado']) ? (int)$d['estado'] : 1,
                $id,
            ]);
            echo json_encode(['ok' => true, 'mensaje' => 'Promoción actualizada correctamente.']);
        } catch (\PDOException $e) {
            error_log('[PromocionesController] editar: ' . $e->getMessage());
            echo json_encode(['ok' => false, 'mensaje' => 'Error al editar la promoción.']);
        }
    }
    elseif ($ac === 'eliminar') {
        $id = (int)($d['id_promocion'] ?? 0);
        
        $conn->beginTransaction();
        try {
            // Eliminar dependencias
            $stmt1 = $conn->prepare("DELETE FROM producto_promocion WHERE id_promocion = ?");
            $stmt1->execute([$id]);

            // Eliminar promocion
            $stmt2 = $conn->prepare("DELETE FROM promocion WHERE id_promocion = ?");
            $stmt2->execute([$id]);

            $conn->commit();
            echo json_encode(['ok' => true]);
        } catch (\Exception $e) {
            $conn->rollBack();
            echo json_encode(['ok' => false, 'mensaje' => 'Error al eliminar la promoción.']);
        }
    }
    elseif ($ac === 'vincular_producto') {
        $id_promo = (int)($d['id_promocion'] ?? 0);
        $id_prod  = (int)($d['id_producto'] ?? 0);
        
        // Verificar duplicados
        $check = $conn->prepare("SELECT COUNT(*) FROM producto_promocion WHERE id_producto = ? AND id_promocion = ?");
        $check->execute([$id_prod, $id_promo]);
        if ((int)$check->fetchColumn() === 0) {
            $stmt = $conn->prepare("INSERT INTO producto_promocion (id_producto, id_promocion) VALUES (?, ?)");
            $stmt->execute([$id_prod, $id_promo]);
        }
        echo json_encode(['ok' => true]);
    }
    elseif ($ac === 'desvincular_producto') {
        $id_promo = (int)($d['id_promocion'] ?? 0);
        $id_prod  = (int)($d['id_producto'] ?? 0);
        
        $stmt = $conn->prepare("DELETE FROM producto_promocion WHERE id_producto = ? AND id_promocion = ?");
        $stmt->execute([$id_prod, $id_promo]);
        echo json_encode(['ok' => true]);
    }
    exit;
}

// GET request

// Listar productos vinculados y disponibles si se pide ?id_productos_promocion=X
if (isset($_GET['id_productos_promocion'])) {
    $id_promo = (int)$_GET['id_productos_promocion'];
    $buscar = trim($_GET['q'] ?? '');

    // Asociados
    $stmtAsoc = $conn->prepare("
        SELECT p.id_producto, p.nombre, p.precio, p.imagen 
        FROM producto_promocion pp 
        JOIN Producto p ON pp.id_producto = p.id_producto 
        WHERE pp.id_promocion = ?
        ORDER BY p.nombre
    ");
    $stmtAsoc->execute([$id_promo]);
    $asociados = $stmtAsoc->fetchAll(\PDO::FETCH_ASSOC);

    // Disponibles
    $whereDisp = "WHERE p.estado = 1 AND p.id_producto NOT IN (SELECT id_producto FROM producto_promocion WHERE id_promocion = :id_promo)";
    if ($buscar) {
        $whereDisp .= " AND p.nombre LIKE :q";
    }
    $sqlDisp = "
        SELECT p.id_producto, p.nombre, p.precio, p.imagen 
        FROM Producto p
        $whereDisp
        ORDER BY p.nombre
        LIMIT 10
    ";
    $stmtDisp = $conn->prepare($sqlDisp);
    $stmtDisp->bindValue(':id_promo', $id_promo, \PDO::PARAM_INT);
    if ($buscar) {
        $stmtDisp->bindValue(':q', "%$buscar%");
    }
    $stmtDisp->execute();
    $disponibles = $stmtDisp->fetchAll(\PDO::FETCH_ASSOC);

    echo json_encode([
        'ok' => true,
        'asociados' => $asociados,
        'disponibles' => $disponibles
    ]);
    exit;
}

// Listar promociones general con paginación
$pagina    = max(1, (int)($_GET['pagina'] ?? 1));
$porPagina = 10;
$offset    = ($pagina - 1) * $porPagina;
$buscar    = trim($_GET['q'] ?? '');

$where = $buscar ? "WHERE nombre_promocion LIKE :q OR descripcion LIKE :q2" : "";

$stmtTotal = $conn->prepare("SELECT COUNT(*) FROM promocion $where");
if ($buscar) {
    $stmtTotal->execute([':q' => "%$buscar%", ':q2' => "%$buscar%"]);
} else {
    $stmtTotal->execute();
}
$total = (int)$stmtTotal->fetchColumn();

$sql = "SELECT id_promocion, nombre_promocion, descripcion, porcentaje_descuento, fecha_inicio, fecha_fin, estado 
        FROM promocion 
        $where 
        ORDER BY id_promocion ASC 
        LIMIT :limit OFFSET :offset";

$stmt = $conn->prepare($sql);
$stmt->bindValue(':limit',  $porPagina, \PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset,    \PDO::PARAM_INT);
if ($buscar) {
    $stmt->bindValue(':q',  "%$buscar%");
    $stmt->bindValue(':q2', "%$buscar%");
}
$stmt->execute();
$promociones = $stmt->fetchAll(\PDO::FETCH_ASSOC);

echo json_encode([
    'ok'          => true,
    'promociones' => $promociones,
    'total'       => $total,
    'pagina'      => $pagina,
    'paginas'     => ceil($total / $porPagina),
    'por_pagina'  => $porPagina
]);
