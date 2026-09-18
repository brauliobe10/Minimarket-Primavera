<?php

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Producto.php';

use Config\Database;
use Models\Producto;

header('Content-Type: application/json');

$termino = trim($_GET['q'] ?? '');

if ($termino == '') {
    echo json_encode([]);
    exit;
}

// Crear conexión
$db = new Database();
$conexion = $db->conectar();

// Crear modelo
$modeloProducto = new Producto($conexion);

// Buscar productos
$resultados = $modeloProducto->buscarPorNombre($termino);

// Devolver JSON
echo json_encode($resultados);