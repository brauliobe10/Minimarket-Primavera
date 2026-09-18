<?php

namespace Config;

/**
 * Clase de seguridad centralizada del sistema.
 * Cubre: Sesiones seguras, CSRF, XSS, Cookies, Cabeceras HTTP de seguridad.
 */
class Security {

    // ─── Constantes de configuración ───────────────────────────────
    private const SESSION_LIFETIME    = 1200;   // 20 minutos de inactividad
    private const SESSION_REGENERATE  = 300;    // Regenerar ID cada 5 minutos
    private const COOKIE_SAMESITE     = 'Lax';

    // ─── Gestión de sesión segura ───────────────────────────────────

    /**
     * Inicia la sesión PHP de forma segura si no está iniciada.
     * Configura HttpOnly, SameSite y protección contra fijación de sesión.
     */
    public static function initSession(): void {
        if (session_status() !== PHP_SESSION_NONE) {
            return;
        }

        // Configuración de cookies de sesión seguras
        ini_set('session.cookie_httponly',    '1');
        ini_set('session.use_only_cookies',   '1');
        ini_set('session.use_strict_mode',    '1');
        ini_set('session.cookie_samesite',    self::COOKIE_SAMESITE);
        ini_set('session.gc_maxlifetime',     (string)self::SESSION_LIFETIME);

        // Solo marcar Secure si hay HTTPS
        $isHttps = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')
                || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
        ini_set('session.cookie_secure', $isHttps ? '1' : '0');

        session_start();

        // Expiración por inactividad
        if (isset($_SESSION['_last_activity'])) {
            if ((time() - $_SESSION['_last_activity']) > self::SESSION_LIFETIME) {
                self::destroySession();
                session_start();
            }
        }
        $_SESSION['_last_activity'] = time();

        // Regenerar ID periódicamente (contra session fixation)
        if (!isset($_SESSION['_created'])) {
            $_SESSION['_created'] = time();
        } elseif ((time() - $_SESSION['_created']) > self::SESSION_REGENERATE) {
            session_regenerate_id(true);
            $_SESSION['_created'] = time();
        }
    }

    /**
     * Destruye completamente la sesión actual.
     */
    public static function destroySession(): void {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(), '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }
        session_destroy();
    }

    // ─── Protección CSRF ────────────────────────────────────────────

    /**
     * Genera y devuelve un Token anti-CSRF almacenado en la sesión.
     */
    public static function generarCSRFToken(): string {
        self::initSession();
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Valida un Token CSRF recibido contra el token de sesión.
     */
    public static function validarCSRFToken(?string $token): bool {
        self::initSession();
        if (empty($_SESSION['csrf_token']) || empty($token)) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Rota el token CSRF (llamar después de validarlo para one-time tokens).
     */
    public static function rotarCSRFToken(): void {
        self::initSession();
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    // ─── Protección XSS ─────────────────────────────────────────────

    /**
     * Escapa cadenas para salida HTML segura (previene XSS).
     */
    public static function escapeHtml(?string $string): string {
        if ($string === null) return '';
        return htmlspecialchars($string, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /**
     * Escapa cadenas para uso seguro dentro de atributos HTML.
     */
    public static function escapeAttr(?string $string): string {
        return self::escapeHtml($string);
    }

    /**
     * Sanitiza entradas de texto: elimina etiquetas HTML y espacios extras.
     * NUNCA reemplaza a prepared statements; solo limpia datos antes de uso.
     *
     * @param mixed $data
     * @return mixed
     */
    public static function sanitizeInput($data) {
        if (is_array($data)) {
            return array_map([self::class, 'sanitizeInput'], $data);
        }
        if (is_string($data)) {
            return trim(strip_tags($data));
        }
        return $data;
    }

    /**
     * Sanitiza un entero recibido por GET/POST.
     */
    public static function sanitizeInt($value, int $default = 0): int {
        $v = filter_var($value, FILTER_VALIDATE_INT);
        return ($v !== false) ? (int)$v : $default;
    }

    /**
     * Sanitiza un flotante recibido por GET/POST.
     */
    public static function sanitizeFloat($value, float $default = 0.0): float {
        $v = filter_var($value, FILTER_VALIDATE_FLOAT);
        return ($v !== false) ? (float)$v : $default;
    }

    // ─── Cabeceras HTTP de seguridad ────────────────────────────────

    /**
     * Envía las cabeceras HTTP de seguridad recomendadas por OWASP.
     * Llamar ANTES de cualquier salida HTML.
     */
    public static function setSecurityHeaders(): void {
        if (headers_sent()) return;

        header('X-Frame-Options: SAMEORIGIN');
        header('X-Content-Type-Options: nosniff');
        header('X-XSS-Protection: 1; mode=block');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        header('Permissions-Policy: geolocation=(), camera=(), microphone=()');

        // CSP básico que permite scripts propios y CDNs ya utilizados en el proyecto
        $csp = implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://code.jquery.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://cdn.tailwindcss.com https://cdn.googlesyndication.com https://www.gstatic.com https://apis.google.com",
            "style-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com https://fonts.googleapis.com",
            "font-src 'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com",
            "img-src 'self' data: https:",
            "connect-src 'self' https://*.googleapis.com https://*.firebaseapp.com",
            "frame-src 'self' https://*.firebaseapp.com https://www.google.com",
            "frame-ancestors 'self'",
        ]);
        header("Content-Security-Policy: $csp");
    }

    // ─── Cookies seguras ────────────────────────────────────────────

    /**
     * Establece una Cookie segura en el cliente.
     */
    public static function setCookie(string $nombre, string $valor, int $dias = 30, bool $httpOnly = true): void {
        $expiracion = time() + ($dias * 24 * 60 * 60);
        $isHttps    = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';

        setcookie($nombre, $valor, [
            'expires'  => $expiracion,
            'path'     => '/',
            'domain'   => '',
            'secure'   => $isHttps,
            'httponly' => $httpOnly,
            'samesite' => self::COOKIE_SAMESITE,
        ]);
    }

    /**
     * Obtiene el valor de una cookie sanitizada.
     */
    public static function getCookie(string $nombre): ?string {
        if (isset($_COOKIE[$nombre])) {
            return self::sanitizeInput($_COOKIE[$nombre]);
        }
        return null;
    }

    /**
     * Elimina una cookie de forma segura.
     */
    public static function deleteCookie(string $nombre): void {
        if (isset($_COOKIE[$nombre])) {
            setcookie($nombre, '', [
                'expires'  => time() - 3600,
                'path'     => '/',
                'httponly' => true,
                'samesite' => self::COOKIE_SAMESITE,
            ]);
            unset($_COOKIE[$nombre]);
        }
    }

    // ─── Tokens de recuperación / sesión persistente ────────────────

    /**
     * Genera un token aleatorio único para cookies "Recordarme" u otras.
     */
    public static function generarRememberToken(): string {
        return bin2hex(random_bytes(32));
    }

    // ─── Validaciones de datos ──────────────────────────────────────

    /**
     * Valida un email con el filtro nativo de PHP.
     */
    public static function validarEmail(string $email): bool {
        return (bool) filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    /**
     * Valida un DNI peruano (exactamente 8 dígitos numéricos).
     */
    public static function validarDNI(string $dni): bool {
        return (bool) preg_match('/^\d{8}$/', $dni);
    }

    /**
     * Valida una contraseña: mínimo 6 caracteres.
     */
    public static function validarPassword(string $password): bool {
        return mb_strlen($password) >= 6;
    }

    /**
     * Valida un número de teléfono peruano (9 dígitos, empieza con 9).
     */
    public static function validarTelefono(string $tel): bool {
        return (bool) preg_match('/^9\d{8}$/', $tel);
    }
}
