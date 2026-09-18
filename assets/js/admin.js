/* admin.js — Dashboard completo */

/* ── Navegación entre secciones ──────────────── */
function navegar(seccion) {
    $('.adm-section').removeClass('active');
    $('.adm-nav-link').removeClass('active');

    $('#sec-' + seccion).addClass('active');
    $(`[data-sec="${seccion}"]`).addClass('active');

    const titulos = {
        dashboard:    'Dashboard',
        productos:    'Productos',
        categorias:   'Categorías',
        promociones:  'Promociones',
        pedidos:      'Pedidos',
        usuarios:     'Usuarios',
        trabajadores: 'Personal / Trabajadores',
        stock:        'Movimiento de stock',
        comprobantes: 'Comprobantes',
        delivery:     'Deliveries',
        repartidores: 'Repartidores',
        ventas:       'Ventas por día',
        soporte:      'Buzón (Soporte)'
    };
    $('#topbarTitle').text(titulos[seccion] || seccion);

    cerrarSidebar();
    window.location.hash = seccion;

    const loaders = {
        dashboard:    cargarDashboard,
        productos:    cargarProductos,
        categorias:   cargarCategorias,
        promociones:  cargarPromociones,
        pedidos:      cargarPedidos,
        usuarios:     cargarUsuarios,
        trabajadores: cargarTrabajadores,
        stock:        cargarStock,
        comprobantes: cargarComprobantes,
        delivery:     cargarDelivery,
        repartidores: cargarRepartidores,
        ventas:       iniciarSeccionVentas,
        soporte:      window.cargarSoporte
    };
    if (loaders[seccion]) loaders[seccion]();
}

/* ── Sidebar móvil ───────────────────────────── */
function toggleSidebar() {
    $('#admSidebar, #admOverlay').toggleClass('open');
}
function cerrarSidebar() {
    $('#admSidebar, #admOverlay').removeClass('open');
}

/* ── Toast ───────────────────────────────────── */
function toast(msg, tipo = 'ok') {
    const $t = $('#admToast');
    $t.text((tipo === 'ok' ? '✓ ' : '✗ ') + msg)
      .removeClass('ok error').addClass(tipo)
      .fadeIn(300)
      .delay(2700)
      .fadeOut(300);
}

/* ── Fetch helper (Refactorizado a jQuery AJAX) ────────────────────────────── */
async function api(url, opts = {}) {
    try {
        return await $.ajax({
            url: url,
            method: opts.method || 'GET',
            contentType: 'application/json',
            data: opts.body ? opts.body : undefined,
            dataType: 'json'
        });
    } catch (err) {
        console.error("AJAX Error:", err);
        return { ok: false, mensaje: "Error de conexión AJAX con el servidor." };
    }
}

/* ══════════════════════════════════════════════
   DASHBOARD
══════════════════════════════════════════════ */
async function cargarDashboard() {
    const data = await api(BASE_URL + '/controllers/admin/DashboardController.php');
    if (!data.ok) return;
    const s = data.stats;
    document.getElementById('statPedidos').textContent    = s.total_pedidos    || 0;
    document.getElementById('statVentas').textContent     = 'S/ ' + parseFloat(s.ventas_hoy || 0).toFixed(2);
    document.getElementById('statUsuarios').textContent   = s.total_usuarios   || 0;
    document.getElementById('statProductos').textContent  = s.total_productos  || 0;

    // Pendientes
    const elPend = document.getElementById('statPendientes');
    if (elPend) elPend.textContent = s.pedidos_pendientes   || 0;

    // Pedidos recientes
    const tbody = document.getElementById('tbodyPedidosRecientes');
    if (!tbody) return;
    tbody.innerHTML = '';
    (data.pedidos_recientes || []).forEach((p, index) => {
        tbody.innerHTML += `<tr>
            <td>${index + 1}</td>
            <td>#${p.id_pedido}</td>
            <td>${p.cliente || 'Sin nombre'}${p.id_usuario_pedido === null ? '<br><span style="background: #e2e8f0; color: #475569; padding: 2px 6px; border-radius: 4px; font-size: 0.70rem; font-weight: 600;">Invitado</span>' : ''}</td>
            <td>${new Date(p.fecha_pedido).toLocaleDateString('es-PE')}</td>
            <td>S/ ${parseFloat(p.total).toFixed(2)}</td>
            <td><span class="badge ${badgePedido(p.estado_pedido)}">${p.estado_pedido}</span></td>
        </tr>`;
    });

    // Renderizar Gráficos (Chart.js)
    if (window.Chart && data.graficos) {
        // 1. Gráfico de Ingresos (Línea)
        const cIngresos = document.getElementById('chartIngresos');
        if (cIngresos) {
            if (window._chartIngresos) window._chartIngresos.destroy();
            const ctx1 = cIngresos.getContext('2d');
            
            // Gradiente suave
            const gradient = ctx1.createLinearGradient(0, 0, 0, 300);
            gradient.addColorStop(0, 'rgba(227, 6, 19, 0.25)'); // Red theme
            gradient.addColorStop(1, 'rgba(227, 6, 19, 0.01)');

            window._chartIngresos = new Chart(ctx1, {
                type: 'line',
                data: {
                    labels: data.graficos.ventas_7dias.map(d => {
                        const date = new Date(d.fecha + 'T00:00:00');
                        return date.toLocaleDateString('es-PE', { weekday: 'short', day: '2-digit' });
                    }),
                    datasets: [{
                        label: 'Ingresos S/',
                        data: data.graficos.ventas_7dias.map(d => parseFloat(d.total)),
                        borderColor: '#e30613',
                        backgroundColor: gradient,
                        borderWidth: 3,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#e30613',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                        fill: true,
                        tension: 0.4 // Curva suave
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#0f172a',
                            titleFont: { size: 13, family: "'Inter', sans-serif" },
                            bodyFont: { size: 14, weight: 'bold', family: "'Inter', sans-serif" },
                            padding: 12,
                            displayColors: false,
                            callbacks: {
                                label: function(context) { return 'S/ ' + context.parsed.y.toFixed(2); }
                            }
                        }
                    },
                    scales: {
                        y: { 
                            beginAtZero: true, 
                            grid: { color: '#f1f5f9', drawBorder: false },
                            ticks: { font: { family: "'Inter', sans-serif" }, color: '#64748b' }
                        },
                        x: { 
                            grid: { display: false, drawBorder: false },
                            ticks: { font: { family: "'Inter', sans-serif" }, color: '#64748b' }
                        }
                    },
                    animation: {
                        y: { duration: 2000, easing: 'easeOutElastic' }
                    }
                }
            });
        }

        // 2. Estado de Pedidos (Doughnut)
        const cEstado = document.getElementById('chartEstado');
        if (cEstado) {
            if (window._chartEstado) window._chartEstado.destroy();
            
            const colorMap = {
                'Pendiente': '#f59e0b',
                'En preparación': '#0ea5e9',
                'En preparacion': '#0ea5e9',
                'En camino': '#8b5cf6',
                'Entregado': '#10b981',
                'Cancelado': '#ef4444'
            };

            const labels = data.graficos.pedidos_estado.map(d => d.estado_pedido);
            const counts = data.graficos.pedidos_estado.map(d => parseInt(d.cantidad));
            const colors = labels.map(l => colorMap[l] || '#94a3b8');

            window._chartEstado = new Chart(cEstado.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: counts,
                        backgroundColor: colors,
                        borderWidth: 0,
                        hoverOffset: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '75%', // Donut delgado elegante
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                padding: 20,
                                font: { family: "'Inter', sans-serif", size: 12 },
                                color: '#475569'
                            }
                        },
                        tooltip: {
                            backgroundColor: '#0f172a',
                            bodyFont: { size: 14, family: "'Inter', sans-serif" },
                            padding: 12,
                            callbacks: {
                                label: function(context) { return ' ' + context.parsed + ' pedidos'; }
                            }
                        }
                    },
                    animation: {
                        animateScale: true,
                        animateRotate: true,
                        duration: 1500
                    }
                }
            });
        }
    }
}

function badgePedido(e) {
    return { 'Pendiente':'badge-yellow','En preparación':'badge-orange',
             'En camino':'badge-blue','Entregado':'badge-green','Cancelado':'badge-red' }[e] || 'badge-gray';
}

/* ══════════════════════════════════════════════
   PRODUCTOS (paginado + búsqueda server-side)
══════════════════════════════════════════════ */
let _categorias   = [];
let _prodPagina   = 1;
let _prodTotal    = 0;
let _prodPaginas  = 1;
let _prodBuscar   = '';
let _prodTimer    = null;

async function cargarProductos(pagina = 1, buscar = _prodBuscar) {
    _prodPagina  = pagina;
    _prodBuscar  = buscar;

    const tbody = document.getElementById('tbodyProductos');
    if (!tbody) return;
    tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;color:#aaa;padding:20px;">Cargando...</td></tr>';

    const qs   = new URLSearchParams({ pagina, q: buscar });
    const data = await api(`${BASE_URL}/controllers/admin/ProductosController.php?${qs}`);
    if (!data.ok) return;

    _prodTotal   = data.total;
    _prodPaginas = data.paginas;
    _categorias  = data.categorias;

    // Llenar select categorías solo la primera vez
    const sel = document.getElementById('mpCategoria');
    if (sel && sel.children.length <= 1) {
        sel.innerHTML = '<option value="">-- Selecciona --</option>';
        _categorias.forEach(c => {
            const op = document.createElement('option');
            op.value       = c.id_categoria;
            op.textContent = c.nombre;
            sel.appendChild(op);
        });
    }

    renderProductos(data.productos);
    renderPaginacion();
}

function renderProductos(lista) {
    const tbody = document.getElementById('tbodyProductos');
    if (!tbody) return;

    if (!lista.length) {
        tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;color:#aaa;padding:24px;">Sin productos</td></tr>';
        return;
    }

    // DocumentFragment — un solo reflow
    const frag = document.createDocumentFragment();
    lista.forEach((p, index) => {
        const tr = document.createElement('tr');
        const numVisual = (_prodPagina - 1) * 30 + index + 1;
        const stockBadge = p.stock_actual <= (p.stock_minimo || 5)
            ? `<span class="badge badge-red">${p.stock_actual}</span>`
            : `<span class="badge badge-green">${p.stock_actual}</span>`;

        tr.innerHTML = `
            <td>${numVisual}</td>
            <td>${p.id_producto}</td>
            <td>${p.imagen ? `<img class="thumb" src="${p.imagen}" loading="lazy" onerror="this.src=BASE_URL + '/img/imagen1.webp'">` : '—'}</td>
            <td><strong>${p.nombre}</strong></td>
            <td>${p.categoria_nombre || '—'}</td>
            <td>S/ ${parseFloat(p.precio).toFixed(2)}</td>
            <td>${stockBadge}</td>
            <td><span class="badge ${p.estado ? 'badge-green' : 'badge-gray'}">${p.estado ? 'Activo' : 'Inactivo'}</span></td>
            <td style="white-space:nowrap;"></td>`;

        // Botones vía JS (evitar innerHTML de strings complejos con JSON)
        const td = tr.lastElementChild;
        const btnEditar = document.createElement('button');
        btnEditar.className = 'adm-btn adm-btn-ghost adm-btn-sm';
        btnEditar.innerHTML = '✏️ Editar';
        btnEditar.onclick   = () => abrirModalProducto(p);

        const btnElim = document.createElement('button');
        btnElim.className = 'adm-btn adm-btn-danger adm-btn-sm';
        btnElim.style.marginLeft = '4px';
        btnElim.innerHTML = '🗑 Eliminar';
        btnElim.onclick   = () => eliminarProducto(p.id_producto, p.nombre);

        td.appendChild(btnEditar);
        td.appendChild(btnElim);
        frag.appendChild(tr);
    });

    tbody.innerHTML = '';
    tbody.appendChild(frag);
}

function renderPaginacion() {
    let pag = document.getElementById('prodPaginacion');
    if (!pag) {
        pag = document.createElement('div');
        pag.id = 'prodPaginacion';
        pag.style.cssText = 'display:flex;gap:6px;align-items:center;justify-content:flex-end;padding:14px 18px;border-top:1px solid #eee;flex-wrap:wrap;';
        document.getElementById('tbodyProductos')?.closest('.adm-card')?.appendChild(pag);
    }

    pag.innerHTML = `<span style="font-size:.8rem;color:#888;margin-right:8px;">
        ${_prodTotal} productos · página ${_prodPagina} de ${_prodPaginas}
    </span>`;

    const btn = (lbl, pg, dis) => {
        const b = document.createElement('button');
        b.className = `adm-btn adm-btn-ghost adm-btn-sm`;
        b.textContent = lbl;
        b.disabled = dis;
        if (!dis) b.onclick = () => cargarProductos(pg);
        return b;
    };

    pag.appendChild(btn('«', 1, _prodPagina === 1));
    pag.appendChild(btn('‹', _prodPagina - 1, _prodPagina === 1));

    // Páginas cercanas
    const inicio = Math.max(1, _prodPagina - 2);
    const fin    = Math.min(_prodPaginas, _prodPagina + 2);
    for (let i = inicio; i <= fin; i++) {
        const b = btn(i, i, false);
        if (i === _prodPagina) { b.style.background = '#e30613'; b.style.color = '#fff'; }
        pag.appendChild(b);
    }

    pag.appendChild(btn('›', _prodPagina + 1, _prodPagina === _prodPaginas));
    pag.appendChild(btn('»', _prodPaginas, _prodPagina === _prodPaginas));
}

function filtrarProductos() {
    clearTimeout(_prodTimer);
    _prodTimer = setTimeout(() => {
        const q = document.getElementById('buscadorProductos')?.value || '';
        cargarProductos(1, q);
    }, 350); // debounce 350ms
}

function abrirModalProducto(p = null) {
    const modal = document.getElementById('modalProducto');
    document.getElementById('mpTitulo').textContent   = p ? 'Editar producto' : 'Nuevo producto';
    document.getElementById('mpId').value             = p?.id_producto   || '';
    document.getElementById('mpNombre').value         = p?.nombre        || '';
    document.getElementById('mpPrecio').value         = p?.precio        || '';
    document.getElementById('mpStock').value          = p?.stock_actual  || '';
    document.getElementById('mpStockMin').value       = p?.stock_minimo  || 5;
    document.getElementById('mpCategoria').value      = p?.id_categoria  || '';
    document.getElementById('mpImagen').value         = p?.imagen        || '';
    document.getElementById('mpDescripcion').value    = p?.descripcion   || '';
    document.getElementById('mpEstado').checked       = p ? p.estado == 1 : true;
    document.getElementById('mpEstadoWrap').style.display = p ? 'block' : 'none';
    previsualizarImgProd(p?.imagen || '');
    modal.classList.add('open');
}

function previsualizarImgProd(url) {
    const img = document.getElementById('mpPreview');
    if (img) { img.src = url; img.style.display = url ? 'block' : 'none'; }
}

function cerrarModalProducto() {
    document.getElementById('modalProducto').classList.remove('open');
}

async function guardarProducto() {
    const id = document.getElementById('mpId').value;
    const payload = {
        accion:       id ? 'editar' : 'crear',
        id_producto:  id,
        nombre:       document.getElementById('mpNombre').value.trim(),
        precio:       document.getElementById('mpPrecio').value,
        stock_actual: document.getElementById('mpStock').value,
        stock_minimo: document.getElementById('mpStockMin').value,
        id_categoria: document.getElementById('mpCategoria').value,
        imagen:       document.getElementById('mpImagen').value.trim(),
        descripcion:  document.getElementById('mpDescripcion').value.trim(),
        estado:       document.getElementById('mpEstado').checked ? 1 : 0,
    };
    if (!payload.nombre || !payload.precio || !payload.id_categoria) {
        toast('Completa los campos requeridos', 'err'); return;
    }
    const data = await api(BASE_URL + '/controllers/admin/ProductosController.php',
        { method: 'POST', body: JSON.stringify(payload) });
    if (data.ok) { toast('Producto guardado'); cerrarModalProducto(); cargarProductos(_prodPagina, _prodBuscar); }
    else toast(data.mensaje || 'Error', 'err');
}

async function eliminarProducto(id, nombre) {
    const result = await Swal.fire({
        title: '¿Estás seguro?',
        text: `¿Eliminar "${nombre}"? Esta acción no se puede deshacer.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e30613',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    });
    if (!result.isConfirmed) return;
    const data = await api(BASE_URL + '/controllers/admin/ProductosController.php',
        { method: 'POST', body: JSON.stringify({ accion: 'eliminar', id_producto: id }) });
    if (data.ok) { toast('Producto eliminado'); cargarProductos(_prodPagina, _prodBuscar); }
    else toast(data.mensaje || 'Error', 'err');
}


/* ══════════════════════════════════════════════
   CATEGORÍAS (paginado + búsqueda server-side)
   ══════════════════════════════════════════════ */
let _catPagina   = 1;
let _catTotal    = 0;
let _catPaginas  = 1;
let _catBuscar   = '';
let _catTimer    = null;

async function cargarCategorias(pagina = 1, buscar = _catBuscar) {
    _catPagina = pagina;
    _catBuscar = buscar;

    const tbody = document.getElementById('tbodyCategorias');
    if (!tbody) return;
    tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;color:#aaa;padding:20px;">Cargando...</td></tr>';

    const qs = new URLSearchParams({ pagina, q: buscar });
    const data = await api(`${BASE_URL}/controllers/admin/CategoriasController.php?${qs}`);
    if (!data.ok) return;

    _catTotal   = data.total;
    _catPaginas = data.paginas;

    renderCategorias(data.categorias);
    renderPaginacionCategorias();
}

function renderCategorias(lista) {
    const tbody = document.getElementById('tbodyCategorias');
    if (!tbody) return;

    if (!lista.length) {
        tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;color:#aaa;padding:24px;">Sin categorías</td></tr>';
        return;
    }

    const frag = document.createDocumentFragment();
    lista.forEach((c, index) => {
        const tr = document.createElement('tr');
        const numVisual = (_catPagina - 1) * 15 + index + 1;
        tr.innerHTML = `
            <td>${numVisual}</td>
            <td>${c.id_categoria}</td>
            <td>${c.imagen ? `<img class="thumb" src="${c.imagen}" loading="lazy" onerror="this.src=BASE_URL + '/img/imagen1.webp'">` : '—'}</td>
            <td><strong>${c.nombre}</strong></td>
            <td><span class="badge badge-gray">${c.grupo}</span></td>
            <td>${c.descripcion || '—'}</td>
            <td style="white-space:nowrap;"></td>`;

        const td = tr.lastElementChild;
        const btnEditar = document.createElement('button');
        btnEditar.className = 'adm-btn adm-btn-ghost adm-btn-sm';
        btnEditar.innerHTML = '✏️ Editar';
        btnEditar.onclick   = () => abrirModalCategoria(c);

        const btnElim = document.createElement('button');
        btnElim.className = 'adm-btn adm-btn-danger adm-btn-sm';
        btnElim.style.marginLeft = '4px';
        btnElim.innerHTML = '🗑 Eliminar';
        btnElim.onclick   = () => eliminarCategoria(c.id_categoria, c.nombre);

        td.appendChild(btnEditar);
        td.appendChild(btnElim);
        frag.appendChild(tr);
    });

    tbody.innerHTML = '';
    tbody.appendChild(frag);
}

function renderPaginacionCategorias() {
    let pag = document.getElementById('catPaginacion');
    if (!pag) {
        pag = document.createElement('div');
        pag.id = 'catPaginacion';
        pag.style.cssText = 'display:flex;gap:6px;align-items:center;justify-content:flex-end;padding:14px 18px;border-top:1px solid #eee;flex-wrap:wrap;';
        document.getElementById('tbodyCategorias')?.closest('.adm-card')?.appendChild(pag);
    }

    pag.innerHTML = `<span style="font-size:.8rem;color:#888;margin-right:8px;">
        ${_catTotal} categorías · página ${_catPagina} de ${_catPaginas}
    </span>`;

    const btn = (lbl, pg, dis) => {
        const b = document.createElement('button');
        b.className = `adm-btn adm-btn-ghost adm-btn-sm`;
        b.textContent = lbl;
        b.disabled = dis;
        if (!dis) b.onclick = () => cargarCategorias(pg);
        return b;
    };

    pag.appendChild(btn('«', 1, _catPagina === 1));
    pag.appendChild(btn('‹', _catPagina - 1, _catPagina === 1));

    const inicio = Math.max(1, _catPagina - 2);
    const fin    = Math.min(_catPaginas, _catPagina + 2);
    for (let i = inicio; i <= fin; i++) {
        const b = btn(i, i, false);
        if (i === _catPagina) { b.style.background = '#e30613'; b.style.color = '#fff'; }
        pag.appendChild(b);
    }

    pag.appendChild(btn('›', _catPagina + 1, _catPagina === _catPaginas));
    pag.appendChild(btn('»', _catPaginas, _catPagina === _catPaginas));
}

function filtrarCategorias() {
    clearTimeout(_catTimer);
    _catTimer = setTimeout(() => {
        const q = document.getElementById('buscadorCategorias')?.value || '';
        cargarCategorias(1, q);
    }, 350);
}

function abrirModalCategoria(c = null) {
    const modal = document.getElementById('modalCategoria');
    document.getElementById('mcTitulo').textContent = c ? 'Editar categoría' : 'Nueva categoría';
    document.getElementById('mcId').value           = c?.id_categoria || '';
    document.getElementById('mcNombre').value       = c?.nombre       || '';
    document.getElementById('mcGrupo').value        = c?.grupo        || 'Comida';
    document.getElementById('mcImagen').value       = c?.imagen       || '';
    document.getElementById('mcDescripcion').value  = c?.descripcion  || '';
    previsualizarImgCat(c?.imagen || '');
    modal.classList.add('open');
}

function previsualizarImgCat(url) {
    const img = document.getElementById('mcPreview');
    if (img) { img.src = url; img.style.display = url ? 'block' : 'none'; }
}

function cerrarModalCategoria() {
    document.getElementById('modalCategoria').classList.remove('open');
}

async function guardarCategoria() {
    const id = document.getElementById('mcId').value;
    const payload = {
        accion:       id ? 'editar' : 'crear',
        id_categoria: id,
        nombre:       document.getElementById('mcNombre').value.trim(),
        grupo:        document.getElementById('mcGrupo').value,
        imagen:       document.getElementById('mcImagen').value.trim(),
        descripcion:  document.getElementById('mcDescripcion').value.trim()
    };
    if (!payload.nombre || !payload.grupo) {
        toast('Completa los campos requeridos', 'err'); return;
    }
    const data = await api(BASE_URL + '/controllers/admin/CategoriasController.php',
        { method: 'POST', body: JSON.stringify(payload) });
    if (data.ok) { 
        toast('Categoría guardada'); 
        cerrarModalCategoria(); 
        cargarCategorias(_catPagina, _catBuscar); 
    }
    else toast(data.mensaje || 'Error', 'err');
}

async function eliminarCategoria(id, nombre) {
    const result = await Swal.fire({
        title: '¿Estás seguro?',
        text: `¿Eliminar "${nombre}"? Esta acción no se puede deshacer.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e30613',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    });
    if (!result.isConfirmed) return;
    const data = await api(BASE_URL + '/controllers/admin/CategoriasController.php',
        { method: 'POST', body: JSON.stringify({ accion: 'eliminar', id_categoria: id }) });
    if (data.ok) { 
        toast('Categoría eliminada'); 
        cargarCategorias(_catPagina, _catBuscar); 
    }
    else toast(data.mensaje || 'Error', 'err');
}


/* ══════════════════════════════════════════════
   PEDIDOS
══════════════════════════════════════════════ */

/* ══════════════════════════════════════════════
   PROMOCIONES (paginado + búsqueda server-side)
   ══════════════════════════════════════════════ */
let _promoPagina   = 1;
let _promoTotal    = 0;
let _promoPaginas  = 1;
let _promoBuscar   = '';
let _promoTimer    = null;

async function cargarPromociones(pagina = 1, buscar = _promoBuscar) {
    _promoPagina = pagina;
    _promoBuscar = buscar;

    const tbody = document.getElementById('tbodyPromociones');
    if (!tbody) return;
    tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;color:#aaa;padding:20px;">Cargando...</td></tr>';

    const qs = new URLSearchParams({ pagina, q: buscar });
    const data = await api(`${BASE_URL}/controllers/admin/PromocionesController.php?${qs}`);
    if (!data.ok) return;

    _promoTotal   = data.total;
    _promoPaginas = data.paginas;

    renderPromociones(data.promociones);
    renderPaginacionPromociones();
}

function renderPromociones(lista) {
    const tbody = document.getElementById('tbodyPromociones');
    if (!tbody) return;

    if (!lista.length) {
        tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;color:#aaa;padding:24px;">Sin promociones</td></tr>';
        return;
    }

    const frag = document.createDocumentFragment();
    lista.forEach((p, index) => {
        const tr = document.createElement('tr');
        const numVisual = (_promoPagina - 1) * 15 + index + 1;
        tr.innerHTML = `
            <td>${numVisual}</td>
            <td>${p.id_promocion}</td>
            <td><strong>${p.nombre_promocion}</strong></td>
            <td>${p.descripcion || '—'}</td>
            <td><span class="badge badge-red">${parseFloat(p.porcentaje_descuento)}%</span></td>
            <td>${p.fecha_inicio}</td>
            <td>${p.fecha_fin}</td>
            <td><span class="badge ${p.estado ? 'badge-green' : 'badge-gray'}">${p.estado ? 'Activo' : 'Inactivo'}</span></td>
            <td style="white-space:nowrap;"></td>`;

        const td = tr.lastElementChild;
        
        const btnProductos = document.createElement('button');
        btnProductos.className = 'adm-btn adm-btn-ghost adm-btn-sm';
        btnProductos.innerHTML = '📦 Productos';
        btnProductos.onclick   = () => abrirModalAsignarProductos(p.id_promocion, p.nombre_promocion);

        const btnEditar = document.createElement('button');
        btnEditar.className = 'adm-btn adm-btn-ghost adm-btn-sm';
        btnEditar.style.marginLeft = '4px';
        btnEditar.innerHTML = '✏️ Editar';
        btnEditar.onclick   = () => abrirModalPromocion(p);

        const btnElim = document.createElement('button');
        btnElim.className = 'adm-btn adm-btn-danger adm-btn-sm';
        btnElim.style.marginLeft = '4px';
        btnElim.innerHTML = '🗑 Eliminar';
        btnElim.onclick   = () => eliminarPromocion(p.id_promocion, p.nombre_promocion);

        td.appendChild(btnProductos);
        td.appendChild(btnEditar);
        td.appendChild(btnElim);
        frag.appendChild(tr);
    });

    tbody.innerHTML = '';
    tbody.appendChild(frag);
}

function renderPaginacionPromociones() {
    let pag = document.getElementById('promoPaginacion');
    if (!pag) {
        pag = document.createElement('div');
        pag.id = 'promoPaginacion';
        pag.style.cssText = 'display:flex;gap:6px;align-items:center;justify-content:flex-end;padding:14px 18px;border-top:1px solid #eee;flex-wrap:wrap;';
        document.getElementById('tbodyPromociones')?.closest('.adm-card')?.appendChild(pag);
    }

    pag.innerHTML = `<span style="font-size:.8rem;color:#888;margin-right:8px;">
        ${_promoTotal} promociones · página ${_promoPagina} de ${_promoPaginas}
    </span>`;

    const btn = (lbl, pg, dis) => {
        const b = document.createElement('button');
        b.className = `adm-btn adm-btn-ghost adm-btn-sm`;
        b.textContent = lbl;
        b.disabled = dis;
        if (!dis) b.onclick = () => cargarPromociones(pg);
        return b;
    };

    pag.appendChild(btn('«', 1, _promoPagina === 1));
    pag.appendChild(btn('‹', _promoPagina - 1, _promoPagina === 1));

    const inicio = Math.max(1, _promoPagina - 2);
    const fin    = Math.min(_promoPaginas, _promoPagina + 2);
    for (let i = inicio; i <= fin; i++) {
        const b = btn(i, i, false);
        if (i === _promoPagina) { b.style.background = '#e30613'; b.style.color = '#fff'; }
        pag.appendChild(b);
    }

    pag.appendChild(btn('›', _promoPagina + 1, _promoPagina === _promoPaginas));
    pag.appendChild(btn('»', _promoPaginas, _promoPagina === _promoPaginas));
}

function filtrarPromociones() {
    clearTimeout(_promoTimer);
    _promoTimer = setTimeout(() => {
        const q = document.getElementById('buscadorPromociones')?.value || '';
        cargarPromociones(1, q);
    }, 350);
}

function abrirModalPromocion(p = null) {
    const modal = document.getElementById('modalPromocion');
    document.getElementById('mproTitulo').textContent = p ? 'Editar promoción' : 'Nueva promoción';
    document.getElementById('mproId').value           = p?.id_promocion          || '';
    document.getElementById('mproNombre').value       = p?.nombre_promocion      || '';
    document.getElementById('mproDescuento').value    = p?.porcentaje_descuento  || '';
    document.getElementById('mproEstado').value       = p ? (p.estado == 1 ? '1' : '0') : '1';
    document.getElementById('mproFechaInicio').value  = p?.fecha_inicio          || '';
    document.getElementById('mproFechaFin').value     = p?.fecha_fin             || '';
    document.getElementById('mproDescripcion').value  = p?.descripcion           || '';
    modal.classList.add('open');
}

function cerrarModalPromocion() {
    document.getElementById('modalPromocion').classList.remove('open');
}

async function guardarPromocion() {
    const id = document.getElementById('mproId').value;
    const payload = {
        accion:               id ? 'editar' : 'crear',
        id_promocion:         id,
        nombre_promocion:     document.getElementById('mproNombre').value.trim(),
        porcentaje_descuento: document.getElementById('mproDescuento').value,
        estado:               parseInt(document.getElementById('mproEstado').value),
        fecha_inicio:         document.getElementById('mproFechaInicio').value || null,
        fecha_fin:            document.getElementById('mproFechaFin').value    || null,
        descripcion:          document.getElementById('mproDescripcion').value.trim()
    };

    if (!payload.nombre_promocion || !payload.porcentaje_descuento || !payload.fecha_inicio || !payload.fecha_fin) {
        toast('Completa los campos requeridos', 'err'); return;
    }

    const data = await api(BASE_URL + '/controllers/admin/PromocionesController.php',
        { method: 'POST', body: JSON.stringify(payload) });
    if (data.ok) {
        toast('Promoción guardada');
        cerrarModalPromocion();
        cargarPromociones(_promoPagina, _promoBuscar);
    } else {
        toast(data.mensaje || 'Error', 'err');
    }
}

async function eliminarPromocion(id, nombre) {
    const result = await Swal.fire({
        title: '¿Estás seguro?',
        text: `¿Eliminar la promoción "${nombre}"? Los productos asignados a ella ya no tendrán descuento.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e30613',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    });
    if (!result.isConfirmed) return;

    const data = await api(BASE_URL + '/controllers/admin/PromocionesController.php',
        { method: 'POST', body: JSON.stringify({ accion: 'eliminar', id_promocion: id }) });
    if (data.ok) {
        toast('Promoción eliminada');
        cargarPromociones(_promoPagina, _promoBuscar);
    } else {
        toast(data.mensaje || 'Error', 'err');
    }
}

/* --- Gestión de Vinculación de Productos --- */
let _mapTimer = null;

function abrirModalAsignarProductos(promoId, promoNombre) {
    const modal = document.getElementById('modalAsignarProductosPromocion');
    document.getElementById('mapPromoId').value = promoId;
    document.getElementById('mapTitulo').textContent = `Productos en "${promoNombre}"`;
    document.getElementById('mapSubtitulo').textContent = 'Agrega productos a la promoción o quítalos de la lista';
    document.getElementById('buscadorProdPromo').value = '';

    cargarProductosDePromocion(promoId);
    modal.classList.add('open');
}

function cerrarModalAsignarProductos() {
    document.getElementById('modalAsignarProductosPromocion').classList.remove('open');
}

async function cargarProductosDePromocion(promoId, busq = '') {
    const data = await api(`${BASE_URL}/controllers/admin/PromocionesController.php?id_productos_promocion=${promoId}&q=${encodeURIComponent(busq)}`);
    if (!data.ok) return;

    // Render asociados (derecha)
    const divAsoc = document.getElementById('listaAsociados');
    divAsoc.innerHTML = '';
    if (!data.asociados.length) {
        divAsoc.innerHTML = '<div style="color:#aaa;text-align:center;padding:20px;font-size:0.85rem;">Ningún producto asociado</div>';
    } else {
        data.asociados.forEach(p => {
            const item = document.createElement('div');
            item.style.cssText = 'display:flex;align-items:center;justify-content:space-between;padding:8px;border-bottom:1px solid #f3f4f6;font-size:0.85rem;';
            item.innerHTML = `
                <div style="display:flex;align-items:center;gap:8px;">
                    <img src="${p.imagen}" style="width:30px;height:30px;object-fit:cover;border-radius:4px;" onerror="this.src=BASE_URL + '/img/imagen1.webp'">
                    <span>${p.nombre} (S/ ${parseFloat(p.precio).toFixed(2)})</span>
                </div>
                <button class="adm-btn adm-btn-danger adm-btn-sm" style="padding: 2px 6px; font-size:0.75rem;">Quitar</button>
            `;
            item.querySelector('button').onclick = () => desvincularProductoDePromocion(promoId, p.id_producto);
            divAsoc.appendChild(item);
        });
    }

    // Render disponibles (izquierda)
    const divDisp = document.getElementById('listaDisponibles');
    divDisp.innerHTML = '';
    if (!data.disponibles.length) {
        divDisp.innerHTML = '<div style="color:#aaa;text-align:center;padding:20px;font-size:0.85rem;">No se encontraron productos libres</div>';
    } else {
        data.disponibles.forEach(p => {
            const item = document.createElement('div');
            item.style.cssText = 'display:flex;align-items:center;justify-content:space-between;padding:8px;border-bottom:1px solid #f3f4f6;font-size:0.85rem;';
            item.innerHTML = `
                <div style="display:flex;align-items:center;gap:8px;">
                    <img src="${p.imagen}" style="width:30px;height:30px;object-fit:cover;border-radius:4px;" onerror="this.src=BASE_URL + '/img/imagen1.webp'">
                    <span>${p.nombre} (S/ ${parseFloat(p.precio).toFixed(2)})</span>
                </div>
                <button class="adm-btn adm-btn-primary adm-btn-sm" style="padding: 2px 6px; font-size:0.75rem;">Agregar</button>
            `;
            item.querySelector('button').onclick = () => vincularProductoAPromocion(promoId, p.id_producto);
            divDisp.appendChild(item);
        });
    }
}

function filtrarProductosDePromocion() {
    clearTimeout(_mapTimer);
    _mapTimer = setTimeout(() => {
        const promoId = document.getElementById('mapPromoId').value;
        const busq = document.getElementById('buscadorProdPromo').value.trim();
        cargarProductosDePromocion(promoId, busq);
    }, 350);
}

async function vincularProductoAPromocion(promoId, productoId) {
    const data = await api(BASE_URL + '/controllers/admin/PromocionesController.php', {
        method: 'POST',
        body: JSON.stringify({ accion: 'vincular_producto', id_promocion: promoId, id_producto: productoId })
    });
    if (data.ok) {
        const busq = document.getElementById('buscadorProdPromo').value.trim();
        cargarProductosDePromocion(promoId, busq);
    }
}

async function desvincularProductoDePromocion(promoId, productoId) {
    const data = await api(BASE_URL + '/controllers/admin/PromocionesController.php', {
        method: 'POST',
        body: JSON.stringify({ accion: 'desvincular_producto', id_promocion: promoId, id_producto: productoId })
    });
    if (data.ok) {
        const busq = document.getElementById('buscadorProdPromo').value.trim();
        cargarProductosDePromocion(promoId, busq);
    }
}

let _pedidos = [];

async function cargarPedidos() {
    const data = await api(BASE_URL + '/controllers/admin/PedidosController.php');
    if (!data.ok) return;
    _pedidos = data.pedidos;
    renderPedidos(_pedidos);
}

function renderPedidos(lista) {
    const tbody = document.getElementById('tbodyPedidos');
    if (!tbody) return;
    tbody.innerHTML = '';
    if (!lista.length) {
        tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;color:#aaa;padding:24px;">Sin pedidos</td></tr>';
        return;
    }
    lista.forEach((p, index) => {
        tbody.innerHTML += `<tr>
            <td>${index + 1}</td>
            <td>#${p.id_pedido}</td>
            <td>${p.cliente || 'Sin nombre'}${p.id_usuario_pedido === null ? '<br><span style="background: #e2e8f0; color: #475569; padding: 2px 6px; border-radius: 4px; font-size: 0.70rem; font-weight: 600;">Invitado</span>' : ''}</td>
            <td>${new Date(p.fecha_pedido).toLocaleDateString('es-PE')}</td>
            <td>${p.tipo_entrega || '—'}</td>
            <td>${p.metodo_pago  || '—'}</td>
            <td>S/ ${parseFloat(p.total).toFixed(2)}</td>
            <td>
                ${['Entregado', 'Cancelado'].includes(p.estado_pedido) ? 
                    `<span class="badge ${p.estado_pedido==='Entregado'?'badge-green':'badge-red'}">${p.estado_pedido}</span>` 
                    : 
                    `<select class="adm-select" onchange="cambiarEstadoPedido(${p.id_pedido}, this.value)">
                        ${['Pendiente','En camino','Entregado','Cancelado'].map(e =>
                            `<option ${p.estado_pedido===e?'selected':''}>${e}</option>`).join('')}
                    </select>`
                }
            </td>
            <td>
                <button class="adm-btn adm-btn-dark adm-btn-sm" onclick="verDetallePedido(${p.id_pedido})">Ver</button>
            </td>
        </tr>`;
    });
}

function filtrarPedidos() {
    const q = document.getElementById('buscadorPedidos').value.toLowerCase();
    const fFecha = document.getElementById('filtroFechaPedidos').value;
    const fEstado = document.getElementById('filtroEstadoPedidos').value;

    renderPedidos(_pedidos.filter(p => {
        const textMatch = (p.cliente || 'Sin nombre').toLowerCase().includes(q) || String(p.id_pedido).includes(q);
        const fechaMatch = !fFecha || p.fecha_pedido.startsWith(fFecha);
        const estadoMatch = !fEstado || p.estado_pedido === fEstado;
        return textMatch && fechaMatch && estadoMatch;
    }));
}

function limpiarFiltrosPedidos() {
    document.getElementById('buscadorPedidos').value = '';
    document.getElementById('filtroFechaPedidos').value = '';
    document.getElementById('filtroEstadoPedidos').value = '';
    filtrarPedidos();
}

async function cambiarEstadoPedido(id, estado) {
    const data = await api(BASE_URL + '/controllers/admin/PedidosController.php',
        { method: 'POST', body: JSON.stringify({ accion: 'cambiar_estado', id_pedido: id, estado }) });
    if (data.ok) toast(`Pedido #${id} → ${estado}`);
    else toast(data.mensaje || 'Error', 'err');
}

async function verDetallePedido(id) {
    const data = await api(`${BASE_URL}/controllers/admin/PedidosController.php?id=${id}`);
    if (!data.ok) return;
    const p = data.pedido;
    const items = (p.detalle || []).map(d =>
        `<tr>
            <td style="padding:12px 0;">
                <div style="font-weight:600; color:#1e293b;">${d.nombre}</div>
                <div style="font-size:0.75rem; color:#64748b; margin-top:3px;">Cant: ${d.cantidad} × S/ ${parseFloat(d.precio_unitario).toFixed(2)}</div>
            </td>
            <td style="text-align:right; font-weight:600; color:#0f172a;">S/ ${parseFloat(d.subtotal).toFixed(2)}</td>
        </tr>`).join('');
        
    document.getElementById('detallePedidoContenido').innerHTML = `
        <div style="background:var(--adm-accent-light, #f8fafc); border-radius:12px; padding:18px; margin-bottom:20px; border:1px solid #e2e8f0;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; border-bottom:1px solid #e2e8f0; padding-bottom:12px;">
                <div>
                    <div style="font-size:0.75rem; text-transform:uppercase; letter-spacing:0.05em; color:#64748b; font-weight:700;">Pedido</div>
                    <div style="font-size:1.4rem; font-weight:800; color:var(--adm-accent);">#${p.id_pedido}</div>
                </div>
                <div style="text-align:right;">
                    <div style="font-size:0.8rem; color:#64748b; font-weight:600;">${new Date(p.fecha_pedido).toLocaleDateString('es-PE', { weekday:'short', day:'2-digit', month:'short', year:'numeric'})}</div>
                    <div style="display:inline-block; margin-top:4px; padding:4px 10px; border-radius:20px; font-size:0.7rem; font-weight:700; background:#e2e8f0; color:#334155;">
                        ${p.estado_pedido}
                    </div>
                </div>
            </div>
            
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                <div>
                    <div style="font-size:0.75rem; color:#64748b; margin-bottom:4px;">Cliente</div>
                    <div style="font-weight:600; font-size:0.9rem; color:#1e293b; display:flex; align-items:center; gap:8px;">
                        <div style="width:24px; height:24px; border-radius:50%; background:var(--adm-accent); color:white; display:flex; align-items:center; justify-content:center; font-size:0.7rem;">
                            ${(p.cliente || 'I').charAt(0).toUpperCase()}
                        </div>
                        ${p.cliente || 'Sin nombre'}${p.id_usuario_pedido === null ? '<br><span style="background: #e2e8f0; color: #475569; padding: 2px 6px; border-radius: 4px; font-size: 0.70rem; font-weight: 600;">Invitado</span>' : ''}
                    </div>
                </div>
                <div>
                    <div style="font-size:0.75rem; color:#64748b; margin-bottom:4px;">Método y Entrega</div>
                    <div style="font-size:0.85rem; color:#334155; font-weight:600;">
                        🚚 ${p.tipo_entrega} <br>
                        💳 ${p.metodo_pago}
                    </div>
                </div>
            </div>
            ${p.estado_pedido === 'Cancelado' && p.motivo_cancelacion ? `
                <div style="margin-top: 15px; padding: 12px; border-radius: 8px; background: #fef2f2; border: 1px solid #fecaca;">
                    <div style="font-size: 0.75rem; color: #ef4444; font-weight: 700; text-transform: uppercase; margin-bottom: 4px;">Motivo de cancelación</div>
                    <div style="font-size: 0.85rem; color: #991b1b;">${p.motivo_cancelacion}</div>
                </div>
            ` : ''}
        </div>
        
        <h4 style="font-size:0.85rem; text-transform:uppercase; color:#94a3b8; letter-spacing:0.05em; margin-bottom:10px; border-bottom:1px solid #f1f5f9; padding-bottom:6px;">Detalle de productos</h4>
        <table style="width:100%; border-collapse:collapse; margin-bottom:20px;">
            <tbody>${items}</tbody>
        </table>
        
        <div style="background:#f8fafc; border-radius:8px; padding:16px; border:1px dashed #cbd5e1;">
            <div style="display:flex; justify-content:space-between; margin-bottom:8px; font-size:0.85rem; color:#64748b;">
                <span>Subtotal (aprox)</span>
                <span style="font-weight:600; color:#334155;">S/ ${(parseFloat(p.total) + parseFloat(p.descuento || 0)).toFixed(2)}</span>
            </div>
            ${p.descuento > 0 ? `
            <div style="display:flex; justify-content:space-between; margin-bottom:12px; font-size:0.85rem; color:#ef4444;">
                <span>Descuento aplicado</span>
                <span style="font-weight:700;">- S/ ${parseFloat(p.descuento).toFixed(2)}</span>
            </div>` : ''}
            <div style="display:flex; justify-content:space-between; align-items:center; margin-top:12px; padding-top:12px; border-top:1px solid #e2e8f0;">
                <span style="font-weight:700; font-size:0.95rem; color:#0f172a;">TOTAL A PAGAR</span>
                <span style="font-size:1.35rem; font-weight:800; color:#e30613;">S/ ${parseFloat(p.total).toFixed(2)}</span>
            </div>
        </div>`;
    document.getElementById('modalDetallePedido').classList.add('open');
}

/* ══════════════════════════════════════════════
   USUARIOS
══════════════════════════════════════════════ */
let _usuarios = [];

async function cargarUsuarios() {
    const data = await api(BASE_URL + '/controllers/admin/UsuariosController.php');
    if (!data.ok) return;
    _usuarios = data.usuarios;
    renderUsuarios(_usuarios);
}

function renderUsuarios(lista) {
    const tbody = document.getElementById('tbodyUsuarios');
    if (!tbody) return;
    tbody.innerHTML = '';
    lista.forEach((u, index) => {
        const dniBadge = u.dni
            ? `<span class="badge badge-blue">${u.dni}</span>`
            : `<span style="color:#aaa;font-size:.78rem;">Sin DNI</span>`;
        tbody.innerHTML += `<tr>
            <td>${index + 1}</td>
            <td>${u.id_usuario}</td>
            <td><strong>${u.nombres} ${u.apellidos}</strong></td>
            <td>${dniBadge}</td>
            <td>${u.correo}</td>
            <td>${u.telefono || '—'}</td>
            <td>${new Date(u.fecha_registro).toLocaleDateString('es-PE')}</td>
            <td>${u.roles || 'Cliente'}</td>
            <td><span class="badge ${u.estado ? 'badge-green':'badge-red'}">${u.estado ? 'Activo':'Inactivo'}</span></td>
            <td>
                <div style="display:flex;gap:5px;">
                    <button class="adm-btn adm-btn-primary adm-btn-sm" onclick="abrirModalRoles(${u.id_usuario}, '${u.nombres} ${u.apellidos}', '${u.roles || 'Cliente'}')">
                        Roles
                    </button>
                    <button class="adm-btn adm-btn-${u.estado?'danger':'success'} adm-btn-sm"
                        onclick="toggleUsuario(${u.id_usuario},${u.estado})">
                        ${u.estado ? 'Desactivar' : 'Activar'}
                    </button>
                </div>
            </td>
        </tr>`;
    });
}

function filtrarUsuarios() {
    const q = document.getElementById('buscadorUsuarios').value.toLowerCase();
    renderUsuarios(_usuarios.filter(u =>
        (u.nombres+' '+u.apellidos+' '+u.correo).toLowerCase().includes(q)));
}

async function toggleUsuario(id, estadoActual) {
    const data = await api(BASE_URL + '/controllers/admin/UsuariosController.php',
        { method: 'POST', body: JSON.stringify({ accion: 'toggle_estado', id_usuario: id, estado: estadoActual ? 0 : 1 }) });
    if (data.ok) { toast('Usuario actualizado'); cargarUsuarios(); }
    else toast(data.mensaje || 'Error', 'err');
}

function abrirModalRoles(idUsuario, nombre, roles) {
    document.getElementById('rolesUsuarioId').value = idUsuario;
    document.getElementById('rolesUsuarioNombre').innerText = nombre;
    
    // Check checkboxes based on roles
    const rArr = roles.split(',').map(r => r.trim());
    document.getElementById('rolAdmin').checked = rArr.includes('Administrador');
    document.getElementById('rolCliente').checked = rArr.includes('Cliente');
    document.getElementById('rolRepartidor').checked = rArr.includes('Repartidor');
    if (document.getElementById('rolAlmacenero')) document.getElementById('rolAlmacenero').checked = rArr.includes('Almacenero');
    if (document.getElementById('rolDespachador')) document.getElementById('rolDespachador').checked = rArr.includes('Despachador');
    if (document.getElementById('rolCajero')) document.getElementById('rolCajero').checked = rArr.includes('Cajero');
    if (document.getElementById('rolSoporte')) document.getElementById('rolSoporte').checked = rArr.includes('Soporte');
    
    document.getElementById('modalRoles').classList.add('open');
}

function cerrarModalRoles() {
    document.getElementById('modalRoles').classList.remove('open');
}

async function guardarRolesUsuario() {
    const idUsuario = document.getElementById('rolesUsuarioId').value;
    let roles = [];
    if(document.getElementById('rolAdmin').checked) roles.push(1);
    if(document.getElementById('rolCliente').checked) roles.push(2);
    if(document.getElementById('rolRepartidor').checked) roles.push(3);
    if(document.getElementById('rolAlmacenero')?.checked) roles.push(4);
    if(document.getElementById('rolDespachador')?.checked) roles.push(5);
    if(document.getElementById('rolCajero')?.checked) roles.push(6);
    if(document.getElementById('rolSoporte')?.checked) roles.push(7);
    
    if (roles.length === 0) {
        toast('El usuario debe tener al menos un rol', 'err');
        return;
    }
    
    const btn = document.getElementById('btnGuardarRoles');
    btn.disabled = true;
    
    const data = await api(BASE_URL + '/controllers/admin/UsuariosController.php', {
        method: 'POST',
        body: JSON.stringify({ accion: 'asignar_roles', id_usuario: idUsuario, roles: roles })
    });
    
    btn.disabled = false;
    if (data.ok) {
        toast('Roles actualizados exitosamente');
        cerrarModalRoles();
        cargarUsuarios();
        cargarRepartidores();
        if (typeof cargarTrabajadores === 'function') cargarTrabajadores();
    } else {
        toast(data.mensaje || 'Error', 'err');
    }
}

/* ══════════════════════════════════════════════
   PERSONAL / TRABAJADORES
══════════════════════════════════════════════ */
let _trabajadores = [];
let _usuariosPlanilla = [];

async function cargarTrabajadores() {
    const data = await api(BASE_URL + '/controllers/admin/TrabajadoresController.php');
    if (!data.ok) {
        toast(data.mensaje || 'Error al cargar trabajadores', 'err');
        return;
    }
    _trabajadores = data.trabajadores || [];
    _usuariosPlanilla = data.usuarios || [];
    
    // Actualizar KPIs
    const total = _trabajadores.length;
    const activos = _trabajadores.filter(t => parseInt(t.estado) === 1).length;
    const despachadores = _trabajadores.filter(t => t.cargo === 'Despachador').length;
    const almaceneros = _trabajadores.filter(t => t.cargo === 'Almacenero').length;
    const repartidores = _trabajadores.filter(t => t.cargo === 'Repartidor').length;

    $('#statTotalTrabajadores').text(total);
    $('#statTrabajadoresActivos').text(activos);
    $('#statTrabDespacho').text(despachadores);
    $('#statTrabAlmacen').text(almaceneros);
    $('#statTrabRepartidores').text(repartidores);

    renderTrabajadores(_trabajadores);
    poblarSelectUsuariosTrabajador();
}

function poblarSelectUsuariosTrabajador() {
    const sel = document.getElementById('trabajadorUsuarioId');
    if (!sel) return;
    sel.innerHTML = '<option value="">-- Elige un usuario por nombre o DNI --</option>';
    
    const idsTrab = new Set(_trabajadores.map(t => parseInt(t.id_usuario)));

    _usuariosPlanilla.forEach(u => {
        const yaEs = idsTrab.has(parseInt(u.id_usuario)) ? ' (Ya asignado)' : '';
        sel.innerHTML += `<option value="${u.id_usuario}">${u.nombres} ${u.apellidos} — DNI: ${u.dni || 'Sin DNI'}${yaEs}</option>`;
    });
}

function renderTrabajadores(lista) {
    const tbody = document.getElementById('tbodyTrabajadores');
    if (!tbody) return;
    tbody.innerHTML = '';

    if (lista.length === 0) {
        tbody.innerHTML = '<tr><td colspan="9" style="text-align:center; padding:30px; color:#64748b;">No se encontraron colaboradores registrados.</td></tr>';
        return;
    }

    const badgeCargo = {
        'Despachador': 'background:#dbeafe; color:#1e40af; border:1px solid #bfdbfe;',
        'Almacenero':  'background:#f3e8ff; color:#6b21a8; border:1px solid #e9d5ff;',
        'Repartidor':  'background:#dcfce7; color:#166534; border:1px solid #bbf7d0;',
        'Cajero':      'background:#ffedd5; color:#9a3412; border:1px solid #fed7aa;',
        'Administrador':'background:#fee2e2; color:#991b1b; border:1px solid #fecaca;'
    };

    lista.forEach((t, idx) => {
        const styleCargo = badgeCargo[t.cargo] || 'background:#f1f5f9; color:#334155;';
        const sueldoStr = t.sueldo ? `S/ ${parseFloat(t.sueldo).toFixed(2)}` : '<span style="color:#94a3b8;font-size:0.8rem;">No def.</span>';
        const entregasStr = t.cargo === 'Repartidor' ? `<small style="display:block;color:#64748b;font-size:0.72rem;">${t.entregas_completadas || 0} entregas</small>` : '';

        tbody.innerHTML += `
            <tr>
                <td>${idx + 1}</td>
                <td>
                    <div style="font-weight:700; color:#0f172a;">${t.nombres} ${t.apellidos}</div>
                    <div style="font-size:0.75rem; color:#64748b;">${t.correo}</div>
                </td>
                <td>
                    <div><span class="badge badge-blue" style="font-size:0.72rem;">${t.dni || 'Sin DNI'}</span></div>
                    <small style="color:#64748b;">${t.telefono || '—'}</small>
                </td>
                <td>
                    <span style="display:inline-block; padding:3px 10px; border-radius:15px; font-size:0.78rem; font-weight:700; ${styleCargo}">
                        ${t.cargo}
                    </span>
                    ${entregasStr}
                </td>
                <td>
                    <span style="font-size:0.82rem; font-weight:600; color:#334155;">${t.turno || 'Mañana'}</span>
                </td>
                <td style="font-weight:600; color:#0f172a;">${sueldoStr}</td>
                <td style="font-size:0.8rem; color:#64748b;">${t.fecha_ingreso ? new Date(t.fecha_ingreso).toLocaleDateString('es-PE') : '—'}</td>
                <td>
                    <span class="badge ${parseInt(t.estado) === 1 ? 'badge-green' : 'badge-red'}">
                        ${parseInt(t.estado) === 1 ? 'Activo' : 'Inactivo'}
                    </span>
                </td>
                <td>
                    <div style="display:flex; gap:6px;">
                        <button class="adm-btn adm-btn-ghost adm-btn-sm" onclick='abrirModalTrabajador(${JSON.stringify(t)})' title="Editar cargo y turno">
                            <i class="fa fa-edit"></i>
                        </button>
                        <button class="adm-btn adm-btn-${parseInt(t.estado) === 1 ? 'danger' : 'success'} adm-btn-sm" onclick="toggleTrabajador(${t.id_trabajador}, ${t.estado})">
                            ${parseInt(t.estado) === 1 ? 'Suspender' : 'Activar'}
                        </button>
                        <button class="adm-btn adm-btn-ghost adm-btn-sm" onclick="eliminarTrabajador(${t.id_trabajador}, '${t.nombres} ${t.apellidos}')" title="Quitar de planilla" style="color:#ef4444;">
                            <i class="fa fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    });
}

function filtrarTrabajadores() {
    const q = (document.getElementById('buscadorTrabajadores')?.value || '').toLowerCase();
    const cargo = document.getElementById('filtroCargoTrabajador')?.value || '';
    const turno = document.getElementById('filtroTurnoTrabajador')?.value || '';

    const filtrados = _trabajadores.filter(t => {
        const texto = `${t.nombres} ${t.apellidos} ${t.dni} ${t.telefono} ${t.correo}`.toLowerCase();
        const cumpleTexto = texto.includes(q);
        const cumpleCargo = !cargo || t.cargo === cargo;
        const cumpleTurno = !turno || t.turno === turno;
        return cumpleTexto && cumpleCargo && cumpleTurno;
    });
    renderTrabajadores(filtrados);
}

function abrirModalTrabajador(trabajador = null) {
    const modal = document.getElementById('modalTrabajador');
    if (!modal) return;

    if (trabajador) {
        document.getElementById('modalTrabajadorTitulo').textContent = 'Editar Colaborador';
        document.getElementById('trabajadorId').value = trabajador.id_trabajador;
        document.getElementById('grupoSelectUsuario').style.display = 'none';
        document.getElementById('trabajadorUsuarioId').removeAttribute('required');
        document.getElementById('infoUsuarioEdicion').style.display = 'block';
        document.getElementById('labelNombreColaborador').textContent = `${trabajador.nombres} ${trabajador.apellidos} (${trabajador.dni || 'Sin DNI'})`;
        document.getElementById('trabajadorCargo').value = trabajador.cargo;
        document.getElementById('trabajadorTurno').value = trabajador.turno || 'Mañana';
        document.getElementById('trabajadorSueldo').value = trabajador.sueldo || '';
        document.getElementById('trabajadorNotas').value = trabajador.notas || '';
    } else {
        document.getElementById('modalTrabajadorTitulo').textContent = 'Nuevo Colaborador';
        document.getElementById('trabajadorId').value = '';
        document.getElementById('grupoSelectUsuario').style.display = 'block';
        document.getElementById('trabajadorUsuarioId').setAttribute('required', 'required');
        document.getElementById('infoUsuarioEdicion').style.display = 'none';
        document.getElementById('formTrabajador').reset();
        poblarSelectUsuariosTrabajador();
    }

    modal.classList.add('open');
}

function cerrarModalTrabajador() {
    const modal = document.getElementById('modalTrabajador');
    if (modal) modal.classList.remove('open');
}

async function guardarTrabajador(e) {
    e.preventDefault();
    const idTrabajador = document.getElementById('trabajadorId').value;
    const esEdicion = !!idTrabajador;

    const payload = {
        accion: esEdicion ? 'actualizar' : 'crear',
        id_trabajador: idTrabajador ? parseInt(idTrabajador) : undefined,
        id_usuario: !esEdicion ? parseInt(document.getElementById('trabajadorUsuarioId').value) : undefined,
        cargo: document.getElementById('trabajadorCargo').value,
        turno: document.getElementById('trabajadorTurno').value,
        sueldo: document.getElementById('trabajadorSueldo').value ? parseFloat(document.getElementById('trabajadorSueldo').value) : null,
        notas: document.getElementById('trabajadorNotas').value.trim()
    };

    if (!esEdicion && (!payload.id_usuario || payload.id_usuario <= 0)) {
        toast('Debes seleccionar un usuario', 'err');
        return;
    }

    const btn = document.getElementById('btnGuardarTrabajador');
    btn.disabled = true;

    const res = await api(BASE_URL + '/controllers/admin/TrabajadoresController.php', {
        method: 'POST',
        body: JSON.stringify(payload)
    });

    btn.disabled = false;

    if (res.ok) {
        toast(res.mensaje || 'Colaborador guardado exitosamente');
        cerrarModalTrabajador();
        cargarTrabajadores();
        cargarUsuarios();
    } else {
        toast(res.mensaje || 'Error al guardar', 'err');
    }
}

async function toggleTrabajador(id, estadoActual) {
    const nuevoEstado = parseInt(estadoActual) === 1 ? 0 : 1;
    const res = await api(BASE_URL + '/controllers/admin/TrabajadoresController.php', {
        method: 'POST',
        body: JSON.stringify({ accion: 'toggle_estado', id_trabajador: id, estado: nuevoEstado })
    });
    if (res.ok) {
        toast('Estado del colaborador actualizado');
        cargarTrabajadores();
        cargarRepartidores();
    } else {
        toast(res.mensaje || 'Error', 'err');
    }
}

async function eliminarTrabajador(id, nombre) {
    const r = await Swal.fire({
        title: '¿Remover colaborador?',
        text: `Se quitará a ${nombre} de la planilla de colaboradores y se revocará su rol operativo. El usuario mantendrá su cuenta de cliente.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e30613',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Sí, remover',
        cancelButtonText: 'Cancelar'
    });
    if (!r.isConfirmed) return;

    const res = await api(BASE_URL + '/controllers/admin/TrabajadoresController.php', {
        method: 'POST',
        body: JSON.stringify({ accion: 'eliminar', id_trabajador: id })
    });
    if (res.ok) {
        toast(res.mensaje || 'Colaborador removido');
        cargarTrabajadores();
        cargarUsuarios();
    } else {
        toast(res.mensaje || 'Error', 'err');
    }
}

/* ══════════════════════════════════════════════
   STOCK
══════════════════════════════════════════════ */
function limpiarFiltrosStock() {
    const tipo = document.getElementById('filtroTipoStock');
    const fecha = document.getElementById('filtroFechaStock');
    if (tipo) tipo.value = '';
    if (fecha) fecha.value = '';
    cargarStock();
}

async function cargarStock() {
    const tipo = document.getElementById('filtroTipoStock')?.value || '';
    const fecha = document.getElementById('filtroFechaStock')?.value || '';
    let url = BASE_URL + '/controllers/admin/StockController.php';
    if (tipo || fecha) {
        url += `?tipo=${encodeURIComponent(tipo)}&fecha=${encodeURIComponent(fecha)}`;
    }
    const data = await api(url);
    if (!data.ok) return;
    const tbody = document.getElementById('tbodyStock');
    if (!tbody) return;
    tbody.innerHTML = '';
    (data.movimientos || []).forEach((m, index) => {
        const tipo = m.tipo_movimiento === 'ENTRADA'
            ? '<span class="badge badge-green">Entrada</span>'
            : '<span class="badge badge-red">Salida</span>';
        tbody.innerHTML += `<tr>
            <td>${index + 1}</td>
            <td>${m.id_movimiento}</td>
            <td>${m.producto}</td>
            <td>${tipo}</td>
            <td>${m.cantidad}</td>
            <td>${new Date(m.fecha_movimiento).toLocaleDateString('es-PE')}</td>
            <td>${m.motivo || '—'}</td>
        </tr>`;
    });
}

/* ══════════════════════════════════════════════
   COMPROBANTES
══════════════════════════════════════════════ */
async function cargarComprobantes() {
    const data = await api(BASE_URL + '/controllers/admin/ComprobantesController.php');
    if (!data.ok) return;
    const tbody = document.getElementById('tbodyComprobantes');
    if (!tbody) return;
    tbody.innerHTML = '';
    (data.comprobantes || []).forEach((c, index) => {
        tbody.innerHTML += `<tr>
            <td>${index + 1}</td>
            <td>${c.id_comprobante}</td>
            <td>${c.tipo_comprobante}</td>
            <td>${c.serie}-${String(c.numero).padStart(8,'0')}</td>
            <td>#${c.id_pedido}</td>
            <td>${new Date(c.fecha_emision).toLocaleDateString('es-PE')}</td>
            <td>S/ ${parseFloat(c.subtotal).toFixed(2)}</td>
            <td>S/ ${parseFloat(c.igv).toFixed(2)}</td>
            <td><strong>S/ ${parseFloat(c.total).toFixed(2)}</strong></td>
            <td><span class="badge badge-green">${c.estado}</span></td>
            <td>
                <a href="${BASE_URL}/views/pdf_comprobante.php?id=${c.id_pedido}" target="_blank" class="adm-btn adm-btn-dark adm-btn-sm" style="text-decoration:none;">Ver PDF</a>
            </td>
        </tr>`;
    });
}

/* ── Init ────────────────────────────────────── */
/**
 * ============================================================================
 * SCRIPT DE PANEL DE ADMINISTRACIÓN (DASHBOARD SPA)
 * ============================================================================
 * 
 * Centraliza la lógica asíncrona (Fetch) para todas las vistas del rol 'Admin'.
 * Actúa como un sistema Single Page Application (SPA) para:
 * 1. Cargar sub-módulos (Productos, Categorías, Usuarios, Pedidos) de manera dinámica.
 * 2. Enviar y recibir JSON estandarizado en los CRUD.
 * 3. Renderizar DataTables y gráficos de estadísticas.
 */
document.addEventListener('DOMContentLoaded', () => {
    // Escuchar cambios de hash (navegación atrás/adelante)
    window.addEventListener('hashchange', () => {
        const hash = window.location.hash.replace('#', '');
        if (hash && document.getElementById('sec-' + hash)) {
            navegar(hash);
        }
    });

    const hash = window.location.hash.replace('#', '');
    if (hash && document.getElementById('sec-' + hash)) {
        navegar(hash);
    } else {
        navegar('dashboard');
    }

    document.getElementById('admOverlay')?.addEventListener('click', cerrarSidebar);
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') {
            cerrarModalProducto();
            document.getElementById('modalDetallePedido')?.classList.remove('open');
        }
    });
});

window.navegar             = navegar;
window.toggleSidebar       = toggleSidebar;
window.abrirModalProducto  = abrirModalProducto;
window.cerrarModalProducto = cerrarModalProducto;
window.guardarProducto     = guardarProducto;
window.eliminarProducto    = eliminarProducto;
window.filtrarProductos    = filtrarProductos;
window.filtrarPedidos      = filtrarPedidos;
window.filtrarUsuarios     = filtrarUsuarios;
window.cambiarEstadoPedido = cambiarEstadoPedido;
window.verDetallePedido    = verDetallePedido;
window.toggleUsuario       = toggleUsuario;
window.previsualizarImgProd = previsualizarImgProd;

/* ══════════════════════════════════════════════
   DELIVERY
══════════════════════════════════════════════ */
let _deliveries = [], _repartidoresDisp = [];

async function cargarDelivery() {
    const data = await api(BASE_URL + '/controllers/admin/DeliveryController.php');
    if (!data.ok) return;
    _deliveries       = data.deliveries;
    _repartidoresDisp = data.repartidores;
    renderDelivery(_deliveries);
}

function renderDelivery(lista) {
    const tbody = document.getElementById('tbodyDelivery');
    if (!tbody) return;
    tbody.innerHTML = '';
    if (!lista.length) {
        tbody.innerHTML = '<tr><td colspan="9" style="text-align:center;color:#aaa;padding:24px;">Sin deliveries</td></tr>';
        return;
    }
    const frag = document.createDocumentFragment();
    lista.forEach((d, index) => {
        const estadoBadge = {
            'Pendiente': 'badge-yellow', 'Asignado': 'badge-orange', 'En camino': 'badge-blue', 'Entregado': 'badge-green'
        }[d.estado_delivery] || 'badge-gray';

        const etiquetaEstado = d.estado_delivery === 'Asignado' ? 'Esperando aceptación' : d.estado_delivery;

        const metodoIcon = {
            'Efectivo': '💵', 'Tarjeta de Crédito/Débito': '💳', 'Yape / Plin': '📱'
        }[d.metodo_pago] || '';
        let pagoHtml = `${metodoIcon} ${d.metodo_pago || '—'}`;
        if (d.metodo_pago === 'Efectivo' && d.monto_recibido) {
            pagoHtml += `<br><span style="color:#059669;font-size:.72rem;">
                Paga con S/ ${parseFloat(d.monto_recibido).toFixed(2)} · vuelto S/ ${parseFloat(d.vuelto||0).toFixed(2)}
            </span>`;
        }

        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${index + 1}</td>
            <td>#${d.id_delivery}</td>
            <td>#${d.id_pedido}</td>
            <td>${d.cliente || 'Sin nombre'}${d.id_usuario_pedido === null ? '<br><span style="background: #e2e8f0; color: #475569; padding: 2px 6px; border-radius: 4px; font-size: 0.70rem; font-weight: 600;">Invitado</span>' : ''}</td>
            <td style="max-width:180px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;" title="${d.direccion_entrega}">${d.direccion_entrega}</td>
            <td>${d.repartidor_nombre || '<span style="color:#aaa;">Sin asignar</span>'}
                ${!d.repartidor_nombre && d.motivo_rechazo ? `<br><span style="color:#ef4444;font-size:.72rem;" title="Motivo del rechazo">⚠ ${d.motivo_rechazo}</span>` : ''}</td>
            <td>S/ ${parseFloat(d.costo_delivery||0).toFixed(2)}</td>
            <td style="font-size:.8rem;">${pagoHtml}</td>
            <td><span class="badge ${estadoBadge}">${etiquetaEstado}</span></td>
            <td></td>`;

        const td = tr.lastElementChild;

        if (d.estado_delivery === 'Pendiente' || d.estado_delivery === 'Asignado') {
            // Aún no aceptado por nadie: se puede asignar/reasignar libremente
            const btnAsig = document.createElement('button');
            btnAsig.className = 'adm-btn adm-btn-dark adm-btn-sm';
            btnAsig.textContent = d.repartidor_nombre ? '↺ Reasignar' : '+ Asignar';
            btnAsig.onclick = () => abrirModalAsignar(d.id_delivery, d.id_pedido);
            td.appendChild(btnAsig);

            if (d.estado_delivery === 'Asignado') {
                const btnQuitar = document.createElement('button');
                btnQuitar.className = 'adm-btn adm-btn-danger adm-btn-sm';
                btnQuitar.style.marginLeft = '4px';
                btnQuitar.textContent = '✕ Quitar';
                btnQuitar.title = 'Quitar asignación y devolver al pool';
                btnQuitar.onclick = () => cancelarAsignacion(d.id_delivery);
                td.appendChild(btnQuitar);
            }
        } else if (d.estado_delivery === 'En camino') {
            // Ya fue aceptado por el repartidor: no se puede reasignar desde aquí
            const span = document.createElement('span');
            span.style.cssText = 'color:#aaa;font-size:.78rem;display:block;margin-bottom:4px;';
            span.textContent = 'Aceptado por el repartidor';
            td.appendChild(span);

            const btnEnt = document.createElement('button');
            btnEnt.className = 'adm-btn adm-btn-success adm-btn-sm';
            btnEnt.textContent = '✓ Entregado';
            btnEnt.onclick = () => cambiarEstadoDelivery(d.id_delivery, 'Entregado');
            td.appendChild(btnEnt);
        }
        frag.appendChild(tr);
    });
    tbody.innerHTML = '';
    tbody.appendChild(frag);
}

function filtrarDelivery() {
    const q = document.getElementById('buscadorDelivery').value.toLowerCase();
    renderDelivery(_deliveries.filter(d =>
        (d.cliente || 'Sin nombre').toLowerCase().includes(q) || String(d.id_delivery).includes(q) || String(d.id_pedido).includes(q)));
}

function abrirModalAsignar(idDelivery, idPedido) {
    document.getElementById('asignarDeliveryId').value = idDelivery;
    document.getElementById('asignarRepTitulo').textContent = `Asignar repartidor — Delivery #${idDelivery}`;
    const sel = document.getElementById('asignarRepSelect');
    sel.innerHTML = '<option value="">-- Selecciona --</option>';
    
    if (_repartidoresDisp.length === 0) {
        sel.innerHTML += `<option disabled>No hay repartidores disponibles</option>`;
    } else {
        _repartidoresDisp.forEach(r => {
            const dniOk   = r.dni_valido == 1 || r.dni_valido === true;
            const placaOk = r.placa_valida == 1 || r.placa_valida === true || (r.placa_vehiculo && r.placa_vehiculo.trim() !== '' && r.placa_vehiculo !== 'Sin placa');

            let razon = '';
            if (!dniOk && !placaOk) razon = ' — ⚠ DNI y Placa pendientes';
            else if (!dniOk)        razon = ' — ⚠ DNI pendiente';
            else if (!placaOk)      razon = ' — ⚠ Placa pendiente';

            const esValido = dniOk && placaOk;
            const etiqueta = esValido
                ? `${r.nombres} · Placa: ${r.placa_vehiculo}`
                : `${r.nombres}${razon}`;
            const disabled = esValido ? '' : 'disabled';
            sel.innerHTML += `<option value="${r.id_repartidor}" ${disabled}>${etiqueta}</option>`;
        });
    }
    document.getElementById('modalAsignarRep').classList.add('open');
}
function cerrarModalAsignar() {
    document.getElementById('modalAsignarRep').classList.remove('open');
}

async function confirmarAsignarRepartidor() {
    const idDel = document.getElementById('asignarDeliveryId').value;
    const idRep = document.getElementById('asignarRepSelect').value;
    if (!idRep) { toast('Selecciona un repartidor', 'err'); return; }
    const data = await api(BASE_URL + '/controllers/admin/DeliveryController.php', {
        method: 'POST',
        body: JSON.stringify({ accion: 'asignar_repartidor', id_delivery: idDel, id_repartidor: idRep })
    });
    if (data.ok) { toast('Repartidor asignado'); cerrarModalAsignar(); cargarDelivery(); }
    else toast(data.mensaje || 'Error', 'err');
}

async function cambiarEstadoDelivery(idDelivery, estado) {
    const data = await api(BASE_URL + '/controllers/admin/DeliveryController.php', {
        method: 'POST',
        body: JSON.stringify({ accion: 'cambiar_estado', id_delivery: idDelivery, estado })
    });
    if (data.ok) { toast('Estado actualizado'); cargarDelivery(); }
    else toast(data.mensaje || 'Error', 'err');
}

async function cancelarAsignacion(idDelivery) {
    const result = await Swal.fire({
        title: '¿Quitar asignación?',
        text: '¿Quitar la asignación de este delivery? Volverá a quedar disponible para asignar.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e30613',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Sí, quitar',
        cancelButtonText: 'Cancelar'
    });
    if (!result.isConfirmed) return;
    const data = await api(BASE_URL + '/controllers/admin/DeliveryController.php', {
        method: 'POST',
        body: JSON.stringify({ accion: 'cancelar_asignacion', id_delivery: idDelivery })
    });
    if (data.ok) { toast('Asignación quitada'); cargarDelivery(); }
    else toast(data.mensaje || 'Error', 'err');
}

/* ══════════════════════════════════════════════
   REPARTIDORES
══════════════════════════════════════════════ */
let _candidatos = [], _candidatosFiltrados = [];

async function cargarRepartidores() {
    const data = await api(BASE_URL + '/controllers/admin/RepartidorController.php');
    if (!data.ok) return;
    _candidatos         = data.candidatos;
    _candidatosFiltrados = data.candidatos;
    renderRepartidores(data.repartidores);
    renderCandidatos(_candidatos);
}

function renderRepartidores(lista) {
    const tbody = document.getElementById('tbodyRepartidores');
    if (!tbody) return;
    const frag = document.createDocumentFragment();
    tbody.innerHTML = '';
    
    const chkAll = document.getElementById('chkAllRepartidores');
    if (chkAll) chkAll.checked = false;

    if (!lista.length) {
        tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;color:#aaa;padding:20px;">Sin repartidores registrados</td></tr>';
        return;
    }
    lista.forEach((r, index) => {
        const datosCompletos = parseInt(r.datos_completos);
        let estadoBadge;
        if (r.estado == 1) {
            estadoBadge = '<span class="badge badge-green">Activo</span>';
        } else if (!datosCompletos) {
            estadoBadge = '<span class="badge badge-orange" style="background:#fff7ed;color:#ea580c;">Pendiente</span>';
        } else {
            estadoBadge = '<span class="badge badge-red">Inactivo</span>';
        }
        
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td><input type="checkbox" class="chk-repartidor" value="${r.id_repartidor}" data-estado="${r.estado}" onchange="updateRepToolbar()"></td>
            <td>${index + 1}</td>
            <td>${r.id_repartidor}</td>
            <td><strong>${r.nombres}</strong><br><span style="font-size:.75rem;color:#888;">${r.correo || ''}</span></td>
            <td>${r.dni_valido
                ? `<span class="badge badge-green">${r.dni}</span>`
                : `<span class="badge badge-red" title="Debe completar su DNI en su perfil">Pendiente</span>`}</td>
            <td>${r.telefono || '<span style="color:#ea580c;font-size:.8rem;">Sin teléfono</span>'}</td>
            <td>${r.placa_vehiculo
                ? `<span class="badge badge-blue">${r.placa_vehiculo}</span>`
                : `<span class="badge badge-gray" title="El repartidor debe completar su placa en su perfil">Sin placa</span>`}</td>
            <td><span style="font-size:.82rem;">${r.entregados || 0}/${r.total_deliveries || 0}</span></td>
            <td>${estadoBadge}</td>`;
        
        frag.appendChild(tr);
    });
    tbody.appendChild(frag);
}

function toggleAllRepartidores(source) {
    const checkboxes = document.querySelectorAll('.chk-repartidor');
    checkboxes.forEach(cb => cb.checked = source.checked);
    updateRepToolbar();
}

function updateRepToolbar() {
    const all     = Array.from(document.querySelectorAll('.chk-repartidor'));
    const checked = all.filter(cb => cb.checked);
    const toolbar = document.getElementById('repToolbar');
    const headerDef = document.getElementById('repHeaderDefault');
    const label   = document.getElementById('repSelLabel');
    const btnAct  = document.getElementById('btnRepActivar');
    const btnDes  = document.getElementById('btnRepDesactivar');

    if (checked.length === 0) {
        toolbar.style.display = 'none';
        if(headerDef) headerDef.style.display = 'flex';
        document.getElementById('chkAllRepartidores').indeterminate = false;
        document.getElementById('chkAllRepartidores').checked = false;
        return;
    }

    toolbar.style.display = 'flex';
    if(headerDef) headerDef.style.display = 'none';
    label.textContent = `${checked.length} seleccionado${checked.length > 1 ? 's' : ''}`;

    const activos   = checked.filter(cb => parseInt(cb.dataset.estado) === 1).length;
    const inactivos = checked.filter(cb => parseInt(cb.dataset.estado) === 0).length;

    // Mostrar/ocultar segun el estado de la seleccion
    btnAct.style.display  = inactivos > 0 ? 'inline-flex' : 'none';
    btnDes.style.display  = activos   > 0 ? 'inline-flex' : 'none';

    // Indeterminate en el checkbox maestro
    const chkAll = document.getElementById('chkAllRepartidores');
    chkAll.indeterminate = checked.length > 0 && checked.length < all.length;
    chkAll.checked = checked.length === all.length;
}

async function accionesLoteRepartidores(accion) {
    const seleccionados = Array.from(document.querySelectorAll('.chk-repartidor:checked'));
    if (seleccionados.length === 0) {
        toast('Selecciona al menos un repartidor', 'err');
        return;
    }

    let objetivos = seleccionados;
    if (accion === 'activar') {
        objetivos = seleccionados.filter(cb => parseInt(cb.dataset.estado) === 0);
        if (objetivos.length === 0) {
            toast('Los repartidores seleccionados ya están activos', 'err');
            return;
        }
    } else if (accion === 'desactivar') {
        objetivos = seleccionados.filter(cb => parseInt(cb.dataset.estado) === 1);
        if (objetivos.length === 0) {
            toast('Los repartidores seleccionados ya están inactivos', 'err');
            return;
        }
    }

    const cant = objetivos.length;
    const txtAccion = accion === 'activar' 
        ? `activar a ${cant} repartidor${cant > 1 ? 'es inactivos' : ' inactivo'}` 
        : (accion === 'desactivar' 
            ? `desactivar a ${cant} repartidor${cant > 1 ? 'es activos' : ' activo'}` 
            : `eliminar a ${cant} repartidor${cant > 1 ? 'es' : ''}`);

    const result = await Swal.fire({
        title: '¿Estás seguro?',
        text: `¿Estás seguro de ${txtAccion}?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e30613',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Sí, proceder',
        cancelButtonText: 'Cancelar'
    });
    if (!result.isConfirmed) return;

    let errores = 0;
    for (const cb of objetivos) {
        const id = cb.value;
        const estadoActual = parseInt(cb.dataset.estado);
        
        try {
            if (accion === 'eliminar') {
                await api(BASE_URL + '/controllers/admin/RepartidorController.php', {
                    method: 'POST',
                    body: JSON.stringify({ accion: 'eliminar', id_repartidor: id })
                });
            } else if (accion === 'activar' || accion === 'desactivar') {
                const nuevoEstado = (accion === 'activar') ? 1 : 0;
                if (estadoActual !== nuevoEstado) {
                    await api(BASE_URL + '/controllers/admin/RepartidorController.php', {
                        method: 'POST',
                        body: JSON.stringify({ accion: 'toggle_estado', id_repartidor: id, estado: nuevoEstado })
                    });
                }
            }
        } catch (e) {
            errores++;
        }
    }
    
    if (errores > 0) {
        toast(`Acción completada con ${errores} errores`, 'err');
    } else {
        toast('Acción completada con éxito');
    }
    
    cargarRepartidores();
}

async function eliminarRepartidor(idRepartidor, nombre) {
    const result = await Swal.fire({
        title: '¿Estás seguro?',
        text: `¿Eliminar a "${nombre}" de la lista de repartidores? Esta acción no se puede deshacer.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e30613',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    });
    if (!result.isConfirmed) return;
    const data = await api(BASE_URL + '/controllers/admin/RepartidorController.php', {
        method: 'POST',
        body: JSON.stringify({ accion: 'eliminar', id_repartidor: idRepartidor })
    });
    if (data.ok) { toast('Repartidor eliminado'); cargarRepartidores(); }
    else toast(data.mensaje || 'Error', 'err');
}

// Removed Asignar rol a repartidor old function

async function toggleRepartidor(idRepartidor, estadoActual, idUsuario) {
    const nuevoEstado = estadoActual ? 0 : 1;
    const data = await api(BASE_URL + '/controllers/admin/RepartidorController.php', {
        method: 'POST',
        body: JSON.stringify({ accion: 'toggle_estado', id_repartidor: idRepartidor, estado: nuevoEstado })
    });
    if (data.ok) { toast('Estado actualizado'); cargarRepartidores(); }
    else toast(data.mensaje || 'Error', 'err');
}

async function pedirPlaca(idRepartidor) {
    const placa = prompt('Ingresa la placa del vehículo:');
    if (!placa) return;
    const data = await api(BASE_URL + '/controllers/admin/RepartidorController.php', {
        method: 'POST',
        body: JSON.stringify({ accion: 'actualizar', id_repartidor: idRepartidor, placa })
    });
    if (data.ok) { toast('Placa guardada'); cargarRepartidores(); }
    else toast(data.mensaje || 'Error', 'err');
}

// Exponer nuevas funciones
window.filtrarDelivery           = filtrarDelivery;
window.abrirModalAsignar         = abrirModalAsignar;
window.cerrarModalAsignar        = cerrarModalAsignar;
window.confirmarAsignarRepartidor= confirmarAsignarRepartidor;
window.cambiarEstadoDelivery     = cambiarEstadoDelivery;
window.cancelarAsignacion        = cancelarAsignacion;
window.abrirModalRoles           = abrirModalRoles;
window.cerrarModalRoles          = cerrarModalRoles;
window.guardarRolesUsuario       = guardarRolesUsuario;
window.toggleRepartidor          = toggleRepartidor;
window.pedirPlaca                = pedirPlaca;
window.toggleAllRepartidores     = toggleAllRepartidores;
window.updateRepToolbar          = updateRepToolbar;
window.accionesLoteRepartidores  = accionesLoteRepartidores;

/* ══════════════════════════════════════════════
   VENTAS POR DÍA
══════════════════════════════════════════════ */

/** Inicializa los date-pickers con el mes actual y carga datos */
function iniciarSeccionVentas() {
    const hoy     = new Date();
    const desdeEl = document.getElementById('ventaDesde');
    const hastaEl = document.getElementById('ventaHasta');
    if (!desdeEl || !hastaEl) return;
    if (!desdeEl.value) {
        const primerDia = new Date(hoy.getFullYear(), hoy.getMonth(), 1);
        desdeEl.value = primerDia.toISOString().slice(0, 10);
    }
    if (!hastaEl.value) {
        hastaEl.value = hoy.toISOString().slice(0, 10);
    }
    cargarVentas();
}

/** Atajos rápidos de rango: 'hoy', 'semana', 'mes' */
function ventasAtajoRango(rango) {
    const hoy  = new Date();
    const hasta = hoy.toISOString().slice(0, 10);
    let desde;
    if (rango === 'hoy') {
        desde = hasta;
    } else if (rango === 'semana') {
        const d = new Date(hoy);
        d.setDate(d.getDate() - 6);
        desde = d.toISOString().slice(0, 10);
    } else { // mes
        desde = new Date(hoy.getFullYear(), hoy.getMonth(), 1).toISOString().slice(0, 10);
    }
    document.getElementById('ventaDesde').value = desde;
    document.getElementById('ventaHasta').value = hasta;
    cargarVentas();
}

/** Carga y renderiza las ventas por día */
async function cargarVentas() {
    const desde  = document.getElementById('ventaDesde')?.value  || '';
    const hasta  = document.getElementById('ventaHasta')?.value  || '';
    const estado = document.getElementById('ventaEstado')?.value || '';

    const tbody = document.getElementById('tbodyVentas');
    const tfoot = document.getElementById('tfootVentas');
    if (!tbody) return;

    tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;color:#aaa;padding:24px;">Cargando...</td></tr>';
    if (tfoot) tfoot.innerHTML = '';
    const vRes = document.getElementById('ventasResumen');
    const vBar = document.getElementById('ventasBarraCard');
    if (vRes) vRes.style.display = 'none';
    if (vBar) vBar.style.display = 'none';
    // Usamos POST para evitar caché del navegador
    const data = await fetch(BASE_URL + '/controllers/admin/VentasDiariasController.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Cache-Control': 'no-cache' },
        body: JSON.stringify({ desde, hasta, estado })
    }).then(r => r.json()).catch(() => ({ ok: false }));

    if (!data.ok) {
        tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;color:#e30613;padding:24px;">Error al cargar los datos</td></tr>';
        return;
    }

    const filas    = data.filas    || [];
    const totales  = data.totales  || {};
    const mejorDia = data.mejor_dia;

    // ── KPI cards ──────────────────────────────────────────────────
    const fmtS  = v  => 'S/ ' + parseFloat(v || 0).toFixed(2);
    const setEl = (id, v) => { const e = document.getElementById(id); if (e) e.textContent = v; };

    setEl('kpiPedidos',    totales.pedidos    || 0);
    setEl('kpiIngresos',   fmtS(totales.ingresos));
    setEl('kpiEntregados', totales.entregados || 0);
    setEl('kpiCancelados', totales.cancelados || 0);
    setEl('kpiMejorDia',   mejorDia
        ? new Date(mejorDia.dia + 'T00:00:00').toLocaleDateString('es-PE', { day:'2-digit', month:'short' })
          + ' · ' + fmtS(mejorDia.ingresos)
        : '—');

    // Mostrar etiqueta del filtro activo en el KPI
    if (estado) {
        const labelMap = {
            'Pendiente': 'Pendientes', 'En preparacion': 'En preparación',
            'En camino': 'En camino', 'Entregado': 'Entregados', 'Cancelado': 'Cancelados'
        };
        const elLbl = document.getElementById('kpiEntregados');
        const elPar = elLbl?.closest('.ventas-kpi');
        if (elPar) {
            elPar.querySelector('span').textContent = labelMap[estado] || estado;
            const countMap = {
                'Pendiente': totales.pendientes, 'En preparacion': totales.en_preparacion,
                'En camino': totales.en_camino, 'Entregado': totales.entregados, 'Cancelado': totales.cancelados
            };
            setEl('kpiEntregados', countMap[estado] || 0);
        }
    } else {
        // Restaurar etiqueta original
        const elPar = document.getElementById('kpiEntregados')?.closest('.ventas-kpi');
        if (elPar) elPar.querySelector('span').textContent = 'Pedidos entregados';
        setEl('kpiEntregados', totales.entregados || 0);
    }

    document.getElementById('ventasResumen').style.display = '';

    // ── Info de período ────────────────────────────────────────────
    const totalDias = filas.length;
    const filtroLbl = estado ? ` · Filtro: "${estado}"` : '';
    setEl('ventasTablaInfo', `${totalDias} día${totalDias !== 1 ? 's' : ''} encontrado${totalDias !== 1 ? 's' : ''}${filtroLbl}`);

    // ── Tabla detallada ────────────────────────────────────────────
    if (filas.length === 0) {
        tbody.innerHTML = '<tr><td colspan="8" style="text-align:center;color:#aaa;padding:30px;">Sin ventas en este período</td></tr>';
        return;
    }

    const fmt = n => parseFloat(n || 0).toFixed(2);

    // Cuando hay filtro de estado, resaltar la columna relevante
    const estadoFiltro = estado;
    const colStyle = (colEstado) => {
        if (!estadoFiltro) return '';
        const map = {
            'Entregado':      'entregados',
            'En camino':      'en_camino',
            'Pendiente':      'pendientes',
            'En preparacion': 'en_preparacion',
            'Cancelado':      'cancelados'
        };
        return map[estadoFiltro] === colEstado
            ? 'style="font-weight:700;color:#e30613;"'
            : 'style="color:#94a3b8;"';
    };

    tbody.innerHTML = filas.map(f => {
        const fecha = new Date(f.dia + 'T00:00:00').toLocaleDateString('es-PE', {
            weekday: 'short', year: 'numeric', month: 'short', day: '2-digit'
        });
        const esMejor = mejorDia && f.dia === mejorDia.dia;
        return `<tr${esMejor ? ' class="ventas-fila-destacada"' : ''}>
            <td><strong>${fecha}</strong>${esMejor ? ' <span class="ventas-mejor-badge">&#9733; Mejor</span>' : ''}</td>
            <td>${f.total_pedidos}</td>
            <td ${colStyle('entregados')}>${f.entregados}</td>
            <td ${colStyle('en_camino')}>${f.en_camino}</td>
            <td ${colStyle('pendientes')}>${f.pendientes}</td>
            <td ${colStyle('cancelados')}><span style="color:${!estadoFiltro && parseInt(f.cancelados) > 0 ? '#dc2626' : 'inherit'};">${f.cancelados}</span></td>
            <td>S/ ${fmt(f.ingresos)}</td>
            <td><strong>S/ ${fmt(f.ingresos_reales)}</strong></td>
        </tr>`;
    }).join('');

    // ── Fila de totales (tfoot) ────────────────────────────────────
    if (tfoot) {
        tfoot.innerHTML = `
            <tr class="ventas-fila-total">
                <td><strong>TOTAL (${totalDias} día${totalDias !== 1 ? 's' : ''})</strong></td>
                <td><strong>${totales.pedidos || 0}</strong></td>
                <td><strong style="color:#059669;">${totales.entregados || 0}</strong></td>
                <td>—</td><td>—</td>
                <td><strong style="color:#dc2626;">${totales.cancelados || 0}</strong></td>
                <td><strong>S/ ${fmt(totales.ingresos)}</strong></td>
                <td><strong>S/ ${fmt(totales.ingresos_reales)}</strong></td>
            </tr>`;
    }

    // ── Gráfica de barras ──────────────────────────────────────────
    renderVentasBarras(filas, desde, hasta);
}

/** Renderiza un mini gráfico de barras CSS puro */
function renderVentasBarras(filas, desde, hasta) {
    const wrap      = document.getElementById('ventasBarras');
    const card      = document.getElementById('ventasBarraCard');
    const periodoEl = document.getElementById('ventasBarraPeriodo');
    if (!wrap || !card) return;
    if (filas.length === 0) { card.style.display = 'none'; return; }

    const maxVal = Math.max(...filas.map(f => parseFloat(f.ingresos || 0)), 1);
    if (periodoEl) {
        const dFmt = d => new Date(d + 'T00:00:00').toLocaleDateString('es-PE', { day:'2-digit', month:'short' });
        periodoEl.textContent = `${dFmt(desde)} — ${dFmt(hasta)}`;
    }

    // Mostrar en orden cronológico (máx 31 barras)
    const mostrar = filas.slice().reverse().slice(0, 31);
    wrap.innerHTML = mostrar.map(f => {
        const pct   = Math.max((parseFloat(f.ingresos || 0) / maxVal) * 100, 2).toFixed(1);
        const fecha = new Date(f.dia + 'T00:00:00').toLocaleDateString('es-PE', { day:'2-digit', month:'short' });
        const monto = 'S/ ' + parseFloat(f.ingresos || 0).toFixed(2);
        return `<div class="ventas-barra-col" title="${fecha}: ${monto}">
            <div class="ventas-barra-val">${monto}</div>
            <div class="ventas-barra-bar" style="height:${pct}%;"></div>
            <div class="ventas-barra-label">${fecha.slice(0, 5)}</div>
        </div>`;
    }).join('');

    card.style.display = '';
}

window.cargarVentas         = cargarVentas;
window.ventasAtajoRango     = ventasAtajoRango;
window.iniciarSeccionVentas = iniciarSeccionVentas;
