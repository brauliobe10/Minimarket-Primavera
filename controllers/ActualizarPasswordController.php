<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../config/Security.php';

use Config\Database;
use Config\Security;

Security::initSession();
Security::setSecurityHeaders();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {
    $accion = $_POST['accion'];

    // Validar que el usuario haya pasado por el Paso 1
    if (!isset($_SESSION['recuperacion_usuario_id'])) {
        echo json_encode(['ok' => false, 'mensaje' => 'Sesión expirada o inválida. Por favor, reinicia el proceso.']);
        exit;
    }

    $id_usuario = $_SESSION['recuperacion_usuario_id'];
    $db = new Database();
    $conn = $db->conectar();

    // ---------------------------------------------------------
    // PASO 2: Confirmar Código
    // ---------------------------------------------------------
    if ($accion === 'confirmar_codigo') {
        $codigo = trim($_POST['codigo'] ?? '');
        
        if (empty($codigo)) {
            echo json_encode(['ok' => false, 'mensaje' => 'Código vacío.']);
            exit;
        }

        $stmt = $conn->prepare("SELECT codigo_recuperacion, codigo_expira FROM usuario WHERE id_usuario = ?");
        $stmt->execute([$id_usuario]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$usuario || $usuario['codigo_recuperacion'] !== $codigo) {
            echo json_encode(['ok' => false, 'mensaje' => 'El código ingresado es incorrecto.']);
            exit;
        }

        if (strtotime($usuario['codigo_expira']) < time()) {
            echo json_encode(['ok' => false, 'mensaje' => 'El código ha expirado. Por favor, solicita uno nuevo.']);
            exit;
        }

        // Si es correcto, marcamos en sesión que ya verificó el código para dejarlo pasar al Paso 3
        $_SESSION['recuperacion_codigo_valido'] = true;
        echo json_encode(['ok' => true, 'mensaje' => 'Código válido.']);
        exit;
    }

    // ---------------------------------------------------------
    // PASO 3: Cambiar Contraseña
    // ---------------------------------------------------------
    if ($accion === 'cambiar_password_recuperacion') {
        if (!isset($_SESSION['recuperacion_codigo_valido']) || !$_SESSION['recuperacion_codigo_valido']) {
            echo json_encode(['ok' => false, 'mensaje' => 'Debes validar el código primero.']);
            exit;
        }

        $password = $_POST['password'] ?? '';
        $confirm = $_POST['confirm'] ?? '';

        if (empty($password) || strlen($password) < 6) {
            echo json_encode(['ok' => false, 'mensaje' => 'La contraseña debe tener al menos 6 caracteres.']);
            exit;
        }

        if ($password !== $confirm) {
            echo json_encode(['ok' => false, 'mensaje' => 'Las contraseñas no coinciden.']);
            exit;
        }

        $hash = password_hash($password, PASSWORD_BCRYPT);

        // Actualizar la contraseña y limpiar los tokens de recuperación
        $stmt = $conn->prepare("UPDATE usuario SET password_hash = ?, codigo_recuperacion = NULL, codigo_expira = NULL WHERE id_usuario = ?");
        
        if ($stmt->execute([$hash, $id_usuario])) {
            // Limpiar la sesión de recuperación
            unset($_SESSION['recuperacion_usuario_id']);
            unset($_SESSION['recuperacion_codigo_valido']);
            
            echo json_encode(['ok' => true, 'mensaje' => 'Contraseña actualizada correctamente. ¡Ya puedes iniciar sesión!']);
        } else {
            echo json_encode(['ok' => false, 'mensaje' => 'Error al guardar la nueva contraseña.']);
        }
        exit;
    }

} else {
    echo json_encode(['ok' => false, 'mensaje' => 'Método no permitido.']);
}
