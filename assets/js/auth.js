/* auth.js */

/* ─────────────────────────────────────────────
   ABRIR LOGIN
───────────────────────────────────────────── */
function abrirModalLogin() {
    const login = document.getElementById('authOverlay');

    if (login) {
        login.classList.add('activo');
    }

    const correo = document.getElementById('authCorreo');
    if (correo) correo.focus();
}

/* ─────────────────────────────────────────────
   CERRAR MODALES
───────────────────────────────────────────── */
function cerrarModales() {
    const login    = document.getElementById('authOverlay');
    const registro = document.getElementById('registroOverlay');
    const recuperar = document.getElementById('recuperarOverlay');

    if (login)    login.classList.remove('activo');
    if (registro) registro.classList.remove('activo');
    if (recuperar) recuperar.classList.remove('activo');

    resetFormularioRecuperacion();
}

/* ─────────────────────────────────────────────
   CAMBIAR ENTRE LOGIN / REGISTRO
───────────────────────────────────────────── */
function cambiarModal(cual) {
    cerrarModales();

    if (cual === 'registro') {
        const registro = document.getElementById('registroOverlay');

        if (registro) {
            registro.classList.add('activo');
        }

        const nombres = document.getElementById('fNombres');
        if (nombres) nombres.focus();

    } else {
        abrirModalLogin();
    }
}



/* ─────────────────────────────────────────────
   RECUPERAR CONTRASEÑA FLOW
───────────────────────────────────────────── */
function abrirModalRecuperar() {
    cerrarModales();
    const modal = document.getElementById('recuperarOverlay');
    if (modal) {
        modal.classList.add('activo');
    }
    const email = document.getElementById('recUserEmail');
    if (email) email.focus();
}

function resetFormularioRecuperacion() {
    const p1 = document.getElementById('recuperarPaso1');
    const p2 = document.getElementById('recuperarPaso2');
    const p3 = document.getElementById('recuperarPaso3');
    const msg = document.getElementById('recuperarMensaje');

    if (p1) p1.style.display = 'block';
    if (p2) p2.style.display = 'none';
    if (p3) p3.style.display = 'none';
    if (msg) {
        msg.style.display = 'none';
        msg.className = '';
        msg.textContent = '';
    }

    const email = document.getElementById('recUserEmail');
    const phone = document.getElementById('recUserPhone');
    const code = document.getElementById('recVerificationCode');
    const newPw = document.getElementById('recNewPassword');
    const confPw = document.getElementById('recConfirmPassword');

    if (email) email.value = '';
    if (code) code.value = '';
    if (newPw) newPw.value = '';
    if (confPw) confPw.value = '';
}

function mostrarRecuperarMensaje(tipo, texto) {
    const msg = document.getElementById('recuperarMensaje');
    if (!msg) return;
    msg.style.display = 'flex';
    msg.className = tipo === 'success' ? 'auth-success' : 'auth-error';
    msg.innerHTML = `<i class="fa ${tipo === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i> <span>${texto}</span>`;
}

async function verificarDatosRecuperacion() {
    const email = document.getElementById('recUserEmail').value.trim();

    if (!email) {
        mostrarRecuperarMensaje('error', 'Por favor, ingresa tu correo.');
        return;
    }

    try {
        const formData = new FormData();
        formData.append('correo', email);

        const response = await fetch(BASE_URL + '/controllers/RecuperarPasswordController.php', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();

        if (data.ok) {
            mostrarRecuperarMensaje('success', 'Código enviado a tu correo.');
            document.getElementById('recuperarPaso1').style.display = 'none';
            document.getElementById('recuperarPaso2').style.display = 'block';
            
            const codeInput = document.getElementById('recVerificationCode');
            if (codeInput) codeInput.focus();
        } else {
            mostrarRecuperarMensaje('error', data.mensaje || 'Los datos ingresados son incorrectos.');
        }
    } catch (e) {
        console.error(e);
        mostrarRecuperarMensaje('error', 'Ocurrió un error al procesar la solicitud.');
    }
}

async function confirmarCodigoRecuperacion() {
    const code = document.getElementById('recVerificationCode').value.trim();

    if (!code) {
        mostrarRecuperarMensaje('error', 'Por favor, ingresa el código de verificación.');
        return;
    }

    try {
        const formData = new FormData();
        formData.append('accion', 'confirmar_codigo');
        formData.append('codigo', code);

        const response = await fetch(BASE_URL + '/controllers/ActualizarPasswordController.php', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();

        if (data.ok) {
            mostrarRecuperarMensaje('success', 'Código verificado.');
            document.getElementById('recuperarPaso2').style.display = 'none';
            document.getElementById('recuperarPaso3').style.display = 'block';
            
            const newPwInput = document.getElementById('recNewPassword');
            if (newPwInput) newPwInput.focus();
        } else {
            mostrarRecuperarMensaje('error', data.mensaje || 'Código de verificación incorrecto.');
        }
    } catch (e) {
        console.error(e);
        mostrarRecuperarMensaje('error', 'Ocurrió un error al verificar el código.');
    }
}

async function cambiarPasswordRecuperacion() {
    const password = document.getElementById('recNewPassword').value;
    const confirm = document.getElementById('recConfirmPassword').value;

    if (!password || !confirm) {
        mostrarRecuperarMensaje('error', 'Por favor, completa ambos campos.');
        return;
    }

    if (password.length < 6) {
        mostrarRecuperarMensaje('error', 'La contraseña debe tener al menos 6 caracteres.');
        return;
    }

    if (password !== confirm) {
        mostrarRecuperarMensaje('error', 'Las contraseñas no coinciden.');
        return;
    }

    try {
        const formData = new FormData();
        formData.append('accion', 'cambiar_password_recuperacion');
        formData.append('password', password);
        formData.append('confirm', confirm);

        const response = await fetch(BASE_URL + '/controllers/ActualizarPasswordController.php', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();

        if (data.ok) {
            Swal.fire({icon: 'success', title: '¡Éxito!', text: '🎉 ' + data.mensaje, confirmButtonColor: '#e30613'});
            cerrarModales();
            abrirModalLogin();
        } else {
            mostrarRecuperarMensaje('error', data.mensaje || 'Error al cambiar la contraseña.');
        }
    } catch (e) {
        console.error(e);
        mostrarRecuperarMensaje('error', 'Ocurrió un error al actualizar la contraseña.');
    }
}



/* ─────────────────────────────────────────────
   MOSTRAR / OCULTAR PASSWORD
───────────────────────────────────────────── */
function togglePw(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);

    if (!input || !icon) return;

    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'fa fa-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'fa fa-eye';
    }
}

/* ─────────────────────────────────────────────
   FORTALEZA DE CONTRASEÑA
───────────────────────────────────────────── */
function evaluarFuerza(valor) {
    const fill = document.getElementById('strengthFill');
    const label = document.getElementById('strengthLabel');

    if (!fill || !label) return;

    let score = 0;

    if (valor.length >= 6) score++;
    if (valor.length >= 10) score++;
    if (/[A-Z]/.test(valor)) score++;
    if (/[0-9]/.test(valor)) score++;
    if (/[^A-Za-z0-9]/.test(valor)) score++;

    const niveles = [
        { width: '0%', color: '#eee', texto: '' },
        { width: '25%', color: '#e74c3c', texto: 'Muy débil' },
        { width: '50%', color: '#e67e22', texto: 'Débil' },
        { width: '70%', color: '#f1c40f', texto: 'Regular' },
        { width: '85%', color: '#2ecc71', texto: 'Buena' },
        { width: '100%', color: '#27ae60', texto: 'Muy fuerte' }
    ];

    const nivel = niveles[Math.min(score, 5)];

    fill.style.width = nivel.width;
    fill.style.background = nivel.color;
    label.textContent = nivel.texto;
    label.style.color = nivel.color;

    validarCoincidencia();
}

/* ─────────────────────────────────────────────
   VALIDAR COINCIDENCIA PASSWORD
───────────────────────────────────────────── */
function validarCoincidencia() {
    const p1 = document.getElementById('fPassword');
    const p2 = document.getElementById('fConfirm');
    const hint = document.getElementById('matchHint');

    if (!p1 || !p2 || !hint) return;

    if (!p2.value) {
        hint.textContent = '';
        p2.classList.remove('valid', 'invalid');
        return;
    }

    if (p1.value === p2.value) {
        hint.textContent = '✓ Las contraseñas coinciden';
        hint.style.color = '#27ae60';
        p2.classList.add('valid');
        p2.classList.remove('invalid');
    } else {
        hint.textContent = '✗ Las contraseñas no coinciden';
        hint.style.color = '#e74c3c';
        p2.classList.add('invalid');
        p2.classList.remove('valid');
    }
}

/* ─────────────────────────────────────────────
   VALIDACIONES
───────────────────────────────────────────── */
function validarNombre(nombre) {
    return nombre.trim().length >= 2 &&
        /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/.test(nombre);
}

function validarEmail(email) {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
}

function validarTelefono(tel) {
    const limpio = tel.replace(/\D/g, '');
    return tel === '' || (limpio.length >= 7 && limpio.length <= 15);
}

/* ─────────────────────────────────────────────
   VALIDAR FORM REGISTRO
───────────────────────────────────────────── */
function validarFormularioRegistro() {
    const nombres = document.getElementById('fNombres');
    const apellidos = document.getElementById('fApellidos');
    const correo = document.getElementById('fCorreo');
    const telefono = document.getElementById('fTelefono');
    const password = document.getElementById('fPassword');
    const confirm = document.getElementById('fConfirm');

    let errores = [];

    if (!nombres.value.trim()) {
        errores.push('El nombre es obligatorio');
    } else if (!validarNombre(nombres.value)) {
        errores.push('Nombre inválido');
    }

    if (!apellidos.value.trim()) {
        errores.push('El apellido es obligatorio');
    } else if (!validarNombre(apellidos.value)) {
        errores.push('Apellido inválido');
    }

    if (!correo.value.trim()) {
        errores.push('El correo es obligatorio');
    } else if (!validarEmail(correo.value)) {
        errores.push('Correo inválido');
    }

    if (telefono.value.trim() && !validarTelefono(telefono.value)) {
        errores.push('Teléfono inválido');
    }

    if (!password.value) {
        errores.push('La contraseña es obligatoria');
    } else if (password.value.length < 6) {
        errores.push('La contraseña debe tener mínimo 6 caracteres');
    }

    if (!confirm.value) {
        errores.push('Debes confirmar la contraseña');
    }

    if (password.value !== confirm.value) {
        errores.push('Las contraseñas no coinciden');
    }

    if (errores.length > 0) {
        Swal.fire({icon: 'warning', title: '⚠️ Corrige:', text: errores.join('\n'), confirmButtonColor: '#e30613'});
        return false;
    }

    return true;
}
/* ─────────────────────────────────────────────
   VALIDACIONES EN TIEMPO REAL
───────────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', function () {

    const correo = document.getElementById('fCorreo');
    const telefono = document.getElementById('fTelefono');
    const nombres = document.getElementById('fNombres');
    const apellidos = document.getElementById('fApellidos');

    if (correo) {
        correo.addEventListener('blur', function () {
            this.classList.toggle('invalid', !validarEmail(this.value));
            this.classList.toggle('valid', validarEmail(this.value));
        });
    }

    if (telefono) {
        telefono.addEventListener('blur', function () {
            if (this.value.trim() === '') return;

            this.classList.toggle('invalid', !validarTelefono(this.value));
            this.classList.toggle('valid', validarTelefono(this.value));
        });
    }

    if (nombres) {
        nombres.addEventListener('blur', function () {
            this.classList.toggle('invalid', !validarNombre(this.value));
            this.classList.toggle('valid', validarNombre(this.value));
        });
    }

    if (apellidos) {
        apellidos.addEventListener('blur', function () {
            this.classList.toggle('invalid', !validarNombre(this.value));
            this.classList.toggle('valid', validarNombre(this.value));
        });
    }

}); // ← aquí cierra correctamente DOMContentLoaded


/* ======================================
   SCROLL A SECCIONES
====================================== */
function scrollToSection(id) {
    const section = document.getElementById(id);

    if (!section) return;

    section.scrollIntoView({
        behavior: "smooth",
        block: "start"
    });
}

async function buscarDNI() {
    const dniInput  = document.getElementById("dniInput");
    const dni       = dniInput.value.trim();
    const estado    = document.getElementById("dniEstado");
    const btnBuscar = document.getElementById("btnBuscarDni");
    const btnLimpiar= document.getElementById("btnLimpiarDni");

    if (dni.length !== 8) {
        estado.textContent = "⚠ El DNI debe tener 8 dígitos";
        estado.style.color = "red";
        return;
    }

    // Estado de carga
    estado.textContent = "Verificando DNI...";
    estado.style.color = "gray";
    if (btnBuscar) { btnBuscar.disabled = true; btnBuscar.textContent = "..."; }

    try {
        const response = await fetch(
            `${BASE_URL}/controllers/ConsultaDniController.php?dni=${dni}`
        );
        const data = await response.json();

        if (btnBuscar) { btnBuscar.disabled = false; btnBuscar.textContent = "Buscar"; }

        if (data.success) {
            document.getElementById("nombresRegistro").value    = data.nombres;
            document.getElementById("apellidosRegistro").value  = data.apellidos;

            estado.textContent = "✓ DNI encontrado";
            estado.style.color = "green";

            // Bloqueamos la edición del DNI
            dniInput.readOnly = true;
            if (btnBuscar)  btnBuscar.style.display  = "none";
            if (btnLimpiar) btnLimpiar.style.display  = "inline-block";

        } else {
            // Limpiar nombres en caso de error
            document.getElementById("nombresRegistro").value   = "";
            document.getElementById("apellidosRegistro").value = "";

            // Detectar si es error de DNI ya registrado
            const yaRegistrado = data.error && data.error.toLowerCase().includes("ya está registrado");

            if (yaRegistrado) {
                estado.innerHTML = `⛔ ${data.error} — <a href="${BASE_URL}/views/inicio.php?login=1" style="color:#c0392b;font-weight:700;text-decoration:underline;">Inicia sesión</a>`;
                estado.style.color = "#c0392b";
                // Bloquear campo y form
                dniInput.readOnly = true;
                if (btnBuscar)  btnBuscar.style.display  = "none";
                if (btnLimpiar) btnLimpiar.style.display  = "inline-block";
            } else {
                estado.textContent = data.error;
                estado.style.color = "red";
            }
        }

    } catch (error) {
        if (btnBuscar) { btnBuscar.disabled = false; btnBuscar.textContent = "Buscar"; }
        estado.textContent = "Error de conexión";
        estado.style.color = "red";
    }
}

function limpiarDNI() {
    const dniInput = document.getElementById("dniInput");
    const estado = document.getElementById("dniEstado");
    const btnBuscar = document.getElementById("btnBuscarDni");
    const btnLimpiar = document.getElementById("btnLimpiarDni");

    dniInput.value = "";
    dniInput.readOnly = false;
    document.getElementById("nombresRegistro").value = "";
    document.getElementById("apellidosRegistro").value = "";
    estado.textContent = "";
    
    if (btnBuscar) btnBuscar.style.display = "inline-block";
    if (btnLimpiar) btnLimpiar.style.display = "none";
    
    dniInput.focus();
}


/* ─────────────────────────────────────────────
   ABRIR MODAL COMPLETAR GOOGLE
───────────────────────────────────────────── */
function abrirCompletarGoogle() {
    const modal = document.getElementById("completarGoogleOverlay");

    if (modal) {
        modal.classList.add("activo");
    }
}

/* ─────────────────────────────────────────────
   VALIDAR FORMULARIO GOOGLE ANTES DE ENVIAR
───────────────────────────────────────────── */
function validarFormGoogle() {
    const dni = document.getElementById('dniGoogle').value.trim();
    const nombres = document.getElementById('nombresGoogle').value.trim();
    const apellidos = document.getElementById('apellidosGoogle').value.trim();
    const telefono = document.getElementById('telefonoGoogle').value.trim();
    const direccion = document.getElementById('direccionGoogle').value.trim();
    const errorDiv = document.getElementById('googleFormError');

    let errores = [];
    if (!dni || !/^\d{8}$/.test(dni)) errores.push('DNI válido (8 dígitos)');
    if (!nombres) errores.push('Nombres');
    if (!apellidos) errores.push('Apellidos');
    if (!telefono || !/^\d{9}$/.test(telefono)) errores.push('Teléfono válido (9 dígitos)');
    if (!direccion) errores.push('Dirección');

    if (errores.length > 0) {
        errorDiv.textContent = 'Completa los campos: ' + errores.join(', ');
        errorDiv.style.display = 'block';
        return false;
    }
    errorDiv.style.display = 'none';
    return true;
}

/* ─────────────────────────────────────────────
   CONSULTAR DNI GOOGLE
───────────────────────────────────────────── */
async function buscarDNIGoogle() {
    const dni = document.getElementById("dniGoogle").value.trim();
    const estado = document.getElementById("dniEstadoGoogle");

    if (!/^\d{8}$/.test(dni)) {
        estado.textContent = "DNI inválido";
        estado.style.color = "red";
        return;
    }

    estado.textContent = "Buscando...";
    estado.style.color = "gray";

    try {
        const response = await fetch(
            `${BASE_URL}/controllers/ConsultaDniController.php?dni=${dni}`
        );

        const data = await response.json();

        if (data.success) {
            document.getElementById("nombresGoogle").value = data.nombres;
            document.getElementById("apellidosGoogle").value = data.apellidos;

            estado.textContent = "✓ DNI encontrado";
            estado.style.color = "green";
        } else {
            estado.textContent = data.error || "No encontrado";
            estado.style.color = "red";

            document.getElementById("nombresGoogle").value = "";
            document.getElementById("apellidosGoogle").value = "";
        }

    } catch (error) {
        console.error(error);
        estado.textContent = "Error al consultar";
        estado.style.color = "red";
    }
}

/* ─────────────────────────────────────────────
   LOCKOUT: CUENTA REGRESIVA VISUAL
───────────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', () => {
    if (typeof window.lockoutRestante !== 'undefined' && window.lockoutRestante > 0) {
        iniciarCuentaRegresivaLockout(window.lockoutRestante);
    }
});

/* ─────────────────────────────────────────────
   SOPORTE PARA TECLA ENTER EN RECUPERACIÓN
───────────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', () => {
    // Paso 1: Enviar código con Enter en el correo
    const emailInput = document.getElementById('recUserEmail');
    if (emailInput) {
        emailInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                verificarDatosRecuperacion();
            }
        });
    }

    // Paso 2: Confirmar código con Enter
    const codeInput = document.getElementById('recVerificationCode');
    if (codeInput) {
        codeInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                confirmarCodigoRecuperacion();
            }
        });
    }

    // Paso 3: Cambiar contraseña con Enter
    const newPwInput = document.getElementById('recNewPassword');
    const confPwInput = document.getElementById('recConfirmPassword');
    
    const submitPaso3 = function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            cambiarPasswordRecuperacion();
        }
    };

    if (newPwInput) newPwInput.addEventListener('keypress', submitPaso3);
    if (confPwInput) confPwInput.addEventListener('keypress', submitPaso3);
});

function iniciarCuentaRegresivaLockout(segundosRestantes) {
    const btn       = document.getElementById('btnAuthLogin');
    const errorBox  = document.getElementById('authLoginError');
    const errorText = document.getElementById('authLoginErrorText');

    if (!btn) return;

    // Deshabilitar botón inmediatamente
    btn.disabled = true;
    btn.style.opacity = '0.5';
    btn.style.cursor  = 'not-allowed';

    // Mostrar mensaje inmediatamente con el valor inicial
    if (errorBox)  errorBox.style.display = 'flex';
    if (errorText) errorText.textContent  = `⏳ Demasiados intentos. Espera ${segundosRestantes} segundo(s).`;

    const intervalo = setInterval(() => {
        segundosRestantes--;

        if (segundosRestantes <= 0) {
            clearInterval(intervalo);

            // Re-habilitar botón
            btn.disabled      = false;
            btn.style.opacity = '1';
            btn.style.cursor  = 'pointer';

            if (errorText) errorText.textContent = '✅ Ya puedes intentar iniciar sesión nuevamente.';

            // Limpiar lockout en el servidor
            fetch(BASE_URL + '/controllers/AuthController.php?accion=limpiar_lockout')
                .then(r => r.json())
                .catch(err => console.warn('Error al limpiar lockout', err));
        } else {
            if (errorText) {
                errorText.textContent = `⏳ Demasiados intentos. Espera ${segundosRestantes} segundo(s).`;
            }
        }
    }, 1000);
}