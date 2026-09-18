<?php

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../config/Security.php';
require_once __DIR__ . '/../models/CarritoModel.php';

use Config\Database;
use Config\Security;
use Models\CarritoModel;

Security::initSession();

header('Content-Type: application/json; charset=utf-8');

try {
    $db = new Database();
    $conexion = $db->conectar();
    $carritoModel = new CarritoModel($conexion);

    $idUsuario = $_SESSION['usuario_id'] ?? 0;
    $action = $_GET['action'] ?? $_POST['action'] ?? '';

    // Si viene por JSON raw body (fetch POST)
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
        if (is_array($input)) {
            $action     = $input['action']     ?? $action;
            $items      = $input['items']      ?? [];
            $idProducto = (int) ($input['id_producto'] ?? 0);
            $cantidad   = (int) ($input['cantidad']    ?? 1);
        } else {
            $items      = $_POST['items']      ?? [];
            $idProducto = (int) ($_POST['id_producto'] ?? 0);
            $cantidad   = (int) ($_POST['cantidad']    ?? 1);
        }
    } else {
        $items      = [];
        $idProducto = (int) ($_GET['id_producto'] ?? 0);
        $cantidad   = (int) ($_GET['cantidad']    ?? 1);
    }

    // Regla 3: Si no hay usuario autenticado (Invitado), la BD no debe usarse para el carrito
    if (!$idUsuario) {
        if ($action === 'obtener') {
            echo json_encode(['ok' => true, 'items' => []], JSON_UNESCAPED_UNICODE);
            exit();
        }
        echo json_encode(['ok' => false, 'mensaje' => 'Los usuarios invitados deben usar localStorage.'], JSON_UNESCAPED_UNICODE);
        exit();
    }

    // Regla 1: Usuario Autenticado -> Operaciones directas a BD
    if ($action === 'obtener') {
        $itemsObtenidos = $carritoModel->obtenerItemsCarrito($idUsuario);
        echo json_encode(['ok' => true, 'items' => $itemsObtenidos], JSON_UNESCAPED_UNICODE);
        exit();
    }

    if ($action === 'agregar') {
        $res = $carritoModel->agregarOActualizarProducto($idUsuario, $idProducto, $cantidad);
        echo json_encode($res, JSON_UNESCAPED_UNICODE);
        exit();
    }

    if ($action === 'eliminar') {
        $res = $carritoModel->eliminarProducto($idUsuario, $idProducto);
        echo json_encode($res, JSON_UNESCAPED_UNICODE);
        exit();
    }

    if ($action === 'vaciar') {
        $res = $carritoModel->vaciarCarrito($idUsuario);
        echo json_encode($res, JSON_UNESCAPED_UNICODE);
        exit();
    }

    if ($action === 'sincronizar') {
        $ok = $carritoModel->sincronizarItems($idUsuario, $items);
        $itemsResult = $carritoModel->obtenerItemsCarrito($idUsuario);
        echo json_encode(['ok' => $ok, 'items' => $itemsResult], JSON_UNESCAPED_UNICODE);
        exit();
    }

    echo json_encode(['ok' => false, 'mensaje' => 'Acción no válida.'], JSON_UNESCAPED_UNICODE);

} catch (\Exception $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'mensaje' => 'Error en el servidor de carrito.'], JSON_UNESCAPED_UNICODE);
}
