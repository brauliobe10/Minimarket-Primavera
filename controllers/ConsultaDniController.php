<?php

header('Content-Type: application/json');

// =========================================
// CONFIGURACIÓN
// =========================================
define('API_TOKEN', 'u3Ubsb0Z55jpSitHkGki09gjLEfMO0L2HS4ehUmLdH30qnzld6oNwdl5cb64'); // pon aquí tu token de api-codart.cgrt.org
define('API_URL', 'https://api-codart.cgrt.org/api/v1/consultas/reniec/dni');

// =========================================
// 1. VALIDAR ENTRADA
// =========================================
if (!isset($_GET['dni'])) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "error"   => "DNI requerido"
    ]);
    exit;
}

$dni = trim($_GET['dni']);

if (!preg_match('/^\d{8}$/', $dni)) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "error"   => "DNI inválido, debe tener 8 dígitos"
    ]);
    exit;
}

// =========================================
// 2. VERIFICAR SI EL DNI YA ESTÁ REGISTRADO EN LA BD
// =========================================
require_once __DIR__ . '/../config/Database.php';
use Config\Database;

try {
    $conn = (new Database())->conectar();
    $stmt = $conn->prepare("SELECT COUNT(*) FROM usuario WHERE dni = ?");
    $stmt->execute([$dni]);
    if ($stmt->fetchColumn() > 0) {
        http_response_code(409);
        echo json_encode([
            "success" => false,
            "error"   => "Este DNI ya está registrado en el sistema."
        ]);
        exit;
    }
} catch (Exception $e) {
    // Si falla la conexión a la BD, continuamos igual con la consulta externa
}

// =========================================
// 3. LLAMADA A LA API
// =========================================
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, API_URL . '/' . urlencode($dni));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Authorization: Bearer ' . API_TOKEN
]);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);

$respuesta = curl_exec($ch);
$httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

// Log opcional para debug (descomenta si lo necesitas, pero NO lo dejes activo en producción)
// error_log("DNI API [$dni] HTTP $httpCode: $respuesta");

if ($curlError) {
    http_response_code(502);
    echo json_encode([
        "success" => false,
        "error"   => "Error de conexión: " . $curlError
    ]);
    exit;
}

$data = json_decode($respuesta, true);

if ($httpCode !== 200 || !is_array($data)) {
    http_response_code($httpCode ?: 500);
    echo json_encode([
        "success"   => false,
        "error"     => "La API respondió con un error",
        "http_code" => $httpCode
    ]);
    exit;
}

// =========================================
// 4. RESPUESTA FINAL (adaptada al formato que espera el frontend)
// =========================================
if (!isset($data['result']) || !is_array($data['result'])) {
    http_response_code(404);
    echo json_encode([
        "success" => false,
        "error"   => "DNI no encontrado"
    ]);
    exit;
}

$result    = $data['result'];
$nombres   = $result['first_name'] ?? '';
$apellidos = trim(($result['first_last_name'] ?? '') . ' ' . ($result['second_last_name'] ?? ''));

echo json_encode([
    "success"         => true,
    "dni"             => $result['document_number'] ?? $dni,
    "nombre_completo" => trim($nombres . ' ' . $apellidos),
    "nombres"         => $nombres,
    "apellidos"       => $apellidos
], JSON_UNESCAPED_UNICODE);