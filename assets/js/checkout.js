/* checkout.js */

function obtenerCarrito() {
    if (window._usuarioId && parseInt(window._usuarioId) > 0) {
        return window._carritoCache || [];
    }
    try {
        return JSON.parse(localStorage.getItem('carrito')) || [];
    }
    catch { return []; }
}

function checkoutVolver(event) {
    if (event) event.preventDefault();
    if (pasoActual > 1) {
        history.back(); // El popstate se encargará de retroceder de paso
    } else {
        window.location.href = BASE_URL + '/views/inicio.php';
    }
}

/* ── Estado global ───────────────────────────────────────────── */
let pasoActual = 1;
let metodoEntrega = 'domicilio'; // domicilio | tienda
let metodoPago = '';          // tarjeta | yape | efectivo

const COSTO_ENVIO = 5.00;

/* ── Inicializar ─────────────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', function () {
    const carrito = obtenerCarrito();

    if (carrito.length === 0) {
        window.location.href = BASE_URL + '/views/inicio.php';
        return;
    }

    let modificado = false;
    carrito.forEach(item => {
        const stockMax = item.stock !== undefined && item.stock !== null ? parseInt(item.stock) : 9999;
        if (item.cantidad > stockMax) {
            item.cantidad = stockMax;
            modificado = true;
        }
    });

    if (modificado) {
        localStorage.setItem('carrito', JSON.stringify(carrito));
    }

    renderResumen(carrito);
    initEntrega();
    initDireccion();
    initPago();

    // Inicializar estado de historia para el paso 1
    history.replaceState({ paso: 1 }, '', '?paso=1');
    mostrarPaso(1, false);
});

/* ── Render resumen lateral ──────────────────────────────────── */
function renderResumen(carrito) {
    const cont = document.getElementById('resumenItems');
    if (!cont) return;

    let subtotalOrig = 0, subtotalDesc = 0;
    cont.innerHTML = '';

    carrito.forEach(item => {
        const orig = item.precioOriginal || item.precio;
        const final = item.precio;
        subtotalOrig += orig * item.cantidad;
        subtotalDesc += final * item.cantidad;

        cont.innerHTML += `
            <div class="resumen-item">
                <img src="${item.imagen}" alt="${item.nombre}"
                     onerror="this.src=BASE_URL + '/img/imagen1.webp'">
                <div class="resumen-item-info">
                    <div class="nombre">${item.nombre}</div>
                    <div class="cant">x${item.cantidad}</div>
                </div>
                <div class="resumen-item-precio">S/ ${(final * item.cantidad).toFixed(2)}</div>
            </div>`;
    });

    const descuento = subtotalOrig - subtotalDesc;
    const envio = metodoEntrega === 'domicilio' ? COSTO_ENVIO : 0;
    const igv = subtotalDesc * 0.18;
    const total = subtotalDesc + igv + envio;

    document.getElementById('resSubtotal').textContent = `S/ ${subtotalOrig.toFixed(2)}`;
    document.getElementById('resIgv').textContent = `S/ ${igv.toFixed(2)}`;
    document.getElementById('resTotal').textContent = `S/ ${total.toFixed(2)}`;

    const lineaEnvio = document.getElementById('lineaEnvioRes');
    if (lineaEnvio) {
        if (metodoEntrega === 'domicilio') {
            lineaEnvio.style.display = 'flex';
            document.getElementById('resEnvio').textContent = `S/ ${envio.toFixed(2)}`;
        } else {
            lineaEnvio.style.display = 'none';
        }
    }

    const lineaDesc = document.getElementById('lineaDescuentoRes');
    if (lineaDesc) {
        lineaDesc.style.display = descuento > 0.001 ? 'flex' : 'none';
        document.getElementById('resDescuento').textContent = `- S/ ${descuento.toFixed(2)}`;
    }
}

/* ── Stepper ─────────────────────────────────────────────────── */
function mostrarPaso(n, pushState = true) {
    if (pushState && (n !== pasoActual || !history.state)) {
        history.pushState({ paso: n }, '', `?paso=${n}`);
    }
    pasoActual = n;

    [1, 2, 3].forEach(i => {
        const stepEl = document.getElementById(`step${i}`);
        const panelEl = document.getElementById(`paso${i}`);

        stepEl.classList.remove('active', 'done');

        if (i === n) {
            stepEl.classList.add('active');
        } else if (i < n) {
            // Paso 2 no se marca como done si se saltó (recojo en tienda)
            if (!(i === 2 && metodoEntrega === 'tienda')) {
                stepEl.classList.add('done');
            }
        }

        if (panelEl) panelEl.style.display = i === n ? 'block' : 'none';
    });

    document.getElementById('line12').classList.toggle('done', n > 1 && metodoEntrega !== 'tienda');
    document.getElementById('line23').classList.toggle('done', n > 2);

    window.scrollTo({ top: 0, behavior: 'smooth' });
}

window.addEventListener('popstate', function (event) {
    if (event.state && event.state.paso) {
        mostrarPaso(event.state.paso, false);
    }
});

/* ── Paso 1: Entrega ─────────────────────────────────────────── */
function initEntrega() {
    document.querySelectorAll('.entrega-opcion').forEach(opt => {
        opt.addEventListener('click', function () {
            document.querySelectorAll('.entrega-opcion').forEach(o => o.classList.remove('selected'));
            this.classList.add('selected');
            metodoEntrega = this.dataset.metodo;
            renderResumen(obtenerCarrito());

            // Auto advance
            const btn = document.getElementById('btnContinuarEntrega');
            if (btn) btn.click();
        });
    });

    document.getElementById('btnContinuarEntrega')?.addEventListener('click', () => {
        const dirDniEl = document.getElementById('dirDNI');
        const esInvitado = dirDniEl && dirDniEl.type !== 'hidden';

        if (metodoEntrega === 'tienda') {
            const resumen = document.getElementById('resumenDireccion');
            if (resumen) {
                resumen.innerHTML = `
                    <strong style="color:#222;">Recojo en tienda</strong><br>
                    <span style="color:#666;font-size:.85rem;">Urb. Los Sauces 6448 – Lambayeque, Perú</span>
                `;
            }

            if (esInvitado) {
                // Configurar paso 2 para tienda (ocultar campos de delivery)
                document.getElementById('tituloPaso2Main').style.display = 'none';
                document.getElementById('tituloPaso2').textContent = 'Datos personales';
                document.querySelectorAll('.campos-delivery').forEach(el => {
                    el.style.display = 'none';
                    const input = el.querySelector('input, textarea');
                    if (input) input.removeAttribute('required');
                });
                mostrarPaso(2);
            } else {
                mostrarPaso(3);
            }
        } else {
            if (esInvitado) {
                document.getElementById('tituloPaso2Main').style.display = 'none';
                document.getElementById('tituloPaso2').textContent = 'Dirección de entrega';
                document.querySelectorAll('.campos-delivery').forEach(el => {
                    el.style.display = 'block';
                    const input = el.querySelector('input');
                    if (input && input.id !== 'dirReferencia') input.setAttribute('required', 'true');
                });
            }
            mostrarPaso(2);
        }
    });
}

/* ── Paso 2: Dirección ───────────────────────────────────────── */
function initDireccion() {
    // Selector de direcciones guardadas
    document.querySelectorAll('.dir-opcion').forEach(opt => {
        opt.addEventListener('click', function () {
            document.querySelectorAll('.dir-opcion').forEach(o => o.classList.remove('selected'));
            this.classList.add('selected');
            this.querySelector('input[type="radio"]').checked = true;

            // Rellenar campos ocultos
            const d = document.getElementById('dirDireccion');
            const t = document.getElementById('dirDistrito');
            const r = document.getElementById('dirReferencia');
            if (d) d.value = this.dataset.direccion || '';
            if (t) t.value = this.dataset.distrito || '';
            if (r) r.value = this.dataset.referencia || '';
        });
    });

    // Inicializar con la dirección seleccionada por defecto
    const selected = document.querySelector('.dir-opcion.selected');
    if (selected) selected.click();

    // Lógica para buscar DNI en el checkout
    const btnBuscarDni = document.getElementById('btnBuscarDniCheckout');
    if (btnBuscarDni) {
        btnBuscarDni.addEventListener('click', async () => {
            const dniInput = document.getElementById("dirDNI");
            const dni = dniInput.value.trim();
            const estado = document.getElementById("dniEstadoCheckout");

            if (btnBuscarDni.textContent.trim() === "Cambiar") {
                dniInput.readOnly = false;
                document.getElementById("dirNombres").readOnly = false;
                document.getElementById("dirApellidos").readOnly = false;

                dniInput.value = "";
                document.getElementById("dirNombres").value = "";
                document.getElementById("dirApellidos").value = "";
                estado.textContent = "";

                btnBuscarDni.textContent = "Buscar";
                btnBuscarDni.style.backgroundColor = "";
                return;
            }

            if (dni.length !== 8) {
                estado.textContent = "⚠ El DNI debe tener 8 dígitos";
                estado.style.color = "red";
                return;
            }

            estado.textContent = "Buscando...";
            estado.style.color = "gray";
            btnBuscarDni.disabled = true;
            btnBuscarDni.textContent = "...";

            try {
                // jQuery AJAX
                const data = await $.ajax({
                    url: `${BASE_URL}/controllers/ConsultaDniController.php?dni=${dni}`,
                    method: 'GET',
                    dataType: 'json'
                });

                btnBuscarDni.disabled = false;
                btnBuscarDni.textContent = "Buscar";

                if (data.success) {
                    const nombresInput = document.getElementById("dirNombres");
                    const apellidosInput = document.getElementById("dirApellidos");

                    nombresInput.value = data.nombres;
                    apellidosInput.value = data.apellidos;

                    dniInput.readOnly = true;
                    nombresInput.readOnly = true;
                    apellidosInput.readOnly = true;

                    btnBuscarDni.textContent = "Cambiar";
                    btnBuscarDni.style.backgroundColor = "#555";

                    estado.textContent = "✓ DNI encontrado";
                    estado.style.color = "green";
                } else {
                    const yaRegistrado = data.error && data.error.toLowerCase().includes("ya está registrado");
                    if (yaRegistrado) {
                        estado.innerHTML = `⛔ ${data.error} — <a href="${BASE_URL}/views/inicio.php?login=1&redirect=checkout" style="color:#c0392b;font-weight:700;text-decoration:underline;">Inicia sesión</a>`;
                        estado.style.color = "#c0392b";

                        dniInput.readOnly = true;
                        document.getElementById("dirNombres").readOnly = true;
                        document.getElementById("dirApellidos").readOnly = true;

                        btnBuscarDni.textContent = "Cambiar";
                        btnBuscarDni.style.backgroundColor = "#555";
                    } else {
                        estado.textContent = data.error;
                        estado.style.color = "red";
                    }
                    document.getElementById("dirNombres").value = "";
                    document.getElementById("dirApellidos").value = "";
                }
            } catch (error) {
                btnBuscarDni.disabled = false;
                btnBuscarDni.textContent = "Buscar";
                estado.textContent = "Error de conexión";
                estado.style.color = "red";
            }
        });
    }

    document.getElementById('btnContinuarDireccion')?.addEventListener('click', () => {
        // Si hay selector de direcciones, verificar que haya una seleccionada
        const haySelector = document.querySelector('.dir-opcion');
        if (haySelector) {
            const seleccionada = document.querySelector('.dir-opcion.selected');
            if (!seleccionada) {
                Swal.fire({ icon: 'warning', title: 'Atención', text: 'Selecciona una dirección de entrega.', confirmButtonColor: '#e30613' });
                return;
            }
            // Mostrar resumen en paso 3
            mostrarResumenDireccion(seleccionada);
        } else {
            // Formulario manual — validar campos required que estén visibles
            const campos = document.querySelectorAll('#paso2 input[required]');
            let valido = true;
            campos.forEach(c => {
                if (c.closest('.campos-delivery') && c.closest('.campos-delivery').style.display === 'none') {
                    // Skip hidden fields
                    return;
                }
                if (!c.value.trim()) {
                    c.style.borderColor = '#e30613';
                    valido = false;
                } else {
                    c.style.borderColor = '#ddd';
                }
            });
            if (!valido) return;

            // Mostrar resumen en paso 3 con datos del formulario
            const resumen = document.getElementById('resumenDireccion');
            if (resumen && metodoEntrega !== 'tienda') {
                const nombres = document.getElementById('dirNombres')?.value || '';
                const apels = document.getElementById('dirApellidos')?.value || '';
                const dir = document.getElementById('dirDireccion')?.value || '';
                const dist = document.getElementById('dirDistrito')?.value || '';
                const tel = document.getElementById('dirTelefono')?.value || '';
                const ref = document.getElementById('dirReferencia')?.value || '';
                resumen.innerHTML = `
                    <strong>${nombres} ${apels}</strong><br>
                    ${dir}, ${dist}<br>
                    ${ref ? `<span style="color:#aaa">${ref}</span><br>` : ''}
                    Tel: ${tel}
                `;
            } else if (resumen && metodoEntrega === 'tienda') {
                // Si es tienda, mantenemos "Recojo en tienda" pero ya guardamos los datos del usuario.
            }
        }

        mostrarPaso(3);
    });
}

function mostrarResumenDireccion(opt) {
    const resumen = document.getElementById('resumenDireccion');
    if (!resumen || !opt) return;

    const etiqueta = opt.querySelector('.dir-info strong')?.childNodes[0]?.textContent?.trim() || '';
    const detalle = opt.querySelectorAll('.dir-info p');
    const direccion = detalle[0]?.textContent?.trim() || '';
    const referencia = detalle[1]?.textContent?.trim() || '';

    resumen.innerHTML = `
        <strong style="color:#222;">${etiqueta}</strong><br>
        ${direccion}
        ${referencia ? `<br><span style="color:#aaa;font-size:.8rem;">${referencia}</span>` : ''}
    `;
}

/* ── Paso 3: Pago ────────────────────────────────────────────── */
let subMetodoYp = '';   // 'yape' | 'plin'
let pagoConfirmado = false; // true cuando el método actual ya pasó su validación
let efectivoMontoPagado = null;
let efectivoVueltoCalc = null;

function totalActual() {
    const txt = document.getElementById('resTotal')?.textContent || 'S/ 0.00';
    return parseFloat(txt.replace('S/', '').replace(',', '').trim()) || 0;
}

function initPago() {
    document.querySelectorAll('.pago-opcion').forEach(opt => {
        opt.addEventListener('click', function () {
            document.querySelectorAll('.pago-opcion').forEach(o => o.classList.remove('selected'));
            this.classList.add('selected');
            metodoPago = this.dataset.pago;
            this.querySelector('input[type="radio"]').checked = true;
            abrirModalPago(metodoPago);
        });
    });

    initTarjetaVisual();
    initYapePlin();
    initEfectivo();
    initModalOverlay();

    document.getElementById('btnVolverPago')?.addEventListener('click', () => {
        mostrarPaso(metodoEntrega === 'tienda' ? 1 : 2);
    });

    document.getElementById('btnCambiarDireccion')?.addEventListener('click', () => {
        mostrarPaso(metodoEntrega === 'tienda' ? 1 : 2);
    });

    document.getElementById('btnConfirmarPago')?.addEventListener('click', () => {
        if (!metodoPago) {
            Swal.fire({ icon: 'warning', title: 'Atención', text: 'Selecciona un método de pago.', confirmButtonColor: '#e30613' });
            return;
        }
        // El envío real del pedido ocurre dentro del modal de pago
        // (botón Pagar / Yapear / Pagar con Plin / Confirmar).
        abrirModalPago(metodoPago);
    });
}

/* ── Modal overlay genérico ──────────────────────────────────── */
function initModalOverlay() {
    document.getElementById('pmClose')?.addEventListener('click', cerrarModalPago);
    // El usuario pidió que NO se cierre al hacer clic afuera
}

function abrirModalPago(metodo) {
    const overlay = document.getElementById('pmOverlay');
    if (!overlay) return;

    document.querySelectorAll('.pm-content').forEach(c => c.classList.remove('visible'));

    if (metodo === 'tarjeta') {
        document.getElementById('pmTarjeta')?.classList.add('visible');
        actualizarMontosModales();
    } else if (metodo === 'yape') {
        document.getElementById('pmYp')?.classList.add('visible');
        actualizarMontosModales();
    } else if (metodo === 'efectivo') {
        document.getElementById('pmEfectivo')?.classList.add('visible');
        actualizarVueltoUI();
    }

    overlay.classList.add('visible');
}

function cerrarModalPago() {
    document.getElementById('pmOverlay')?.classList.remove('visible');
}

/* ── Tarjeta visual interactiva + validación ─────────────────── */
function detectarMarcaTarjeta(numero) {
    if (/^4/.test(numero)) return 'visa';
    if (/^(5[1-5]|2[2-7])/.test(numero)) return 'mastercard';
    return null;
}

function initTarjetaVisual() {
    const numInput = document.getElementById('ccNumeroInput');
    const vencInput = document.getElementById('ccVencInput');
    const cvvInput = document.getElementById('ccCvvInput');
    const nombreInput = document.getElementById('ccNombreInput');

    const numDisplay = document.getElementById('ccNumberDisplay');
    const vencDisplay = document.getElementById('ccExpiryDisplay');
    const cvvDisplay = document.getElementById('ccCvvDisplay');
    const nombreDisplay = document.getElementById('ccNameDisplay');
    const ccInner = document.getElementById('ccVisualInner');

    // ── Lógica del Dropdown Personalizado ──
    const customDropdown = document.getElementById('customCardDropdown');
    const dropdownTrigger = document.getElementById('dropdownCardTrigger');
    const dropdownOptions = document.getElementById('dropdownCardOptions');
    const hiddenInput = document.getElementById('selTarjetaGuardada');

    if (customDropdown && dropdownTrigger && hiddenInput) {
        dropdownTrigger.addEventListener('click', () => {
            customDropdown.classList.toggle('open');
        });

        document.addEventListener('click', (e) => {
            if (!customDropdown.contains(e.target)) {
                customDropdown.classList.remove('open');
            }
        });

        const options = customDropdown.querySelectorAll('.custom-option');
        options.forEach(opt => {
            opt.addEventListener('click', () => {
                // Remover active de todas
                options.forEach(o => o.classList.remove('active'));
                // Agregar active a la seleccionada
                opt.classList.add('active');

                // Actualizar input hidden
                const id = opt.getAttribute('data-value');
                hiddenInput.value = id;

                // Actualizar contenido del trigger
                dropdownTrigger.querySelector('.dropdown-selected-content').innerHTML = opt.innerHTML;

                customDropdown.classList.remove('open');

                // Aplicar datos a la tarjeta visual
                const fgGuardar = document.querySelector('.save-card-toggle');
                const ccGuardarInput = document.getElementById('ccGuardarInput');

                if (!id) {
                    if (fgGuardar) fgGuardar.style.display = 'flex';
                    if (numInput) numInput.value = '';
                    if (vencInput) vencInput.value = '';
                    if (numInput) numInput.dispatchEvent(new Event('input'));
                    if (vencInput) vencInput.dispatchEvent(new Event('input'));
                    return;
                }

                const tarjeta = window._tarjetasGuardadas?.find(t => t.id_tarjeta == id);
                if (tarjeta) {
                    if (fgGuardar) fgGuardar.style.display = 'none';
                    if (ccGuardarInput) ccGuardarInput.checked = false;

                    if (numInput) {
                        window._isProgrammaticCC = true;
                        numInput.value = tarjeta.numero;
                        numInput.dispatchEvent(new Event('input'));
                        window._isProgrammaticCC = false;
                    }
                    if (vencInput) {
                        vencInput.value = tarjeta.vencimiento;
                        vencInput.dispatchEvent(new Event('input'));
                    }
                    if (nombreInput) {
                        nombreInput.value = tarjeta.titular;
                        nombreInput.dispatchEvent(new Event('input'));
                    }
                }
            });
        });
    }

    // Autocompletar nombre corto si existe y está vacío
    if (nombreInput && !nombreInput.value && window._checkoutNombre) {
        let p = window._checkoutNombre.trim().split(/\s+/);
        let nomCorto = p[0] || '';
        let apeCorto = '';
        if (p.length === 2) apeCorto = p[1];
        else if (p.length >= 3) apeCorto = p[2]; // toma el primer apellido

        let nombrePre = (nomCorto + ' ' + apeCorto).trim().toUpperCase();
        nombreInput.value = nombrePre;
        if (nombreDisplay) nombreDisplay.textContent = nombrePre || 'NOMBRE APELLIDO';
    }

    numInput?.addEventListener('input', () => {
        // Si el usuario edita manualmente el número de tarjeta, ya no es la tarjeta guardada
        if (!window._isProgrammaticCC) {
            const selTarjeta = document.getElementById('selTarjetaGuardada');
            if (selTarjeta && selTarjeta.value) {
                selTarjeta.value = '';

                // Revertir el dropdown visual a "Usar una tarjeta nueva"
                const dropdownTrigger = document.getElementById('dropdownCardTrigger');
                if (dropdownTrigger) {
                    dropdownTrigger.querySelector('.dropdown-selected-content').innerHTML = '<i class="fa fa-plus-circle"></i> Usar una tarjeta nueva';
                }
                const options = document.querySelectorAll('#dropdownCardOptions .custom-option');
                options.forEach(o => o.classList.remove('active'));
                const firstOpt = document.querySelector('#dropdownCardOptions .custom-option[data-value=""]');
                if (firstOpt) firstOpt.classList.add('active');

                // Volver a mostrar el botón de guardar
                const fgGuardar = document.querySelector('.save-card-toggle');
                if (fgGuardar) fgGuardar.style.display = 'flex';
            }
        }

        numInput.value = numInput.value.replace(/\D/g, '').slice(0, 16).replace(/(.{4})/g, '$1 ').trim();
        numDisplay.textContent = numInput.value || '•••• •••• •••• ••••';

        // Autoseleccionar tarjeta guardada si el número coincide exactamente
        const rawNum = numInput.value.replace(/\s/g, '');
        if (!window._isProgrammaticCC && rawNum.length === 16 && window._tarjetasGuardadas) {
            const matchedCard = window._tarjetasGuardadas.find(t => t.numero === rawNum);
            if (matchedCard) {
                const opt = document.querySelector(`#dropdownCardOptions .custom-option[data-value="${matchedCard.id_tarjeta}"]`);
                if (opt) {
                    opt.click();
                    return;
                }
            }
        }

        const marca = detectarMarcaTarjeta(rawNum);
        const brandEl = document.getElementById('ccBrandLogo');
        const ccFront = document.querySelector('.cc-front');
        const ccBack = document.querySelector('.cc-back');

        if (brandEl) {
            brandEl.textContent = marca === 'mastercard' ? '' : 'VISA';
            brandEl.className = 'cc-brand' + (marca === 'mastercard' ? ' cc-brand-mastercard' : '');
        }
        if (ccFront && ccBack) {
            if (marca === 'mastercard') {
                ccFront.classList.add('cc-mc-theme');
                ccBack.classList.add('cc-mc-theme');
            } else {
                ccFront.classList.remove('cc-mc-theme');
                ccBack.classList.remove('cc-mc-theme');
            }
        }
    });

    vencInput?.addEventListener('input', () => {
        let v = vencInput.value.replace(/\D/g, '').slice(0, 4);
        if (v.length > 2) v = v.slice(0, 2) + '/' + v.slice(2);
        vencInput.value = v;
        vencDisplay.textContent = v || 'MM/AA';
    });

    nombreInput?.addEventListener('input', () => {
        nombreInput.value = nombreInput.value.replace(/[^A-Za-zÀ-ÿñÑ\s]/g, '');
        nombreDisplay.textContent = nombreInput.value.trim().toUpperCase() || 'NOMBRE APELLIDO';
    });

    cvvInput?.addEventListener('input', () => {
        cvvInput.value = cvvInput.value.replace(/\D/g, '').slice(0, 3);
        cvvDisplay.textContent = cvvInput.value || '•••';
    });
    cvvInput?.addEventListener('focus', () => ccInner?.classList.add('flipped'));
    cvvInput?.addEventListener('blur', () => ccInner?.classList.remove('flipped'));

    document.getElementById('btnGuardarTarjeta')?.addEventListener('click', () => {
        const err = validarTarjeta();
        const errEl = document.getElementById('ccError');
        if (err) {
            errEl.textContent = err;
            errEl.classList.add('visible');
            return;
        }
        errEl.classList.remove('visible');
        document.getElementById('resumenTarjeta').textContent =
            `Tarjeta terminada en ${numInput.value.replace(/\s/g, '').slice(-4)}`;
        pagoConfirmado = true;
        cerrarModalPago();
        confirmarPedido();
    });
}

function validarTarjeta() {
    const numero = document.getElementById('ccNumeroInput').value.replace(/\s/g, '');
    const venc = document.getElementById('ccVencInput').value;
    const cvv = document.getElementById('ccCvvInput').value;
    const nombre = document.getElementById('ccNombreInput').value.trim();

    if (numero.length !== 16) return 'Ingresa un número de tarjeta válido (16 dígitos).';

    const marca = detectarMarcaTarjeta(numero);
    if (!marca) return 'Solo se aceptan tarjetas Visa o Mastercard.';

    const match = venc.match(/^(\d{2})\/(\d{2})$/);
    if (!match) return 'Ingresa una fecha de vencimiento válida (MM/AA).';
    const mm = parseInt(match[1], 10);
    const yy = parseInt(match[2], 10);
    if (mm < 1 || mm > 12) return 'El mes de vencimiento no es válido.';

    const ahora = new Date();
    const yyActual = ahora.getFullYear() % 100;
    const mmActual = ahora.getMonth() + 1;
    if (yy < yyActual || (yy === yyActual && mm < mmActual)) {
        return 'Tu tarjeta está vencida. Verifica la fecha.';
    }

    if (cvv.length !== 3) return 'El CVV debe tener 3 dígitos.';
    if (!nombre) return 'Ingresa el nombre del titular de la tarjeta.';
    if (!/^[A-Za-zÀ-ÿñÑ\s]+$/.test(nombre)) return 'El nombre solo debe contener letras.';

    return null;
}


/* ── Yape / Plin ──────────────────────────────────────────────── */
function initYapePlin() {
    document.querySelectorAll('.yp-opcion').forEach(opt => {
        opt.addEventListener('click', function () {
            document.querySelectorAll('.yp-opcion').forEach(o => o.classList.remove('selected'));
            this.classList.add('selected');
            this.querySelector('input[type="radio"]').checked = true;
            subMetodoYp = this.dataset.yp;

            document.getElementById('yapePanel')?.classList.toggle('visible', subMetodoYp === 'yape');
            document.getElementById('plinPanel')?.classList.toggle('visible', subMetodoYp === 'plin');

            actualizarMontosModales();
        });
    });

    // Solo dígitos y longitud máxima en celular / código
    ['yapeCelular', 'plinCelular'].forEach(id => {
        document.getElementById(id)?.addEventListener('input', function () {
            this.value = this.value.replace(/\D/g, '').slice(0, 9);
        });
    });
    ['yapeCodigo', 'plinCodigo'].forEach(id => {
        document.getElementById(id)?.addEventListener('input', function () {
            this.value = this.value.replace(/\D/g, '').slice(0, 6);
        });
    });

    document.getElementById('btnYapear')?.addEventListener('click', () => {
        const err = validarYapePlin('yape');
        const errEl = document.getElementById('yapeError');
        if (err) { errEl.textContent = err; errEl.classList.add('visible'); return; }
        errEl.classList.remove('visible');
        metodoPago = 'yape';
        document.getElementById('resumenYp').textContent =
            `Yape · celular terminado en ${document.getElementById('yapeCelular').value.slice(-3)}`;
        pagoConfirmado = true;
        cerrarModalPago();
        confirmarPedido();
    });

    document.getElementById('btnPlinear')?.addEventListener('click', () => {
        const err = validarYapePlin('plin');
        const errEl = document.getElementById('plinError');
        if (err) { errEl.textContent = err; errEl.classList.add('visible'); return; }
        errEl.classList.remove('visible');
        metodoPago = 'yape'; // se mantiene el mismo método de pago a nivel de backend
        document.getElementById('resumenYp').textContent =
            `Plin · celular terminado en ${document.getElementById('plinCelular').value.slice(-3)}`;
        pagoConfirmado = true;
        cerrarModalPago();
        confirmarPedido();
    });
}

function validarYapePlin(tipo) {
    if (subMetodoYp !== tipo) return 'Selecciona ' + (tipo === 'yape' ? 'Yape' : 'Plin') + ' para continuar.';

    const celular = document.getElementById(tipo + 'Celular').value;
    const codigo = document.getElementById(tipo + 'Codigo').value.replace(/\s+/g, '');

    if (!/^9\d{8}$/.test(celular)) return 'Ingresa un celular válido de 9 dígitos (empieza con 9).';
    if (codigo.length !== 6) return 'Ingresa el código de aprobación de 6 dígitos.';

    return null;
}

function actualizarMontosModales() {
    const totalText = document.getElementById('resTotal')?.textContent || 'S/ 0.00';
    const totalNum = totalText.replace('S/ ', '').trim();
    const yapeMonto = document.getElementById('yapeMonto');
    const plinMonto = document.getElementById('plinMonto');
    const ccMonto = document.getElementById('ccMonto');
    if (yapeMonto) yapeMonto.textContent = totalNum;
    if (plinMonto) plinMonto.textContent = totalNum;
    if (ccMonto) ccMonto.textContent = totalNum;
}

/* ── Efectivo ─────────────────────────────────────────────────── */
function initEfectivo() {
    const montoInput = document.getElementById('efectivoMonto');

    montoInput?.addEventListener('input', function () {
        this.value = this.value.replace(/[^\d.]/g, '').replace(/(\..*)\./g, '$1');
        actualizarVueltoUI();
    });

    document.getElementById('btnGuardarEfectivo')?.addEventListener('click', () => {
        const errEl = document.getElementById('efectivoError');
        const monto = parseFloat(montoInput.value);
        const total = totalActual();

        if (isNaN(monto) || monto <= 0) {
            errEl.textContent = 'Ingresa el monto con el que vas a pagar.';
            errEl.classList.add('visible');
            return;
        }
        if (monto < total) {
            errEl.textContent = `El monto debe ser mayor o igual al total (S/ ${total.toFixed(2)}).`;
            errEl.classList.add('visible');
            return;
        }

        errEl.classList.remove('visible');
        efectivoMontoPagado = monto;
        efectivoVueltoCalc = +(monto - total).toFixed(2);
        document.getElementById('resumenEfectivo').textContent =
            efectivoVueltoCalc > 0
                ? `Pagarás con S/ ${monto.toFixed(2)} · vuelto S/ ${efectivoVueltoCalc.toFixed(2)}`
                : 'Pago exacto, sin vuelto';
        pagoConfirmado = true;
        cerrarModalPago();
        confirmarPedido();
    });
}

function actualizarVueltoUI() {
    const montoInput = document.getElementById('efectivoMonto');
    const box = document.getElementById('efectivoVueltoBox');
    const total = totalActual();
    const monto = parseFloat(montoInput?.value);

    if (!box) return;
    if (isNaN(monto) || monto < total) {
        box.style.display = 'none';
        return;
    }
    const vuelto = +(monto - total).toFixed(2);
    document.getElementById('efectivoVuelto').textContent =
        vuelto > 0 ? `S/ ${vuelto.toFixed(2)}` : 'S/ 0.00 (pago exacto)';
    box.style.display = 'block';
}

/* ── Confirmar pedido ────────────────────────────────────────── */
async function confirmarPedido() {
    const btn = document.getElementById('btnConfirmarPago');
    if (btn) { btn.disabled = true; btn.textContent = 'Procesando...'; }

    const carrito = obtenerCarrito();

    // Calcular totales
    let subtotalOrig = 0, subtotalDesc = 0;
    carrito.forEach(item => {
        subtotalOrig += (item.precioOriginal || item.precio) * item.cantidad;
        subtotalDesc += item.precio * item.cantidad;
    });
    const descuento = subtotalOrig - subtotalDesc;
    const igv = subtotalDesc * 0.18;
    const envio = metodoEntrega === 'domicilio' ? 5.00 : 0;
    const total = subtotalDesc + igv + envio;

    // Id de dirección seleccionada (si hay selector)
    const radioDir = document.querySelector('input[name="dir_seleccionada"]:checked');
    const idDireccion = radioDir ? parseInt(radioDir.value) : null;

    const payload = {
        tipo_entrega: metodoEntrega,
        metodo_pago: metodoPago,
        items: carrito,
        subtotal_original: subtotalOrig,
        subtotal_descuento: subtotalDesc,
        igv: igv,
        envio: envio,
        total: total,
        id_direccion: idDireccion,

        // Datos de invitado
        guest_dni: document.getElementById('dirDNI')?.value || '',
        guest_nombre: document.getElementById('dirNombres')?.value || '',
        guest_apellido: document.getElementById('dirApellidos')?.value || '',
        guest_telefono: document.getElementById('dirTelefono')?.value || '',
        guest_correo: document.getElementById('dirCorreo')?.value || '', // si no existe, será vacío
        guest_direccion: document.getElementById('dirDireccion')?.value || '',
        guest_distrito: document.getElementById('dirDistrito')?.value || '',
        guest_referencia: document.getElementById('dirReferencia')?.value || ''
    };

    // Datos extra para pago en efectivo (solo informativos, no se persisten en BD)
    if (metodoPago === 'efectivo' && efectivoMontoPagado !== null) {
        payload.monto_efectivo = efectivoMontoPagado;
        payload.vuelto_efectivo = efectivoVueltoCalc;
    }

    // Datos de tarjeta
    if (metodoPago === 'tarjeta') {
        const numInputVal = document.getElementById('ccNumeroInput')?.value || '';
        const numStr = numInputVal.replace(/\s/g, '');

        // Verificar si se seleccionó una tarjeta del dropdown y si el número no fue cambiado por otro diferente
        let idTarjetaGuardada = document.getElementById('selTarjetaGuardada')?.value || null;
        if (idTarjetaGuardada) {
            const tarjetaOriginal = window._tarjetasGuardadas?.find(t => t.id_tarjeta == idTarjetaGuardada);
            if (tarjetaOriginal && tarjetaOriginal.numero !== numStr) {
                // El usuario seleccionó una guardada pero luego borró el número y escribió otro distinto. Es una nueva.
                idTarjetaGuardada = null;
            }
        }

        payload.tarjeta_datos = {
            id_tarjeta: idTarjetaGuardada,
            numero: numStr,
            vencimiento: document.getElementById('ccVencInput')?.value || '',
            titular: document.getElementById('ccNombreInput')?.value || '',
            guardar: document.getElementById('ccGuardarInput')?.checked || false,
            marca: detectarMarcaTarjeta(numStr) || 'Desconocida'
        };
    }

    try {
        // jQuery AJAX
        const data = await $.ajax({
            url: BASE_URL + '/controllers/ProcesarPedidoController.php',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(payload),
            dataType: 'json'
        });

        if (!data.ok) {
            Swal.fire({ icon: 'error', title: 'Error', text: data.mensaje || 'No se pudo procesar el pedido', confirmButtonColor: '#e30613' });
            if (btn) { btn.disabled = false; btn.innerHTML = '<i class="fa fa-lock" style="margin-right:.4rem;"></i> Confirmar pedido'; }
            return;
        }

        // Vaciar carrito local y memoria
        localStorage.removeItem('carrito');
        localStorage.removeItem('carrito_invitado');
        window._carritoCache = [];
        const counter = document.querySelector('.cart-count');
        if (counter) counter.textContent = '0';

        // Ocultar checkout, mostrar comprobante
        document.getElementById('checkoutContenido').style.display = 'none';
        document.getElementById('checkoutExito')?.style && (document.getElementById('checkoutExito').style.display = 'none');

        llenarComprobante(data, carrito, {
            subtotalOrig, subtotalDesc, descuento, igv, envio, total
        });

        document.getElementById('comprobanteWrap').style.display = 'block';
        window.scrollTo({ top: 0, behavior: 'smooth' });

    } catch (err) {
        console.error(err);
        Swal.fire({ icon: 'error', title: 'Detalle al procesar', text: 'Ocurrió un detalle al procesar: ' + (err.message || err), confirmButtonColor: '#e30613' });
        if (btn) { btn.disabled = false; btn.innerHTML = '<i class="fa fa-lock" style="margin-right:.4rem;"></i> Confirmar pedido'; }
    }
}

/* ── Llenar comprobante ──────────────────────────────────────── */
function llenarComprobante(data, carrito, totales) {
    const comp = data.comprobante;

    document.getElementById('compSerieNum').textContent =
        `${comp.serie}-${comp.numero}`;
    document.getElementById('compFecha').textContent =
        new Date().toLocaleDateString('es-PE', { day: '2-digit', month: '2-digit', year: 'numeric' });
    document.getElementById('compPedido').textContent = `#${data.id_pedido}`;
    const compNum = document.getElementById('compNumPedido');
    if (compNum) compNum.textContent = data.id_pedido;

    // Cliente: nombre desde inputs o session
    let nombre = (document.getElementById('dirNombres')?.value || '').trim();
    let apellidos = (document.getElementById('dirApellidos')?.value || '').trim();
    let nombreCompleto = nombre ? `${nombre} ${apellidos}`.trim() : window._checkoutNombre || 'Invitado';

    const dni = document.getElementById('dirDNI')?.value || window._checkoutDNI || 'N/A';
    const telefono = document.getElementById('dirTelefono')?.value || window._checkoutTelefono || 'N/A';

    let direccion = 'Recojo en tienda';
    if (metodoEntrega === 'domicilio') {
        let dirInput = document.getElementById('dirDireccion')?.value;
        let dirSelect = document.querySelector('.dir-opcion.selected')?.dataset?.direccion;
        direccion = dirInput || dirSelect || 'N/A';
    }

    let pago = '';
    const selectedPago = document.querySelector('input[name="pago"]:checked');
    if (selectedPago) {
        // En checkout.php el texto está en un span, no h4
        const spanText = selectedPago.closest('label').querySelector('span');
        if (spanText) pago = spanText.textContent;
    }

    document.getElementById('compCliente').textContent = nombreCompleto;
    document.getElementById('compDni').textContent = dni;

    const filaDireccion = document.getElementById('compFilaDireccion');
    if (metodoEntrega === 'domicilio') {
        if (filaDireccion) filaDireccion.style.display = 'flex';
        document.getElementById('compDireccion').textContent = direccion;
    } else {
        if (filaDireccion) filaDireccion.style.display = 'none';
    }

    document.getElementById('compTelefono').textContent = telefono;
    document.getElementById('compEntrega').textContent = metodoEntrega === 'domicilio' ? 'Delivery' : 'Recojo en tienda';
    document.getElementById('compPago').textContent = pago || 'Efectivo';

    // Items
    const tbody = document.getElementById('compItems');
    tbody.innerHTML = '';
    carrito.forEach(item => {
        const sub = (item.precio * item.cantidad).toFixed(2);
        tbody.innerHTML += `
            <tr>
                <td>${item.nombre}</td>
                <td style="text-align:center">${item.cantidad}</td>
                <td>S/ ${parseFloat(item.precio).toFixed(2)}</td>
                <td>S/ ${sub}</td>
            </tr>`;
    });

    // Totales
    document.getElementById('compSubtotal').textContent = `S/ ${totales.subtotalOrig.toFixed(2)}`;
    document.getElementById('compIgv').textContent = `S/ ${totales.igv.toFixed(2)}`;
    const filaEnvioComp = document.getElementById('compFilaEnvio');
    if (metodoEntrega === 'domicilio') {
        filaEnvioComp.style.display = 'flex';
        document.getElementById('compEnvio').textContent = `S/ ${totales.envio.toFixed(2)}`;
    } else {
        filaEnvioComp.style.display = 'none';
    }
    document.getElementById('compTotal').textContent = `S/ ${totales.total.toFixed(2)}`;

    const filaDesc = document.getElementById('compFilaDesc');
    if (totales.descuento > 0.001) {
        filaDesc.style.display = 'flex';
        document.getElementById('compDescuento').textContent = `- S/ ${totales.descuento.toFixed(2)}`;
    } else {
        filaDesc.style.display = 'none';
    }

    // Vuelto (solo pago en efectivo)
    const filaVuelto = document.getElementById('compFilaVuelto');
    if (metodoPago === 'efectivo' && efectivoMontoPagado !== null) {
        filaVuelto.style.display = 'flex';
        document.getElementById('compVuelto').textContent =
            `Paga con S/ ${efectivoMontoPagado.toFixed(2)} → vuelto S/ ${efectivoVueltoCalc.toFixed(2)}`;
    } else if (filaVuelto) {
        filaVuelto.style.display = 'none';
    }
}
