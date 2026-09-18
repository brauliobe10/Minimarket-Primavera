<?php
require_once __DIR__ . '/../AuthController.php';
require_once __DIR__ . '/../../config/Database.php';

use Config\Database;

header('Content-Type: application/json');
requireAdmin();

$conn = (new Database())->conectar();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $d = json_decode(file_get_contents('php://input'), true);
    if (!is_array($d)) {
        echo json_encode(['ok' => false, 'mensaje' => 'Datos inválidos.']);
        exit;
    }
    $ac = trim($d['accion'] ?? '');

    try {
        if ($ac === 'crear') {
            $nombre = trim($d['nombre'] ?? '');
            if (empty($nombre)) {
                echo json_encode(['ok' => false, 'mensaje' => 'El nombre de la categoría es obligatorio.']);
                exit;
            }
            $stmt = $conn->prepare("INSERT INTO Categoria (nombre, descripcion, imagen, grupo) VALUES (?, ?, ?, ?)");
            $stmt->execute([
                $nombre,
                trim($d['descripcion'] ?? ''),
                trim($d['imagen']      ?? ''),
                trim($d['grupo']       ?? ''),
            ]);
            echo json_encode(['ok' => true, 'mensaje' => 'Categoría creada correctamente.']);

        } elseif ($ac === 'editar') {
            $id     = (int)($d['id_categoria'] ?? 0);
            $nombre = trim($d['nombre'] ?? '');
            if ($id <= 0 || empty($nombre)) {
                echo json_encode(['ok' => false, 'mensaje' => 'ID y nombre son obligatorios.']);
                exit;
            }
            $stmt = $conn->prepare("UPDATE Categoria SET nombre = ?, descripcion = ?, imagen = ?, grupo = ? WHERE id_categoria = ?");
            $stmt->execute([
                $nombre,
                trim($d['descripcion'] ?? ''),
                trim($d['imagen']      ?? ''),
                trim($d['grupo']       ?? ''),
                $id,
            ]);
            echo json_encode(['ok' => true, 'mensaje' => 'Categoría actualizada correctamente.']);

        } elseif ($ac === 'eliminar') {
            $id = (int)($d['id_categoria'] ?? 0);
            if ($id <= 0) {
                echo json_encode(['ok' => false, 'mensaje' => 'ID inválido.']);
                exit;
            }
            $stmt = $conn->prepare("DELETE FROM Categoria WHERE id_categoria = ?");
            $stmt->execute([$id]);
            echo json_encode(['ok' => true, 'mensaje' => 'Categoría eliminada correctamente.']);
        } else {
            echo json_encode(['ok' => false, 'mensaje' => 'Acción no reconocida.']);
        }
    } catch (\PDOException $e) {
        error_log('[CategoriasController] ' . $e->getMessage());
        if ($e->getCode() == '23000') {
            echo json_encode(['ok' => false, 'mensaje' => 'No se puede eliminar la categoría porque tiene productos asociados.']);
        } else {
            echo json_encode(['ok' => false, 'mensaje' => 'Error en base de datos. Inténtalo de nuevo.']);
        }
    }
    exit;
}

// GET request: List categories with pagination
$pagina    = max(1, (int)($_GET['pagina'] ?? 1));
$porPagina = 15;
$offset    = ($pagina - 1) * $porPagina;
$buscar    = trim($_GET['q'] ?? '');

$where = $buscar ? "WHERE nombre LIKE :q OR descripcion LIKE :q2 OR grupo LIKE :q3" : "";

$stmtTotal = $conn->prepare("SELECT COUNT(*) FROM Categoria $where");
if ($buscar) {
    $stmtTotal->execute([':q' => "%$buscar%", ':q2' => "%$buscar%", ':q3' => "%$buscar%"]);
} else {
    $stmtTotal->execute();
}
$total = (int)$stmtTotal->fetchColumn();

$sql = "SELECT id_categoria, nombre, descripcion, imagen, grupo 
        FROM Categoria 
        $where 
        ORDER BY id_categoria ASC 
        LIMIT :limit OFFSET :offset";

$stmt = $conn->prepare($sql);
$stmt->bindValue(':limit',  $porPagina, \PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset,    \PDO::PARAM_INT);
if ($buscar) {
    $stmt->bindValue(':q',  "%$buscar%");
    $stmt->bindValue(':q2', "%$buscar%");
    $stmt->bindValue(':q3', "%$buscar%");
}
$stmt->execute();
$categorias = $stmt->fetchAll(\PDO::FETCH_ASSOC);

echo json_encode([
    'ok'         => true,
    'categorias' => $categorias,
    'total'      => $total,
    'pagina'     => $pagina,
    'paginas'    => ceil($total / $porPagina),
    'por_pagina' => $porPagina
]);
