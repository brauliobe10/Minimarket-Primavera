/* panel_trabajador.js — Lógica operativa del Portal de Empleados */

let _portalData = {
    trabajador: null,
    pedidos_despacho: [],
    inventario: [],
    movimientos: [],
    entregas: []
};

let _posCart = [];

document.addEventListener('DOMContentLoaded', () => {
    cargarDatosPortal();
});

async function api(url, options = {}) {
    const res = await fetch(url, {
        headers: { 'Content-Type': 'application/json' },
        ...options
    });
    return res.json();
}

// ──────────────────────────────────────────────
// CARGA INICIAL
// ──────────────────────────────────────────────
async function cargarDatosPortal() {
    try {
        const data = await api(BASE_URL + '/controllers/PanelTrabajadorController.php');
        if (!data.ok) {
            Swal.fire('Atención', data.mensaje || 'Acceso restringido', 'error').then(() => {
                window.location.href = BASE_URL + '/views/inicio.php';
            });
            return;
        }

        _portalData = data;
        actualizarHeaderUsuario();
        configurarPestanasPorRol();
        renderDespacho();
        renderAlmacen();
        renderPOS();
        renderReparto();
    } catch (err) {
        console.error('Error cargando portal:', err);
    }
}

function actualizarHeaderUsuario() {
    const t = _portalData.trabajador;
    if (!t) return;

    document.getElementById('ptUserNombre').textContent = `${t.nombres} ${t.apellidos}`;
    document.getElementById('ptUserTurno').textContent = `Turno ${t.turno || 'Mañana'}`;

    const badge = document.getElementById('ptUserCargoBadge');
    badge.textContent = t.cargo;

    const roleClassMap = {
        'Despachador': 'role-despachador',
        'Almacenero':  'role-almacenero',
        'Repartidor':  'role-repartidor',
        'Cajero':      'role-cajero',
        'Administrador':'role-admin'
    };
    badge.className = `pt-badge-role ${roleClassMap[t.cargo] || 'role-despachador'}`;
}

function configurarPestanasPorRol() {
    const cargo = _portalData.trabajador?.cargo || '';
    const esAdmin = cargo === 'Administrador';

    // Ocultar pestañas no pertinentes para roles operativos únicos
    if (!esAdmin) {
        if (cargo === 'Despachador') {
            document.querySelectorAll('[data-tab="almacen"], [data-tab="caja"], [data-tab="reparto"]').forEach(el => el.style.display = 'none');
            cambiarTab('despacho');
        } else if (cargo === 'Almacenero') {
            document.querySelectorAll('[data-tab="despacho"], [data-tab="caja"], [data-tab="reparto"]').forEach(el => el.style.display = 'none');
            cambiarTab('almacen');
        } else if (cargo === 'Cajero') {
            document.querySelectorAll('[data-tab="almacen"], [data-tab="reparto"]').forEach(el => el.style.display = 'none');
            cambiarTab('caja');
        } else if (cargo === 'Repartidor') {
            document.querySelectorAll('[data-tab="almacen"], [data-tab="caja"]').forEach(el => el.style.display = 'none');
            cambiarTab('reparto');
        }
    } else {
        cambiarTab('despacho');
    }
}

function cambiarTab(tabId) {
    document.querySelectorAll('.pt-tab-btn').forEach(btn => btn.classList.remove('active'));
    document.querySelectorAll('.pt-section').forEach(sec => sec.classList.remove('active'));

    const activeBtn = document.querySelector(`[data-tab="${tabId}"]`);
    const activeSec = document.getElementById(`tab-${tabId}`);

    if (activeBtn) activeBtn.classList.add('active');
    if (activeSec) activeSec.classList.add('active');
}

// ──────────────────────────────────────────────
// 1. MÓDULO DESPACHO (PICKING & PACKING)
// ──────────────────────────────────────────────
function renderDespacho() {
    const grid = document.getElementById('gridPedidosDespacho');
    const badgeCount = document.getElementById('countDespacho');
    const pedidos = _portalData.pedidos_despacho || [];

    if (badgeCount) badgeCount.textContent = pedidos.length;
    if (!grid) return;

    if (pedidos.length === 0) {
        grid.innerHTML = `
            <div style="grid-column: 1/-1; text-align: center; padding: 50px; background: white; border-radius: 12px; border: 1px dashed #cbd5e1;">
                <div style="font-size: 2.5rem; margin-bottom: 10px;">📦</div>
                <h4 style="color: #0f172a; font-weight: 700;">¡Bandeja al día!</h4>
                <p style="color: #64748b; font-size: 0.9rem;">No hay pedidos pendientes de empaque en este momento.</p>
            </div>
        `;
        return;
    }

    grid.innerHTML = pedidos.map(p => {
        const statusClass = p.estado_pedido === 'Listo para entrega' 
            ? 'status-Listo' 
            : (p.estado_pedido === 'En preparación' ? 'status-En_preparacion' : 'status-Pendiente');

        const items = p.items || [];
        const itemsHtml = items.map((it, i) => `
            <label class="pt-check-item">
                <input type="checkbox" id="chk_${p.id_pedido}_${i}" onchange="verificarItemsDespacho(${p.id_pedido})">
                <span class="pt-item-qty">${it.cantidad}x</span>
                <span style="flex:1; color:#1e293b;">${it.nombre}</span>
                <span style="font-size:0.78rem; color:#64748b;">S/ ${(it.precio * it.cantidad).toFixed(2)}</span>
            </label>
        `).join('');

        let botonAccion = '';
        if (p.estado_pedido === 'Pendiente') {
            botonAccion = `
                <button class="pt-btn pt-btn-blue pt-btn-full" onclick="iniciarPreparacion(${p.id_pedido})">
                    <i class="fa fa-hand-holding-box"></i> Empezar a Armar
                </button>
            `;
        } else if (p.estado_pedido === 'En preparación') {
            botonAccion = `
                <button class="pt-btn pt-btn-success pt-btn-full" id="btnListo_${p.id_pedido}" onclick="completarDespacho(${p.id_pedido})">
                    <i class="fa fa-check-circle"></i> Marcar Listo para Entrega
                </button>
            `;
        } else {
            botonAccion = `
                <div style="background:#ecfdf5; color:#059669; font-weight:700; text-align:center; padding:8px; border-radius:6px; font-size:0.85rem; width:100%;">
                    ✓ Empacado — Esperando ${p.tipo_entrega === 'Delivery' ? 'Repartidor' : 'Retiro en Tienda'}
                </div>
            `;
        }

        return `
            <div class="pt-order-card ${statusClass}" id="card_pedido_${p.id_pedido}">
                <div>
                    <div class="pt-order-head">
                        <div>
                            <span class="pt-order-id">#${p.id_pedido}</span>
                            <span style="font-size:0.75rem; font-weight:700; background:#f1f5f9; padding:2px 8px; border-radius:10px; margin-left:6px;">
                                ${p.tipo_entrega}
                            </span>
                        </div>
                        <div class="pt-order-time">${new Date(p.fecha_pedido).toLocaleTimeString('es-PE', {hour: '2-digit', minute:'2-digit'})}</div>
                    </div>

                    <div class="pt-order-client">
                        <strong>${p.nombre_cliente}</strong>
                        <span>📞 ${p.telefono_cliente || 'Sin teléfono'} ${p.direccion_entrega ? '• ' + p.direccion_entrega : ''}</span>
                    </div>

                    <div style="font-size:0.8rem; font-weight:700; color:#64748b; margin-bottom:6px; text-transform:uppercase; letter-spacing:0.04em;">
                        Productos por recolectar (${items.length}):
                    </div>
                    <div class="pt-checklist-box">
                        ${itemsHtml}
                    </div>
                </div>

                <div class="pt-order-actions">
                    ${botonAccion}
                </div>
            </div>
        `;
    }).join('');
}

function verificarItemsDespacho(idPedido) {
    const card = document.getElementById(`card_pedido_${idPedido}`);
    if (!card) return;
    const checkboxes = card.querySelectorAll('input[type="checkbox"]');
    const todosMarcados = Array.from(checkboxes).every(c => c.checked);
    const btnListo = document.getElementById(`btnListo_${idPedido}`);
    if (btnListo) {
        if (todosMarcados) {
            btnListo.style.boxShadow = '0 0 10px rgba(16, 185, 129, 0.5)';
        } else {
            btnListo.style.boxShadow = 'none';
        }
    }
}

async function iniciarPreparacion(idPedido) {
    const res = await api(BASE_URL + '/controllers/PanelTrabajadorController.php', {
        method: 'POST',
        body: JSON.stringify({ accion: 'actualizar_estado_pedido', id_pedido: idPedido, nuevo_estado: 'En preparación' })
    });
    if (res.ok) {
        Swal.fire({
            title: '¡Preparación Iniciada!',
            text: `El pedido #${idPedido} ahora está en preparación.`,
            icon: 'success',
            timer: 1500,
            showConfirmButton: false
        });
        cargarDatosPortal();
    } else {
        Swal.fire('Error', res.mensaje || 'No se pudo actualizar', 'error');
    }
}

async function completarDespacho(idPedido) {
    const card = document.getElementById(`card_pedido_${idPedido}`);
    const checkboxes = card ? card.querySelectorAll('input[type="checkbox"]') : [];
    const faltan = Array.from(checkboxes).some(c => !c.checked);

    if (faltan) {
        const conf = await Swal.fire({
            title: '¿Productos pendientes en el checklist?',
            text: 'No has marcado todos los productos como recolectados en la lista. ¿Seguro que está completo el paquete?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, empaque completo',
            cancelButtonText: 'Revisar de nuevo'
        });
        if (!conf.isConfirmed) return;
    }

    const res = await api(BASE_URL + '/controllers/PanelTrabajadorController.php', {
        method: 'POST',
        body: JSON.stringify({ accion: 'actualizar_estado_pedido', id_pedido: idPedido, nuevo_estado: 'Listo para entrega' })
    });

    if (res.ok) {
        Swal.fire({
            title: '¡Paquete Listo!',
            text: `El pedido #${idPedido} está listo para ser despachado.`,
            icon: 'success',
            timer: 1800,
            showConfirmButton: false
        });
        cargarDatosPortal();
    } else {
        Swal.fire('Error', res.mensaje || 'No se pudo actualizar', 'error');
    }
}

// ──────────────────────────────────────────────
// 2. MÓDULO ALMACÉN (STOCK & INVENTARIO)
// ──────────────────────────────────────────────
function renderAlmacen() {
    const tbody = document.getElementById('tbodyStockAlmacen');
    const tbodyMovs = document.getElementById('tbodyMovimientosAlmacen');
    const badgeCount = document.getElementById('countAlmacen');
    const inventario = _portalData.inventario || [];
    const criticos = inventario.filter(p => parseInt(p.stock) <= 5).length;

    if (badgeCount) badgeCount.textContent = criticos > 0 ? `${criticos} críticos` : inventario.length;
    if (!tbody) return;

    tbody.innerHTML = inventario.map((p, i) => {
        const stock = parseInt(p.stock);
        let badgeClass = 'stock-optimo';
        let badgeText = `${stock} unidades`;

        if (stock <= 5) {
            badgeClass = 'stock-critico';
            badgeText = `⚠️ Crítico: ${stock}`;
        } else if (stock <= 15) {
            badgeClass = 'stock-bajo';
            badgeText = `Bajo: ${stock}`;
        }

        return `
            <tr>
                <td>${i + 1}</td>
                <td>
                    <div style="display:flex; align-items:center; gap:10px;">
                        <img src="${p.imagen || BASE_URL + '/img/imagen1.webp'}" style="width:36px; height:36px; border-radius:6px; object-fit:cover; border:1px solid #e2e8f0;" onerror="this.src=BASE_URL + '/img/imagen1.webp'">
                        <div>
                            <strong style="display:block; color:#0f172a;">${p.nombre_producto}</strong>
                            <small style="color:#64748b;">${p.nombre_categoria || 'Sin categoría'}</small>
                        </div>
                    </div>
                </td>
                <td style="font-weight:700; color:#0f172a;">S/ ${parseFloat(p.precio).toFixed(2)}</td>
                <td>
                    <span class="pt-stock-badge ${badgeClass}">${badgeText}</span>
                </td>
                <td>
                    <div style="display:flex; gap:6px;">
                        <button class="pt-btn pt-btn-success" style="padding:4px 10px; font-size:0.75rem;" onclick="abrirModalMovStock('ENTRADA', ${p.id_producto}, '${p.nombre_producto.replace(/'/g, "\\'")}', ${stock})">
                            + Entrada
                        </button>
                        <button class="pt-btn pt-btn-ghost" style="padding:4px 10px; font-size:0.75rem; color:#dc2626;" onclick="abrirModalMovStock('SALIDA', ${p.id_producto}, '${p.nombre_producto.replace(/'/g, "\\'")}', ${stock})">
                            - Merma
                        </button>
                    </div>
                </td>
            </tr>
        `;
    }).join('');

    // Movimientos recientes
    if (tbodyMovs) {
        const movs = _portalData.movimientos || [];
        tbodyMovs.innerHTML = movs.map(m => `
            <tr>
                <td><strong>${m.nombre_producto}</strong></td>
                <td>
                    <span class="pt-stock-badge ${m.tipo_movimiento === 'ENTRADA' ? 'stock-optimo' : 'stock-critico'}" style="font-size:0.72rem;">
                        ${m.tipo_movimiento === 'ENTRADA' ? '+' : '-'}${m.cantidad}
                    </span>
                </td>
                <td style="font-size:0.8rem; color:#64748b;">${m.motivo || 'Ajuste'}</td>
                <td style="font-size:0.78rem; color:#64748b;">${new Date(m.fecha_movimiento).toLocaleString('es-PE')}</td>
            </tr>
        `).join('');
    }
}

function filtrarInventario() {
    const q = (document.getElementById('buscadorAlmacen')?.value || '').toLowerCase();
    const rows = document.querySelectorAll('#tbodyStockAlmacen tr');
    rows.forEach(r => {
        r.style.display = r.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
}

function abrirModalMovStock(tipo, idProducto, nombre, stockActual) {
    document.getElementById('movStockTipo').value = tipo;
    document.getElementById('movStockIdProducto').value = idProducto;
    document.getElementById('movStockTitulo').textContent = tipo === 'ENTRADA' ? 'Ingreso de Mercadería (Entrada)' : 'Ajuste o Merma (Salida)';
    document.getElementById('movStockProductoNombre').textContent = `${nombre} (Stock actual: ${stockActual})`;
    document.getElementById('movStockCantidad').value = '';
    document.getElementById('movStockMotivo').value = tipo === 'ENTRADA' ? 'Recepción de compra / Proveedor' : 'Producto dañado o vencido';

    document.getElementById('modalMovimientoStock').classList.add('open');
}

function cerrarModalMovStock() {
    document.getElementById('modalMovimientoStock').classList.remove('open');
}

async function guardarMovimientoStock(e) {
    e.preventDefault();
    const idProducto = parseInt(document.getElementById('movStockIdProducto').value);
    const tipo = document.getElementById('movStockTipo').value;
    const cantidad = parseInt(document.getElementById('movStockCantidad').value);
    const motivo = document.getElementById('movStockMotivo').value.trim();

    if (cantidad <= 0) {
        Swal.fire('Cantidad inválida', 'La cantidad debe ser mayor a 0', 'warning');
        return;
    }

    const res = await api(BASE_URL + '/controllers/PanelTrabajadorController.php', {
        method: 'POST',
        body: JSON.stringify({
            accion: 'movimiento_stock',
            id_producto: idProducto,
            tipo: tipo,
            cantidad: cantidad,
            motivo: motivo
        })
    });

    if (res.ok) {
        Swal.fire({
            title: 'Stock Actualizado',
            text: res.mensaje,
            icon: 'success',
            timer: 1500,
            showConfirmButton: false
        });
        cerrarModalMovStock();
        cargarDatosPortal();
    } else {
        Swal.fire('Error', res.mensaje || 'No se pudo registrar el movimiento', 'error');
    }
}

// ──────────────────────────────────────────────
// 3. MÓDULO CAJA RÁPIDA (POS)
// ──────────────────────────────────────────────
function renderPOS() {
    const contenedor = document.getElementById('posGridProductos');
    if (!contenedor) return;

    const productos = _portalData.inventario || [];
    contenedor.innerHTML = productos.map(p => `
        <div class="pt-pos-card" onclick='agregarAlCarritoPOS(${JSON.stringify(p)})'>
            <img src="${p.imagen || BASE_URL + '/img/imagen1.webp'}" onerror="this.src=BASE_URL + '/img/imagen1.webp'">
            <div class="pt-pos-card-name">${p.nombre_producto}</div>
            <div class="pt-pos-card-price">S/ ${parseFloat(p.precio).toFixed(2)}</div>
            <small style="color:${parseInt(p.stock) <= 5 ? '#ef4444' : '#64748b'}; font-size:0.72rem;">Stock: ${p.stock}</small>
        </div>
    `).join('');
}

function filtrarProductosPOS() {
    const q = (document.getElementById('posBuscador')?.value || '').toLowerCase();
    const cards = document.querySelectorAll('.pt-pos-card');
    cards.forEach(c => {
        c.style.display = c.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
}

function agregarAlCarritoPOS(prod) {
    const stock = parseInt(prod.stock);
    if (stock <= 0) {
        Swal.fire('Sin stock', 'Este producto está agotado', 'warning');
        return;
    }

    const item = _posCart.find(it => it.id_producto === prod.id_producto);
    if (item) {
        if (item.cantidad + 1 > stock) {
            Swal.fire('Stock insuficiente', `Solo hay ${stock} unidades disponibles`, 'warning');
            return;
        }
        item.cantidad++;
    } else {
        _posCart.push({
            id_producto: prod.id_producto,
            nombre: prod.nombre_producto,
            precio: parseFloat(prod.precio),
            stock: stock,
            cantidad: 1
        });
    }

    actualizarCarritoPOS();
}

function modificarCantidadPOS(idProducto, delta) {
    const item = _posCart.find(it => it.id_producto === idProducto);
    if (!item) return;

    item.cantidad += delta;
    if (item.cantidad <= 0) {
        _posCart = _posCart.filter(it => it.id_producto !== idProducto);
    } else if (item.cantidad > item.stock) {
        item.cantidad = item.stock;
        Swal.fire('Límite alcanzado', `Máximo disponible: ${item.stock}`, 'info');
    }

    actualizarCarritoPOS();
}

function actualizarCarritoPOS() {
    const cont = document.getElementById('posCartItems');
    const totalEl = document.getElementById('posCartTotal');
    if (!cont) return;

    let total = 0;
    if (_posCart.length === 0) {
        cont.innerHTML = '<div style="text-align:center; color:#94a3b8; padding:20px; font-size:0.85rem;">Carrito vacío. Selecciona productos.</div>';
        if (totalEl) totalEl.textContent = 'S/ 0.00';
        return;
    }

    cont.innerHTML = _posCart.map(it => {
        const sub = it.precio * it.cantidad;
        total += sub;
        return `
            <div class="pt-cart-row">
                <div style="flex:1;">
                    <strong>${it.nombre}</strong>
                    <div style="color:#64748b; font-size:0.75rem;">S/ ${it.precio.toFixed(2)} c/u</div>
                </div>
                <div style="display:flex; align-items:center; gap:6px;">
                    <button type="button" class="pt-btn pt-btn-ghost" style="padding:2px 8px; font-size:0.75rem;" onclick="modificarCantidadPOS(${it.id_producto}, -1)">-</button>
                    <span style="font-weight:700; width:20px; text-align:center;">${it.cantidad}</span>
                    <button type="button" class="pt-btn pt-btn-ghost" style="padding:2px 8px; font-size:0.75rem;" onclick="modificarCantidadPOS(${it.id_producto}, 1)">+</button>
                </div>
                <div style="width:70px; text-align:right; font-weight:700; color:#0f172a;">
                    S/ ${sub.toFixed(2)}
                </div>
            </div>
        `;
    }).join('');

    if (totalEl) totalEl.textContent = `S/ ${total.toFixed(2)}`;
}

async function finalizarVentaPOS(e) {
    e.preventDefault();
    if (_posCart.length === 0) {
        Swal.fire('Carrito vacío', 'Agrega productos a la venta', 'warning');
        return;
    }

    const payload = {
        accion: 'venta_caja',
        items: _posCart,
        dni_cliente: document.getElementById('posDniCliente')?.value || '00000000',
        nombre_cliente: document.getElementById('posNombreCliente')?.value || 'Cliente Mostrador',
        metodo_pago: document.getElementById('posMetodoPago')?.value || 'Efectivo',
        tipo_comprobante: document.getElementById('posTipoComprobante')?.value || 'Boleta'
    };

    const res = await api(BASE_URL + '/controllers/PanelTrabajadorController.php', {
        method: 'POST',
        body: JSON.stringify(payload)
    });

    if (res.ok) {
        Swal.fire({
            title: '¡Venta Registrada!',
            html: `
                <div style="text-align:center;">
                    <div style="font-size:1.1rem; color:#10b981; font-weight:800; margin-bottom:8px;">${res.comprobante}</div>
                    <p style="font-size:0.95rem; color:#334155;">Total cobrado: <strong>S/ ${res.total}</strong></p>
                    <p style="font-size:0.8rem; color:#64748b; margin-top:6px;">Stock y caja actualizados exitosamente.</p>
                </div>
            `,
            icon: 'success'
        });

        _posCart = [];
        actualizarCarritoPOS();
        cargarDatosPortal();
    } else {
        Swal.fire('Error en la venta', res.mensaje || 'No se pudo procesar', 'error');
    }
}

// ──────────────────────────────────────────────
// 4. MÓDULO REPARTO (DELIVERY)
// ──────────────────────────────────────────────
function renderReparto() {
    const cont = document.getElementById('contenedorEntregasReparto');
    if (!cont) return;

    const entregas = _portalData.entregas || [];
    if (entregas.length === 0) {
        cont.innerHTML = `
            <div style="text-align:center; padding:40px; background:white; border-radius:12px; border:1px dashed #cbd5e1;">
                <div style="font-size:2.5rem; margin-bottom:10px;">🛵</div>
                <h4 style="color:#0f172a; font-weight:700;">Sin entregas activas</h4>
                <p style="color:#64748b; font-size:0.9rem;">No tienes entregas pendientes en tu ruta.</p>
            </div>
        `;
        return;
    }

    cont.innerHTML = entregas.map(d => `
        <div class="pt-card" style="margin-bottom:14px; border-left:4px solid #3b82f6;">
            <div class="pt-card-header" style="padding:12px 16px;">
                <div style="font-weight:800; font-size:1rem;">Delivery #${d.id_delivery} (Pedido #${d.id_pedido})</div>
                <span class="pt-badge-role role-repartidor">${d.estado_delivery}</span>
            </div>
            <div class="pt-card-body" style="padding:14px 16px; display:flex; justify-content:space-between; flex-wrap:wrap; gap:12px; align-items:center;">
                <div>
                    <strong>Cliente: ${d.nombre_cliente}</strong> (📞 ${d.telefono_cliente || '—'})
                    <div style="font-size:0.85rem; color:#64748b; margin-top:2px;">📍 ${d.direccion_entrega} ${d.referencia ? '(' + d.referencia + ')' : ''}</div>
                    <div style="font-size:0.85rem; color:#0f172a; font-weight:700; margin-top:4px;">Total a cobrar: S/ ${parseFloat(d.total).toFixed(2)}</div>
                </div>
                <div>
                    <a href="${BASE_URL}/views/panel_repartidor.php" class="pt-btn pt-btn-blue">
                        <i class="fa fa-map-marked-alt"></i> Ver en Mapa y Entregar
                    </a>
                </div>
            </div>
        </div>
    `).join('');
}
