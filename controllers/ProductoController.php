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

$idCategoria = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$categoria = $categoriaModel->obtenerPorId($idCategoria);

$productos = $productoModel->obtenerPorCategoria($idCategoria);

$rangoPrecios = $productoModel->obtenerRangoPreciosPorCategoria($idCategoria);

$precioMin = floor($rangoPrecios['precio_min']);
$precioMax = ceil($rangoPrecios['precio_max']);