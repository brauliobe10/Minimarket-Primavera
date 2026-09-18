<?php
namespace Config;

require_once __DIR__ . '/../lib/PHPMailer/Exception.php';
require_once __DIR__ . '/../lib/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/../lib/PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailHelper {
    
    private function getMailer(): PHPMailer {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com'; 
        $mail->SMTPAuth   = true;
        $mail->Username   = 'rialexpe@gmail.com'; 
        $mail->Password   = 'aqda rmzv qnmp bvsc'; 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;
        $mail->setFrom('rialexpe@gmail.com', 'Market Primavera');
        $mail->CharSet    = 'UTF-8';
        return $mail;
    }

    public function enviarComprobantePedido(string $emailDestino, string $nombreCliente, array $pedido) {
        if (empty($emailDestino)) return false;

        try {
            $mail = $this->getMailer();
            $mail->addAddress($emailDestino, $nombreCliente);
            $mail->isHTML(true);
            $mail->Subject = 'Comprobante de Pedido #' . $pedido['id_pedido'] . ' - Market Primavera';
            
            $html  = "<div style='font-family:Arial,sans-serif;max-width:600px;margin:auto;border:1px solid #ddd;border-radius:8px;overflow:hidden;'>";
            $html .= "<div style='background:#e30613;padding:20px;text-align:center;'>";
            $html .= "<h1 style='color:#fff;margin:0;'>Market Primavera</h1>";
            $html .= "<p style='color:#fff;margin:5px 0 0;'>RUC: 20612345678</p>";
            $html .= "</div>";
            $html .= "<div style='padding:25px;'>";
            $html .= "<h2 style='color:#333;'>¡Gracias por tu compra, {$nombreCliente}!</h2>";
            $html .= "<p style='color:#555;'>Tu pedido ha sido procesado exitosamente.</p>";
            $html .= "<table style='width:100%;border-collapse:collapse;margin-top:15px;'>";
            $html .= "<tr style='background:#f5f5f5;'><td style='padding:10px;font-weight:bold;'>N° de Pedido</td><td style='padding:10px;'>#{$pedido['id_pedido']}</td></tr>";
            $html .= "<tr><td style='padding:10px;font-weight:bold;'>Tipo de entrega</td><td style='padding:10px;'>{$pedido['tipo_entrega']}</td></tr>";
            $html .= "<tr style='background:#f5f5f5;'><td style='padding:10px;font-weight:bold;'>Método de pago</td><td style='padding:10px;'>{$pedido['metodo_pago']}</td></tr>";
            $html .= "<tr><td style='padding:10px;font-weight:bold;'>Total pagado</td><td style='padding:10px;color:#e30613;font-size:1.2em;font-weight:bold;'>S/ " . number_format($pedido['total'], 2) . "</td></tr>";
            $html .= "</table>";
            $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
            $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
            $baseUrl = "{$protocol}://{$host}/TecW_PAF";

            $html .= "<div style='margin-top:25px;text-align:center;'>";
            $html .= "<a href='{$baseUrl}/views/pdf_comprobante.php?id={$pedido['id_pedido']}' style='display:inline-block;padding:12px 25px;background:#e30613;color:#fff;text-decoration:none;border-radius:6px;font-weight:bold;'>Ver Boleta PDF</a>";
            $html .= "</div>";
            $html .= "<p style='color:#888;font-size:0.85em;margin-top:20px;border-top:1px solid #eee;padding-top:15px;'>Este correo es generado automáticamente. Puedes ver tus pedidos en tu perfil.</p>";
            $html .= "</div></div>";

            $mail->Body = $html;
            $mail->AltBody = "Gracias {$nombreCliente}. Tu pedido #{$pedido['id_pedido']} fue procesado. Total: S/ " . number_format($pedido['total'], 2);
            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Error enviando comprobante a {$emailDestino}: {$e->getMessage()}");
            return false;
        }
    }

    public function enviarActualizacionEstado(string $emailDestino, string $nombreCliente, int $idPedido, string $nuevoEstado) {
        if (empty($emailDestino)) return false;

        try {
            $mail = $this->getMailer();
            $mail->addAddress($emailDestino, $nombreCliente);
            $mail->isHTML(true);
            $mail->Subject = 'Actualización de Pedido #' . $idPedido . ' - Market Primavera';
            
            $colorEstado = ['En camino' => '#f39c12', 'Entregado' => '#27ae60', 'Cancelado' => '#e74c3c'][$nuevoEstado] ?? '#e30613';
            $html  = "<div style='font-family:Arial,sans-serif;max-width:600px;margin:auto;border:1px solid #ddd;border-radius:8px;overflow:hidden;'>";
            $html .= "<div style='background:#e30613;padding:20px;text-align:center;'>";
            $html .= "<h1 style='color:#fff;margin:0;'>Market Primavera</h1></div>";
            $html .= "<div style='padding:25px;'>";
            $html .= "<h2 style='color:#333;'>Hola, {$nombreCliente}</h2>";
            $html .= "<p style='color:#555;'>Tu pedido <strong>#{$idPedido}</strong> ha cambiado de estado.</p>";
            $html .= "<div style='background:{$colorEstado};color:#fff;padding:15px;border-radius:6px;text-align:center;font-size:1.3em;font-weight:bold;margin:20px 0;'>{$nuevoEstado}</div>";
            $html .= "<p style='color:#555;'>Ingresa a tu perfil para ver el detalle de tu pedido.</p>";
            $html .= "<p style='color:#888;font-size:0.85em;margin-top:20px;border-top:1px solid #eee;padding-top:15px;'>Equipo Market Primavera</p>";
            $html .= "</div></div>";

            $mail->Body = $html;
            $mail->AltBody = "Hola {$nombreCliente}. Tu pedido #{$idPedido} ahora está: {$nuevoEstado}.";
            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Error enviando actualización a {$emailDestino}: {$e->getMessage()}");
            return false;
        }
    }
}