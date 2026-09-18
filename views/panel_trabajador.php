<?php
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../config/Security.php';

requireTrabajador();
\Config\Security::setSecurityHeaders();

$nombreUsuario = $_SESSION['usuario_nombre'] ?? 'Colaborador';
$esAdmin = esAdmin();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<script>window.BASE_URL = '<?= BASE_URL ?>';</script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Operativo – Market Primavera</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/panel_trabajador.css?v=<?= time() ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

    <!-- ── Header Superior ── -->
    <header class="pt-header">
        <div class="pt-header-container">
            <a href="#" class="pt-brand">
                <div class="pt-brand-logo">
                    <i class="fa fa-boxes-packing"></i>
                </div>
                <div class="pt-brand-text">
                    <strong>Market Primavera</strong>
                    <span>Portal de Operaciones</span>
                </div>
            </a>

            <div class="pt-user-meta">
                <div class="pt-status-pill">
                    <span class="pt-status-dot"></span>
                    <span>En Turno</span>
                </div>

                <span id="ptUserCargoBadge" class="pt-badge-role role-despachador">Colaborador</span>

                <div style="text-align: right; display: none; @media(min-width: 640px){display:block;}">
                    <div style="font-weight: 700; font-size: 0.9rem; color: #0f172a;" id="ptUserNombre"><?= htmlspecialchars($nombreUsuario) ?></div>
                    <div style="font-size: 0.75rem; color: #64748b;" id="ptUserTurno">Turno Activo</div>
                </div>

                <?php if ($esAdmin): ?>
                <a href="<?= BASE_URL ?>/views/admin/index.php" class="pt-btn-logout" style="color: #e30613; border-color: #fecaca; background: #fef2f2;">
                    <i class="fa fa-cog"></i> Panel Admin
                </a>
                <?php endif; ?>

                <a href="<?= BASE_URL ?>/controllers/AuthController.php?logout=1" class="pt-btn-logout">
                    <i class="fa fa-sign-out-alt"></i> Salir
                </a>
            </div>
        </div>
    </header>

    <!-- ── Barra de Pestañas / Módulos ── -->
    <div class="pt-tabs-wrapper">
        <div class="pt-tabs-container">
            <button class="pt-tab-btn active" data-tab="despacho" onclick="cambiarTab('despacho')">
                <i class="fa fa-dolly"></i> Despacho & Picking
                <span class="pt-tab-count" id="countDespacho">0</span>
            </button>

            <button class="pt-tab-btn" data-tab="almacen" onclick="cambiarTab('almacen')">
                <i class="fa fa-warehouse"></i> Almacén & Stock
                <span class="pt-tab-count" id="countAlmacen">0</span>
            </button>

            <button class="pt-tab-btn" data-tab="caja" onclick="cambiarTab('caja')">
                <i class="fa fa-cash-register"></i> Punto de Venta (POS)
            </button>

            <button class="pt-tab-btn" data-tab="reparto" onclick="cambiarTab('reparto')">
                <i class="fa fa-motorcycle"></i> Entregas & Ruta
            </button>
        </div>
    </div>

    <!-- ── Contenido Principal ── -->
    <main class="pt-main">

        <!-- ════ 1. MÓDULO DESPACHO ════ -->
        <section id="tab-despacho" class="pt-section active">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:12px;">
                <div>
                    <h2 style="font-size:1.4rem; font-weight:800; color:#0f172a;">Bandeja de Preparación de Pedidos</h2>
                    <p style="font-size:0.85rem; color:#64748b;">Recolecta los productos de las góndolas, márcalos en el checklist y empácalos.</p>
                </div>
                <button class="pt-btn pt-btn-ghost" onclick="cargarDatosPortal()">
                    <i class="fa fa-sync-alt"></i> Actualizar Pedidos
                </button>
            </div>

            <div class="pt-orders-grid" id="gridPedidosDespacho">
                <div style="grid-column: 1/-1; text-align:center; padding:40px; color:#64748b;">
                    <i class="fa fa-spinner fa-spin fa-2x"></i>
                    <p style="margin-top:10px;">Cargando pedidos para despacho...</p>
                </div>
            </div>
        </section>

        <!-- ════ 2. MÓDULO ALMACÉN ════ -->
        <section id="tab-almacen" class="pt-section">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:12px;">
                <div>
                    <h2 style="font-size:1.4rem; font-weight:800; color:#0f172a;">Control de Almacén e Inventario</h2>
                    <p style="font-size:0.85rem; color:#64748b;">Registra ingresos de mercancía de proveedores y mermas por rotura o vencimiento.</p>
                </div>
                <div style="display:flex; gap:10px;">
                    <input type="text" id="buscadorAlmacen" class="pt-btn pt-btn-ghost" style="text-align:left; width:250px; cursor:text;" placeholder="Buscar producto en almacén..." oninput="filtrarInventario()">
                    <button class="pt-btn pt-btn-ghost" onclick="cargarDatosPortal()"><i class="fa fa-sync-alt"></i></button>
                </div>
            </div>

            <div class="pt-card">
                <div class="pt-card-header">
                    <h3><i class="fa fa-boxes-stacked" style="color:var(--primary);"></i> Stock Actual de Productos</h3>
                </div>
                <div class="pt-card-body pt-table-wrap" style="padding:0;">
                    <table class="pt-table">
                        <thead>
                            <tr>
                                <th>N°</th>
                                <th>Producto</th>
                                <th>Precio Venta</th>
                                <th>Existencias</th>
                                <th>Acción Rápida</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyStockAlmacen">
                            <tr><td colspan="5" style="text-align:center; padding:30px; color:#64748b;">Cargando inventario...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Movimientos de stock recientes -->
            <div class="pt-card">
                <div class="pt-card-header">
                    <h3><i class="fa fa-history" style="color:#64748b;"></i> Últimos Movimientos Registrados</h3>
                </div>
                <div class="pt-card-body pt-table-wrap" style="padding:0;">
                    <table class="pt-table">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Movimiento</th>
                                <th>Motivo</th>
                                <th>Fecha y Hora</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyMovimientosAlmacen">
                            <tr><td colspan="4" style="text-align:center; padding:20px; color:#64748b;">Cargando movimientos...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- ════ 3. MÓDULO CAJA RÁPIDA (POS) ════ -->
        <section id="tab-caja" class="pt-section">
            <div style="margin-bottom:20px;">
                <h2 style="font-size:1.4rem; font-weight:800; color:#0f172a;">Terminal de Caja y Venta en Mostrador</h2>
                <p style="font-size:0.85rem; color:#64748b;">Haz clic en los productos para agregarlos al ticket de cobro directo.</p>
            </div>

            <div class="pt-pos-layout">
                <!-- Catálogo rápido -->
                <div>
                    <input type="text" id="posBuscador" class="pt-btn pt-btn-ghost" style="width:100%; text-align:left; margin-bottom:16px; cursor:text; padding:12px 16px; font-size:0.95rem;" placeholder="🔍 Buscar por nombre de producto..." oninput="filtrarProductosPOS()">
                    <div class="pt-pos-products" id="posGridProductos">
                        <!-- Render dinámico -->
                    </div>
                </div>

                <!-- Panel de ticket / cobro -->
                <div class="pt-pos-cart-panel">
                    <h3 style="font-size:1.15rem; font-weight:800; color:#0f172a; margin-bottom:12px; display:flex; justify-content:space-between; align-items:center;">
                        <span><i class="fa fa-receipt" style="color:var(--primary);"></i> Venta Actual</span>
                        <button type="button" class="pt-btn pt-btn-ghost" style="padding:3px 8px; font-size:0.75rem;" onclick="_posCart=[]; actualizarCarritoPOS();">Limpiar</button>
                    </h3>

                    <div class="pt-cart-items" id="posCartItems">
                        <div style="text-align:center; color:#94a3b8; padding:20px; font-size:0.85rem;">Carrito vacío. Selecciona productos.</div>
                    </div>

                    <div style="display:flex; justify-content:space-between; align-items:center; margin:16px 0; font-size:1.1rem; font-weight:800;">
                        <span>TOTAL:</span>
                        <span id="posCartTotal" style="color:var(--primary); font-size:1.4rem;">S/ 0.00</span>
                    </div>

                    <form onsubmit="finalizarVentaPOS(event)">
                        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:10px; margin-bottom:12px;">
                            <div>
                                <label style="font-size:0.75rem; font-weight:700; color:#475569; display:block; margin-bottom:4px;">DNI Cliente</label>
                                <input type="text" id="posDniCliente" maxlength="8" class="pt-btn pt-btn-ghost" style="width:100%; text-align:left; padding:8px 10px;" placeholder="00000000">
                            </div>
                            <div>
                                <label style="font-size:0.75rem; font-weight:700; color:#475569; display:block; margin-bottom:4px;">Comprobante</label>
                                <select id="posTipoComprobante" class="pt-btn pt-btn-ghost" style="width:100%; padding:8px 10px;">
                                    <option value="Boleta">Boleta</option>
                                    <option value="Factura">Factura</option>
                                </select>
                            </div>
                        </div>

                        <div style="margin-bottom:12px;">
                            <label style="font-size:0.75rem; font-weight:700; color:#475569; display:block; margin-bottom:4px;">Nombre del Cliente</label>
                            <input type="text" id="posNombreCliente" class="pt-btn pt-btn-ghost" style="width:100%; text-align:left; padding:8px 10px;" placeholder="Venta Mostrador">
                        </div>

                        <div style="margin-bottom:16px;">
                            <label style="font-size:0.75rem; font-weight:700; color:#475569; display:block; margin-bottom:4px;">Método de Pago</label>
                            <select id="posMetodoPago" class="pt-btn pt-btn-ghost" style="width:100%; padding:8px 10px;">
                                <option value="Efectivo">Efectivo</option>
                                <option value="Yape">Yape</option>
                                <option value="Plin">Plin</option>
                                <option value="Tarjeta">Tarjeta POS</option>
                            </select>
                        </div>

                        <button type="submit" class="pt-btn pt-btn-primary pt-btn-full" style="padding:12px; font-size:1rem;">
                            <i class="fa fa-cash-register"></i> Cobrar y Finalizar Venta
                        </button>
                    </form>
                </div>
            </div>
        </section>

        <!-- ════ 4. MÓDULO REPARTO ════ -->
        <section id="tab-reparto" class="pt-section">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:12px;">
                <div>
                    <h2 style="font-size:1.4rem; font-weight:800; color:#0f172a;">Gestión de Entregas & Deliveries</h2>
                    <p style="font-size:0.85rem; color:#64748b;">Rutas asignadas, clientes y confirmación de entrega en domicilio.</p>
                </div>
                <a href="<?= BASE_URL ?>/views/panel_repartidor.php" class="pt-btn pt-btn-primary">
                    <i class="fa fa-map-location-dot"></i> Abrir Panel de Reparto Completo
                </a>
            </div>

            <div id="contenedorEntregasReparto">
                <!-- Render dinámico -->
            </div>
        </section>

    </main>

    <!-- ── Modal Movimiento Stock (Almacén) ── -->
    <div class="pt-modal-bg" id="modalMovimientoStock">
        <div class="pt-modal">
            <button class="pt-modal-close" onclick="cerrarModalMovStock()">✕</button>
            <h3 id="movStockTitulo" style="font-size:1.2rem; font-weight:800; color:#0f172a; margin-bottom:6px;">Movimiento de Stock</h3>
            <p id="movStockProductoNombre" style="font-size:0.85rem; color:#64748b; margin-bottom:16px;">Producto</p>

            <form onsubmit="guardarMovimientoStock(event)">
                <input type="hidden" id="movStockIdProducto">
                <input type="hidden" id="movStockTipo">

                <div style="margin-bottom:14px;">
                    <label style="font-size:0.8rem; font-weight:700; color:#334155; display:block; margin-bottom:4px;">Cantidad de unidades *</label>
                    <input type="number" id="movStockCantidad" min="1" required class="pt-btn pt-btn-ghost" style="width:100%; text-align:left; padding:10px; font-size:1rem;" placeholder="Ej. 10">
                </div>

                <div style="margin-bottom:20px;">
                    <label style="font-size:0.8rem; font-weight:700; color:#334155; display:block; margin-bottom:4px;">Motivo o Proveedor *</label>
                    <input type="text" id="movStockMotivo" required class="pt-btn pt-btn-ghost" style="width:100%; text-align:left; padding:10px;" placeholder="Ej. Compra Factura F001-492 / Merma rotura">
                </div>

                <div style="display:flex; justify-content:flex-end; gap:10px;">
                    <button type="button" class="pt-btn pt-btn-ghost" onclick="cerrarModalMovStock()">Cancelar</button>
                    <button type="submit" class="pt-btn pt-btn-primary">Guardar Movimiento</button>
                </div>
            </form>
        </div>
    </div>

    <script src="<?= BASE_URL ?>/assets/js/panel_trabajador.js?v=<?= time() ?>"></script>
</body>
</html>
