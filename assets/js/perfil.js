// ===============================
// PERFIL COMPLETO
// ===============================

document.addEventListener("DOMContentLoaded", function () {
    configurarNavegacion();
    configurarValidaciones();
    configurarAutoHideAlerts();
    inicializarSeccion();
});

/**
 * ============================================================================
 * SCRIPT DE GESTIÓN DE PERFIL DEL USUARIO
 * ============================================================================
 * 
 * Controlador en el frontend (SPA parcial) que administra el perfil del cliente.
 * Se encarga de:
 * - Navegación asíncrona entre secciones (Mis Datos, Pedidos, Soporte).
 * - Carga de pedidos vía AJAX sin recargar la página.
 * - Validación y envío de datos personales y direcciones.
 * - Gestión de estado (cancelación dinámica) y renderizado de respuestas.
 */

// Se ejecuta cuando el DOM está listo
// Detectar cambio de hash (#pedidos, #direcciones, etc.)
window.addEventListener("hashchange", function () {
    const hash = window.location.hash.replace("#", "");

    if (
        hash === "direcciones" ||
        hash === "gestion" ||
        hash === "pedidos" ||
        hash === "mensajes"
    ) {
        cambiarSeccion(hash);
    }
});
// ===============================
// SECCIÓN ACTUAL
// ===============================

function inicializarSeccion() {
    setTimeout(() => {
        const hash = window.location.hash.replace("#", "");

        if (
            hash === "direcciones" ||
            hash === "gestion" ||
            hash === "pedidos" ||
            hash === "mensajes"
        ) {
            cambiarSeccion(hash);
        } else {
            cambiarSeccion("perfil");
        }
    }, 50);
}

// ===============================
// MENÚ LATERAL
// ===============================

function configurarNavegacion() {
    const menuLinks = document.querySelectorAll(".perfil-menu-link[data-seccion]");

    menuLinks.forEach(link => {
        link.addEventListener("click", function (e) {
            e.preventDefault();

            const seccion = this.dataset.seccion;
            cambiarSeccion(seccion);
        });
    });
}

function cambiarSeccion(nombreSeccion) {
    document.querySelectorAll(".perfil-seccion-contenido").forEach(sec => {
        sec.style.display = "none";
    });

    document.querySelectorAll(".perfil-menu-link").forEach(link => {
        link.classList.remove("active");
    });

    const seccionMostrar = document.getElementById(
        "seccion" + capitalize(nombreSeccion)
    );

    if (seccionMostrar) {
        seccionMostrar.style.display = "block";
    }

    const linkActivo = document.querySelector(
        `.perfil-menu-link[data-seccion="${nombreSeccion}"]`
    );

    if (linkActivo) {
        linkActivo.classList.add("active");
    }

    actualizarTitulos(nombreSeccion);

    if (nombreSeccion === 'mensajes') {
        cargarMensajesSoporte();
    }

    // IMPORTANTE
    window.location.hash = nombreSeccion;
}

// ===============================
// TITULOS
// ===============================

function actualizarTitulos(seccion) {
    const titulo = document.getElementById("mainTitle");
    const subtitulo = document.getElementById("mainSubtitle");

    const data = {
        perfil: ["Perfil", "Actualiza tus datos personales"],
        direcciones: ["Direcciones", "Administra tus direcciones"],
        gestion: ["Gestión de cuenta", "Seguridad y contraseña"],
        pedidos: ["Pedidos", "Historial de compras"],
        mensajes: ["Mis Mensajes", "Tu historial de consultas con soporte"]
    };

    if (titulo && data[seccion]) titulo.textContent = data[seccion][0];
    if (subtitulo && data[seccion]) subtitulo.textContent = data[seccion][1];
}

// ===============================
// FORM DIRECCIONES
// ===============================
function mostrarFormularioDireccion() {
    const form = document.getElementById("formularioDireccion");

    // Resetear a modo nueva dirección
    document.getElementById("accionDireccion").value = "guardarDireccion";
    document.getElementById("dirId").value = "";

    const formInterno = document.getElementById("formNuevaDireccion");
    if (formInterno) formInterno.reset();

    if (form) {
        form.style.display = "block";
        form.scrollIntoView({
            behavior: "smooth",
            block: "start"
        });
    }
}

function ocultarFormularioDireccion() {
    const form = document.getElementById("formularioDireccion");
    const formInterno = document.getElementById("formNuevaDireccion");

    if (form) form.style.display = "none";
    if (formInterno) formInterno.reset();
}

// ===============================
// PASSWORD VISIBILITY
// ===============================

function togglePasswordVisibility(inputId) {
    const input = document.getElementById(inputId);

    if (!input) return;

    const boton = input.nextElementSibling;
    const icono = boton ? boton.querySelector("i") : null;

    if (input.type === "password") {
        input.type = "text";

        if (icono) {
            icono.classList.replace("fa-eye", "fa-eye-slash");
        }
    } else {
        input.type = "password";

        if (icono) {
            icono.classList.replace("fa-eye-slash", "fa-eye");
        }
    }
}

// ===============================
// LIMPIAR PASSWORD FORM
// ===============================

function limpiarFormPassword() {
    const form = document.getElementById("formCambiarPassword");

    if (form) {
        form.reset();
    }

    cambiarSeccion("perfil");
}

// ===============================
// VALIDACIONES
// ===============================

function configurarValidaciones() {
    const formPerfil = document.getElementById("perfilForm");
    const formPassword = document.getElementById("formCambiarPassword");

    if (formPerfil) {
        formPerfil.addEventListener("submit", validarPerfil);
    }

    if (formPassword) {
        formPassword.addEventListener("submit", validarPassword);
    }
}

function validarPerfil(e) {
    const telefono = document.getElementById("perfilTelefono");

    if (telefono && telefono.value.length > 0) {
        if (!/^[0-9+\s-]+$/.test(telefono.value)) {
            e.preventDefault();
            mostrarAlerta("Teléfono inválido", "error");
            return;
        }
    }
}

function validarPassword(e) {
    const actual = document.getElementById("passwordActual");
    const nueva = document.getElementById("passwordNueva");
    const confirmar = document.getElementById("passwordConfirmar");

    if (!actual.value || !nueva.value || !confirmar.value) {
        e.preventDefault();
        mostrarAlerta("Completa todos los campos", "error");
        return;
    }

    if (nueva.value.length < 6) {
        e.preventDefault();
        mostrarAlerta("La nueva contraseña debe tener al menos 6 caracteres", "error");
        return;
    }

    if (nueva.value !== confirmar.value) {
        e.preventDefault();
        mostrarAlerta("Las contraseñas no coinciden", "error");
        return;
    }
}

// ===============================
// ALERTAS
// ===============================

function mostrarAlerta(mensaje, tipo = "success") {
    const alerta = document.createElement("div");

    alerta.className = `perfil-alerta ${tipo}`;
    alerta.textContent = mensaje;

    document.body.appendChild(alerta);

    setTimeout(() => {
        alerta.classList.add("show");
    }, 100);

    setTimeout(() => {
        alerta.remove();
    }, 3000);
}

function configurarAutoHideAlerts() {
    const alertas = document.querySelectorAll(".alert");

    if (alertas.length > 0) {
        setTimeout(() => {
            alertas.forEach(alerta => {
                alerta.style.display = "none";
            });
        }, 4000);
    }
}

// ===============================
// UTILIDADES
// ===============================

function volverPaginaAnterior() {
    window.history.back();
}

function capitalize(texto) {
    return texto.charAt(0).toUpperCase() + texto.slice(1);
}

// ===============================
// EXPORT PDF PEDIDOS (si usas jsPDF)
// ===============================

/* ── Cargar pedidos desde BD ─────────────────────────────────── */
async function cargarPedidos() {
    const lista  = document.getElementById('listaPedidos');
    const empty  = document.getElementById('pedidosEmpty');
    if (!lista) return;

    lista.innerHTML = '<p style="color:#aaa;text-align:center;padding:20px;">Cargando pedidos...</p>';

    try {
        // jQuery AJAX
        const data = await $.ajax({
            url: BASE_URL + '/controllers/GetPedidosController.php',
            method: 'GET',
            dataType: 'json'
        });

        if (!data.ok || data.pedidos.length === 0) {
            lista.innerHTML = '';
            if (empty) empty.style.display = 'block';
            return;
        }

        // Guardar datos en window para filtrar y PDF
        window._pedidosData = data.pedidos;
        renderPedidosCliente(data.pedidos);

    } catch (err) {
        console.error(err);
        lista.innerHTML = '<p style="color:#e30613;text-align:center;padding:20px;">Error al cargar pedidos.</p>';
    }
}

function renderPedidosCliente(pedidos) {
    const lista = document.getElementById('listaPedidos');
    const empty = document.getElementById('pedidosEmpty');
    
    if (!pedidos || pedidos.length === 0) {
        lista.innerHTML = '';
        if (empty) empty.style.display = 'block';
        return;
    }

    if (empty) empty.style.display = 'none';
    lista.innerHTML = '';

    pedidos.forEach(ped => {
        const fecha     = new Date(ped.fecha_pedido).toLocaleDateString('es-PE',
                          { day:'2-digit', month:'2-digit', year:'numeric' });
        const total     = 'S/ ' + parseFloat(ped.total || 0).toFixed(2);
        const descuento = parseFloat(ped.descuento || 0);
        const igv       = parseFloat(ped.comp_igv  || 0);
        const compRef   = ped.comp_serie
            ? `${ped.comp_serie}-${String(ped.comp_numero).padStart(8,'0')}`
            : '—';

        const estadoClass = {
            'Pendiente':       'estado-pendiente',
            'En preparación':  'estado-en-preparacion',
            'En camino':       'estado-en-camino',
            'Entregado':       'estado-entregado',
            'Cancelado':       'estado-cancelado'
        }[ped.estado_pedido] || 'estado-pendiente';

        const itemsHTML = (ped.detalle || []).map(it => `
            <div class="ped-item">
                <img src="${it.imagen || ''}" alt="${it.nombre}"
                     onerror="this.src=BASE_URL + '/img/imagen1.webp'">
                <span class="ped-item-nombre">${it.nombre}</span>
                <span class="ped-item-cant">x${it.cantidad}</span>
                <span class="ped-item-precio">S/ ${parseFloat(it.subtotal).toFixed(2)}</span>
            </div>`).join('');

        lista.innerHTML += `
        <div class="pedido-card" id="pedidoCard${ped.id_pedido}">
            <div class="pedido-head">
                <div>
                    <strong>Pedido #${ped.id_pedido}</strong>
                    <span class="ped-fecha">${fecha}</span>
                </div>
                <span class="ped-estado ${estadoClass}">${ped.estado_pedido || 'Pendiente'}</span>
            </div>

            <div class="ped-items">${itemsHTML}</div>

            <div class="pedido-foot">
                <div class="ped-meta">
                    <span><i class="fa fa-truck"></i> ${ped.tipo_entrega || '—'}</span>
                    <span><i class="fa fa-credit-card"></i> ${ped.metodo_pago || '—'}</span>
                    <span><i class="fa fa-file-invoice"></i> ${compRef}</span>
                </div>
                <div class="ped-totales">
                    ${descuento > 0 ? `<span class="ped-desc">Desc: - S/ ${descuento.toFixed(2)}</span>` : ''}
                    <span class="ped-total">${total}</span>
                    ${ped.estado_pedido === 'Pendiente' 
                        ? `<button class="btn-cancelar-pedido" style="background: #e30613; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 0.85rem; margin-right: 5px;"
                                   onclick="cancelarPedidoCliente(${ped.id_pedido})">
                               <i class="fa fa-times"></i> Cancelar
                           </button>`
                        : ''}
                    ${ped.comp_serie
                        ? `<button class="btn-pdf-pedido"
                                   onclick="descargarComprobante(${ped.id_pedido})">
                               <i class="fa fa-file-pdf"></i> Ver PDF
                           </button>`
                        : ''}
                </div>
            </div>
        </div>`;
    });
}

function filtrarPedidosCliente() {
    const q = (document.getElementById('buscadorPedidosCliente').value || '').toLowerCase();
    const fFecha = document.getElementById('filtroFechaPedidosCliente').value;
    const fEstado = document.getElementById('filtroEstadoPedidosCliente').value;

    renderPedidosCliente(_pedidosFiltrados = window._pedidosData.filter(ped => {
        const txt = (ped.id_pedido + ' ' + (ped.detalle || []).map(i=>i.nombre).join(' ')).toLowerCase();
        const mTxt = !q || txt.includes(q);
        const mFec = !fFecha || ped.fecha_pedido.startsWith(fFecha);
        const mEst = !fEstado || ped.estado_pedido === fEstado;
        return mTxt && mFec && mEst;
    }));
}

function limpiarFiltrosPedidosCliente() {
    const q = document.getElementById('buscadorPedidosCliente');
    if (q) q.value = '';
    document.getElementById('filtroFechaPedidosCliente').value = '';
    document.getElementById('filtroEstadoPedidosCliente').value = '';
    filtrarPedidosCliente();
}

/* ── Descargar comprobante PDF ───────────────────────────────── */
function descargarComprobante(idPedido) {
    window.open(`${BASE_URL}/views/pdf_comprobante.php?id=${idPedido}`, '_blank');
}

function exportarPedidosPDF() {
    // Descarga todos los pedidos como lista (función legacy)
    if (window._pedidosData && window._pedidosData.length > 0) {
        window._pedidosData.forEach(p => descargarComprobante(p.id_pedido));
    }
}

// Cargar pedidos cuando se abre la sección
const _origCambiarSeccion = window.cambiarSeccion;
document.addEventListener('DOMContentLoaded', function () {
    // Si la URL tiene #pedidos, cargar al inicio
    if (window.location.hash === '#pedidos') {
        setTimeout(cargarPedidos, 300);
    }
    // Interceptar clic en "Pedidos" del menú lateral
    document.querySelectorAll('[data-seccion="pedidos"]').forEach(el => {
        el.addEventListener('click', () => setTimeout(cargarPedidos, 100));
    });
});

window.descargarComprobante = descargarComprobante;
window.cargarPedidos = cargarPedidos;

// ===============================
// EDITAR DIRECCIÓN
// ===============================

function editarDireccion(direccion) {
    const form = document.getElementById("formularioDireccion");
    if (!form) return;

    // Forzar abrir sección Direcciones
    cambiarSeccion("direcciones");

    document.getElementById("accionDireccion").value = "editarDireccion";
    document.getElementById("dirId").value = direccion.id_direccion;
    document.getElementById("dirEtiqueta").value = direccion.etiqueta;
    document.getElementById("dirCompleta").value = direccion.direccion;
    document.getElementById("dirReferencia").value =
        direccion.referencia || "";

    document.getElementById("dirPredeterminada").checked =
        direccion.predeterminada == 1;

    form.style.display = "block";

    form.scrollIntoView({
        behavior: "smooth",
        block: "start"
    });
}

window.editarDireccion = editarDireccion;

// ===============================
// GLOBAL
// ===============================

window.cambiarSeccion = cambiarSeccion;
window.mostrarFormularioDireccion = mostrarFormularioDireccion;
window.ocultarFormularioDireccion = ocultarFormularioDireccion;

// ===============================
// SOPORTE (MIS MENSAJES)
// ===============================
let soporteGlobalCliente = [];

function cargarMensajesSoporte() {
    const cont = document.getElementById('listaMensajesSoporte');
    if (!cont) return;

    cont.innerHTML = '<p>Cargando tus mensajes...</p>';

    fetch(BASE_URL + '/controllers/SoporteClienteController.php?accion=mis_mensajes')
        .then(res => res.json())
        .then(data => {
            if (!data.success) {
                cont.innerHTML = `<p style="color:red;">Error: ${data.message}</p>`;
                return;
            }

            soporteGlobalCliente = data.data;
            populateMotivosDropdownCliente(soporteGlobalCliente);
            renderizarListaMensajes(soporteGlobalCliente);
        })
        .catch(err => {
            cont.innerHTML = '<p style="color:red;">Error de conexión.</p>';
        });
}

let pedidoACancelar = null;

function cancelarPedidoCliente(id_pedido) {
    pedidoACancelar = id_pedido;
    const modal = document.getElementById('modalCancelarPedido');
    if(modal) {
        modal.style.display = 'flex';
        document.getElementById('motivoCancelacionText').value = ''; // clear previous
        
        // Asignar evento al botón de confirmar
        const btnConfirmar = document.getElementById('btnConfirmarCancelacion');
        btnConfirmar.onclick = () => procesarCancelacionCliente();
    }
}

function cerrarModalCancelar() {
    pedidoACancelar = null;
    const modal = document.getElementById('modalCancelarPedido');
    if(modal) modal.style.display = 'none';
}

async function procesarCancelacionCliente() {
    if (!pedidoACancelar) return;
    
    const motivo = document.getElementById('motivoCancelacionText').value.trim();
    const btn = document.getElementById('btnConfirmarCancelacion');
    btn.innerHTML = 'Cancelando...';
    btn.disabled = true;
    
    try {
        // jQuery AJAX
        const data = await $.ajax({
            url: BASE_URL + '/controllers/CancelarPedidoController.php',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                id_pedido: pedidoACancelar,
                motivo: motivo
            }),
            dataType: 'json'
        });
        
        btn.innerHTML = 'Confirmar Cancelación';
        btn.disabled = false;
        
        if (data.success) {
            Swal.fire({icon: 'success', title: 'Éxito', text: 'El pedido ha sido cancelado exitosamente.', confirmButtonColor: '#e30613'}).then(() => {
                cargarPedidos();
            });
            cerrarModalCancelar();
        } else {
            Swal.fire({icon: 'error', title: 'Error', text: data.message || 'No se pudo cancelar el pedido.', confirmButtonColor: '#e30613'});
        }
    } catch (err) {
        btn.innerHTML = 'Confirmar Cancelación';
        btn.disabled = false;
        Swal.fire({icon: 'error', title: 'Error de conexión', text: 'Error de conexión al intentar cancelar el pedido.', confirmButtonColor: '#e30613'});
    }
}

function renderizarListaMensajes(mensajes) {
    const cont = document.getElementById('listaMensajesSoporte');
    if (!cont) return;

    if (mensajes.length === 0) {
        cont.innerHTML = '<div style="background:#f8fafc; padding:20px; border-radius:8px; text-align:center;"><p>Aún no has enviado consultas de soporte.</p><a href="AtencionCliente.php" style="color:#e30613; font-weight:bold; text-decoration:none;">Crear nueva consulta</a></div>';
        return;
    }

    let html = '';
    mensajes.forEach(m => {
        const esRespondido = m.estado === 'respondido' || m.estado === 'Respondido';
        const badgeStyle = esRespondido 
            ? 'background: #dcfce7; color: #166534; padding: 4px 8px; border-radius: 4px; font-size: 0.8rem; font-weight: bold;' 
            : 'background: #fef9c3; color: #854d0e; padding: 4px 8px; border-radius: 4px; font-size: 0.8rem; font-weight: bold;';

        html += `
            <div style="border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; margin-bottom: 20px; background: white; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px;">
                    <h3 style="margin: 0; color: #0f172a; font-size: 1.1rem;">${m.asunto}</h3>
                    <span style="${badgeStyle}">${m.estado}</span>
                </div>
                <small style="color: #94a3b8; display: block; margin-bottom: 15px;"><i class="far fa-clock"></i> Creado el: ${new Date(m.fecha).toLocaleString()}</small>
                <button class="btn-chat-hilo" onclick='abrirModalChatCliente(${JSON.stringify(m).replace(/'/g, "&#39;")})' style="background: linear-gradient(135deg, #e30613, #b9000b); color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-size: 0.95rem; font-weight: bold; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 6px rgba(227,6,19,0.25); transition: transform 0.2s, box-shadow 0.2s;" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 12px rgba(227,6,19,0.3)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 6px rgba(227,6,19,0.25)';">
                    <i class="fa fa-comments"></i> Abrir Chat
                </button>
            </div>
        `;
    });
    cont.innerHTML = html;
}

function populateMotivosDropdownCliente(mensajes) {
    const select = document.getElementById('filtroMotivoCliente');
    if (!select) return;
    
    const valorActual = select.value;
    
    const motivosEstandar = {
        "pedido": "Consulta sobre pedido",
        "producto": "Información de producto",
        "devolucion": "Devolución o cambio",
        "entrega": "Problemas de entrega",
        "facturacion": "Facturación",
        "sugerencia": "Sugerencia",
        "otro": "Otro"
    };
    
    const motivosDB = [...new Set(mensajes.map(m => m.asunto))].filter(m => m && !motivosEstandar[m]);
    
    let html = '<option value="">Todos los motivos</option>';
    
    for (const [val, label] of Object.entries(motivosEstandar)) {
        html += `<option value="${val}">${label}</option>`;
    }
    
    motivosDB.forEach(motivo => {
        html += `<option value="${motivo}">${motivo}</option>`;
    });
    
    select.innerHTML = html;
    select.value = valorActual;
}

function filtrarMensajesCliente() {
    const term = document.getElementById('buscadorMensajesCliente').value.toLowerCase();
    const fecha = document.getElementById('filtroFechaCliente').value;
    const motivo = document.getElementById('filtroMotivoCliente').value;

    const filtrados = soporteGlobalCliente.filter(m => {
        const matchText = 
            (m.asunto && m.asunto.toLowerCase().includes(term)) ||
            (m.mensaje && m.mensaje.toLowerCase().includes(term)) ||
            (m.estado && m.estado.toLowerCase().includes(term));
            
        let matchFecha = true;
        if (fecha) {
            const fechaMsj = new Date(m.fecha).toISOString().split('T')[0];
            matchFecha = (fechaMsj === fecha);
        }
        
        let matchMotivo = true;
        if (motivo) {
            if (motivo === 'otro') {
                const motivosEstandarKeys = ['pedido', 'producto', 'devolucion', 'entrega', 'facturacion', 'sugerencia', 'otro'];
                matchMotivo = !motivosEstandarKeys.includes(m.asunto);
            } else {
                matchMotivo = (m.asunto === motivo);
            }
        }
        
        return matchText && matchFecha && matchMotivo;
    });
    renderizarListaMensajes(filtrados);
}

function limpiarFiltrosClienteSoporte() {
    document.getElementById('buscadorMensajesCliente').value = '';
    document.getElementById('filtroFechaCliente').value = '';
    document.getElementById('filtroMotivoCliente').value = '';
    filtrarMensajesCliente();
}

function abrirModalChatCliente(m) {
    document.getElementById('chatClienteId').value = m.id;
    document.getElementById('chatClienteAsunto').textContent = m.asunto;
    
    const historial = document.getElementById('chatClienteHistorial');
    historial.innerHTML = '<div style="text-align: center; color: #94a3b8;">Cargando mensajes...</div>';
    document.getElementById('chatClienteRespuesta').value = '';
    
    document.getElementById('modalChatCliente').style.display = 'flex';

    fetch(`${BASE_URL}/controllers/SoporteClienteController.php?accion=obtener_hilo&id=${m.id}`)
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                renderizarHiloCliente(data.data);
            } else {
                historial.innerHTML = `<div style="text-align:center; color:red;">${data.message}</div>`;
            }
        })
        .catch(() => {
            historial.innerHTML = '<div style="text-align:center; color:red;">Error de conexión.</div>';
        });
}

function renderizarHiloCliente(mensajes) {
    const historial = document.getElementById('chatClienteHistorial');
    historial.innerHTML = '';
    
    mensajes.forEach(msg => {
        const esCliente = msg.remitente === 'cliente';
        const bg = esCliente ? '#e30613' : '#e2e8f0'; // Red for client, light gray-blue for admin
        const color = esCliente ? '#ffffff' : '#0f172a';
        const align = esCliente ? 'flex-end' : 'flex-start';
        const borderRadius = esCliente ? '20px 20px 0 20px' : '20px 20px 20px 0';
        const timeColor = esCliente ? 'rgba(255,255,255,0.7)' : '#64748b';
        
        const div = document.createElement('div');
        div.style.alignSelf = align;
        div.style.background = bg;
        div.style.color = color;
        div.style.padding = '12px 18px';
        div.style.borderRadius = borderRadius;
        div.style.maxWidth = '80%';
        div.style.boxShadow = '0 3px 6px rgba(0,0,0,0.08)';
        
        div.innerHTML = `
            <div style="margin-bottom: 5px; line-height: 1.4;">${msg.mensaje.replace(/\n/g, '<br>')}</div>
            <div style="font-size: 0.75em; text-align: right; color: ${timeColor};">${new Date(msg.fecha).toLocaleString()}</div>
        `;
        historial.appendChild(div);
    });
    
    // Privacy / Acceptance rule: Only allow interaction if admin has responded at least once
    const tieneRespuestaAdmin = mensajes.some(m => m.remitente === 'admin');
    const txtRespuesta = document.getElementById('chatClienteRespuesta');
    const btnResponder = document.getElementById('btnChatClienteResponder');
    
    if (tieneRespuestaAdmin) {
        txtRespuesta.disabled = false;
        btnResponder.disabled = false;
        txtRespuesta.placeholder = 'Escribe tu mensaje...';
        btnResponder.style.opacity = '1';
    } else {
        txtRespuesta.disabled = true;
        btnResponder.disabled = true;
        txtRespuesta.placeholder = 'Esperando a que un administrador responda...';
        btnResponder.style.opacity = '0.5';
    }

    historial.scrollTop = historial.scrollHeight;
}

function cerrarModalChatCliente() {
    document.getElementById('modalChatCliente').style.display = 'none';
}

document.addEventListener('DOMContentLoaded', () => {
    const formChat = document.getElementById('formChatCliente');
    if (formChat) {
        formChat.addEventListener('submit', (e) => {
            e.preventDefault();
            const id = document.getElementById('chatClienteId').value;
            const respuesta = document.getElementById('chatClienteRespuesta').value.trim();
            const btn = document.getElementById('btnChatClienteResponder');

            if (!respuesta) return;

            btn.disabled = true;
            const originalIcon = btn.innerHTML;
            btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i>';

            const formData = new FormData();
            formData.append('accion', 'responder');
            formData.append('id', id);
            formData.append('respuesta', respuesta);

            fetch(BASE_URL + '/controllers/SoporteClienteController.php', {
                method: 'POST',
                body: formData
            })
            .then(r => r.json())
            .then(data => {
                btn.disabled = false;
                btn.innerHTML = originalIcon;
                if (data.success) {
                    document.getElementById('chatClienteRespuesta').value = '';
                    fetch(`${BASE_URL}/controllers/SoporteClienteController.php?accion=obtener_hilo&id=${id}`)
                        .then(r => r.json())
                        .then(hilo => { if(hilo.success) renderizarHiloCliente(hilo.data); });
                    cargarMensajesSoporte();
                } else {
                    Swal.fire({icon: 'warning', title: 'Atención', text: data.message, confirmButtonColor: '#e30613'});
                }
            })
            .catch(() => {
                btn.disabled = false;
                btn.innerHTML = originalIcon;
                Swal.fire({icon: 'error', title: 'Error', text: 'Ocurrió un error.', confirmButtonColor: '#e30613'});
            });
        });
    }
});
window.filtrarMensajesCliente = filtrarMensajesCliente;
window.limpiarFiltrosClienteSoporte = limpiarFiltrosClienteSoporte;
window.abrirModalChatCliente = abrirModalChatCliente;
window.cerrarModalChatCliente = cerrarModalChatCliente;
window.cargarMensajesSoporte = cargarMensajesSoporte;
window.togglePasswordVisibility = togglePasswordVisibility;
window.limpiarFormPassword = limpiarFormPassword;
window.volverPaginaAnterior = volverPaginaAnterior;
window.exportarPedidosPDF = exportarPedidosPDF;