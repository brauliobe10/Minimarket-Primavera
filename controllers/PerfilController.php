<?php

require_once __DIR__ . '/../config/Database.php';

use Config\Database;

if (session_status() === PHP_SESSION_NONE) {
    require_once __DIR__ . '/../config/Security.php';
    \Config\Security::initSession();
    \Config\Security::setSecurityHeaders();
}

if (!isset($_SESSION['usuario_id'])) {
    header("Location: " . BASE_URL . "/views/inicio.php?login=1");
    exit;
}

$db = (new Database())->conectar();

try {
    
/* =========================================
   ACTUALIZAR PERFIL
========================================= */
if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['accion']) &&
    $_POST['accion'] === 'actualizar'
) {

    $nombre    = trim($_POST['nombre'] ?? '');
    $apellido  = trim($_POST['apellido'] ?? '');
    $dni       = trim($_POST['dni'] ?? '');
    $telefono  = trim($_POST['telefono'] ?? '');
    $direccion = trim($_POST['direccion'] ?? '');

    // Validar nombre y apellido
    if (empty($nombre) || empty($apellido)) {
        $_SESSION['perfil_error'] = "Nombre y apellido son obligatorios.";
        header("Location: " . BASE_URL . "/views/perfil.php");
        exit;
    }

    // Validar DNI
    if ($dni !== '' && !preg_match('/^\d{8}$/', $dni)) {
        $_SESSION['perfil_error'] = "El DNI debe tener exactamente 8 dígitos.";
        header("Location: " . BASE_URL . "/views/perfil.php");
        exit;
    }

    // Obtener datos actuales para proteger datos sensibles
    $stmtActual = $db->prepare("SELECT nombres, apellidos, dni FROM usuario WHERE id_usuario = ?");
    $stmtActual->execute([$_SESSION['usuario_id']]);
    $actual = $stmtActual->fetch(\PDO::FETCH_ASSOC);

    // Si ya existen en BD, se ignora lo enviado y se conserva lo de la base de datos
    if (!empty(trim($actual['nombres'] ?? ''))) {
        $nombre = $actual['nombres'];
    }
    if (!empty(trim($actual['apellidos'] ?? ''))) {
        $apellido = $actual['apellidos'];
    }
    if (!empty(trim($actual['dni'] ?? ''))) {
        $dni = $actual['dni'];
    } else {
        // Solo si el DNI estaba vacío y se está enviando uno nuevo, validamos que no exista
        if ($dni !== '') {
            $check = $db->prepare("
                SELECT id_usuario
                FROM usuario
                WHERE dni = ?
                AND id_usuario != ?
            ");

            $check->execute([$dni, $_SESSION['usuario_id']]);

            if ($check->fetch()) {
                $_SESSION['perfil_error'] = "Ese DNI ya está registrado por otro usuario.";
                header("Location: " . BASE_URL . "/views/perfil.php");
                exit;
            }
        }
    }

    /* ACTUALIZAR TABLA USUARIO */
    $stmt = $db->prepare("
        UPDATE usuario
        SET nombres = ?,
            apellidos = ?,
            dni = ?,
            telefono = ?,
            direccion = ?
        WHERE id_usuario = ?
    ");

    $stmt->execute([
        $nombre,
        $apellido,
        $dni ?: null,
        $telefono ?: null,
        $direccion ?: null,
        $_SESSION['usuario_id']
    ]);

    if (isset($_POST['placa'])) {
        $placa = trim($_POST['placa']);
        $db->prepare("UPDATE repartidor SET placa_vehiculo = ? WHERE id_usuario = ?")->execute([$placa ?: null, $_SESSION['usuario_id']]);
    }

    /* AUTO-ACTIVAR REPARTIDOR si ahora tiene todos los datos completos */
    $checkRep = $db->prepare("
        SELECT r.id_repartidor, r.estado, r.placa_vehiculo, r.telefono AS rep_tel,
               u.dni, u.telefono AS usr_tel
        FROM repartidor r
        JOIN usuario u ON r.id_usuario = u.id_usuario
        WHERE r.id_usuario = ?
    ");
    $checkRep->execute([$_SESSION['usuario_id']]);
    $rep = $checkRep->fetch(\PDO::FETCH_ASSOC);
    
    if ($rep && $rep['estado'] == 0) {
        $dniOk   = !empty($rep['dni']) && preg_match('/^\d{8}$/', $rep['dni']);
        $telOk   = !empty($rep['rep_tel']) || !empty($rep['usr_tel']);
        $placaOk = !empty($rep['placa_vehiculo']);
        
        if ($dniOk && $telOk && $placaOk) {
            $db->prepare("UPDATE repartidor SET estado = 1 WHERE id_repartidor = ?")
               ->execute([$rep['id_repartidor']]);
        }
    }

    /* ACTUALIZAR TABLA DIRECCION */
    if (!empty($direccion)) {

        $checkDir = $db->prepare("
            SELECT id_direccion
            FROM direccion
            WHERE id_usuario = ?
            LIMIT 1
        ");
        $checkDir->execute([$_SESSION['usuario_id']]);

        $direccionExistente = $checkDir->fetch(\PDO::FETCH_ASSOC);

        if ($direccionExistente) {

            $updateDir = $db->prepare("
                UPDATE direccion
                SET direccion = ?
                WHERE id_usuario = ?
            ");

            $updateDir->execute([
                $direccion,
                $_SESSION['usuario_id']
            ]);

        } else {

            $insertDir = $db->prepare("
                INSERT INTO direccion
                (
                    id_usuario,
                    etiqueta,
                    departamento,
                    provincia,
                    distrito,
                    direccion,
                    referencia,
                    predeterminada
                )
                VALUES
                (
                    ?,
                    'Principal',
                    'Lambayeque',
                    'Chiclayo',
                    'Chiclayo',
                    ?,
                    NULL,
                    1
                )
            ");

            $insertDir->execute([
                $_SESSION['usuario_id'],
                $direccion
            ]);
        }
    }

    $_SESSION['usuario_nombre'] = $nombre;
    $_SESSION['perfil_ok'] = "Perfil actualizado correctamente.";

    header("Location: " . BASE_URL . "/views/perfil.php");
    exit;
}
    /* =========================================
       CAMBIAR CONTRASEÑA
    ========================================= */
    if (
        $_SERVER['REQUEST_METHOD'] === 'POST' &&
        isset($_POST['accion']) &&
        $_POST['accion'] === 'cambiarPassword'
    ) {

        $passwordActual    = $_POST['password_actual'] ?? '';
        $passwordNueva     = $_POST['password_nueva'] ?? '';
        $passwordConfirmar = $_POST['password_confirmar'] ?? '';

        if (empty($passwordNueva) || empty($passwordConfirmar)) {
            $_SESSION['perfil_error'] = "Las nuevas contraseñas son obligatorias.";
            header("Location: " . BASE_URL . "/views/perfil.php");
            exit;
        }

        if ($passwordNueva !== $passwordConfirmar) {
            $_SESSION['perfil_error'] = "Las nuevas contraseñas no coinciden.";
            header("Location: " . BASE_URL . "/views/perfil.php");
            exit;
        }

        if (strlen($passwordNueva) < 6) {
            $_SESSION['perfil_error'] = "La nueva contraseña debe tener mínimo 6 caracteres.";
            header("Location: " . BASE_URL . "/views/perfil.php");
            exit;
        }

        $stmt = $db->prepare("
            SELECT password_hash
            FROM usuario
            WHERE id_usuario = ?
        ");

        $stmt->execute([$_SESSION['usuario_id']]);
        $usuarioPassword = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$usuarioPassword) {
            $_SESSION['perfil_error'] = "Usuario no encontrado.";
            header("Location: " . BASE_URL . "/views/perfil.php");
            exit;
        }

        if (!empty($usuarioPassword['password_hash'])) {
            if (empty($passwordActual)) {
                $_SESSION['perfil_error'] = "La contraseña actual es obligatoria para realizar el cambio.";
                header("Location: " . BASE_URL . "/views/perfil.php");
                exit;
            }

            if (!password_verify($passwordActual, $usuarioPassword['password_hash'])) {
                $_SESSION['perfil_error'] = "La contraseña actual es incorrecta.";
                header("Location: " . BASE_URL . "/views/perfil.php");
                exit;
            }
        }

        $nuevoHash = password_hash($passwordNueva, PASSWORD_DEFAULT);

        $stmt = $db->prepare("
            UPDATE usuario
            SET password_hash = ?
            WHERE id_usuario = ?
        ");

        $stmt->execute([
            $nuevoHash,
            $_SESSION['usuario_id']
        ]);

        $_SESSION['perfil_ok'] = "Contraseña actualizada correctamente.";

        header("Location: " . BASE_URL . "/views/perfil.php");
        exit;
    }

    /* =========================================
       GUARDAR NUEVA DIRECCIÓN
    ========================================= */
    if (
        $_SERVER['REQUEST_METHOD'] === 'POST' &&
        isset($_POST['accion']) &&
        $_POST['accion'] === 'guardarDireccion'
    ) {

        $etiqueta       = trim($_POST['etiqueta'] ?? '');
        $distrito       = trim($_POST['distrito'] ?? 'Chiclayo');
        $direccionTxt   = trim($_POST['direccion'] ?? '');
        $referencia     = trim($_POST['referencia'] ?? '');
        $predeterminada = isset($_POST['predeterminada']) ? 1 : 0;

        if (!$etiqueta || !$direccionTxt) {
            $_SESSION['perfil_error'] = "Completa la etiqueta y la dirección.";
            header("Location: " . BASE_URL . "/views/perfil.php");
            exit;
        }

        if ($predeterminada) {
            $db->prepare("
                UPDATE direccion
                SET predeterminada = 0
                WHERE id_usuario = ?
            ")->execute([$_SESSION['usuario_id']]);
        }

        $stmt = $db->prepare("
            INSERT INTO direccion
            (id_usuario, etiqueta, departamento, provincia, distrito, direccion, referencia, predeterminada)
            VALUES
            (?, ?, 'Lambayeque', 'Chiclayo', ?, ?, ?, ?)
        ");

        $stmt->execute([
            $_SESSION['usuario_id'],
            $etiqueta,
            $distrito,
            $direccionTxt,
            $referencia ?: null,
            $predeterminada
        ]);

        $_SESSION['perfil_ok'] = "Dirección guardada correctamente.";
        header("Location: " . BASE_URL . "/views/perfil.php");
        exit;
    }

    /* =========================================
       EDITAR DIRECCIÓN
    ========================================= */
    if (
        $_SERVER['REQUEST_METHOD'] === 'POST' &&
        isset($_POST['accion']) &&
        $_POST['accion'] === 'editarDireccion'
    ) {

        $idDireccion    = (int) ($_POST['id_direccion'] ?? 0);
        $etiqueta       = trim($_POST['etiqueta'] ?? '');
        $direccionTxt   = trim($_POST['direccion'] ?? '');
        $referencia     = trim($_POST['referencia'] ?? '');
        $predeterminada = isset($_POST['predeterminada']) ? 1 : 0;

        if (!$idDireccion || !$etiqueta || !$direccionTxt) {
            $_SESSION['perfil_error'] = "Completa la etiqueta y la dirección.";
            header("Location: " . BASE_URL . "/views/perfil.php");
            exit;
        }

        if ($predeterminada) {
            $db->prepare("
                UPDATE direccion
                SET predeterminada = 0
                WHERE id_usuario = ?
            ")->execute([$_SESSION['usuario_id']]);
        }

        $stmt = $db->prepare("
            UPDATE direccion
            SET etiqueta = ?, direccion = ?, referencia = ?, predeterminada = ?
            WHERE id_direccion = ? AND id_usuario = ?
        ");

        $stmt->execute([
            $etiqueta,
            $direccionTxt,
            $referencia ?: null,
            $predeterminada,
            $idDireccion,
            $_SESSION['usuario_id']
        ]);

        $_SESSION['perfil_ok'] = "Dirección actualizada correctamente.";
        header("Location: " . BASE_URL . "/views/perfil.php");
        exit;
    }

    /* =========================================
       ELIMINAR DIRECCIÓN
    ========================================= */
    if (
        $_SERVER['REQUEST_METHOD'] === 'POST' &&
        isset($_POST['accion']) &&
        $_POST['accion'] === 'eliminarDireccion'
    ) {

        $idDireccion = (int) ($_POST['id_direccion'] ?? 0);

        $stmt = $db->prepare("
            DELETE FROM direccion
            WHERE id_direccion = ? AND id_usuario = ?
        ");

        $stmt->execute([
            $idDireccion,
            $_SESSION['usuario_id']
        ]);

        $_SESSION['perfil_ok'] = "Dirección eliminada.";
        header("Location: " . BASE_URL . "/views/perfil.php");
        exit;
    }

    /* =========================================
       CARGAR DATOS
    ========================================= */
    $stmt = $db->prepare("
        SELECT *
        FROM usuario
        WHERE id_usuario = ?
    ");

    $stmt->execute([$_SESSION['usuario_id']]);
    $usuario = $stmt->fetch(\PDO::FETCH_ASSOC);

    $stmtRep = $db->prepare("SELECT placa_vehiculo FROM repartidor WHERE id_usuario = ?");
    $stmtRep->execute([$_SESSION['usuario_id']]);
    $repData = $stmtRep->fetch(\PDO::FETCH_ASSOC);
    $placaVehiculo = $repData ? $repData['placa_vehiculo'] : null;

    $stmtDir = $db->prepare("
        SELECT *
        FROM direccion
        WHERE id_usuario = ?
        ORDER BY predeterminada DESC, id_direccion DESC
    ");

    $stmtDir->execute([$_SESSION['usuario_id']]);
    $direcciones = $stmtDir->fetchAll(\PDO::FETCH_ASSOC);

    if (!$usuario) {
        session_destroy();
        header("Location: " . BASE_URL . "/views/inicio.php");
        exit;
    }

} catch (\Exception $e) {

    $_SESSION['perfil_error'] = "Error interno: " . $e->getMessage();

    header("Location: " . BASE_URL . "/views/perfil.php");
    exit;
}
?>