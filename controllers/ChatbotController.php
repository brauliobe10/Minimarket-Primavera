<?php

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Chatbot.php';

use Config\Database;
use Models\Chatbot;

header('Content-Type: application/json; charset=utf-8');

try {
    $db = new Database();
    $conexion = $db->conectar();
    $chatbotModel = new Chatbot($conexion);

    $action = $_GET['action'] ?? $_POST['action'] ?? '';

    // Si viene por JSON raw body (fetch POST)
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
        if (is_array($input)) {
            $action = $input['action'] ?? $action;
            $mensaje = $input['mensaje'] ?? '';
        } else {
            $mensaje = $_POST['mensaje'] ?? '';
        }
    } else {
        $mensaje = $_GET['mensaje'] ?? '';
    }

    if ($action === 'recomendadas') {
        $preguntas = $chatbotModel->obtenerPreguntasRecomendadas();
        echo json_encode([
            'ok' => true,
            'preguntas' => $preguntas
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    if ($action === 'consultar' || $_SERVER['REQUEST_METHOD'] === 'POST') {
        // Sanitizar y limitar longitud del mensaje
        $mensaje = mb_substr(trim($mensaje ?? ''), 0, 500);

        if (empty($mensaje)) {
            echo json_encode([
                'ok'       => false,
                'respuesta' => 'El mensaje no puede estar vacío.'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }

        $respuesta = $chatbotModel->procesarMensaje($mensaje);
        echo json_encode([
            'ok'       => true,
            'respuesta' => $respuesta
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Acción no definida: devolver recomendadas por defecto
    echo json_encode([
        'ok' => true,
        'preguntas' => $chatbotModel->obtenerPreguntasRecomendadas()
    ], JSON_UNESCAPED_UNICODE);

} catch (\Exception $e) {
    http_response_code(500);
    echo json_encode([
        'ok' => false,
        'respuesta' => 'Ocurrió un error al procesar la consulta en el servidor.'
    ], JSON_UNESCAPED_UNICODE);
}
