<?php

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Categoria.php';

use Config\Database;
use Models\Categoria;

$db = new Database();
$conexion = $db->conectar();

$categoriaModel = new Categoria($conexion);

$comida = $categoriaModel->obtenerPorGrupo('Comida');
$bebidas = $categoriaModel->obtenerPorGrupo('Bebidas');
$limpieza = $categoriaModel->obtenerPorGrupo('Limpieza del hogar');
$cuidado = $categoriaModel->obtenerPorGrupo('Cuidado personal');