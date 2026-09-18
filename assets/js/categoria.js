const overlay = document.getElementById('modalOverlay');
const cerrar = document.getElementById('modalCerrar');
const modalAgregarBtn = document.getElementById('modalAgregarCarrito');

let cardActualModal = null;

/* =========================================
   MODAL PRODUCTO
========================================= */

function abrirModal(card) {
    if (!card || !overlay) return;

    cardActualModal = card;

    document.getElementById('modalImagen').src = card.dataset.imagen;
    document.getElementById('modalImagen').alt = card.dataset.nombre;
    document.getElementById('modalNombre').textContent = card.dataset.nombre;
    document.getElementById('modalPrecio').textContent = 'S/ ' + parseFloat(card.dataset.precio).toFixed(2);
    document.getElementById('modalDescripcion').textContent =
        card.dataset.descripcion || 'Sin descripción disponible.';

    const stock = parseInt(card.dataset.stock);
    const modalStock = document.getElementById('modalStock');
    if (modalStock) {
        if (!isNaN(stock) && stock > 0) {
            modalStock.innerHTML = `Stock disponible: <span>${stock}</span>`;
            modalStock.classList.remove('agotado');
        } else {
            modalStock.textContent = 'Agotado';
            modalStock.classList.add('agotado');
        }
    }

    const modalCantidad = document.getElementById('modalCantidad');
    if (modalCantidad) {
        modalCantidad.value = (!isNaN(stock) && stock > 0) ? 1 : 0;
        if (!isNaN(stock)) {
            modalCantidad.setAttribute('max', stock);
        }
    }

    if (modalAgregarBtn) {
        if (!isNaN(stock) && stock <= 0) {
            modalAgregarBtn.disabled = true;
            modalAgregarBtn.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="8" cy="21" r="1"></circle>
                    <circle cx="19" cy="21" r="1"></circle>
                    <path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"></path>
                </svg> Agotado`;
        } else {
            modalAgregarBtn.disabled = false;
            modalAgregarBtn.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="8" cy="21" r="1"></circle>
                    <circle cx="19" cy="21" r="1"></circle>
                    <path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"></path>
                </svg> Agregar`;
        }
    }

    overlay.classList.add('activo');
    overlay.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
    document.documentElement.style.overflow = 'hidden';
}
function cerrarModal() {
    if (!overlay) return;

    overlay.classList.remove('activo');
    overlay.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
    document.documentElement.style.overflow = '';

    document.querySelectorAll(".producto-highlight").forEach(card => {
        card.classList.remove("producto-highlight");
    });

    cardActualModal = null;
}
/* =========================================
   STORAGE (REGLAS DEL CARRITO)
========================================= */

window._carritoCache = window._carritoCache || [];

function obtenerCarrito() {
    if (window._usuarioId && parseInt(window._usuarioId) > 0) {
        return window._carritoCache || [];
    }
    try {
        return JSON.parse(localStorage.getItem('carrito')) || [];
    } catch {
        return [];
    }
}

function guardarCarrito(carrito) {
    if (window._usuarioId && parseInt(window._usuarioId) > 0) {
        window._carritoCache = carrito;
        fetch(BASE_URL + '/controllers/CarritoController.php?action=sincronizar', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ items: carrito })
        }).catch(err => console.error('Error sincronizando carrito con BD:', err));
    } else {
        localStorage.setItem('carrito', JSON.stringify(carrito));
    }
    actualizarContadorCarrito();
    renderCarrito();
}

// Cargar carrito al iniciar la página
document.addEventListener('DOMContentLoaded', function () {
    if (window._usuarioId && parseInt(window._usuarioId) > 0) {
        // Regla 5: Eliminar inmediatamente el carrito en localStorage al iniciar sesión (nunca fusionar)
        localStorage.removeItem('carrito');
        localStorage.removeItem('carrito_invitado');

        // Cargar únicamente el carrito ACTIVO desde la Base de Datos
        fetch(BASE_URL + '/controllers/CarritoController.php?action=obtener')
            .then(res => res.json())
            .then(data => {
                if (data.ok && Array.isArray(data.items)) {
                    window._carritoCache = data.items;
                    actualizarContadorCarrito();
                    renderCarrito();
                }
            })
            .catch(err => console.error('Error cargando carrito desde BD:', err));
    } else {
        actualizarContadorCarrito();
        renderCarrito();
    }
});

/* =========================================
   CONTADOR
========================================= */

function actualizarContadorCarrito() {
    const carrito = obtenerCarrito();

    const totalItems = carrito.reduce((acc, item) => {
        return acc + item.cantidad;
    }, 0);

    document.querySelectorAll('.cart-count').forEach(el => {
        el.textContent = totalItems;
    });
}

/* =========================================
   STOCK Y CANTIDAD
========================================= */

function obtenerStockProducto(elemento) {
    if (cardActualModal && (elemento.closest('#modalOverlay') || elemento.classList.contains('modal-input-cantidad'))) {
        const modalStockVal = parseInt(cardActualModal.dataset.stock);
        if (!isNaN(modalStockVal)) return modalStockVal;
    }
    const card = elemento.closest('.card-producto');
    if (card && card.dataset.stock !== undefined) {
        const cardStockVal = parseInt(card.dataset.stock);
        if (!isNaN(cardStockVal)) return cardStockVal;
    }
    const input = elemento.classList.contains('input-cantidad') ? elemento : elemento.querySelector('.input-cantidad');
    if (input && input.hasAttribute('max')) {
        const maxAttr = parseInt(input.getAttribute('max'));
        if (!isNaN(maxAttr)) return maxAttr;
    }
    return 9999;
}

/* =========================================
   MODAL DE ALERTA DE STOCK DE CORTE MODERNO
========================================= */

function crearAlertaStockDOM() {
    if (document.getElementById('modalStockAlerta')) return;

    const overlay = document.createElement('div');
    overlay.id = 'modalStockAlerta';
    overlay.className = 'modal-stock-overlay';

    overlay.innerHTML = `
        <div class="modal-stock-card" role="dialog" aria-modal="true">
            <button class="modal-stock-cerrar" onclick="cerrarAlertaStock()" aria-label="Cerrar">✕</button>
            <div class="modal-stock-icono">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#e30613" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                    <line x1="12" y1="9" x2="12" y2="13"></line>
                    <line x1="12" y1="17" x2="12.01" y2="17"></line>
                </svg>
            </div>
            <h3 id="modalStockTitulo" class="modal-stock-titulo">Cantidad No Disponible</h3>
            <p id="modalStockMensaje" class="modal-stock-mensaje"></p>
            <div class="modal-stock-acciones">
                <button class="btn-stock-entendido" onclick="cerrarAlertaStock()">Entendido</button>
            </div>
        </div>
    `;

    document.body.appendChild(overlay);

    overlay.addEventListener('click', (e) => {
        if (e.target === overlay) cerrarAlertaStock();
    });
}

function mostrarAlertaStock(mensaje, titulo = 'Cantidad No Disponible') {
    crearAlertaStockDOM();
    const overlay = document.getElementById('modalStockAlerta');
    const msgEl = document.getElementById('modalStockMensaje');
    const titleEl = document.getElementById('modalStockTitulo');

    if (msgEl) msgEl.textContent = mensaje;
    if (titleEl) titleEl.textContent = titulo;

    if (overlay) {
        overlay.classList.add('activo');
    }
}

function cerrarAlertaStock() {
    const overlay = document.getElementById('modalStockAlerta');
    if (overlay) {
        overlay.classList.remove('activo');
    }
}

function cambiarCantidad(boton, delta, event) {
    if (event) event.stopPropagation();

    const contenedor = boton.closest('.selector-cantidad');
    if (!contenedor) return;

    const input = contenedor.querySelector('.input-cantidad');
    if (!input) return;

    const stockMax = obtenerStockProducto(boton);
    if (stockMax <= 0) {
        input.value = 0;
        return;
    }

    let valorActual = parseInt(input.value);
    if (isNaN(valorActual)) valorActual = 1;

    let valor = valorActual + delta;

    if (valor < 1) {
        valor = 1;
    } else if (valor > stockMax) {
        valor = stockMax;
        mostrarAlertaStock(`Solo hay ${stockMax} unidad(es) disponible(s) en stock.`);
    }

    input.value = valor;
}

function validarEntradaCantidad(input) {
    const stockMax = obtenerStockProducto(input);
    if (stockMax <= 0) {
        input.value = 0;
        return;
    }

    let val = parseInt(input.value);
    if (isNaN(val) || val < 1) {
        input.value = 1;
    } else if (val > stockMax) {
        mostrarAlertaStock(`No puedes solicitar más de ${stockMax} unidad(es) disponible(s) en stock.`);
        // Dejamos el valor excedido en el input para que 'agregarAlCarrito' 
        // lo atrape y rechace la acción sin agregar nada en absoluto.
    }
}

/* =========================================
   AGREGAR CARRITO
========================================= */

function agregarAlCarritoPorId(btn, event) {
    if (event) event.stopPropagation();

    const card = btn.closest('.card-producto');
    if (!card) return;

    agregarAlCarrito(card);
}

function agregarAlCarrito(card, desdeModal = false) {
    if (!card) return;

    const id             = card.id.replace('producto-', '');
    const nombre         = card.dataset.nombre;
    const precio         = parseFloat(card.dataset.precio);
    const precioOriginal = parseFloat(card.dataset.precioOriginal || card.dataset.precio);
    const imagen         = card.dataset.imagen;
    const stockDisponible = parseInt(card.dataset.stock) || 0;

    if (stockDisponible <= 0) {
        mostrarAlertaStock('Este producto está agotado y no cuenta con stock disponible.', 'Producto Agotado');
        return;
    }

    let cantidadSeleccionada = 1;

    if (desdeModal) {
        const modalInput = document.getElementById('modalCantidad');
        cantidadSeleccionada = modalInput ? parseInt(modalInput.value) : 1;
    } else {
        const inputCantidad = card.querySelector('.input-cantidad');
        cantidadSeleccionada = inputCantidad ? parseInt(inputCantidad.value) : 1;
    }

    if (isNaN(cantidadSeleccionada) || cantidadSeleccionada < 1) {
        cantidadSeleccionada = 1;
    }

    let carrito = obtenerCarrito();
    const existente = carrito.find(item => String(item.id) === String(id));
    const cantidadEnCarrito = existente ? existente.cantidad : 0;
    const totalDeseado = cantidadEnCarrito + cantidadSeleccionada;

    if (totalDeseado > stockDisponible) {
        const disponibleMas = Math.max(0, stockDisponible - cantidadEnCarrito);
        if (disponibleMas === 0) {
            mostrarAlertaStock(`Ya tienes en tu carrito las ${stockDisponible} unidades disponibles de este producto.`);
        } else {
            mostrarAlertaStock(`No es posible agregar ${cantidadSeleccionada} unidades más. Solo quedan ${disponibleMas} unidad(es) disponibles (Stock: ${stockDisponible}, Ya en carrito: ${cantidadEnCarrito}).`);
        }
        return;
    }

    if (existente) {
        existente.cantidad += cantidadSeleccionada;
    } else {
        carrito.push({ id, nombre, precio, precioOriginal, imagen, cantidad: cantidadSeleccionada, stock: stockDisponible });
    }

    const inputCantidadCard = card.querySelector('.input-cantidad');
    if (inputCantidadCard) inputCantidadCard.value = 1;

    guardarCarrito(carrito);
    mostrarNotificacionCarrito(nombre);

    if (desdeModal) {
        cerrarModal();
        abrirCarrito();
    }
}
function cerrarCarrito() {
    const drawer = document.getElementById('drawerCarrito');
    const carritoOverlay = document.getElementById('drawerOverlay');

    if (!drawer) return;

    drawer.classList.remove('activo');

    // quitar blur
    if (carritoOverlay) {
        carritoOverlay.classList.remove('activo');
    }

    // restaurar scroll
    document.body.style.overflow = '';
    document.documentElement.style.overflow = '';
}
/* =========================================
   ELIMINAR
========================================= */

function eliminarDelCarrito(id) {
    let carrito = obtenerCarrito();
    carrito = carrito.filter(item => String(item.id) !== String(id));

    if (window._usuarioId && parseInt(window._usuarioId) > 0) {
        window._carritoCache = carrito;
        fetch(BASE_URL + '/controllers/CarritoController.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'eliminar', id_producto: id })
        }).catch(err => console.error('Error al eliminar producto del carrito en BD:', err));
    }

    guardarCarrito(carrito);
}

/* =========================================
   RENDER
========================================= */

function renderCarrito() {
    const carrito     = obtenerCarrito();
    const contenedor  = document.getElementById('carritoItems');
    const subtotalEl  = document.getElementById('carritoSubtotal');
    const descuentoEl = document.getElementById('carritoDescuento');
    const igvEl       = document.getElementById('carritoIgv');
    const totalEl     = document.getElementById('carritoTotal');
    const lineaDesc   = document.getElementById('lineaDescuento');

    if (!contenedor) return;

    contenedor.innerHTML = '';

    if (carrito.length === 0) {
        contenedor.innerHTML = `
            <div class="carrito-vacio">
                <i class="fa fa-shopping-cart"></i>
                Tu carrito está vacío
            </div>
        `;
        if (subtotalEl)  subtotalEl.textContent  = 'S/ 0.00';
        if (descuentoEl) descuentoEl.textContent = 'S/ 0.00';
        if (igvEl)       igvEl.textContent       = 'S/ 0.00';
        if (totalEl)     totalEl.textContent     = 'S/ 0.00';
        if (lineaDesc)   lineaDesc.style.display = 'none';
        return;
    }

    let subtotalSinDesc = 0;
    let subtotalConDesc = 0;

    carrito.forEach(item => {
        const precioOriginal = item.precioOriginal || item.precio;
        const precioFinal    = item.precio;

        const cardEl = document.getElementById('producto-' + item.id);
        if (cardEl && cardEl.dataset.stock !== undefined && cardEl.dataset.stock !== '') {
            const currentStock = parseInt(cardEl.dataset.stock);
            if (!isNaN(currentStock)) {
                item.stock = currentStock;
            }
        }

        const maxStock = obtenerStockItem(item);

        if (item.cantidad > maxStock && maxStock >= 0) {
            item.cantidad = maxStock > 0 ? maxStock : 1;
        }

        const totalItem = precioFinal * item.cantidad;

        subtotalSinDesc += precioOriginal * item.cantidad;
        subtotalConDesc += totalItem;

        const tieneDesc = precioOriginal > precioFinal;

        contenedor.innerHTML += `
            <div class="carrito-item">
                <img src="${item.imagen}" alt="${item.nombre}"
                     onerror="this.src=BASE_URL + '/img/imagen1.webp'">
                <div class="carrito-info">
                    <h4>${item.nombre}</h4>
                    <p class="item-precio">S/ ${precioFinal.toFixed(2)}
                        ${tieneDesc
                            ? `<small style="color:#aaa;text-decoration:line-through;font-size:.75rem;font-weight:400;margin-left:4px;">S/ ${precioOriginal.toFixed(2)}</small>`
                            : ''}
                    </p>
                    <div class="item-controles">
                        <button class="btn-qty" onclick="cambiarCantidadCarrito('${item.id}', -1)">−</button>
                        <input type="number" class="qty-input" value="${item.cantidad}" min="1" max="${maxStock}"
                               onchange="actualizarCantidadInputCarrito('${item.id}', this)"
                               onkeydown="if(event.key==='Enter') this.blur()"
                               aria-label="Cantidad de ${item.nombre}">
                        <button class="btn-qty" onclick="cambiarCantidadCarrito('${item.id}', 1)">+</button>
                    </div>
                </div>
                <button class="btn-eliminar-item" onclick="eliminarDelCarrito('${item.id}')" title="Eliminar">
                    <i class="fa fa-trash"></i>
                </button>
            </div>
        `;
    });

    const descuento  = subtotalSinDesc - subtotalConDesc;
    const igv        = subtotalConDesc * 0.18;
    const total      = subtotalConDesc + igv;

    if (subtotalEl)  subtotalEl.textContent  = `S/ ${subtotalSinDesc.toFixed(2)}`;
    if (igvEl)       igvEl.textContent       = `S/ ${igv.toFixed(2)}`;
    if (totalEl)     totalEl.textContent     = `S/ ${total.toFixed(2)}`;

    if (lineaDesc) {
        if (descuento > 0.001) {
            lineaDesc.style.display  = 'flex';
            if (descuentoEl) descuentoEl.textContent = `S/ ${descuento.toFixed(2)}`;
        } else {
            lineaDesc.style.display = 'none';
        }
    }
}

/* =========================================
   VACIAR CARRITO
========================================= */
async function vaciarCarrito() {
    const result = await Swal.fire({
        title: '¿Estás seguro?',
        text: '¿Vaciar todo el carrito?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e30613',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Sí, vaciar',
        cancelButtonText: 'Cancelar'
    });
    if (!result.isConfirmed) return;
    if (window._usuarioId && parseInt(window._usuarioId) > 0) {
        window._carritoCache = [];
        fetch(BASE_URL + '/controllers/CarritoController.php?action=vaciar', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' }
        }).catch(err => console.error('Error vaciando carrito en BD:', err));
    } else {
        localStorage.removeItem('carrito');
    }
    actualizarContadorCarrito();
    renderCarrito();
}

/* =========================================
   OBTENER STOCK DE UN ITEM DEL CARRITO
========================================= */
function obtenerStockItem(item) {
    if (!item) return 9999;
    const cardEl = document.getElementById('producto-' + item.id);
    if (cardEl && cardEl.dataset.stock !== undefined && cardEl.dataset.stock !== '') {
        const parsed = parseInt(cardEl.dataset.stock);
        if (!isNaN(parsed)) return parsed;
    }
    if (item.stock !== undefined && item.stock !== null && !isNaN(parseInt(item.stock))) {
        return parseInt(item.stock);
    }
    return 9999;
}

/* =========================================
   CAMBIAR CANTIDAD DESDE CARRITO
========================================= */
function cambiarCantidadCarrito(id, delta) {
    const carrito = obtenerCarrito();
    const idx     = carrito.findIndex(i => String(i.id) === String(id));
    if (idx === -1) return;

    const maxStock = obtenerStockItem(carrito[idx]);

    let nuevaCant = carrito[idx].cantidad + delta;
    if (nuevaCant < 1) nuevaCant = 1;
    if (nuevaCant > maxStock) {
        nuevaCant = maxStock;
        mostrarAlertaStock(`Solo hay ${maxStock} unidad(es) disponible(s) en stock.`);
    }

    carrito[idx].cantidad = nuevaCant;
    guardarCarrito(carrito);
}

function actualizarCantidadInputCarrito(id, input) {
    const carrito = obtenerCarrito();
    const idx     = carrito.findIndex(i => String(i.id) === String(id));
    if (idx === -1) return;

    const maxStock = obtenerStockItem(carrito[idx]);

    let val = parseInt(input.value);
    if (isNaN(val) || val < 1) {
        val = 1;
        input.value = 1;
        carrito[idx].cantidad = 1;
        guardarCarrito(carrito);
    } else if (val > maxStock) {
        // NO autocompletar con stock máximo por seguridad. Revertir a la cantidad anterior.
        mostrarAlertaStock(`No puedes solicitar más de ${maxStock} unidad(es) disponible(s) en stock.`);
        input.value = carrito[idx].cantidad;
    } else {
        carrito[idx].cantidad = val;
        guardarCarrito(carrito);
    }
}

/* =========================================
   DRAWER
========================================= */
function abrirCarrito() {
    const drawer = document.getElementById('drawerCarrito');
    const carritoOverlay = document.getElementById('drawerOverlay');

    if (!drawer) return;

    // cerrar modal producto
    cerrarModal();

    // limpiar overlay del modal
    if (overlay) {
        overlay.classList.remove('activo');
        overlay.setAttribute('aria-hidden', 'true');
    }

    // abrir drawer
    drawer.classList.add('activo');

    // activar blur fondo
    if (carritoOverlay) {
        carritoOverlay.classList.add('activo');
    }

    // bloquear scroll
    document.body.style.overflow = 'hidden';
    document.documentElement.style.overflow = 'hidden';

    renderCarrito();
}

/* =========================================
   FINALIZAR
========================================= */

async function finalizarCompra(event) {
    if (event) event.preventDefault();

    const carrito = obtenerCarrito();

    if (!carrito || carrito.length === 0) {
        mostrarAlertaStock('Tu carrito está vacío. Agrega productos antes de finalizar la compra.', 'Carrito Vacío');
        return;
    }

    let tieneProblema = false;
    let mensajeError = '';

    carrito.forEach((item, idx) => {
        const maxStock = obtenerStockItem(item);
        if (maxStock <= 0) {
            tieneProblema = true;
            mensajeError = `El producto "${item.nombre}" se encuentra agotado. Por favor elimínalo de tu carrito para poder continuar.`;
        } else if (item.cantidad > maxStock) {
            tieneProblema = true;
            carrito[idx].cantidad = maxStock;
            mensajeError = `El producto "${item.nombre}" supera el stock disponible (${maxStock} unidades). Se ha ajustado la cantidad al máximo permitido.`;
        }
    });

    if (tieneProblema) {
        guardarCarrito(carrito);
        mostrarAlertaStock(mensajeError, 'Verifica tu Carrito');
        return;
    }

    // Si el usuario está logueado, sincronizar el carrito a la BD ANTES de redirigir
    // para que checkout.php lo encuentre al leer la BD
    if (window._usuarioId && parseInt(window._usuarioId) > 0) {
        try {
            await fetch(BASE_URL + '/controllers/CarritoController.php?action=sincronizar', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ items: carrito })
            });
        } catch(e) {
            console.warn('No se pudo sincronizar carrito antes de checkout, continuando...', e);
        }
    }

    window.location.href = BASE_URL + '/views/checkout.php';
}











/* =========================================
   NOTIFICACIÓN
========================================= */

function mostrarNotificacionCarrito(nombre) {
    const noti = document.createElement('div');

    noti.className = 'carrito-notificacion';
    noti.textContent = `${nombre} agregado al carrito`;

    document.body.appendChild(noti);

    setTimeout(() => noti.classList.add('show'), 50);

    setTimeout(() => {
        noti.classList.remove('show');

        setTimeout(() => {
            noti.remove();
        }, 300);
    }, 2000);
}
/* =========================================
   INIT
========================================= */

document.addEventListener('DOMContentLoaded', () => {
    actualizarContadorCarrito();
    renderCarrito();

    const cards = document.querySelectorAll('.card-producto');

    cards.forEach(card => {
        card.style.cursor = 'pointer';

        card.addEventListener('click', () => {
            abrirModal(card);
        });
    });

    document.querySelectorAll('.selector-cantidad').forEach(selector => {
        selector.addEventListener('click', e => {
            e.stopPropagation();
        });
    });

    // Event listeners para validación manual de cantidad en inputs
    document.querySelectorAll('.input-cantidad').forEach(input => {
        input.addEventListener('change', () => validarEntradaCantidad(input));
        input.addEventListener('blur', () => validarEntradaCantidad(input));
    });

    // botón carrito
    const cartIcon = document.getElementById('cart-icon');
    if (cartIcon) {
        cartIcon.addEventListener('click', abrirCarrito);
    }

    // overlay drawer carrito
    const drawerOverlay = document.getElementById('drawerOverlay');
    if (drawerOverlay) {
        drawerOverlay.addEventListener('click', cerrarCarrito);
    }

    // cerrar modal producto
    if (cerrar) {
        cerrar.addEventListener('click', cerrarModal);
    }

    // cerrar modal clickeando fondo
    if (overlay) {
        overlay.addEventListener('click', e => {
            if (e.target === overlay) {
                cerrarModal();
            }
        });
    }

    // agregar desde modal
    if (modalAgregarBtn) {
        modalAgregarBtn.addEventListener('click', () => {
            agregarAlCarrito(cardActualModal, true);
        });
    }
});

/* =========================================
   TECLA ESC
========================================= */


document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        cerrarModal();
        cerrarCarrito();
        cerrarAlertaStock();
    }
});

document.addEventListener("DOMContentLoaded", () => {
    const hash = window.location.hash;

    if (hash && hash.startsWith("#producto-")) {
        const producto = document.querySelector(hash);

        if (producto) {
             producto.scrollIntoView({
            behavior: "smooth",
            block: "start"
            });
            // quitar highlight anterior
            if (cardActualModal) {
                cardActualModal.classList.remove("producto-highlight");
            }

            // guardar producto actual
            producto.classList.add("producto-highlight");
            cardActualModal = producto;

            setTimeout(() => {
                abrirModal(producto);

                setTimeout(() => {
                    history.replaceState(
                        null,
                        null,
                        window.location.pathname + window.location.search
                    );
                }, 300);

            }, 800);
        }
    }
});

/* =========================================
   FILTRO PRECIO + CATEGORÍA + MÁS VENDIDOS
========================================= */

document.addEventListener("DOMContentLoaded", () => {
    const slider       = document.getElementById("sliderPrecio");
    const precioActual  = document.getElementById("precioActual");
    const gridProductos = document.querySelector(".grid-productos");
    const cards         = document.querySelectorAll(".card-producto");

    if (!cards.length) return;

    // Guardar orden original de las tarjetas
    const originalCardsOrder = Array.from(cards);

    function aplicarFiltros() {
        const precioMax        = slider ? parseFloat(slider.value) : Infinity;
        const tipoSeleccionado = document.querySelector('input[name="tipo"]:checked')?.value || 'todos';
        const contador         = document.getElementById("contadorProductos");

        if (tipoSeleccionado === 'mas_vendidos' && gridProductos) {
            // Ordenar por ventas descendente (solo productos que han vendido)
            const sortedCards = Array.from(cards).sort((a, b) => {
                const vA = parseInt(a.dataset.ventas) || 0;
                const vB = parseInt(b.dataset.ventas) || 0;
                return vB - vA;
            });
            sortedCards.forEach(card => gridProductos.appendChild(card));
        } else if (gridProductos) {
            // Restaurar orden original al cambiar de filtro
            originalCardsOrder.forEach(card => gridProductos.appendChild(card));
        }

        let visibles = 0;

        cards.forEach(card => {
            const precio         = parseFloat(card.dataset.precio);
            const precioOriginal = parseFloat(card.dataset.precioOriginal || card.dataset.precio);
            const tieneOferta    = precioOriginal > precio + 0.001;
            const totalVendido   = parseInt(card.dataset.ventas) || 0;

            const pasaPrecio = isNaN(precioMax) ? true : (precio <= precioMax);
            let pasaTipo     = true;

            if (tipoSeleccionado === 'ofertas') {
                pasaTipo = tieneOferta;
            } else if (tipoSeleccionado === 'mas_vendidos') {
                // Solo mostrar productos que tengan al menos 1 venta registrada
                pasaTipo = totalVendido > 0;
            }

            const mostrar = pasaPrecio && pasaTipo;

            card.style.display = mostrar ? "" : "none";
            if (mostrar) visibles++;
        });

        if (contador) contador.textContent = `${visibles} producto${visibles !== 1 ? 's' : ''}`;
    }

    if (slider) {
        slider.addEventListener("input", function () {
            if (precioActual) precioActual.textContent = `Hasta S/ ${parseFloat(this.value).toFixed(2)}`;
            aplicarFiltros();
        });
    }

    document.querySelectorAll('input[name="tipo"]').forEach(radio => {
        radio.addEventListener("change", aplicarFiltros);
    });

    const btnLimpiar = document.querySelector(".btn-limpiar");
    if (btnLimpiar) {
        btnLimpiar.addEventListener("click", () => {
            if (slider) {
                slider.value = slider.max;
                if (precioActual) precioActual.textContent = `Hasta S/ ${slider.max}`;
            }

            const filtroTodos = document.getElementById("filtroTodos");
            if (filtroTodos) filtroTodos.checked = true;

            aplicarFiltros();
        });
    }
});


/* =========================================
   GLOBALES
========================================= */

window.abrirModal = abrirModal;
window.cerrarModal = cerrarModal;
window.cambiarCantidad = cambiarCantidad;
window.validarEntradaCantidad = validarEntradaCantidad;
window.agregarAlCarrito = agregarAlCarrito;
window.agregarAlCarritoPorId = agregarAlCarritoPorId;
window.abrirCarrito = abrirCarrito;
window.cerrarCarrito = cerrarCarrito;
window.eliminarDelCarrito = eliminarDelCarrito;
window.vaciarCarrito = vaciarCarrito;
window.cambiarCantidadCarrito = cambiarCantidadCarrito;
window.actualizarCantidadInputCarrito = actualizarCantidadInputCarrito;
window.mostrarAlertaStock = mostrarAlertaStock;
window.cerrarAlertaStock = cerrarAlertaStock;
window.finalizarCompra = finalizarCompra;
window.obtenerStockItem = obtenerStockItem;

/* ── Carrusel genérico (ofertas + destacados) ──────────────────── */
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.btn-carrusel').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var targetId = btn.getAttribute('data-target');
            var grid = targetId
                ? document.getElementById(targetId)
                : btn.closest('.contenedor-carrusel').querySelector('.grid-destacados');
            if (!grid) return;
            grid.scrollLeft += btn.classList.contains('prev') ? -300 : 300;
        });
    });
});

/* =========================================
   MENÚ HAMBURGUESA
========================================= */
(function () {
    const btn      = document.querySelector('.hamburger-menu');
    const menu     = document.querySelector('.menu-hamburguesa');
    const closeBtn = document.querySelector('.close-menu');

    if (!btn || !menu) return;

    // Overlay para cerrar al tocar fuera
    let overlay = document.getElementById('menuOverlay');
    if (!overlay) {
        overlay = document.createElement('div');
        overlay.id = 'menuOverlay';
        overlay.style.cssText = [
            'position:fixed', 'inset:0',
            'background:rgba(0,0,0,.45)',
            'z-index:79',
            'display:none',
            'backdrop-filter:blur(2px)'
        ].join(';');
        document.body.appendChild(overlay);
    }

    function abrirMenu() {
        menu.setAttribute('aria-hidden', 'false');
        btn.setAttribute('aria-expanded', 'true');
        overlay.style.display = 'block';
        document.body.style.overflow = 'hidden';
    }

    function cerrarMenuFn() {
        menu.setAttribute('aria-hidden', 'true');
        btn.setAttribute('aria-expanded', 'false');
        overlay.style.display = 'none';
        document.body.style.overflow = '';
    }

    // Exponer globalmente (usado en onclick de mis pedidos)
    window.cerrarMenu = cerrarMenuFn;

    btn.addEventListener('click', abrirMenu);
    if (closeBtn) closeBtn.addEventListener('click', cerrarMenuFn);
    overlay.addEventListener('click', cerrarMenuFn);

    // Cerrar con Escape
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') cerrarMenuFn();
    });

    // Accordion
    menu.querySelectorAll('.accordion-toggle').forEach(function (toggle) {
        toggle.addEventListener('click', function () {
            const li = this.closest('.accordion');
            const isOpen = li.classList.contains('open');
            // Cierra todos
            menu.querySelectorAll('.accordion').forEach(a => a.classList.remove('open'));
            if (!isOpen) li.classList.add('open');
            this.setAttribute('aria-expanded', !isOpen);
        });
    });
})();
