<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../config/Security.php';

use Config\Database;
use Config\Security;

Security::initSession();
Security::setSecurityHeaders();

/* ════════════════════════════════════════════════════
   ACCION PARA GUARDAR PREFERENCIAS EN COOKIES
════════════════════════════════════════════════════ */
if (isset($_GET['accion']) && $_GET['accion'] === 'guardar_preferencia') {
    header('Content-Type: application/json');
    $tema = Security::sanitizeInput($_GET['tema'] ?? 'claro');
    $vista = Security::sanitizeInput($_GET['vista'] ?? 'rejilla');
    Security::setCookie('user_prefs', json_encode(['tema' => $tema, 'vista' => $vista]), 30);
    echo json_encode(['ok' => true, 'mensaje' => 'Preferencia guardada en cookie']);
    exit();
}

/* ════════════════════════════════════════════════════
   LIMPIAR LOCKOUT (llamado por AJAX desde auth.js)
════════════════════════════════════════════════════ */
if (isset($_GET['accion']) && $_GET['accion'] === 'limpiar_lockout') {
    header('Content-Type: application/json');
    unset($_SESSION['failed_login_attempts']);
    unset($_SESSION['lockout_time']);
    echo json_encode(['ok' => true]);
    exit();
}

/* ════════════════════════════════════════════════════
   CERRAR SESIÓN (Limpia también la cookie remember_me)
════════════════════════════════════════════════════ */
if (isset($_GET['logout'])) {
    Security::deleteCookie('remember_me');
    Security::destroySession();
    header('Location: ' . BASE_URL . '/views/inicio.php');
    exit();
}

/* ════════════════════════════════════════════════════
   ACCIONES POST
════════════════════════════════════════════════════ */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {
    $accion = $_POST['accion'];

    // Validar CSRF para solicitudes normales de formulario
    if (in_array($accion, ['login', 'registro', 'completarGoogle'])) {
        $csrfToken = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
        if (!Security::validarCSRFToken($csrfToken)) {
            $_SESSION['loginError'] = 'Error de seguridad: Token CSRF inválido o expirado.';
            header('Location: ' . BASE_URL . '/views/inicio.php');
            exit();
        }
    }

    /* ──────────────────────────────────────────
       LOGIN NORMAL (Máximo 3 Intentos - 15 Segundos)
    ────────────────────────────────────────── */
    if ($accion === 'login') {
        $isAjax = !empty($_POST['ajax']);

        $responder = function($url) use ($isAjax) {
            if ($isAjax) {
                header('Content-Type: application/json; charset=utf-8');
                $err = $_SESSION['loginError'] ?? '';
                if ($err) {
                    echo json_encode(['ok' => false, 'mensaje' => $err]);
                    unset($_SESSION['loginError']);
                } else {
                    echo json_encode(['ok' => true, 'redirect' => $url]);
                }
                exit();
            } else {
                header('Location: ' . $url);
                exit();
            }
        };

        // Verificar si está bloqueado
        if (isset($_SESSION['lockout_time']) && time() < $_SESSION['lockout_time']) {
            $restante = $_SESSION['lockout_time'] - time();
            $_SESSION['loginError'] = "Demasiados intentos. Intente de nuevo en {$restante} segundos.";
            $responder(BASE_URL . '/views/inicio.php');
        }

        $correo   = Security::sanitizeInput($_POST['correo'] ?? '');
        $password = $_POST['password'] ?? '';
        $recordarme = !empty($_POST['recordarme']);

        if ($correo === '' || $password === '') {
            $_SESSION['loginError'] = 'Completa todos los campos.';
            $responder(BASE_URL . '/views/inicio.php');
        }

        $db   = new Database();
        $conn = $db->conectar();

        $stmt = $conn->prepare("
            SELECT u.id_usuario,
                   u.nombres,
                   u.password_hash,
                   GROUP_CONCAT(r.nombre_rol) AS roles
            FROM usuario u
            LEFT JOIN usuario_rol ur ON u.id_usuario = ur.id_usuario
            LEFT JOIN rol r ON ur.id_rol = r.id_rol
            WHERE u.correo = :correo
              AND u.estado = 1
            GROUP BY u.id_usuario
        ");
        $stmt->execute(['correo' => $correo]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario && password_verify($password, $usuario['password_hash'])) {
            // Éxito → limpiar lockout y regenerar ID de sesión (previene session fixation)
            unset($_SESSION['failed_login_attempts']);
            unset($_SESSION['lockout_time']);
            session_regenerate_id(true);

            $_SESSION['usuario_id']     = $usuario['id_usuario'];
            $_SESSION['usuario_nombre'] = $usuario['nombres'];
            $_SESSION['es_admin']       = str_contains($usuario['roles'] ?? '', 'Administrador');
            $_SESSION['es_repartidor']  = str_contains($usuario['roles'] ?? '', 'Repartidor');
            $_SESSION['es_cliente']     = str_contains($usuario['roles'] ?? '', 'Cliente');
            $_SESSION['es_almacenero']  = str_contains($usuario['roles'] ?? '', 'Almacenero');
            $_SESSION['es_despachador'] = str_contains($usuario['roles'] ?? '', 'Despachador');
            $_SESSION['es_cajero']      = str_contains($usuario['roles'] ?? '', 'Cajero');
            $_SESSION['es_soporte']     = str_contains($usuario['roles'] ?? '', 'Soporte');
            $_SESSION['es_trabajador']  = ($_SESSION['es_admin'] || $_SESSION['es_repartidor'] || $_SESSION['es_almacenero'] || $_SESSION['es_despachador'] || $_SESSION['es_cajero']);
            $_SESSION['login_success']  = true;
            $_SESSION['login_method']   = 'manual';
            $_SESSION['_created']       = time();

            // Si marcó "Recordarme", guardar cookie de inicio de sesión persistente (30 días)
            if ($recordarme) {
                $tokenRemember = Security::generarRememberToken();
                Security::setCookie('remember_me', base64_encode($usuario['id_usuario'] . ':' . $tokenRemember), 30);
            }

            if ($_SESSION['es_admin'] || $_SESSION['es_soporte']) {
                $responder(BASE_URL . '/views/admin/index.php');
            } elseif ($_SESSION['es_despachador'] || $_SESSION['es_almacenero'] || $_SESSION['es_cajero']) {
                $responder(BASE_URL . '/views/panel_trabajador.php');
            } elseif ($_SESSION['es_repartidor']) {
                $responder(BASE_URL . '/views/panel_trabajador.php');
            } else {
                $redirect = $_POST['redirect'] ?? '';
                if ($redirect === 'checkout') {
                    $responder(BASE_URL . '/views/checkout.php');
                } else {
                    $responder(BASE_URL . '/views/inicio.php');
                }
            }

        } else {
            // Fallo → incrementar intentos (Máximo 3 intentos)
            if (!isset($_SESSION['failed_login_attempts'])) {
                $_SESSION['failed_login_attempts'] = 0;
            }
            $_SESSION['failed_login_attempts']++;

            if ($_SESSION['failed_login_attempts'] >= 3) {
                $_SESSION['lockout_time'] = time() + 15;
                $_SESSION['failed_login_attempts'] = 0;
                $_SESSION['loginError'] = 'Demasiados intentos fallidos. Bloqueado por 15 segundos.';
            } else {
                $intentosRestantes = 3 - $_SESSION['failed_login_attempts'];
                $_SESSION['loginError'] = "Correo o contraseña incorrectos. Quedan {$intentosRestantes} intento(s).";
            }

            $responder(BASE_URL . '/views/inicio.php');
        }
    }

    /* ──────────────────────────────────────────
       REGISTRO NORMAL
    ────────────────────────────────────────── */
    if ($accion === 'registro') {
        $dni       = trim($_POST['dni'] ?? '');
        $nombres   = trim($_POST['nombres'] ?? '');
        $apellidos = trim($_POST['apellidos'] ?? '');
        $correo    = trim($_POST['correo'] ?? '');
        $telefono  = trim($_POST['telefono'] ?? '');
        $direccion = trim($_POST['direccion'] ?? '');
        $password  = $_POST['password'] ?? '';
        $confirm   = $_POST['confirm'] ?? '';

        if (!$dni || !preg_match('/^\d{8}$/', $dni)) {
            $_SESSION['registroError'] = 'DNI inválido.';
            header('Location: ' . BASE_URL . '/views/inicio.php');
            exit();
        }

        if (!$nombres || !$apellidos || !$correo || !$password || !$confirm) {
            $_SESSION['registroError'] = 'Completa todos los campos obligatorios.';
            header('Location: ' . BASE_URL . '/views/inicio.php');
            exit();
        }

        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['registroError'] = 'Correo inválido.';
            header('Location: ' . BASE_URL . '/views/inicio.php');
            exit();
        }

        if (strlen($password) < 6) {
            $_SESSION['registroError'] = 'La contraseña debe tener mínimo 6 caracteres.';
            header('Location: ' . BASE_URL . '/views/inicio.php');
            exit();
        }

        if ($password !== $confirm) {
            $_SESSION['registroError'] = 'Las contraseñas no coinciden.';
            header('Location: ' . BASE_URL . '/views/inicio.php');
            exit();
        }

        $db   = new Database();
        $conn = $db->conectar();

        $check = $conn->prepare("SELECT id_usuario FROM usuario WHERE correo = :correo OR dni = :dni");
        $check->execute(['correo' => $correo, 'dni' => $dni]);

        if ($check->fetch()) {
            $_SESSION['registroError'] = 'Ese correo o DNI ya existe.';
            header('Location: ' . BASE_URL . '/views/inicio.php');
            exit();
        }

        $hash = password_hash($password, PASSWORD_BCRYPT);

        $ins = $conn->prepare("
            INSERT INTO usuario
            (dni, nombres, apellidos, correo, password_hash, telefono, direccion, estado)
            VALUES
            (:dni, :nombres, :apellidos, :correo, :hash, :telefono, :direccion, 1)
        ");
        $ins->execute([
            'dni'       => $dni,
            'nombres'   => $nombres,
            'apellidos' => $apellidos,
            'correo'    => $correo,
            'hash'      => $hash,
            'telefono'  => $telefono ?: null,
            'direccion' => $direccion ?: null,
        ]);

        $nuevoId = $conn->lastInsertId();

        $conn->prepare("INSERT INTO usuario_rol (id_usuario, id_rol) VALUES (:uid, 2)")
             ->execute(['uid' => $nuevoId]);

        if (!empty($direccion)) {
            $distrito = trim($_POST['distrito'] ?? 'Chiclayo');
            $conn->prepare("
                INSERT INTO direccion
                (id_usuario, etiqueta, departamento, provincia, distrito, direccion, referencia, predeterminada)
                VALUES (:uid, 'Principal', 'Lambayeque', 'Chiclayo', :distrito, :direccion, NULL, 1)
            ")->execute(['uid' => $nuevoId, 'distrito' => $distrito, 'direccion' => $direccion]);
        }

        $_SESSION['usuario_id']    = $nuevoId;
        $_SESSION['usuario_nombre'] = $nombres;
        $_SESSION['es_admin']       = false;
        $_SESSION['es_repartidor']  = false;
        $_SESSION['es_cliente']     = true;
        $_SESSION['login_method']   = 'manual';

        header('Location: ' . BASE_URL . '/views/inicio.php');
        exit();
    }

    /* ──────────────────────────────────────────
       COMPLETAR DATOS GOOGLE
    ────────────────────────────────────────── */
    if ($accion === 'completarGoogle') {
        $dni       = trim($_POST['dni'] ?? '');
        $nombres   = trim($_POST['nombres'] ?? '');
        $apellidos = trim($_POST['apellidos'] ?? '');
        $telefono  = trim($_POST['telefono'] ?? '');
        $direccion = trim($_POST['direccion'] ?? '');

        if (!$dni || !$nombres || !$apellidos || !preg_match('/^\d{8}$/', $dni)) {
            $_SESSION['registroError'] = 'DNI, nombres y apellidos son obligatorios.';
            header('Location: ' . BASE_URL . '/views/inicio.php');
            exit();
        }

        if (!$telefono || !preg_match('/^\d{9}$/', $telefono)) {
            $_SESSION['registroError'] = 'Teléfono obligatorio (9 dígitos).';
            header('Location: ' . BASE_URL . '/views/inicio.php');
            exit();
        }

        if (!$direccion) {
            $_SESSION['registroError'] = 'Dirección obligatoria.';
            header('Location: ' . BASE_URL . '/views/inicio.php');
            exit();
        }

        $db   = new Database();
        $conn = $db->conectar();

        $checkQuery = "SELECT id_usuario FROM usuario WHERE dni = :dni";
        $checkParams = ['dni' => $dni];
        
        if (isset($_SESSION['usuario_id'])) {
            $checkQuery .= " AND id_usuario != :uid";
            $checkParams['uid'] = $_SESSION['usuario_id'];
        }

        $check = $conn->prepare($checkQuery);
        $check->execute($checkParams);

        if ($check->fetch()) {
            $_SESSION['registroError'] = 'Ese DNI ya está registrado.';
            header('Location: ' . BASE_URL . '/views/inicio.php');
            exit();
        }

        if (isset($_SESSION['google_temp_user'])) {
            $temp = $_SESSION['google_temp_user'];
            
            $ins = $conn->prepare("
                INSERT INTO usuario
                (dni, nombres, apellidos, correo, password_hash, google_uid, telefono, direccion, estado)
                VALUES
                (:dni, :nombres, :apellidos, :correo, NULL, :uid, :telefono, :direccion, 1)
            ");
            $ins->execute([
                'dni'       => $dni,
                'nombres'   => $nombres,
                'apellidos' => $apellidos,
                'correo'    => $temp['correo'],
                'uid'       => $temp['uid'],
                'telefono'  => $telefono ?: null,
                'direccion' => $direccion ?: null,
            ]);

            $nuevoId = $conn->lastInsertId();

            $conn->prepare("INSERT INTO usuario_rol (id_usuario, id_rol) VALUES (:uid, 2)")
                 ->execute(['uid' => $nuevoId]);

            $_SESSION['usuario_id']    = $nuevoId;
            $_SESSION['es_admin']       = false;
            $_SESSION['es_repartidor']  = false;
            $_SESSION['es_cliente']     = true;
            $_SESSION['login_method']   = 'google';
            
            unset($_SESSION['google_temp_user']);
            
        } elseif (isset($_SESSION['usuario_id'])) {
            $conn->prepare("
                UPDATE usuario
                SET dni = :dni, nombres = :nombres, apellidos = :apellidos,
                    telefono = :telefono, direccion = :direccion
                WHERE id_usuario = :uid
            ")->execute([
                'dni'       => $dni,
                'nombres'   => $nombres,
                'apellidos' => $apellidos,
                'telefono'  => $telefono ?: null,
                'direccion' => $direccion ?: null,
                'uid'       => $_SESSION['usuario_id'],
            ]);
        } else {
            $_SESSION['registroError'] = 'Sesión de Google expirada o inválida.';
            header('Location: ' . BASE_URL . '/views/inicio.php');
            exit();
        }

        if (!empty($direccion)) {
            $checkDir = $conn->prepare("SELECT id_direccion FROM direccion WHERE id_usuario = :uid LIMIT 1");
            $checkDir->execute(['uid' => $_SESSION['usuario_id']]);
            if (!$checkDir->fetch()) {
                $distrito = trim($_POST['distrito'] ?? 'Chiclayo');
                $conn->prepare("
                    INSERT INTO direccion
                    (id_usuario, etiqueta, departamento, provincia, distrito, direccion, referencia, predeterminada)
                    VALUES (:uid, 'Principal', 'Lambayeque', 'Chiclayo', :distrito, :direccion, NULL, 1)
                ")->execute(['uid' => $_SESSION['usuario_id'], 'distrito' => $distrito, 'direccion' => $direccion]);
            }
        }

        $_SESSION['usuario_nombre'] = $nombres;
        $_SESSION['login_method']   = 'google';
        $_SESSION['login_success']  = true;
        unset($_SESSION['google_pendiente']);
        header('Location: ' . BASE_URL . '/views/inicio.php');
        exit();
    }

    /* ──────────────────────────────────────────
       LOGIN GOOGLE (AJAX)
    ────────────────────────────────────────── */
    if ($accion === 'login_google') {
        header('Content-Type: application/json');

        $correo = trim($_POST['correo'] ?? '');
        $nombre = trim($_POST['nombre'] ?? 'Usuario');
        $uid    = trim($_POST['uid'] ?? '');

        if (!$correo || !$uid) {
            echo json_encode(['ok' => false, 'mensaje' => 'Datos incompletos']);
            exit();
        }

        $db   = new Database();
        $conn = $db->conectar();

        $stmt = $conn->prepare("SELECT * FROM usuario WHERE correo = ? OR google_uid = ? LIMIT 1");
        $stmt->execute([$correo, $uid]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario) {
            $_SESSION['usuario_id']    = $usuario['id_usuario'];
            $_SESSION['usuario_nombre'] = $usuario['nombres'] ?? $nombre;
            $_SESSION['es_admin']       = false;
            $_SESSION['es_repartidor']  = false;
            $_SESSION['login_method']   = 'google';

            if (empty($usuario['google_uid'])) {
                $conn->prepare("UPDATE usuario SET google_uid = ? WHERE id_usuario = ?")
                     ->execute([$uid, $usuario['id_usuario']]);
            }

            $necesitaCompletar = empty($usuario['dni']) && empty($usuario['password_hash']);
            if ($necesitaCompletar) {
                $_SESSION['google_pendiente'] = true;
            } else {
                $_SESSION['login_success'] = true;
            }
            echo json_encode(['ok' => true, 'nuevo' => $necesitaCompletar]);
            exit();
        } else {
            $partes    = explode(' ', $nombre, 2);
            $nombres   = $partes[0] ?? 'Usuario';
            $apellidos = $partes[1] ?? '';

            $_SESSION['google_temp_user'] = [
                'nombres'   => $nombres,
                'apellidos' => $apellidos,
                'correo'    => $correo,
                'uid'       => $uid
            ];

            $_SESSION['google_pendiente'] = true;

            echo json_encode(['ok' => true, 'nuevo' => true]);
            exit();
        }
    }

    /* ──────────────────────────────────────────
       RECUPERACIÓN - PASO 1: verificar correo + teléfono
    ────────────────────────────────────────── */
    if ($accion === 'verificar_recuperacion') {
        header('Content-Type: application/json');

        $correo   = trim($_POST['correo'] ?? '');
        $telefono = trim($_POST['telefono'] ?? '');

        if (!$correo || !$telefono) {
            echo json_encode(['ok' => false, 'mensaje' => 'Completa todos los campos.']);
            exit();
        }

        $db   = new Database();
        $conn = $db->conectar();

        $stmt = $conn->prepare("
            SELECT id_usuario FROM usuario
            WHERE correo = :correo AND telefono = :telefono AND estado = 1
            LIMIT 1
        ");
        $stmt->execute(['correo' => $correo, 'telefono' => $telefono]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario) {
            $codigo = rand(100000, 999999);
            $_SESSION['recuperar_codigo']      = $codigo;
            $_SESSION['recuperar_usuario_id']  = $usuario['id_usuario'];
            $_SESSION['recuperar_autorizado']  = false;

            echo json_encode([
                'ok'          => true,
                'mensaje'     => 'Código de verificación generado.',
                'codigo_demo' => $codigo,
            ]);
        } else {
            echo json_encode(['ok' => false, 'mensaje' => 'Los datos no coinciden con ningún usuario registrado.']);
        }
        exit();
    }

    /* ──────────────────────────────────────────
       RECUPERACIÓN - PASO 2: confirmar código
    ────────────────────────────────────────── */
    if ($accion === 'confirmar_codigo') {
        header('Content-Type: application/json');

        $codigo = trim($_POST['codigo'] ?? '');

        if (!$codigo) {
            echo json_encode(['ok' => false, 'mensaje' => 'Ingresa el código.']);
            exit();
        }

        if (isset($_SESSION['recuperar_codigo']) && $_SESSION['recuperar_codigo'] == $codigo) {
            $_SESSION['recuperar_autorizado'] = true;
            echo json_encode(['ok' => true, 'mensaje' => 'Código verificado con éxito.']);
        } else {
            echo json_encode(['ok' => false, 'mensaje' => 'Código incorrecto o expirado.']);
        }
        exit();
    }

    /* ──────────────────────────────────────────
       RECUPERACIÓN - PASO 3: cambiar contraseña
    ────────────────────────────────────────── */
    if ($accion === 'cambiar_password_recuperacion') {
        header('Content-Type: application/json');

        $password = $_POST['password'] ?? '';
        $confirm  = $_POST['confirm']  ?? '';

        if (empty($_SESSION['recuperar_autorizado']) || empty($_SESSION['recuperar_usuario_id'])) {
            echo json_encode(['ok' => false, 'mensaje' => 'Acceso no autorizado o sesión expirada.']);
            exit();
        }

        if (strlen($password) < 6) {
            echo json_encode(['ok' => false, 'mensaje' => 'La contraseña debe tener mínimo 6 caracteres.']);
            exit();
        }

        if ($password !== $confirm) {
            echo json_encode(['ok' => false, 'mensaje' => 'Las contraseñas no coinciden.']);
            exit();
        }

        $db   = new Database();
        $conn = $db->conectar();

        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $conn->prepare("UPDATE usuario SET password_hash = :hash WHERE id_usuario = :id");
        $ok   = $stmt->execute(['hash' => $hash, 'id' => $_SESSION['recuperar_usuario_id']]);

        if ($ok) {
            unset($_SESSION['recuperar_codigo'], $_SESSION['recuperar_usuario_id'], $_SESSION['recuperar_autorizado']);
            echo json_encode(['ok' => true, 'mensaje' => 'Contraseña cambiada exitosamente.']);
        } else {
            echo json_encode(['ok' => false, 'mensaje' => 'Error al guardar la nueva contraseña.']);
        }
        exit();
    }
}

/* ════════════════════════════════════════════════════
   HELPERS (usados en otros archivos con require_once)
════════════════════════════════════════════════════ */
function estaLogueado(): bool {
    if (isset($_SESSION['usuario_id'])) {
        return true;
    }

    // Verificar si existe cookie "remember_me" para auto-login
    $cookie = Security::getCookie('remember_me');
    if ($cookie) {
        $partes = explode(':', base64_decode($cookie));
        if (count($partes) === 2 && is_numeric($partes[0])) {
            $userId = (int)$partes[0];
            try {
                $conn = (new \Config\Database())->conectar();
                $stmt = $conn->prepare("
                    SELECT u.id_usuario, u.nombres, GROUP_CONCAT(r.nombre_rol) AS roles
                    FROM usuario u
                    LEFT JOIN usuario_rol ur ON u.id_usuario = ur.id_usuario
                    LEFT JOIN rol r ON ur.id_rol = r.id_rol
                    WHERE u.id_usuario = ? AND u.estado = 1
                    GROUP BY u.id_usuario
                ");
                $stmt->execute([$userId]);
                $user = $stmt->fetch(\PDO::FETCH_ASSOC);

                if ($user) {
                    $_SESSION['usuario_id']     = $user['id_usuario'];
                    $_SESSION['usuario_nombre'] = $user['nombres'];
                    $_SESSION['es_admin']       = str_contains($user['roles'] ?? '', 'Administrador');
                    $_SESSION['es_repartidor']  = str_contains($user['roles'] ?? '', 'Repartidor');
                    $_SESSION['es_cliente']     = str_contains($user['roles'] ?? '', 'Cliente');
                    $_SESSION['es_soporte']     = str_contains($user['roles'] ?? '', 'Soporte');
                    $_SESSION['login_method']   = 'manual'; // asumido por cookie
                    return true;
                }
            } catch (\Exception $e) {
                // Si falla, se ignora
            }
        }
    }

    return false;
}

function esAdmin(): bool {
    return !empty($_SESSION['es_admin']);
}

function esSoporte(): bool {
    return !empty($_SESSION['es_soporte']);
}

function esRepartidor(): bool {
    return !empty($_SESSION['es_repartidor']);
}

function esAlmacenero(): bool {
    return !empty($_SESSION['es_almacenero']);
}

function esDespachador(): bool {
    return !empty($_SESSION['es_despachador']);
}

function esCajero(): bool {
    return !empty($_SESSION['es_cajero']);
}

function esTrabajador(): bool {
    return !empty($_SESSION['es_trabajador']) || esAdmin() || esRepartidor() || esAlmacenero() || esDespachador() || esCajero();
}

function esCliente(): bool {
    return !empty($_SESSION['es_cliente']);
}

function requireAdmin(): void {
    if (!estaLogueado()) {
        http_response_code(403);
        header('Location: ' . BASE_URL . '/views/inicio.php');
        exit();
    }
    if (!esAdmin()) {
        http_response_code(403);
        header('Location: ' . BASE_URL . '/views/inicio.php');
        exit();
    }
}

function requireSoporteOrAdmin(): void {
    if (!estaLogueado()) {
        http_response_code(403);
        header('Location: ' . BASE_URL . '/views/inicio.php');
        exit();
    }
    if (!esAdmin() && !esSoporte()) {
        http_response_code(403);
        header('Location: ' . BASE_URL . '/views/inicio.php');
        exit();
    }
}

function requireRepartidor(): void {
    if (!estaLogueado()) {
        http_response_code(403);
        header('Location: ' . BASE_URL . '/views/inicio.php');
        exit();
    }
    if (!esRepartidor()) {
        http_response_code(403);
        header('Location: ' . BASE_URL . '/views/inicio.php');
        exit();
    }
}

function requireTrabajador(): void {
    if (!estaLogueado()) {
        http_response_code(403);
        header('Location: ' . BASE_URL . '/views/inicio.php');
        exit();
    }
    if (!esTrabajador()) {
        http_response_code(403);
        header('Location: ' . BASE_URL . '/views/inicio.php');
        exit();
    }
}

function requireClienteOrGuest(): void {
    if (estaLogueado() && !esCliente()) {
        if (esAdmin() || esSoporte()) {
            header('Location: ' . BASE_URL . '/views/admin/index.php');
        } elseif (esTrabajador()) {
            header('Location: ' . BASE_URL . '/views/panel_trabajador.php');
        } else {
            header('Location: ' . BASE_URL . '/controllers/AuthController.php?logout=1');
        }
        exit();
    }
}

function sincronizarRoles(): void {
    if (!isset($_SESSION['usuario_id'])) return;

    try {
        $conn = (new \Config\Database())->conectar();
        $stmt = $conn->prepare("
            SELECT GROUP_CONCAT(r.nombre_rol) AS roles
            FROM usuario_rol ur
            JOIN rol r ON ur.id_rol = r.id_rol
            WHERE ur.id_usuario = ?
        ");
        $stmt->execute([$_SESSION['usuario_id']]);
        $row   = $stmt->fetch(PDO::FETCH_ASSOC);
        $roles = $row['roles'] ?? '';

        $_SESSION['es_admin']       = str_contains($roles, 'Administrador');
        $_SESSION['es_repartidor']  = str_contains($roles, 'Repartidor');
        $_SESSION['es_cliente']     = str_contains($roles, 'Cliente');
        $_SESSION['es_almacenero']  = str_contains($roles, 'Almacenero');
        $_SESSION['es_despachador'] = str_contains($roles, 'Despachador');
        $_SESSION['es_cajero']      = str_contains($roles, 'Cajero');
        $_SESSION['es_soporte']     = str_contains($roles, 'Soporte');
        $_SESSION['es_trabajador']  = ($_SESSION['es_admin'] || $_SESSION['es_repartidor'] || $_SESSION['es_almacenero'] || $_SESSION['es_despachador'] || $_SESSION['es_cajero']);
    } catch (Exception $e) {
        // Silencioso — mantener valores actuales
    }
}