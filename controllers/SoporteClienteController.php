<?php

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/AtencionCliente.php';
require_once __DIR__ . '/AuthController.php';

use Models\AtencionCliente;

header('Content-Type: application/json');

if (!estaLogueado()) {
    echo json_encode(["success" => false, "message" => "Acceso no autorizado."]);
    exit;
}

$accion = $_POST['accion'] ?? $_GET['accion'] ?? '';
$soporteModelo = new AtencionCliente();
$usuario_id = $_SESSION['usuario_id'];

if ($accion === 'mis_mensajes') {
    try {
        $mensajes = $soporteModelo->obtenerPorUsuario($usuario_id);
        echo json_encode(["success" => true, "data" => $mensajes]);
    } catch (\Exception $e) {
        echo json_encode(["success" => false, "message" => "Error al obtener mensajes."]);
    }
    exit;
}

if ($accion === 'obtener_hilo') {
    $id = (int)($_GET['id'] ?? 0);
    if ($id <= 0) {
        echo json_encode(["success" => false, "message" => "ID no proporcionado."]);
        exit;
    }
    try {
        // Validate ticket belongs to user
        $ticket = $soporteModelo->obtenerPorId($id);
        if (!$ticket || $ticket['usuario_id'] != $usuario_id) {
            echo json_encode(["success" => false, "message" => "No tienes permiso para ver este ticket."]);
            exit;
        }

        $mensajes = $soporteModelo->obtenerMensajesHilo($id);
        echo json_encode(["success" => true, "data" => $mensajes]);
    } catch (\Exception $e) {
        echo json_encode(["success" => false, "message" => "Error al obtener hilo."]);
    }
    exit;
}

if ($accion === 'responder') {
    $id        = (int)($_POST['id']        ?? 0);
    $respuesta = trim($_POST['respuesta'] ?? '');

    if ($id <= 0 || empty($respuesta)) {
        echo json_encode(["success" => false, "message" => "El ID y la respuesta son obligatorios."]);
        exit;
    }

    // Limitar longitud de la respuesta (anti-DoS)
    if (mb_strlen($respuesta) > 2000) {
        echo json_encode(["success" => false, "message" => "La respuesta es demasiado larga (máximo 2000 caracteres)."]);
        exit;
    }

    try {
        $ticket = $soporteModelo->obtenerPorId($id);
        if (!$ticket || $ticket['usuario_id'] != $usuario_id) {
            echo json_encode(["success" => false, "message" => "Ticket no válido."]);
            exit;
        }

        // Privacy rule: Admin must have replied at least once
        $mensajesHilo = $soporteModelo->obtenerMensajesHilo($id);
        $tieneRespuestaAdmin = false;
        foreach ($mensajesHilo as $msg) {
            if ($msg['remitente'] === 'admin') {
                $tieneRespuestaAdmin = true;
                break;
            }
        }

        if (!$tieneRespuestaAdmin) {
            echo json_encode(["success" => false, "message" => "Debes esperar a que un administrador responda primero."]);
            exit;
        }

        if ($soporteModelo->responderCliente($id, $respuesta)) {
            echo json_encode(["success" => true, "message" => "Respuesta enviada con éxito."]);
        } else {
            echo json_encode(["success" => false, "message" => "No se pudo guardar la respuesta."]);
        }
    } catch (\Exception $e) {
        error_log('[SoporteClienteController] responder: ' . $e->getMessage());
        echo json_encode(["success" => false, "message" => "Error interno al responder. Inténtalo de nuevo."]);
    }
    exit;
}
