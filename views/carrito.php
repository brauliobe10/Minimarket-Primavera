<script>
    window._usuarioId = <?= json_encode($_SESSION['usuario_id'] ?? 0) ?>;
    window._estaLogueado = <?= json_encode(isset($_SESSION['usuario_id'])) ?>;

    // Si es un invitado (sin sesión iniciada), el carrito no se recuerda y se borra al cerrar/navegar como invitado
    if (!window._estaLogueado) {
        localStorage.removeItem('carrito_invitado');
        localStorage.removeItem('carrito');
    }
</script>

<div id="drawerOverlay" class="drawer-overlay" onclick="cerrarCarrito()"></div>

<div id="drawerCarrito" class="drawer-carrito">

    <div class="drawer-header">
        <h2>
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                 fill="none" stroke="currentColor" stroke-width="2.5"
                 stroke-linecap="round" stroke-linejoin="round">
                <circle cx="9" cy="21" r="1"></circle>
                <circle cx="20" cy="21" r="1"></circle>
                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"></path>
            </svg>
            Mi Carrito
        </h2>
        <button class="btn-cerrar-drawer" onclick="cerrarCarrito()">✕</button>
    </div>

    <div id="carritoItems" class="drawer-body"></div>

    <div class="drawer-footer">

        <div class="resumen-linea">
            <span>Subtotal:</span>
            <span id="carritoSubtotal">S/ 0.00</span>
        </div>

        <div class="resumen-linea descuento" id="lineaDescuento" style="display:none;">
            <span>Descuento:</span>
            <span id="carritoDescuento">S/ 0.00</span>
        </div>

        <div class="resumen-linea">
            <span>IGV (18%):</span>
            <span id="carritoIgv">S/ 0.00</span>
        </div>

        <div class="resumen-linea total">
            <span>Total:</span>
            <span id="carritoTotal">S/ 0.00</span>
        </div>

        <button class="btn-finalizar" onclick="finalizarCompra(event)">Finalizar compra</button>
        <button class="btn-vaciar-carrito" onclick="vaciarCarrito()">Vaciar carrito</button>

    </div>

</div>



