<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$registroError = $_SESSION['registroError'] ?? "";
unset($_SESSION['registroError']);
?>

<div class="auth-overlay" id="registroOverlay">
    <div class="auth-card auth-card--wide">

        <button type="button"
                class="auth-card-close"
                onclick="cerrarModales()">
            ✕
        </button>

        <a href="<?= BASE_URL ?>/views/inicio.php" class="auth-logo">
            <svg xmlns="http://www.w3.org/2000/svg"
                 width="26"
                 height="26"
                 viewBox="0 0 24 24"
                 fill="none"
                 stroke="currentColor"
                 stroke-width="2"
                 stroke-linecap="round"
                 stroke-linejoin="round">
                <circle cx="9" cy="21" r="1"></circle>
                <circle cx="20" cy="21" r="1"></circle>
                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"></path>
            </svg>
            Market Primavera
        </a>

        <h2>Crear cuenta</h2>

        <?php if (!empty($registroError)): ?>
            <div class="auth-error">
                <i class="fa fa-exclamation-circle"></i>
                <?= htmlspecialchars($registroError) ?>
            </div>
        <?php endif; ?>

        <form method="POST"
              action="<?= BASE_URL ?>/controllers/AuthController.php"
              novalidate>

            <input type="hidden" name="accion" value="registro">
            <input type="hidden" name="csrf_token" value="<?= \Config\Security::generarCSRFToken() ?>">

           <div class="auth-group dni-group">
    <label>DNI <span class="required">*</span></label>

    <div class="dni-box">
        <input type="text"
               name="dni"
               id="dniInput"
               maxlength="8"
               inputmode="numeric"
               oninput="this.value=this.value.replace(/\D/g,'').slice(0,8)"
               placeholder="Ingresa tu DNI">

        <button type="button"
                id="btnBuscarDni"
                class="btn-dni"
                onclick="buscarDNI()">
            Buscar
        </button>
        <button type="button"
                id="btnLimpiarDni"
                class="btn-dni"
                style="display:none; background-color: #6c757d;"
                onclick="limpiarDNI()">
            Editar
        </button>
    </div>

    <small id="dniEstado"></small>
</div>
            <!-- NOMBRES / APELLIDOS -->
            <div class="form-row-2">

                <div class="auth-group">
                    <label>Nombres <span class="required">*</span></label>
                    <input type="text"
                           name="nombres"
                           id="nombresRegistro"
                           placeholder="Juan"
                           readonly
                           required>
                </div>

                <div class="auth-group">
                    <label>Apellidos <span class="required">*</span></label>
                    <input type="text"
                           name="apellidos"
                           id="apellidosRegistro"
                           placeholder="Pérez"
                           readonly
                           required>
                </div>

            </div>

            <!-- CORREO -->
            <div class="auth-group">
                <label>Correo electrónico <span class="required">*</span></label>
                <input type="email"
                       name="correo"
                       id="fCorreo"
                       placeholder="ejemplo@correo.com"
                       required>
            </div>

            <!-- TELÉFONO / DIRECCIÓN -->
            <div class="form-row-2">

                <div class="auth-group">
                    <label>Teléfono</label>
                    <input type="tel"
                           name="telefono"
                           id="fTelefono"
                           maxlength="9"
                           inputmode="numeric"
                           oninput="this.value=this.value.replace(/\D/g,'').slice(0,9)"
                           placeholder="987654321">
                </div>

                <div class="auth-group">
                    <label>Distrito</label>
                    <select name="distrito" id="fDistrito" style="width:100%;padding:10px;border:1px solid #ccc;border-radius:4px;font-family:inherit;">
                        <option value="Chiclayo">Chiclayo</option>
                        <option value="José Leonardo Ortiz">José Leonardo Ortiz</option>
                        <option value="La Victoria">La Victoria</option>
                        <option value="Pimentel">Pimentel</option>
                    </select>
                </div>

                <div class="auth-group">
                    <label>Dirección</label>
                    <input type="text"
                           name="direccion"
                           id="fDireccion"
                           placeholder="Calle 123, Apt 4B">
                </div>

            </div>

            <!-- PASSWORD -->
            <div class="auth-group">
                <label>Contraseña <span class="required">*</span></label>

                <div class="auth-pw-wrap">
                    <input type="password"
                           name="password"
                           id="fPassword"
                           placeholder="Mínimo 6 caracteres"
                           required
                           oninput="evaluarFuerza(this.value)">

                    <button type="button"
                            class="auth-toggle-pw"
                            onclick="togglePw('fPassword','eye1')">
                        <i class="fa fa-eye" id="eye1"></i>
                    </button>
                </div>
            </div>

            <!-- CONFIRM PASSWORD -->
            <div class="auth-group">
                <label>Confirmar contraseña <span class="required">*</span></label>

                <div class="auth-pw-wrap">
                    <input type="password"
                           name="confirm"
                           id="fConfirm"
                           placeholder="Repite tu contraseña"
                           required
                           oninput="validarCoincidencia()">

                    <button type="button"
                            class="auth-toggle-pw"
                            onclick="togglePw('fConfirm','eye2')">
                        <i class="fa fa-eye" id="eye2"></i>
                    </button>
                </div>
            </div>

            <!-- BOTÓN CREAR -->
            <button type="submit" class="btn-auth-primary">
                <i class="fa fa-user-plus" style="margin-right:.4rem;"></i>
                Crear cuenta
            </button>

        </form>

        <!-- GOOGLE -->
        <div class="auth-divider">o</div>

        <button type="button"
                class="btn-google"
                onclick="loginGoogle()">

            <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg"
                 alt="Google">

            <span>Continuar con Google</span>

        </button>

        <!-- INVITADO -->
        <button type="button"
                class="btn-auth-guest"
                onclick="cerrarModales()">
            Continuar como invitado
        </button>

        <div class="auth-footer">
            ¿Ya tienes cuenta?
            <a href="#"
               onclick="cambiarModal('login'); return false;">
                Inicia sesión
            </a>
        </div>

    </div>
</div>
<div class="auth-overlay" id="completarGoogleOverlay" style="cursor:default;">
    <div class="auth-card auth-card--wide" onclick="event.stopPropagation();">

        <a href="<?= BASE_URL ?>/views/inicio.php" class="auth-logo" onclick="event.preventDefault();">
            <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24"
                 fill="none" stroke="currentColor" stroke-width="2"
                 stroke-linecap="round" stroke-linejoin="round">
                <circle cx="9" cy="21" r="1"></circle>
                <circle cx="20" cy="21" r="1"></circle>
                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"></path>
            </svg>
            Market Primavera
        </a>

        <h2>Completa tus datos</h2>
        <p style="text-align:center;color:#666;font-size:.85rem;margin-bottom:1rem;">
            Para finalizar tu registro debes completar todos los campos obligatorios.
        </p>

        <form method="POST"
              action="<?= BASE_URL ?>/controllers/AuthController.php">

            <input type="hidden" name="accion" value="completarGoogle">
            <input type="hidden" name="csrf_token" value="<?= \Config\Security::generarCSRFToken() ?>">

            <!-- DNI -->
            <div class="auth-group dni-group">
                <label>DNI <span class="required">*</span></label>

                <div class="dni-box">
                    <input type="text"
                           name="dni"
                           id="dniGoogle"
                           maxlength="8"
                           inputmode="numeric"
                           required
                           oninput="this.value=this.value.replace(/\D/g,'').slice(0,8)"
                           placeholder="Ingresa tu DNI">

                    <button type="button"
                            class="btn-dni"
                            onclick="buscarDNIGoogle()">
                        Buscar
                    </button>
                </div>

                <small id="dniEstadoGoogle"></small>
            </div>

            <!-- Nombres -->
            <div class="auth-group">
                <label>Nombres <span class="required">*</span></label>
                <input type="text"
                       name="nombres"
                       id="nombresGoogle"
                       required
                       readonly>
            </div>

            <!-- Apellidos -->
            <div class="auth-group">
                <label>Apellidos <span class="required">*</span></label>
                <input type="text"
                       name="apellidos"
                       id="apellidosGoogle"
                       required
                       readonly>
            </div>

            <!-- Teléfono -->
            <div class="auth-group">
                <label>Teléfono <span class="required">*</span></label>
                <input type="text"
                       name="telefono"
                       id="telefonoGoogle"
                       maxlength="9"
                       inputmode="numeric"
                       required
                       oninput="this.value=this.value.replace(/\D/g,'').slice(0,9)">
            </div>

            <!-- Dirección -->
            <div class="auth-group">
                <label>Dirección <span class="required">*</span></label>
                <input type="text"
                       name="direccion"
                       id="direccionGoogle"
                       required>
            </div>

            <div id="googleFormError" style="color:#e30613;font-size:.85rem;text-align:center;margin-bottom:10px;display:none;"></div>

            <button type="submit" class="btn-auth-primary" onclick="return validarFormGoogle()">
                Guardar datos
            </button>

            <a href="<?= BASE_URL ?>/controllers/AuthController.php?logout=1" style="display: block; text-align: center; margin-top: 15px; color: #666; text-decoration: none; font-size: 0.9rem;">
                Salir
            </a>

        </form>

    </div>
</div>

