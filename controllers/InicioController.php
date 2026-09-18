<?php

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Producto.php';
require_once __DIR__ . '/../models/Categoria.php';

use Config\Database;
use Models\Producto;
use Models\Categoria;

$db = new Database();
$conexion = $db->conectar();

$productoModel = new Producto($conexion);
$categoriaModel = new Categoria($conexion);

$productosDestacados = $productoModel->obtenerDestacados(8);
$productosOferta     = $productoModel->obtenerEnOferta();
$categorias          = $categoriaModel->obtenerTodas();

