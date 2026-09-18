<?php

require_once __DIR__ . '/AuthController.php';
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Producto.php';
require_once __DIR__ . '/../models/Categoria.php';

use Config\Database;
use Models\Producto;
use Models\Categoria;

requireAdmin();

$db         = new Database();
$conexion   = $db->conectar();

// ── Métodos adicionales que necesitamos en el modelo ──────────
// Los ejecutamos directamente con PDO para no tocar el modelo existente

function obtenerTodosProductos(\PDO $conn): array {
    $stmt = $conn->query(
        "SELECT p.id_producto, p.nombre, p.descripcion, p.precio,
                p.stock_actual, p.imagen, p.estado, p.id_categoria,
                c.nombre AS categoria_nombre
         FROM Producto p
         LEFT JOIN Categoria c ON p.id_categoria = c.id_categoria
         ORDER BY p.id_producto DESC"
    );
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
}

function obtenerTodasCategorias(\PDO $conn): array {
    $stmt = $conn->query("SELECT id_categoria, nombre FROM Categoria ORDER BY nombre");
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
}

$mensaje = '';
$tipoMsg = '';

// ── CRUD ───────────────────────────────────────────────────────
$accion = $_POST['accion'] ?? $_GET['accion'] ?? '';

if ($accion === 'crear' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $conexion->prepare(
        "INSERT INTO Producto (nombre, descripcion, precio, stock_actual, imagen, id_categoria, estado)
         VALUES (:nombre, :descripcion, :precio, :stock, :imagen, :cat, 1)"
    );
    $stmt->execute([
        'nombre'      => trim($_POST['nombre']),
        'descripcion' => trim($_POST['descripcion']),
        'precio'      => (float) $_POST['precio'],
        'stock'       => (int)   $_POST['stock_actual'],
        'imagen'      => trim($_POST['imagen']),
        'cat'         => (int)   $_POST['id_categoria'],
    ]);
    $mensaje = 'Producto creado correctamente.';
    $tipoMsg = 'ok';
}

if ($accion === 'editar' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $conexion->prepare(
        "UPDATE Producto SET nombre=:nombre, descripcion=:descripcion,
         precio=:precio, stock_actual=:stock, imagen=:imagen,
         id_categoria=:cat, estado=:estado
         WHERE id_producto=:id"
    );
    $stmt->execute([
        'nombre'      => trim($_POST['nombre']),
        'descripcion' => trim($_POST['descripcion']),
        'precio'      => (float) $_POST['precio'],
        'stock'       => (int)   $_POST['stock_actual'],
        'imagen'      => trim($_POST['imagen']),
        'cat'         => (int)   $_POST['id_categoria'],
        'estado'      => isset($_POST['estado']) ? 1 : 0,
        'id'          => (int)   $_POST['id_producto'],
    ]);
    $mensaje = 'Producto actualizado correctamente.';
    $tipoMsg = 'ok';
}

if ($accion === 'eliminar' && isset($_GET['id'])) {
    $stmt = $conexion->prepare("UPDATE Producto SET estado = 0 WHERE id_producto = :id");
    $stmt->execute(['id' => (int) $_GET['id']]);
    $mensaje = 'Producto desactivado (Soft Delete) correctamente.';
    $tipoMsg = 'ok';
}

$productos   = obtenerTodosProductos($conexion);
$categorias  = obtenerTodasCategorias($conexion);

// Producto a editar (si viene el formulario de edición)
$productoEditar = null;
if (isset($_GET['editar'])) {
    $stmt = $conexion->prepare("SELECT * FROM Producto WHERE id_producto = :id");
    $stmt->execute(['id' => (int) $_GET['editar']]);
    $productoEditar = $stmt->fetch(\PDO::FETCH_ASSOC);
}
