<?php

require_once __DIR__ . '/../../config/Database.php';
require_once __DIR__ . '/../../models/AtencionCliente.php';
require_once __DIR__ . '/../AuthController.php';
require_once __DIR__ . '/../../lib/PHPMailer/Exception.php';
require_once __DIR__ . '/../../lib/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/../../lib/PHPMailer/SMTP.php';

use Models\AtencionCliente;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');

if (!estaLogueado() || (!esAdmin() && !esSoporte())) {
    echo json_encode(["success" => false, "message" => "Acceso no autorizado."]);
    exit;
}

$accion = $_POST['accion'] ?? $_GET['accion'] ?? '';
$soporteModelo = new AtencionCliente();

if ($accion === 'listar') {
    try {
        $tickets = $soporteModelo->obtenerTodos();
        echo json_encode(["success" => true, "data" => $tickets]);
    } catch (\Exception $e) {
        echo json_encode(["success" => false, "message" => "Error al listar mensajes."]);
    }
    exit;
}

if ($accion === 'obtener_hilo') {
    $id = $_GET['id'] ?? '';
    if (empty($id)) {
        echo json_encode(["success" => false, "message" => "ID no proporcionado."]);
        exit;
    }
    try {
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

    // Limitar longitud de la respuesta (anti-spam/DoS)
    if (mb_strlen($respuesta) > 5000) {
        echo json_encode(["success" => false, "message" => "La respuesta es demasiado larga."]);
        exit;
    }

    try {
        // Obtenemos los datos originales para enviar el correo
        $ticket = $soporteModelo->obtenerPorId($id);
        
        if (!$ticket) {
            echo json_encode(["success" => false, "message" => "El ticket no existe."]);
            exit;
        }

        if ($soporteModelo->responder($id, $respuesta)) {
            // Enviar correo de notificación
            enviarCorreoNotificacion($ticket['correo'], $ticket['nombres'], $ticket['asunto']);
            echo json_encode(["success" => true, "message" => "Respuesta enviada con éxito."]);
        } else {
            echo json_encode(["success" => false, "message" => "No se pudo guardar la respuesta."]);
        }

    } catch (\Exception $e) {
        error_log('[SoporteAdminController] responder: ' . $e->getMessage());
        echo json_encode(["success" => false, "message" => "Error interno al enviar respuesta. Inténtalo de nuevo."]);
    }
    exit;
}

function enviarCorreoNotificacion($correoDestino, $nombre, $asunto) {
    $mail = new PHPMailer(true);
    $linkBuzon = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http')
        . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . BASE_URL . '/views/perfil.php#mensajes';
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com'; 
        $mail->SMTPAuth   = true;
        // Cuenta principal de Market Primavera
        $mail->Username   = 'rialexpe@gmail.com'; 
        $mail->Password   = 'aqda rmzv qnmp bvsc'; 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;
        $mail->CharSet    = 'UTF-8';

        $mail->setFrom('rialexpe@gmail.com', 'Market Primavera');
        $mail->addAddress($correoDestino, $nombre);

        $mail->isHTML(true);
        $mail->Subject = 'Respuesta a tu consulta: ' . $asunto;

        $htmlContent = "
        <div style='font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 40px 0;'>
            <div style='max-width: 600px; background-color: #ffffff; margin: 0 auto; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05);'>
                <div style='background-color: #e30613; padding: 25px; text-align: center;'>
                    <h1 style='color: #ffffff; margin: 0; font-size: 24px;'>Market Primavera</h1>
                </div>
                
                <div style='padding: 40px 30px; color: #333333;'>
                    <h2 style='color: #2c3e50; font-size: 20px; margin-top: 0;'>¡Hola, $nombre!</h2>
                    <p style='font-size: 16px; line-height: 1.6; color: #555555;'>
                        El equipo de atención al cliente de <strong>Market Primavera</strong> ha respondido a tu consulta sobre <strong>'$asunto'</strong>.
                    </p>
                    
                    <div style='text-align: center; margin: 40px 0;'>
                        <a href='{$linkBuzon}' 
                           style='background-color: #e30613; color: #ffffff; padding: 14px 35px; text-decoration: none; border-radius: 5px; font-weight: bold; font-size: 16px; display: inline-block;'>
                           Leer Respuesta en tu Buzón
                        </a>
                    </div>
                    
                    <p style='font-size: 14px; color: #888888; text-align: center; margin-top: 30px;'>
                        Este es un correo automático. Por favor, no respondas directamente a este correo.<br>
                        Para continuar la conversación, usa tu buzón en la página web.
                    </p>
                </div>
            </div>
        </div>
        ";

        $mail->Body = $htmlContent;
        $mail->send();
    } catch (Exception $e) {
        error_log("Error al enviar correo de soporte: {$mail->ErrorInfo}");
    }
}
