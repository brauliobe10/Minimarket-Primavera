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
        if ($ac === 'crear') {
            $nombre   = trim($d['nombre']   ?? '');
            $precio   = (float)($d['precio'] ?? 0);
            $stock    = (int)($d['stock_actual'] ?? 0);
            $stockMin = (int)($d['stock_minimo'] ?? 0);
            $idCat    = (int)($d['id_categoria'] ?? 0);

            if (empty($nombre) || $precio <= 0 || $idCat <= 0) {
                echo json_encode(['ok' => false, 'mensaje' => 'Nombre, precio y categoría son obligatorios.']);
                exit;
            }

            $conn->prepare(
                "INSERT INTO Producto (nombre, descripcion, precio, stock_actual, stock_minimo, imagen, id_categoria, estado)
                 VALUES (?, ?, ?, ?, ?, ?, ?, 1)"
            )->execute([
                $nombre,
                trim($d['descripcion'] ?? ''),
                $precio,
                $stock,
                $stockMin,
                trim($d['imagen'] ?? ''),
                $idCat,
            ]);
            echo json_encode(['ok' => true, 'mensaje' => 'Producto creado correctamente.']);

        } elseif ($ac === 'editar') {
            $id       = (int)($d['id_producto'] ?? 0);
            $nombre   = trim($d['nombre']   ?? '');
            $precio   = (float)($d['precio'] ?? 0);
            $stock    = (int)($d['stock_actual'] ?? 0);
            $stockMin = (int)($d['stock_minimo'] ?? 0);
            $idCat    = (int)($d['id_categoria'] ?? 0);
            $estado   = (int)($d['estado'] ?? 1);

            if ($id <= 0 || empty($nombre) || $precio <= 0) {
                echo json_encode(['ok' => false, 'mensaje' => 'Datos insuficientes para editar.']);
                exit;
            }

            $conn->prepare(
                "UPDATE Producto SET nombre=?, descripcion=?, precio=?, stock_actual=?,
                 stock_minimo=?, imagen=?, id_categoria=?, estado=? WHERE id_producto=?"
            )->execute([
                $nombre,
                trim($d['descripcion'] ?? ''),
                $precio,
                $stock,
                $stockMin,
                trim($d['imagen'] ?? ''),
                $idCat,
                $estado,
                $id,
            ]);
            echo json_encode(['ok' => true, 'mensaje' => 'Producto actualizado correctamente.']);

        } elseif ($ac === 'eliminar') {
            $id = (int)($d['id_producto'] ?? 0);
            if ($id <= 0) {
                echo json_encode(['ok' => false, 'mensaje' => 'ID inválido.']);
                exit;
            }
            $conn->prepare("UPDATE Producto SET estado=0 WHERE id_producto=?")->execute([$id]);
            echo json_encode(['ok' => true, 'mensaje' => 'Producto desactivado.']);
        } else {
            echo json_encode(['ok' => false, 'mensaje' => 'Acción no reconocida.']);
        }
    } catch (\PDOException $e) {
        error_log('[ProductosController] ' . $e->getMessage());
        echo json_encode(['ok' => false, 'mensaje' => 'Error en base de datos. Inténtalo de nuevo.']);
    }
    exit;
}

$pagina   = max(1, (int)($_GET['pagina'] ?? 1));
$porPagina = 30;
$offset    = ($pagina - 1) * $porPagina;
$buscar    = trim($_GET['q'] ?? '');

$where = $buscar ? "WHERE p.nombre LIKE :q OR p.descripcion LIKE :q2" : "";

$stmtTotal = $conn->prepare("SELECT COUNT(*) FROM Producto p $where");
$buscar ? $stmtTotal->execute([':q'=>"%$buscar%",':q2'=>"%$buscar%"]) : $stmtTotal->execute();
$total = (int)$stmtTotal->fetchColumn();

$sql = "SELECT p.id_producto, p.nombre, p.descripcion, p.precio,
               p.stock_actual, p.stock_minimo, p.imagen, p.estado, p.id_categoria,
               c.nombre AS categoria_nombre
        FROM Producto p LEFT JOIN Categoria c ON p.id_categoria=c.id_categoria
        $where ORDER BY p.id_producto ASC LIMIT :limit OFFSET :offset";

$stmt = $conn->prepare($sql);
$stmt->bindValue(':limit',  $porPagina, \PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset,    \PDO::PARAM_INT);
if ($buscar) {
    $stmt->bindValue(':q',  "%$buscar%");
    $stmt->bindValue(':q2', "%$buscar%");
}
$stmt->execute();
$productos = $stmt->fetchAll(\PDO::FETCH_ASSOC);
$categorias = $conn->query("SELECT id_categoria, nombre FROM Categoria ORDER BY nombre")->fetchAll(\PDO::FETCH_ASSOC);

echo json_encode([
    'ok'        => true,
    'productos' => $productos,
    'categorias'=> $categorias,
    'total'     => $total,
    'pagina'    => $pagina,
    'paginas'   => ceil($total / $porPagina),
    'por_pagina'=> $porPagina
]);
