<?php

require_once __DIR__ . "/../config/Database.php";
require_once __DIR__ . "/../models/AtencionCliente.php";
require_once __DIR__ . "/AuthController.php";

use Models\AtencionCliente;

header("Content-Type: application/json; charset=UTF-8");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!estaLogueado()) {
    echo json_encode([
        "success" => false,
        "message" => "Debes iniciar sesión para enviar un mensaje de soporte."
    ]);
    exit;
}

$asunto = trim($_POST['asunto'] ?? '');
if ($asunto === 'otro') {
    $asunto = trim($_POST['asunto_otro'] ?? 'Otro');
}
$asunto  = mb_substr($asunto, 0, 200);
$mensaje = mb_substr(trim($_POST['mensaje'] ?? ''), 0, 3000);
$aceptoTerminos = isset($_POST['acepto-terminos']);

if (empty($asunto) || empty($mensaje) || !$aceptoTerminos) {
    echo json_encode([
        "success" => false,
        "message" => "Faltan campos obligatorios."
    ]);
    exit;
}

$datos = [
    "usuario_id" => $_SESSION['usuario_id'],
    "asunto" => $asunto,
    "mensaje" => $mensaje
];

try {
    $atencionCliente = new AtencionCliente();

    if ($atencionCliente->guardar($datos)) {
        echo json_encode([
            "success" => true,
            "message" => "Mensaje enviado con éxito."
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Ocurrió un error al guardar."
        ]);
    }

} catch (\Exception $e) {
    error_log('[AtencionClienteController] ' . $e->getMessage());
    echo json_encode([
        "success" => false,
        "message" => "Error interno al enviar el mensaje. Inténtalo de nuevo."
    ]);
}