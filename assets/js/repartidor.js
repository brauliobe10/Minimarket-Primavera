/* repartidor.js — versión con sidebar y secciones */

/* ─── Sidebar toggle (móvil) ─────────────────────── */
function toggleSidebar() {
    const sidebar  = document.getElementById('repSidebar');
    const overlay  = document.getElementById('repOverlay');
    const isOpen   = sidebar.classList.contains('open');
    sidebar.classList.toggle('open', !isOpen);
    overlay.classList.toggle('open', !isOpen);
}

/* ─── Init ─────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', () => {
    // Escuchar hashchange para navegación
    window.addEventListener('hashchange', () => {
        const hash = window.location.hash.replace('#', '');
        if (SECTION_TITLES[hash]) {
            cambiarSeccion(hash);
        }
    });

    const hash = window.location.hash.replace('#', '');
    if (SECTION_TITLES[hash]) {
        cambiarSeccion(hash);
    } else {
        cambiarSeccion('disponibles');
    }
    
    verificarPerfilRepartidor();
    cargarPanel();
    
    // Auto-refresh cada 60s
    setInterval(cargarPanel, 60000);
});

/* ─── Cambiar sección activa ─────────────────────── */
const SECTION_TITLES = {
    disponibles: 'Pedidos Disponibles',
    activas:     'Mis Entregas Activas',
    asignados:   'Pedidos Asignados',
    historial:   'Historial de Entregas',
};

function cambiarSeccion(key) {
    // Ocultar todas las secciones
    document.querySelectorAll('.rep-section-content').forEach(s => s.classList.remove('active'));
    // Quitar active de nav items
    document.querySelectorAll('.rep-nav-item').forEach(b => b.classList.remove('active'));

    // Activar sección
    const sec = document.getElementById('sec-' + key);
    if (sec) sec.classList.add('active');

    // Activar nav item
    const btn = document.querySelector(`.rep-nav-item[data-section="${key}"]`);
    if (btn) btn.classList.add('active');

    // Actualizar título del topbar
    const titleEl = document.getElementById('repPageTitle');
    if (titleEl) titleEl.textContent = SECTION_TITLES[key] || key;

    // Cerrar sidebar en móvil
    if (window.innerWidth <= 900) toggleSidebar();
    
    // Guardar en hash
    window.location.hash = key;
}

/* ─── Actualizar badge de nav ─────────────────────── */
function actualizarBadge(id, count) {
    const el = document.getElementById(id);
    if (!el) return;
    if (count > 0) {
        el.textContent = count;
        el.style.display = '';
    } else {
        el.style.display = 'none';
    }
}

/* ─── Renderizar lista en su contenedor ────────────── */
function renderLista(containerId, tarjetas, emptyIcon, emptyText) {
    const cont = document.getElementById(containerId);
    if (!cont) return;
    if (!tarjetas || tarjetas.length === 0) {
        cont.innerHTML = `
            <div class="rep-empty-state">
                <i class="fa ${emptyIcon}"></i>
                <p>${emptyText}</p>
            </div>`;
        return;
    }
    const frag = document.createDocumentFragment();
    tarjetas.forEach(t => frag.appendChild(t));
    cont.replaceChildren(frag);
}

/* ─── Cargar panel completo ───────────────────────── */
async function cargarPanel() {
    // Spin del botón de refresh
    const refreshBtn = document.querySelector('.rep-refresh-btn');
    if (refreshBtn) refreshBtn.classList.add('spinning');

    let data;
    try {
        const resp = await fetch(BASE_URL + '/controllers/PanelRepartidorController.php');
        data = await resp.json();
    } catch (err) {
        ['listDisponibles','listActivas','listAsignados','listHistorial'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.innerHTML = `<div class="rep-empty-state"><i class="fa fa-wifi"></i><p>Error de conexión. Intenta actualizar.</p></div>`;
        });
        console.error('Error cargando panel de repartidor:', err);
        if (refreshBtn) refreshBtn.classList.remove('spinning');
        return;
    } finally {
        if (refreshBtn) refreshBtn.classList.remove('spinning');
    }

    if (!data.ok) {
        ['listDisponibles','listActivas','listAsignados','listHistorial'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.innerHTML = `<div class="rep-empty-state"><i class="fa fa-circle-exclamation"></i><p>${data.mensaje || 'Error al cargar'}</p></div>`;
        });
        return;
    }

    // Stats
    const s = data.stats || {};
    const set = (id, val) => { const el = document.getElementById(id); if (el) el.textContent = val; };
    set('repTotal',      s.total      || 0);
    set('repEnCamino',   s.en_camino  || 0);
    set('repPendientes', s.pendientes || 0);
    set('repEntregados', s.entregados || 0);
    set('repGanancias',  `S/ ${parseFloat(s.ganancias || 0).toFixed(2)}`);

    // Aviso DNI
    const dniWarning = document.getElementById('repDniWarning');
    if (dniWarning) dniWarning.style.display = data.dni_valido ? 'none' : 'flex';

    // Clasificar deliveries
    const lista      = data.deliveries  || [];
    const disponibles = data.disponibles || [];
    const asignados   = lista.filter(d => d.estado_delivery === 'Asignado');
    const enCamino    = lista.filter(d => d.estado_delivery === 'En camino');
    const entregados  = lista.filter(d => d.estado_delivery === 'Entregado');
    window._historialCache = entregados;

    // Actualizar badges de sidebar
    actualizarBadge('badgeDisponibles', disponibles.length);
    actualizarBadge('badgeActivas',     enCamino.length);
    actualizarBadge('badgeAsignados',   asignados.length);
    actualizarBadge('badgeHistorial',   entregados.length);

    // Renderizar cada sección
    renderLista(
        'listDisponibles',
        disponibles.map((d, index) => crearCardDisponible(d, index, data.dni_valido, enCamino.length > 0)),
        'fa-bell-slash', 'No hay pedidos disponibles por ahora'
    );
    renderLista(
        'listActivas',
        enCamino.map((d, index) => crearCard(d, index, data.dni_valido, false)),
        'fa-truck-fast', 'Sin entregas activas en este momento'
    );
    renderLista(
        'listAsignados',
        asignados.map((d, index) => crearCard(d, index, data.dni_valido, enCamino.length > 0)),
        'fa-box-open', 'El admin no te ha asignado pedidos aún'
    );
    renderHistorial(entregados, data.dni_valido);

    // Si hay pedidos asignados, resaltar esa sección en nav
    if (asignados.length > 0) {
        const navAsignados = document.querySelector('.rep-nav-item[data-section="asignados"]');
        if (navAsignados && !navAsignados.classList.contains('active')) {
            navAsignados.style.animation = 'none';
        }
    }
}

function renderHistorial(historial, dniValido = true) {
    if (!historial) return;
    const filtroFecha = document.getElementById('filtroFechaHistorial');
    let dataFiltrada = historial;
    
    if (filtroFecha && filtroFecha.value) {
        dataFiltrada = historial.filter(d => d.hora_entrega && d.hora_entrega.startsWith(filtroFecha.value));
    }
    
    renderLista(
        'listHistorial',
        dataFiltrada.map((d, index) => crearCard(d, index, dniValido, false)),
        'fa-clock-rotate-left', 'Sin historial de entregas para esta fecha'
    );
}

/* ─── Fila de pago ───────────────────────────────── */
function filaPago(d) {
    if (!d.metodo_pago) return '';
    const icono = { 'Efectivo': '💵', 'Tarjeta de Crédito/Débito': '💳', 'Yape / Plin': '📱' }[d.metodo_pago] || '💰';
    const esEfectivo = d.metodo_pago === 'Efectivo' && d.monto_recibido;
    return `
        <div class="rep-info-row${esEfectivo ? ' rep-pago-efectivo' : ''}">
            <span style="font-size:1rem;line-height:1;">${icono}</span>
            <div>
                <strong>${d.metodo_pago}</strong>
                ${esEfectivo ? `<br>Cliente paga con <strong>S/ ${parseFloat(d.monto_recibido).toFixed(2)}</strong> →
                    lleva vuelto de <strong>S/ ${parseFloat(d.vuelto || 0).toFixed(2)}</strong>` : ''}
            </div>
        </div>`;
}

/* ─── Card disponible ────────────────────────────── */
function crearCardDisponible(d, index, dniValido, tieneActiva) {
    const div = document.createElement('div');
    div.className = 'rep-card rep-card-disponible';
    div.innerHTML = `
        <div class="rep-card-head">
            <strong>N° ${index + 1} · Delivery #${d.id_delivery} — Pedido #${d.id_pedido}</strong>
            <span class="rep-badge rep-badge-disponible">Disponible</span>
        </div>
        <div class="rep-card-body">
            <div class="rep-info-row">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 1 1 18 0z"/>
                    <circle cx="12" cy="10" r="3"/>
                </svg>
                <div><strong>${d.direccion_entrega}</strong>
                    ${d.referencia ? `<br><span style="color:#888;font-size:.8rem;">${d.referencia}</span>` : ''}
                </div>
            </div>
            <div class="rep-info-row">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                    <circle cx="12" cy="7" r="4"/>
                </svg>
                <span>${d.cliente || 'Sin nombre'} ${d.id_usuario_pedido === null ? '<span style="background: #e2e8f0; color: #475569; padding: 2px 6px; border-radius: 4px; font-size: 0.75rem; margin-left: 5px; font-weight: bold;">Invitado</span>' : ''}</span>
            </div>
            <div class="rep-info-row">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="1" x2="12" y2="23"/>
                    <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                </svg>
                <span>Total: <strong>S/ ${parseFloat(d.total_pedido||0).toFixed(2)}</strong>
                &nbsp;·&nbsp; Delivery: <strong>S/ ${parseFloat(d.costo_delivery||0).toFixed(2)}</strong></span>
            </div>
            ${filaPago(d)}
        </div>`;

    const actions = document.createElement('div');
    actions.className = 'rep-card-actions';

    const btnTomar = document.createElement('button');
    btnTomar.className = 'rep-btn rep-btn-primary';
    btnTomar.innerHTML = `<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/>
        <circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/>
    </svg> Tomar entrega`;
    if (!dniValido || tieneActiva) {
        btnTomar.disabled = true;
        btnTomar.title = !dniValido
            ? 'Completa tu DNI en tu perfil para poder tomar pedidos'
            : 'Ya tienes una entrega en camino. Entrégala antes de tomar otra.';
    } else {
        btnTomar.onclick = () => accionDelivery('tomar_delivery', d.id_delivery, btnTomar);
    }

    const btnMaps = document.createElement('a');
    btnMaps.className = 'rep-btn rep-btn-ghost';
    btnMaps.href = `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(d.direccion_entrega)}`;
    btnMaps.target = '_blank';
    btnMaps.innerHTML = `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 1 1 18 0z"/>
        <circle cx="12" cy="10" r="3"/>
    </svg> Ver mapa`;

    actions.appendChild(btnTomar);
    actions.appendChild(btnMaps);
    div.appendChild(actions);
    return div;
}

/* ─── Card estándar (asignado / en camino / entregado) ── */
function crearCard(d, index, dniValido, tieneActiva) {
    const div = document.createElement('div');
    const estadoKey = (d.estado_delivery || '').toLowerCase().replace(/ /g, '-');
    div.className = `rep-card estado-${estadoKey}`;

    const badgeClass = {
        'Asignado':  'rep-badge-asignado',
        'En camino': 'rep-badge-en-camino',
        'Entregado': 'rep-badge-entregado'
    }[d.estado_delivery] || 'rep-badge-pendiente';

    const etiquetaEstado = d.estado_delivery === 'Asignado' ? 'Esperando tu respuesta' : d.estado_delivery;

    const horaSalida  = d.hora_salida  ? new Date(d.hora_salida).toLocaleTimeString('es-PE',{hour:'2-digit',minute:'2-digit'}) : '—';
    const horaEntrega = d.hora_entrega ? new Date(d.hora_entrega).toLocaleTimeString('es-PE',{hour:'2-digit',minute:'2-digit'}) : '—';

    div.innerHTML = `
        <div class="rep-card-head">
            <strong>N° ${index + 1} · Delivery #${d.id_delivery} — Pedido #${d.id_pedido}</strong>
            <span class="rep-badge ${badgeClass}">${etiquetaEstado}</span>
        </div>
        <div class="rep-card-body">
            <div class="rep-info-row">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 1 1 18 0z"/>
                    <circle cx="12" cy="10" r="3"/>
                </svg>
                <div><strong>${d.direccion_entrega}</strong>
                    ${d.referencia ? `<br><span style="color:#888;font-size:.8rem;">${d.referencia}</span>` : ''}
                </div>
            </div>
            <div class="rep-info-row">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                    <circle cx="12" cy="7" r="4"/>
                </svg>
                <div>${d.cliente || 'Sin nombre'} ${d.id_usuario_pedido === null ? '<span style="background: #e2e8f0; color: #475569; padding: 2px 6px; border-radius: 4px; font-size: 0.75rem; margin-left: 5px; font-weight: bold;">Invitado</span>' : ''} ${d.tel_cliente
                    ? `· <a href="tel:${d.tel_cliente}" style="color:#3b82f6;">${d.tel_cliente}</a>` : ''}</div>
            </div>
            <div class="rep-info-row">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="12" y1="1" x2="12" y2="23"/>
                    <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                </svg>
                <div>Total: <strong>S/ ${parseFloat(d.total_pedido||0).toFixed(2)}</strong>
                &nbsp;·&nbsp; Delivery: <strong>S/ ${parseFloat(d.costo_delivery||0).toFixed(2)}</strong></div>
            </div>
            ${filaPago(d)}
            ${d.hora_salida || d.hora_entrega ? `
            <div class="rep-info-row">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                </svg>
                <div>
                    ${d.hora_salida  ? `Salida: <strong>${horaSalida}</strong>` : ''}
                    ${d.hora_entrega ? ` · Entrega: <strong>${horaEntrega}</strong>` : ''}
                </div>
            </div>` : ''}
        </div>`;

    if (d.estado_delivery !== 'Entregado') {
        const actions = document.createElement('div');
        actions.className = 'rep-card-actions';

        if (d.estado_delivery === 'Asignado') {
            const btnAceptar = document.createElement('button');
            btnAceptar.className = 'rep-btn rep-btn-success';
            btnAceptar.innerHTML = `<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="20 6 9 17 4 12"/>
            </svg> Aceptar`;
            if (!dniValido || tieneActiva) {
                btnAceptar.disabled = true;
                btnAceptar.title = !dniValido
                    ? 'Completa tu DNI en tu perfil para poder aceptar pedidos'
                    : 'Ya tienes una entrega en camino. Entrégala antes de aceptar otra.';
            } else {
                btnAceptar.onclick = () => accionDelivery('aceptar_delivery', d.id_delivery, btnAceptar);
            }
            actions.appendChild(btnAceptar);

            const btnRechazar = document.createElement('button');
            btnRechazar.className = 'rep-btn rep-btn-danger';
            btnRechazar.innerHTML = `<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
            </svg> Rechazar`;
            btnRechazar.onclick = () => {
                const motivo = prompt(
                    '¿Por qué rechazas este pedido? (el admin verá este motivo)',
                    'Ocupado con otro pedido'
                );
                if (motivo === null) return;
                accionDelivery('rechazar_delivery', d.id_delivery, btnRechazar, { motivo });
            };
            actions.appendChild(btnRechazar);
        }

        if (d.estado_delivery === 'En camino') {
            const btn = document.createElement('button');
            btn.className = 'rep-btn rep-btn-success';
            btn.innerHTML = `<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="20 6 9 17 4 12"/>
            </svg> Marcar entregado`;
            btn.onclick = () => accionDelivery('entregar', d.id_delivery, btn);
            actions.appendChild(btn);

            const btnCancelar = document.createElement('button');
            btnCancelar.className = 'rep-btn rep-btn-danger';
            btnCancelar.innerHTML = `<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
            </svg> Cancelar entrega`;
            btnCancelar.onclick = async () => {
                const result = await Swal.fire({
                    title: '¿Estás seguro?',
                    text: '¿Seguro que deseas cancelar esta entrega? El pedido volverá a estar disponible para otros repartidores.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#e30613',
                    cancelButtonColor: '#64748b',
                    confirmButtonText: 'Sí, cancelar',
                    cancelButtonText: 'No'
                });
                if(result.isConfirmed) {
                    accionDelivery('cancelar_entrega', d.id_delivery, btnCancelar);
                }
            };
            actions.appendChild(btnCancelar);
        }

        const btnMaps = document.createElement('a');
        btnMaps.className = 'rep-btn rep-btn-ghost';
        btnMaps.href = `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(d.direccion_entrega)}`;
        btnMaps.target = '_blank';
        btnMaps.innerHTML = `<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 1 1 18 0z"/>
            <circle cx="12" cy="10" r="3"/>
        </svg> Ver mapa`;
        actions.appendChild(btnMaps);

        div.appendChild(actions);
    }
    return div;
}

/* ─── Acción de delivery ─────────────────────────── */
async function accionDelivery(accion, idDelivery, btn, extra) {
    btn.disabled = true;
    const textoOriginal = btn.innerHTML;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Procesando...';
    try {
        const resp = await fetch(BASE_URL + '/controllers/PanelRepartidorController.php', {
            method:  'POST',
            headers: { 'Content-Type': 'application/json' },
            body:    JSON.stringify({ accion, id_delivery: idDelivery, ...extra })
        });
        const data = await resp.json();
        if (data.ok) {
            cargarPanel();
        } else {
            btn.disabled = false;
            btn.innerHTML = textoOriginal;
            Swal.fire({icon: 'error', title: 'Error', text: data.mensaje || 'Error al procesar', confirmButtonColor: '#e30613'});
        }
    } catch {
        btn.disabled = false;
        btn.innerHTML = textoOriginal;
        Swal.fire({icon: 'error', title: 'Error de conexión', text: 'Error de conexión. Intenta de nuevo.', confirmButtonColor: '#e30613'});
    }
}

/* ─── Auto-refresh cada 60 s ─────────────────────── */
setInterval(cargarPanel, 60000);
document.addEventListener('DOMContentLoaded', cargarPanel);
