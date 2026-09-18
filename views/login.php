<?php
// Evitar acceso directo a este snippet
if (basename($_SERVER['PHP_SELF']) === 'login.php') {
    header("Location: " . BASE_URL . "/views/inicio.php");
    exit;
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$loginError = $_SESSION['loginError'] ?? "";
unset($_SESSION['loginError']);

$lockoutRestante = 0;
if (isset($_SESSION['lockout_time']) && time() < $_SESSION['lockout_time']) {
    $lockoutRestante = $_SESSION['lockout_time'] - time();
}
?>
<script>
    window.lockoutRestante = <?= (int)$lockoutRestante ?>;
</script>

<div class="auth-overlay" id="authOverlay">
    <div class="auth-card">

        <button type="button" class="auth-card-close" onclick="cerrarModales()">✕</button>

        <a href="<?= BASE_URL ?>/views/inicio.php" class="auth-logo">
            <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24"
                 fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="9" cy="21" r="1"></circle>
                <circle cx="20" cy="21" r="1"></circle>
                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"></path>
            </svg>
            Market Primavera
        </a>

        <h2>Iniciar sesión</h2>

        <div class="auth-error" id="authLoginError" style="<?= empty($loginError) ? 'display: none;' : '' ?>">
            <i class="fa fa-exclamation-circle"></i>
            <span id="authLoginErrorText"><?= htmlspecialchars($loginError) ?></span>
        </div>

        <form id="loginFormModal" onsubmit="submitLoginAjax(event)">
            <input type="hidden" name="accion" value="login">
            <input type="hidden" name="ajax" value="1">
            <input type="hidden" name="redirect" value="<?= htmlspecialchars($_GET['redirect'] ?? '') ?>">
            <input type="hidden" name="csrf_token" value="<?= \Config\Security::generarCSRFToken() ?>">

            <div class="auth-group">
                <label>Correo electrónico</label>
                <input
                    type="email"
                    name="correo"
                    id="authCorreo"
                    placeholder="ejemplo@correo.com"
                    value="<?= htmlspecialchars($_POST['correo'] ?? '') ?>"
                    required>
            </div>

            <div class="auth-group">
                <label>Contraseña</label>

                <div class="auth-pw-wrap">
                    <input
                        type="password"
                        id="authPassword"
                        name="password"
                        placeholder="Tu contraseña"
                        required>

                    <button type="button"
                            class="auth-toggle-pw"
                            onclick="togglePw('authPassword','authEye')">
                        <i class="fa fa-eye" id="authEye"></i>
                    </button>
                </div>
            </div>

            <div class="auth-forgot-pw" style="text-align: right; margin-bottom: 1.2rem; margin-top: -0.4rem; font-size: 0.82rem;">
                <a href="#" onclick="abrirModalRecuperar(); return false;" style="color: #e30613; text-decoration: none; font-weight: 600;">¿Olvidaste tu contraseña?</a>
            </div>

            <button type="submit" class="btn-auth-primary" id="btnAuthLogin">
                Entrar
            </button>
        </form>
<div class="auth-divider">o</div>

<button type="button"
        class="btn-google"
        onclick="loginGoogle()">
        
    <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg"
         alt="Google">

    <span>Continuar con Google</span>

</button>
        <button type="button"
                class="btn-auth-guest"
                onclick="cerrarModales()">
            Continuar como invitado
        </button>

        <div class="auth-footer">
            ¿No tienes cuenta?
            <a href="#" onclick="cambiarModal('registro'); return false;">
                Regístrate
            </a>
        </div>

    </div>
</div>

<!-- Modal Restablecer Contraseña -->
<div class="auth-overlay" id="recuperarOverlay">
    <div class="auth-card">
        <button type="button" class="auth-card-close" onclick="cerrarModales()">✕</button>

        <div class="auth-logo">
            <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24"
                 fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="9" cy="21" r="1"></circle>
                <circle cx="20" cy="21" r="1"></circle>
                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"></path>
            </svg>
            Market Primavera
        </div>

        <h2 id="recuperarTitulo">Restablecer contraseña</h2>

        <!-- Alert general para errores o éxitos -->
        <div id="recuperarMensaje" style="display: none;"></div>

        <!-- PASO 1: Ingreso de correo -->
        <div id="recuperarPaso1">
            <p style="font-size: 0.85rem; color: #666; margin-bottom: 1.2rem; text-align: center;">
                Ingresa tu correo electrónico registrado para enviarte un código de recuperación.
            </p>
            <div class="auth-group">
                <label>Correo electrónico</label>
                <input type="email" id="recUserEmail" placeholder="ejemplo@correo.com" required>
            </div>
            <button type="button" class="btn-auth-primary" onclick="verificarDatosRecuperacion()">
                Enviar código
            </button>
        </div>

        <!-- PASO 2: Ingreso de código -->
        <div id="recuperarPaso2" style="display: none;">
            <p style="font-size: 0.85rem; color: #666; margin-bottom: 1.2rem; text-align: center;">
                Revisa tu bandeja de entrada o la carpeta de SPAM. Hemos enviado un código de seguridad a tu correo.
            </p>
            <div class="auth-group">
                <label>Código de verificación (6 dígitos)</label>
                <input type="text" id="recVerificationCode" placeholder="000000" maxlength="6" style="text-align: center; font-size: 1.2rem; letter-spacing: 4px;" required>
            </div>
            <button type="button" class="btn-auth-primary" onclick="confirmarCodigoRecuperacion()">
                Confirmar código
            </button>
        </div>

        <!-- PASO 3: Nueva contraseña -->
        <div id="recuperarPaso3" style="display: none;">
            <p style="font-size: 0.85rem; color: #666; margin-bottom: 1.2rem; text-align: center;">
                Crea una nueva contraseña segura para tu cuenta.
            </p>
            <div class="auth-group">
                <label>Nueva contraseña</label>
                <input type="password" id="recNewPassword" placeholder="Mínimo 6 caracteres" required>
            </div>
            <div class="auth-group">
                <label>Confirmar nueva contraseña</label>
                <input type="password" id="recConfirmPassword" placeholder="Repite la contraseña" required>
            </div>
            <button type="button" class="btn-auth-primary" onclick="cambiarPasswordRecuperacion()">
                Actualizar contraseña
            </button>
        </div>

        <div class="auth-footer" style="margin-top: 1.4rem; border-top: 1px solid #eee; padding-top: 1rem;">
            <a href="#" onclick="cerrarModales(); abrirModalLogin(); return false;">
                Volver a Iniciar sesión
            </a>
        </div>
    </div>
</div>

<script>
async function submitLoginAjax(e) {
    e.preventDefault();
    const form = e.target;
    const formData = new FormData(form);
    const btn = document.getElementById('btnAuthLogin');
    
    if (btn && btn.disabled) return;
    
    try {
        if (btn) {
            btn.disabled = true;
            btn.style.opacity = '0.7';
        }
        
        const response = await fetch('<?= BASE_URL ?>/controllers/AuthController.php', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();
        
        if (data.ok) {
            window.location.href = data.redirect || '<?= BASE_URL ?>/views/inicio.php';
        } else {
            const errorBox = document.getElementById('authLoginError');
            const errorText = document.getElementById('authLoginErrorText');
            if (errorBox && errorText) {
                errorBox.style.display = 'flex';
                errorText.textContent = data.mensaje || 'Error al iniciar sesión';
            }
            
            // Check for lockout seconds
            const match = data.mensaje ? data.mensaje.match(/(\d+) segundos?/) : null;
            if (match && typeof iniciarCuentaRegresivaLockout === 'function') {
                iniciarCuentaRegresivaLockout(parseInt(match[1]));
            } else {
                if (btn) {
                    btn.disabled = false;
                    btn.style.opacity = '1';
                }
            }
        }
    } catch (error) {
        console.error(error);
        if (btn) {
            btn.disabled = false;
            btn.style.opacity = '1';
        }
    }
}
</script>

