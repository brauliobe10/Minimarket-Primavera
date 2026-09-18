<?php
session_start();
require_once __DIR__ . '/../config/Database.php';

use Config\Database;

// Incluir PHPMailer (sin Composer)
require_once __DIR__ . '/../lib/PHPMailer/Exception.php';
require_once __DIR__ . '/../lib/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/../lib/PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = trim($_POST['correo'] ?? '');

    if (empty($correo)) {
        echo json_encode(['ok' => false, 'mensaje' => 'Por favor, ingresa tu correo.']);
        exit;
    }

    $db = new Database();
    $conn = $db->conectar();

    // Buscar al usuario por correo
    $stmt = $conn->prepare("SELECT id_usuario, nombres FROM usuario WHERE correo = ? AND estado = 1");
    $stmt->execute([$correo]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        echo json_encode(['ok' => false, 'mensaje' => 'Los datos ingresados no coinciden con ningún registro.']);
        exit;
    }

    // Generar código de 6 dígitos
    $codigo = sprintf("%06d", mt_rand(1, 999999));
    
    // Guardar en la base de datos con expiración de 15 minutos
    $expira = date('Y-m-d H:i:s', strtotime('+15 minutes'));
    $updateStmt = $conn->prepare("UPDATE usuario SET codigo_recuperacion = ?, codigo_expira = ? WHERE id_usuario = ?");
    $updateStmt->execute([$codigo, $expira, $usuario['id_usuario']]);

    $_SESSION['recuperacion_usuario_id'] = $usuario['id_usuario'];

    // ---------------------------------------------------------
    // CONFIGURACIÓN DE ENVÍO DE CORREO (PHPMailer)
    // ---------------------------------------------------------
    $mail = new PHPMailer(true);
    try {
        // Configuracion del Servidor SMTP
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com'; 
        $mail->SMTPAuth   = true;
        // ==========================================
        // IMPORTANTE: REEMPLAZA ESTOS DATOS
        // ==========================================
        $mail->Username   = 'rialexpe@gmail.com'; 
        $mail->Password   = 'aqda rmzv qnmp bvsc'; 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;
        $mail->CharSet    = 'UTF-8';

        $mail->setFrom('rialexpe@gmail.com', 'Market Primavera');
        $mail->addAddress($correo, $usuario['nombres']);

        // Contenido del correo (Plantilla HTML Profesional)
        $mail->isHTML(true);
        $mail->Subject = 'Recuperación de Contraseña - Market Primavera';

        // Diseño en HTML
        $htmlContent = "
        <div style='font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 40px 0;'>
            <div style='max-width: 600px; background-color: #ffffff; margin: 0 auto; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05);'>
                <!-- Header -->
                <div style='background-color: #e30613; padding: 25px; text-align: center;'>
                    <h1 style='color: #ffffff; margin: 0; font-size: 24px; letter-spacing: 1px;'>Market Primavera</h1>
                </div>
                
                <!-- Body -->
                <div style='padding: 40px 30px; color: #333333;'>
                    <h2 style='margin-top: 0; color: #111111; font-size: 20px;'>Hola, {$usuario['nombres']}</h2>
                    <p style='font-size: 16px; line-height: 1.6; color: #555555;'>
                        Hemos recibido una solicitud para restablecer la contraseña de tu cuenta. Por favor, utiliza el siguiente código de seguridad para continuar con el proceso.
                    </p>
                    
                    <div style='text-align: center; margin: 35px 0;'>
                        <div style='display: inline-block; background-color: #f8f9fa; border: 2px dashed #cccccc; border-radius: 8px; padding: 15px 40px;'>
                            <span style='font-size: 32px; font-weight: bold; letter-spacing: 5px; color: #e30613;'>{$codigo}</span>
                        </div>
                    </div>
                    
                    <p style='font-size: 14px; line-height: 1.6; color: #777777; text-align: center;'>
                        <em>Este código expirará en 15 minutos. Si no solicitaste este cambio, puedes ignorar este correo.</em>
                    </p>
                </div>
                
                <!-- Footer -->
                <div style='background-color: #f9f9f9; padding: 20px; text-align: center; border-top: 1px solid #eeeeee;'>
                    <p style='margin: 0; font-size: 12px; color: #999999;'>
                        &copy; " . date('Y') . " Market Primavera. Todos los derechos reservados.<br>
                        No respondas a este correo generado automáticamente.
                    </p>
                </div>
            </div>
        </div>
        ";

        $mail->Body = $htmlContent;

        $mail->send();
        echo json_encode(['ok' => true, 'mensaje' => 'Código enviado por correo.']);
    } catch (Exception $e) {
        echo json_encode(['ok' => false, 'mensaje' => 'Error al enviar el correo: ' . $mail->ErrorInfo]);
    }

} else {
    echo json_encode(['ok' => false, 'mensaje' => 'Método no permitido.']);
}
