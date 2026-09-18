<?php
require_once __DIR__ . '/../../controllers/AuthController.php';
require_once __DIR__ . '/../../config/Security.php';
requireSoporteOrAdmin();
\Config\Security::setSecurityHeaders();
$nombreAdmin = $_SESSION['usuario_nombre'] ?? 'Admin';
$inicial     = strtoupper(substr($nombreAdmin, 0, 1));
?>
<!DOCTYPE html>
<html lang="es">
<head>
<script>window.BASE_URL = '<?= BASE_URL ?>';</script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Admin – Market Primavera</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<body class="admin-body">

<!-- Overlay móvil -->
<div id="admOverlay" class="adm-sidebar-overlay"></div>

<!-- ── Sidebar ── -->
<aside class="adm-sidebar" id="admSidebar">
    <a href="<?= BASE_URL ?>/views/inicio.php" class="adm-logo">
        <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24"
             fill="none" stroke="#e30613" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle>
            <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"></path>
        </svg>
        <div class="adm-logo-text">
            <strong>Market Primavera</strong>
            <span>PANEL ADMIN</span>
        </div>
    </a>

    <nav class="adm-nav">
        <?php if (esAdmin()): ?>
        <div class="adm-nav-section">Principal</div>

        <button class="adm-nav-link" data-sec="dashboard" onclick="navegar('dashboard')">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
            Dashboard
        </button>

        <div class="adm-nav-section">Gestión</div>

        <button class="adm-nav-link" data-sec="productos" onclick="navegar('productos')">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
            Productos
        </button>

        <button class="adm-nav-link" data-sec="categorias" onclick="navegar('categorias')">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19h16v2H4zm16-4H4v2h16zm0-4H4v2h16zm0-4H4v2h16z"/></svg>
            Categorías
        </button>

        <button class="adm-nav-link" data-sec="promociones" onclick="navegar('promociones')">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            Promociones
        </button>

        <button class="adm-nav-link" data-sec="pedidos" onclick="navegar('pedidos')">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 3h5v5"/><path d="M8 3H3v5"/><path d="M12 22v-8"/><path d="M16 12H8"/><path d="M21 8v13H3V8"/></svg>
            Pedidos
        </button>

        <button class="adm-nav-link" data-sec="usuarios" onclick="navegar('usuarios')">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            Usuarios
        </button>

        <button class="adm-nav-link" data-sec="trabajadores" onclick="navegar('trabajadores')">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            Personal / Trabajadores
        </button>
        <?php endif; ?>

        <?php if (esAdmin() || esSoporte()): ?>
        <div class="adm-nav-section">Atención al Cliente</div>
        <button class="adm-nav-link" data-sec="soporte" onclick="navegar('soporte')">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
            Buzón (Soporte)
        </button>
        <?php endif; ?>

        <?php if (esAdmin()): ?>
        <div class="adm-nav-section">Operaciones</div>

        <button class="adm-nav-link" data-sec="stock" onclick="navegar('stock')">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="20" x2="12" y2="10"/><line x1="18" y1="20" x2="18" y2="4"/><line x1="6" y1="20" x2="6" y2="16"/></svg>
            Stock
        </button>

        <button class="adm-nav-link" data-sec="ventas" onclick="navegar('ventas')">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
            Ventas por día
        </button>

        <button class="adm-nav-link" data-sec="comprobantes" onclick="navegar('comprobantes')">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
            Comprobantes
        </button>

        <div class="adm-nav-section">Delivery</div>

        <button class="adm-nav-link" data-sec="delivery" onclick="navegar('delivery')">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
            Deliveries
        </button>

        <button class="adm-nav-link" data-sec="repartidores" onclick="navegar('repartidores')">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M20 21a8 8 0 1 0-16 0"/><path d="M12 12v9"/><path d="M9 18l3 3 3-3"/></svg>
            Repartidores
        </button>
        <?php endif; ?>
    </nav>

    <div class="adm-sidebar-footer">
        <div class="adm-user-info">
            <div class="adm-avatar"><?= $inicial ?></div>
            <div>
                <span class="adm-user-name"><?= htmlspecialchars($nombreAdmin) ?></span>
                <span class="adm-user-role"><?= esAdmin() ? 'Administrador' : 'Soporte' ?></span>
                <?php
                $sessionTimeout = 1200;
                $elapsed = time() - ($_SESSION['_last_activity'] ?? time());
                $remaining = max(0, $sessionTimeout - $elapsed);
                ?>
                <span class="adm-user-role" style="color:#f59e0b; font-size:0.7rem; font-weight:bold; margin-top:4px;" id="admSessionTimer" data-remaining="<?= $remaining ?>">
                    <i class="fa fa-clock"></i> Sesión: <span id="admTimeLeft">--:--</span>
                </span>
            </div>
        </div>
        <a href="<?= BASE_URL ?>/views/panel_trabajador.php" target="_blank" class="adm-logout" style="margin-bottom: 8px; color: #059669; border-color: #a7f3d0; background: #ecfdf5; font-weight:600;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
            Portal Operativo
        </a>
        <?php if (esCliente()): ?>
        <a href="<?= BASE_URL ?>/views/inicio.php" class="adm-logout" style="margin-bottom: 8px; color: #3b82f6; border-color: #e2e8f0;">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
            Volver a la tienda
        </a>
        <?php endif; ?>
        <a href="<?= BASE_URL ?>/controllers/AuthController.php?logout=1" class="adm-logout">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            Cerrar sesión
        </a>
    </div>
</aside>

<!-- ── Main ── -->
<div class="adm-main">

    <!-- Top bar -->
    <header class="adm-topbar">
        <div style="display:flex;align-items:center;gap:12px;">
            <button class="adm-hamburger" onclick="toggleSidebar()">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
            </button>
            <span class="adm-topbar-title" id="topbarTitle">Dashboard</span>
        </div>
        <div class="adm-topbar-right">
        </div>
    </header>

    <div class="adm-page">

        <?php if (esAdmin()): ?>
        <!-- ══ DASHBOARD ══ -->
        <section id="sec-dashboard" class="adm-section active">
            <div class="adm-stats">
                <div class="adm-stat">
                    <div class="adm-stat-icon red">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 3h5v5"/><path d="M8 3H3v5"/><path d="M12 22v-8"/><path d="M16 12H8"/><path d="M21 8v13H3V8"/></svg>
                    </div>
                    <div class="adm-stat-info">
                        <strong id="statPedidos">—</strong>
                        <span>Total pedidos</span>
                    </div>
                </div>
                <div class="adm-stat">
                    <div class="adm-stat-icon green">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                    </div>
                    <div class="adm-stat-info">
                        <strong id="statVentas">—</strong>
                        <span>Ventas hoy</span>
                    </div>
                </div>
                <div class="adm-stat">
                    <div class="adm-stat-icon blue">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    </div>
                    <div class="adm-stat-info">
                        <strong id="statUsuarios">—</strong>
                        <span>Usuarios activos</span>
                    </div>
                </div>
                <div class="adm-stat">
                    <div class="adm-stat-icon orange">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
                    </div>
                    <div class="adm-stat-info">
                        <strong id="statProductos">—</strong>
                        <span>Productos activos</span>
                    </div>
                </div>
                <div class="adm-stat">
                    <div class="adm-stat-icon" style="background:#fef9c3;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#ca8a04" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 8v4l3 3"/></svg>
                    </div>
                    <div class="adm-stat-info">
                        <strong id="statPendientes">—</strong>
                        <span>Pedidos pendientes</span>
                    </div>
                </div>
            </div>

            <!-- Gráficos del Dashboard -->
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <div class="adm-grid-asym" style="margin-bottom:20px; grid-template-columns: 2fr 1fr;">
                <div class="adm-card">
                    <div class="adm-card-head"><h3>Ingresos (Últimos 7 días)</h3></div>
                    <div class="adm-card-body" style="padding: 20px;">
                        <canvas id="chartIngresos" style="width:100%; height:300px;"></canvas>
                    </div>
                </div>
                <div class="adm-card">
                    <div class="adm-card-head"><h3>Estado de Pedidos</h3></div>
                    <div class="adm-card-body" style="padding: 20px; display:flex; justify-content:center; align-items:center;">
                        <canvas id="chartEstado" style="width:100%; max-height:300px;"></canvas>
                    </div>
                </div>
            </div>

            <div class="adm-card">
                <div class="adm-card-head"><h3>Pedidos recientes</h3></div>
                <div class="adm-card-body adm-table-wrap">
                    <table class="adm-table">
                        <thead><tr><th>N°</th><th>ID</th><th>Cliente</th><th>Fecha</th><th>Total</th><th>Estado</th></tr></thead>
                        <tbody id="tbodyPedidosRecientes"></tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- ══ PRODUCTOS ══ -->
        <section id="sec-productos" class="adm-section">
            <div class="adm-search">
                <input class="adm-input" id="buscadorProductos" placeholder="Buscar producto..."
                       oninput="filtrarProductos()">
                <button class="adm-btn adm-btn-primary" onclick="abrirModalProducto()">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Nuevo producto
                </button>
            </div>
            <div class="adm-card">
                <div class="adm-card-body adm-table-wrap">
                    <table class="adm-table">
                        <thead><tr><th>N°</th><th>ID</th><th>Img</th><th>Nombre</th><th>Categoría</th><th>Precio</th><th>Stock</th><th>Estado</th><th>Acciones</th></tr></thead>
                        <tbody id="tbodyProductos"></tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- ══ CATEGORÍAS ══ -->
        <section id="sec-categorias" class="adm-section">
            <div class="adm-search">
                <input class="adm-input" id="buscadorCategorias" placeholder="Buscar categoría..."
                       oninput="filtrarCategorias()">
                <button class="adm-btn adm-btn-primary" onclick="abrirModalCategoria()">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Nueva categoría
                </button>
            </div>
            <div class="adm-card">
                <div class="adm-card-body adm-table-wrap">
                    <table class="adm-table">
                        <thead><tr><th>N°</th><th>ID</th><th>Imagen</th><th>Nombre</th><th>Grupo</th><th>Descripción</th><th>Acciones</th></tr></thead>
                        <tbody id="tbodyCategorias"></tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- ══ PROMOCIONES ══ -->
        <section id="sec-promociones" class="adm-section">
            <div class="adm-search">
                <input class="adm-input" id="buscadorPromociones" placeholder="Buscar promoción..."
                       oninput="filtrarPromociones()">
                <button class="adm-btn adm-btn-primary" onclick="abrirModalPromocion()">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Nueva promoción
                </button>
            </div>
            <div class="adm-card">
                <div class="adm-card-body adm-table-wrap">
                    <table class="adm-table">
                        <thead><tr><th>N°</th><th>ID</th><th>Nombre</th><th>Descripción</th><th>Descuento</th><th>Inicio</th><th>Fin</th><th>Estado</th><th>Acciones</th></tr></thead>
                        <tbody id="tbodyPromociones"></tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- ══ PEDIDOS ══ -->
        <section id="sec-pedidos" class="adm-section">
            <div class="adm-search" style="display:flex; gap:10px; flex-wrap:wrap; align-items:center;">
                <input class="adm-input" id="buscadorPedidos" placeholder="Buscar cliente o #pedido..."
                       oninput="filtrarPedidos()" style="flex:1; min-width:200px;">
                <input type="date" class="adm-input" id="filtroFechaPedidos" onchange="filtrarPedidos()" style="width:auto;">
                <select class="adm-input" id="filtroEstadoPedidos" onchange="filtrarPedidos()" style="width:auto;">
                    <option value="">Todos los estados</option>
                    <option value="Pendiente">Pendiente</option>
                    <option value="En camino">En camino</option>
                    <option value="Entregado">Entregado</option>
                    <option value="Cancelado">Cancelado</option>
                </select>
                <button class="adm-btn adm-btn-ghost" onclick="limpiarFiltrosPedidos()" style="white-space:nowrap;">Limpiar</button>
            </div>
            <div class="adm-card">
                <div class="adm-card-body adm-table-wrap">
                    <table class="adm-table">
                        <thead><tr><th>N°</th><th>ID</th><th>Cliente</th><th>Fecha</th><th>Entrega</th><th>Pago</th><th>Total</th><th>Estado</th><th></th></tr></thead>
                        <tbody id="tbodyPedidos"></tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- ══ USUARIOS ══ -->
        <section id="sec-usuarios" class="adm-section">
            <div class="adm-search">
                <input class="adm-input" id="buscadorUsuarios" placeholder="Buscar usuario..."
                       oninput="filtrarUsuarios()">
            </div>
            <div class="adm-card">
                <div class="adm-card-body adm-table-wrap">
                    <table class="adm-table">
                        <thead><tr><th>N°</th><th>ID</th><th>Nombre</th><th>DNI</th><th>Correo</th><th>Teléfono</th><th>Registro</th><th>Rol</th><th>Estado</th><th></th></tr></thead>
                        <tbody id="tbodyUsuarios"></tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- ══ PERSONAL / TRABAJADORES ══ -->
        <section id="sec-trabajadores" class="adm-section">
            <div class="adm-stats" style="grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); margin-bottom: 20px;">
                <div class="adm-stat">
                    <div class="adm-stat-icon blue">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                    </div>
                    <div class="adm-stat-info">
                        <strong id="statTotalTrabajadores">0</strong>
                        <span>Total planilla</span>
                    </div>
                </div>
                <div class="adm-stat">
                    <div class="adm-stat-icon green">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                    </div>
                    <div class="adm-stat-info">
                        <strong id="statTrabajadoresActivos">0</strong>
                        <span>Activos hoy</span>
                    </div>
                </div>
                <div class="adm-stat">
                    <div class="adm-stat-icon orange">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
                    </div>
                    <div class="adm-stat-info">
                        <strong id="statTrabDespacho">0</strong>
                        <span>Despachadores</span>
                    </div>
                </div>
                <div class="adm-stat">
                    <div class="adm-stat-icon" style="background:#f3e8ff;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#9333ea" stroke-width="2"><line x1="12" y1="20" x2="12" y2="10"/><line x1="18" y1="20" x2="18" y2="4"/><line x1="6" y1="20" x2="6" y2="16"/></svg>
                    </div>
                    <div class="adm-stat-info">
                        <strong id="statTrabAlmacen">0</strong>
                        <span>Almaceneros</span>
                    </div>
                </div>
                <div class="adm-stat">
                    <div class="adm-stat-icon" style="background:#e0f2fe;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#0284c7" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M20 21a8 8 0 1 0-16 0"/></svg>
                    </div>
                    <div class="adm-stat-info">
                        <strong id="statTrabRepartidores">0</strong>
                        <span>Repartidores</span>
                    </div>
                </div>
            </div>

            <div class="adm-search" style="display:flex; justify-content:space-between; flex-wrap:wrap; gap:10px; align-items:center;">
                <div style="display:flex; gap:10px; flex:1; min-width:260px;">
                    <input class="adm-input" id="buscadorTrabajadores" placeholder="Buscar colaborador por nombre, DNI o teléfono..." oninput="filtrarTrabajadores()" style="flex:1;">
                    <select class="adm-input" id="filtroCargoTrabajador" onchange="filtrarTrabajadores()" style="width:160px;">
                        <option value="">Todos los cargos</option>
                        <option value="Despachador">Despachador</option>
                        <option value="Almacenero">Almacenero</option>
                        <option value="Repartidor">Repartidor</option>
                        <option value="Cajero">Cajero</option>
                    </select>
                    <select class="adm-input" id="filtroTurnoTrabajador" onchange="filtrarTrabajadores()" style="width:140px;">
                        <option value="">Todos los turnos</option>
                        <option value="Mañana">Mañana</option>
                        <option value="Tarde">Tarde</option>
                        <option value="Noche">Noche</option>
                        <option value="Completo">Completo</option>
                    </select>
                </div>
                <div style="display:flex; gap:10px;">
                    <a href="<?= BASE_URL ?>/views/panel_trabajador.php" target="_blank" class="adm-btn adm-btn-ghost" style="display:inline-flex; align-items:center; gap:6px; color:#059669; border-color:#a7f3d0; text-decoration:none;">
                        <i class="fa fa-external-link-alt"></i> Portal Operativo
                    </a>
                    <button class="adm-btn adm-btn-primary" onclick="abrirModalTrabajador()">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        Nuevo Colaborador
                    </button>
                </div>
            </div>

            <div class="adm-card">
                <div class="adm-card-body adm-table-wrap">
                    <table class="adm-table">
                        <thead>
                            <tr>
                                <th>N°</th>
                                <th>Colaborador</th>
                                <th>DNI / Contacto</th>
                                <th>Cargo</th>
                                <th>Turno</th>
                                <th>Sueldo</th>
                                <th>Ingreso</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyTrabajadores">
                            <tr><td colspan="9" style="text-align:center; padding:30px; color:#64748b;">Cargando personal...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- ══ STOCK ══ -->
        <section id="sec-stock" class="adm-section">
            <div class="adm-card">
                <div class="adm-card-head" style="display:flex; justify-content:space-between; flex-wrap:wrap; gap:10px;">
                    <h3>Movimientos de stock</h3>
                    <div style="display:flex; gap:10px; align-items:center;">
                        <select id="filtroTipoStock" class="adm-input" style="width:130px; height:32px; font-size:0.85rem;" onchange="cargarStock()">
                            <option value="">Todos los tipos</option>
                            <option value="ENTRADA">Entrada</option>
                            <option value="SALIDA">Salida</option>
                        </select>
                        <input type="date" id="filtroFechaStock" class="adm-input" style="height:32px; font-size:0.85rem;" onchange="cargarStock()">
                        <button class="adm-btn adm-btn-ghost" style="height:32px; padding:0 12px;" onclick="limpiarFiltrosStock()" title="Limpiar filtros">
                            <i class="fas fa-eraser"></i>
                        </button>
                    </div>
                </div>
                <div class="adm-card-body adm-table-wrap">
                    <table class="adm-table">
                        <thead><tr><th>N°</th><th>ID</th><th>Producto</th><th>Tipo</th><th>Cantidad</th><th>Fecha</th><th>Motivo</th></tr></thead>
                        <tbody id="tbodyStock"></tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- ══ COMPROBANTES ══ -->
        <section id="sec-comprobantes" class="adm-section">
            <div class="adm-card">
                <div class="adm-card-head"><h3>Comprobantes electrónicos</h3></div>
                <div class="adm-card-body adm-table-wrap">
                    <table class="adm-table">
                        <thead><tr><th>N°</th><th>ID</th><th>Tipo</th><th>Serie-Número</th><th>Pedido</th><th>Fecha</th><th>Subtotal</th><th>IGV</th><th>Total</th><th>Estado</th><th></th></tr></thead>
                        <tbody id="tbodyComprobantes"></tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- ══ DELIVERY ══ -->
        <section id="sec-delivery" class="adm-section">
            <div class="adm-search">
                <input class="adm-input" id="buscadorDelivery" placeholder="Buscar cliente o #delivery..."
                       oninput="filtrarDelivery()">

            </div>
            <div class="adm-card">
                <div class="adm-card-body adm-table-wrap">
                    <table class="adm-table">
                        <thead>
                            <tr>
                                <th>N°</th>
                                <th>ID</th>
                                <th>Pedido</th>
                                <th>Cliente</th>
                                <th>Dirección</th>
                                <th>Repartidor</th>
                                <th>Costo</th>
                                <th>Pago</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyDelivery"></tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- ══ REPARTIDORES ══ -->
        <section id="sec-repartidores" class="adm-section">
            <div style="margin-bottom:20px; width: 100%;">
                <!-- Lista repartidores activos -->
                <div class="adm-card">
                    <div class="adm-card-head" style="min-height:60px; padding:0 20px;">
                        <!-- Default Header State -->
                        <div id="repHeaderDefault" style="display:flex; justify-content:space-between; align-items:center; width:100%;">
                            <h3>Repartidores</h3>
                            <span id="repSelCount" style="font-size:.78rem; color:#888;">Selecciona repartidores para acciones</span>
                        </div>
                        
                        <!-- Selected Toolbar State -->
                        <div id="repToolbar" style="display:none; align-items:center; justify-content:space-between; width:100%; background:var(--adm-accent-light, #f1f5f9); padding:8px 12px; border-radius:8px;">
                            <span id="repSelLabel" style="font-size:.85rem; font-weight:700; color:#0f172a;"></span>
                            <div style="display:flex; align-items:center; gap:8px;">
                                <button id="btnRepActivar" class="adm-btn adm-btn-sm adm-btn-success" onclick="accionesLoteRepartidores('activar')">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                    Activar
                                </button>
                                <button id="btnRepDesactivar" class="adm-btn adm-btn-sm adm-btn-danger" onclick="accionesLoteRepartidores('desactivar')">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                    Desactivar
                                </button>
                                <button class="adm-btn adm-btn-sm adm-btn-ghost" onclick="accionesLoteRepartidores('eliminar')">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/></svg>
                                    Eliminar
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="adm-card-body adm-table-wrap">
                        <table class="adm-table">
                            <thead>
                                <tr>
                                    <th style="width: 40px;"><input type="checkbox" id="chkAllRepartidores" onchange="toggleAllRepartidores(this)"></th>
                                    <th>N°</th>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>DNI</th>
                                    <th>Teléfono</th>
                                    <th>Placa</th>
                                    <th>Entregas</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody id="tbodyRepartidores"></tbody>
                        </table>
                    </div>
                </div>

                <!-- Asignar rol removido -->
        </section>

        <!-- ══ VENTAS POR DÍA ══ -->
        <section id="sec-ventas" class="adm-section">

            <!-- Filtros -->
            <div class="adm-card" style="margin-bottom:20px;">
                <div class="adm-card-head" style="padding-bottom:0;">
                    <h3><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;margin-right:6px;"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>Filtrar ventas</h3>
                </div>
                <div class="adm-card-body">
                    <div class="ventas-filtros">
                        <div class="ventas-filtro-grupo">
                            <label class="ventas-filtro-label">Desde</label>
                            <input type="date" id="ventaDesde" class="adm-input ventas-filtro-input">
                        </div>
                        <div class="ventas-filtro-grupo">
                            <label class="ventas-filtro-label">Hasta</label>
                            <input type="date" id="ventaHasta" class="adm-input ventas-filtro-input">
                        </div>
                        <div class="ventas-filtro-grupo">
                            <label class="ventas-filtro-label">Estado pedido</label>
                            <select id="ventaEstado" class="adm-input ventas-filtro-input">
                                <option value="">Todos</option>
                                <option value="Entregado">Entregado</option>
                                <option value="En camino">En camino</option>
                                <option value="En preparacion">En preparación</option>
                                <option value="Pendiente">Pendiente</option>
                                <option value="Cancelado">Cancelado</option>
                            </select>
                        </div>
                        <div class="ventas-filtro-acciones">
                            <button class="adm-btn adm-btn-primary" onclick="cargarVentas()">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                                Buscar
                            </button>
                            <button class="adm-btn adm-btn-ghost" onclick="ventasAtajoRango('mes')" title="Mes actual">Este mes</button>
                            <button class="adm-btn adm-btn-ghost" onclick="ventasAtajoRango('semana')" title="Últimos 7 días">7 días</button>
                            <button class="adm-btn adm-btn-ghost" onclick="ventasAtajoRango('hoy')" title="Hoy">Hoy</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tarjetas resumen del período -->
            <div class="ventas-resumen" id="ventasResumen" style="display:none;">
                <div class="ventas-kpi ventas-kpi-blue">
                    <div class="ventas-kpi-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 3h5v5"/><path d="M8 3H3v5"/><path d="M12 22v-8"/><path d="M16 12H8"/><path d="M21 8v13H3V8"/></svg></div>
                    <div>
                        <strong id="kpiPedidos">0</strong>
                        <span>Pedidos totales</span>
                    </div>
                </div>
                <div class="ventas-kpi ventas-kpi-green">
                    <div class="ventas-kpi-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg></div>
                    <div>
                        <strong id="kpiIngresos">S/ 0.00</strong>
                        <span>Ingresos del período</span>
                    </div>
                </div>
                <div class="ventas-kpi ventas-kpi-emerald">
                    <div class="ventas-kpi-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg></div>
                    <div>
                        <strong id="kpiEntregados">0</strong>
                        <span>Pedidos entregados</span>
                    </div>
                </div>
                <div class="ventas-kpi ventas-kpi-red">
                    <div class="ventas-kpi-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg></div>
                    <div>
                        <strong id="kpiCancelados">0</strong>
                        <span>Cancelados</span>
                    </div>
                </div>
                <div class="ventas-kpi ventas-kpi-purple">
                    <div class="ventas-kpi-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></div>
                    <div>
                        <strong id="kpiMejorDia">—</strong>
                        <span>Mejor día</span>
                    </div>
                </div>
            </div>


            <!-- Tabla detallada por día -->
            <div class="adm-card">
                <div class="adm-card-head">
                    <h3>Detalle por día</h3>
                    <span id="ventasTablaInfo" style="font-size:.78rem;color:#888;"></span>
                </div>
                <div class="adm-card-body adm-table-wrap">
                    <table class="adm-table">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Pedidos</th>
                                <th>Entregados</th>
                                <th>En camino</th>
                                <th>Pendientes</th>
                                <th>Cancelados</th>
                                <th>Ingresos (bruto)</th>
                                <th>Ingresos (entregados)</th>
                            </tr>
                        </thead>
                        <tbody id="tbodyVentas">
                            <tr><td colspan="8" style="text-align:center;color:#aaa;padding:30px;">Aplica un filtro para ver las ventas</td></tr>
                        </tbody>
                        <tfoot id="tfootVentas"></tfoot>
                    </table>
                </div>
            </div>

        </section>
        <?php endif; ?>

        <!-- ══ SECCIÓN BUZÓN / SOPORTE ══ -->
        <section id="sec-soporte" class="adm-section">
            <div class="adm-search" style="margin-bottom: 20px; display: flex; gap: 10px; flex-wrap: wrap; align-items: center;">
                <input class="adm-input" id="buscadorSoporte" placeholder="Buscar por DNI, nombre..."
                       oninput="filtrarSoporte()" style="flex: 1; min-width: 200px; padding: 10px 15px; border-radius: 8px; border: 1px solid #cbd5e1;">
                
                <input type="date" class="adm-input" id="filtroSoporteFecha" onchange="filtrarSoporte()" style="padding: 10px; border-radius: 8px; border: 1px solid #cbd5e1; color: #475569;" title="Filtrar por fecha">
                
                <select class="adm-input" id="filtroSoporteMotivo" onchange="filtrarSoporte()" style="padding: 10px; border-radius: 8px; border: 1px solid #cbd5e1; color: #475569;">
                    <option value="">Todos los motivos</option>
                </select>

                <button class="adm-btn" onclick="limpiarFiltrosSoporte()" style="background: #475569; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; color: white; display: flex; align-items: center; gap: 8px; font-weight: bold; box-shadow: 0 2px 4px rgba(0,0,0,0.1); transition: transform 0.2s, background 0.2s;" onmouseover="this.style.background='#334155'; this.style.transform='translateY(-2px)';" onmouseout="this.style.background='#475569'; this.style.transform='translateY(0)';">
                    <i class="fa fa-eraser"></i> Limpiar
                </button>
            </div>
            <div class="adm-card">
                <div class="adm-card-head">
                    <h3>Buzón de Atención al Cliente</h3>
                    <button class="adm-btn adm-btn-primary" onclick="cargarSoporte()">
                        <i class="fa fa-sync"></i> Actualizar
                    </button>
                </div>
                <div class="adm-card-body">
                    <table class="adm-table" id="tablaSoporte">
                        <thead>
                            <tr>
                                <th>N°</th>
                                <th>ID</th>
                                <th>Cliente</th>
                                <th>Asunto</th>
                                <th>Fecha</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td colspan="6" style="text-align:center;">Cargando mensajes...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <?php if (esAdmin()): ?>
    </div><!-- /adm-page -->
    <?php endif; ?>
    <?php if (!esAdmin()): ?>
    </div>
    <?php endif; ?>
</div><!-- /adm-main -->

<!-- ── Modal Producto ── -->
<div class="adm-modal-bg" id="modalProducto">
    <div class="adm-modal">
        <button class="adm-modal-close" onclick="cerrarModalProducto()">✕</button>
        <h3 id="mpTitulo">Nuevo producto</h3>
        <input type="hidden" id="mpId">
        <div class="adm-form-row">
            <div class="adm-fg full">
                <label>Nombre *</label>
                <input type="text" id="mpNombre" maxlength="150">
            </div>
            <div class="adm-fg">
                <label>Precio (S/) *</label>
                <input type="number" id="mpPrecio" step="0.01" min="0">
            </div>
            <div class="adm-fg">
                <label>Stock *</label>
                <input type="number" id="mpStock" min="0">
            </div>
            <div class="adm-fg">
                <label>Stock mínimo</label>
                <input type="number" id="mpStockMin" min="0" value="5">
            </div>
            <div class="adm-fg">
                <label>Categoría *</label>
                <select id="mpCategoria"></select>
            </div>
            <div class="adm-fg full">
                <label>URL Imagen</label>
                <input type="text" id="mpImagen" placeholder="https://..." oninput="previsualizarImgProd(this.value)">
                <img id="mpPreview" style="display:none;width:72px;height:72px;object-fit:cover;border-radius:8px;margin-top:6px;border:1px solid #eee;">
            </div>
            <div class="adm-fg full">
                <label>Descripción</label>
                <textarea id="mpDescripcion" maxlength="255"></textarea>
            </div>
            <div class="adm-fg full" id="mpEstadoWrap">
                <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-weight:600;">
                    <input type="checkbox" id="mpEstado" checked> Producto activo
                </label>
            </div>
        </div>
        <div class="adm-modal-actions">
            <button class="adm-btn adm-btn-ghost" onclick="cerrarModalProducto()">Cancelar</button>
            <button class="adm-btn adm-btn-primary" onclick="guardarProducto()">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                Guardar
            </button>
        </div>
    </div>
</div>

<!-- ── Modal Categoría ── -->
<div class="adm-modal-bg" id="modalCategoria">
    <div class="adm-modal">
        <button class="adm-modal-close" onclick="cerrarModalCategoria()">✕</button>
        <h3 id="mcTitulo">Nueva categoría</h3>
        <input type="hidden" id="mcId">
        <div class="adm-form-row">
            <div class="adm-fg full">
                <label>Nombre *</label>
                <input type="text" id="mcNombre" maxlength="100">
            </div>
            <div class="adm-fg full">
                <label>Grupo (Comida, Bebidas, Limpieza del hogar, Cuidado Personal) *</label>
                <select id="mcGrupo">
                    <option value="Comida">Comida</option>
                    <option value="Bebidas">Bebidas</option>
                    <option value="Limpieza del hogar">Limpieza del hogar</option>
                    <option value="Cuidado Personal">Cuidado Personal</option>
                </select>
            </div>
            <div class="adm-fg full">
                <label>URL Imagen</label>
                <input type="text" id="mcImagen" placeholder="https://..." oninput="previsualizarImgCat(this.value)">
                <img id="mcPreview" style="display:none;width:72px;height:72px;object-fit:cover;border-radius:8px;margin-top:6px;border:1px solid #eee;">
            </div>
            <div class="adm-fg full">
                <label>Descripción</label>
                <textarea id="mcDescripcion" maxlength="255"></textarea>
            </div>
        </div>
        <div class="adm-modal-actions">
            <button class="adm-btn adm-btn-ghost" onclick="cerrarModalCategoria()">Cancelar</button>
            <button class="adm-btn adm-btn-primary" onclick="guardarCategoria()">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                Guardar
            </button>
        </div>
    </div>
</div>

<!-- ── Modal Promoción ── -->
<div class="adm-modal-bg" id="modalPromocion">
    <div class="adm-modal">
        <button class="adm-modal-close" onclick="cerrarModalPromocion()">✕</button>
        <h3 id="mproTitulo">Nueva promoción</h3>
        <input type="hidden" id="mproId">
        <div class="adm-form-row">
            <div class="adm-fg full">
                <label>Nombre *</label>
                <input type="text" id="mproNombre" maxlength="100">
            </div>
            <div class="adm-fg">
                <label>Porcentaje Descuento (%) *</label>
                <input type="number" id="mproDescuento" step="0.01" min="0" max="100">
            </div>
            <div class="adm-fg">
                <label>Estado</label>
                <select id="mproEstado">
                    <option value="1">Activo</option>
                    <option value="0">Inactivo</option>
                </select>
            </div>
            <div class="adm-fg">
                <label>Fecha Inicio *</label>
                <input type="date" id="mproFechaInicio">
            </div>
            <div class="adm-fg">
                <label>Fecha Fin *</label>
                <input type="date" id="mproFechaFin">
            </div>
            <div class="adm-fg full">
                <label>Descripción</label>
                <textarea id="mproDescripcion" maxlength="255"></textarea>
            </div>
        </div>
        <div class="adm-modal-actions">
            <button class="adm-btn adm-btn-ghost" onclick="cerrarModalPromocion()">Cancelar</button>
            <button class="adm-btn adm-btn-primary" onclick="guardarPromocion()">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                Guardar
            </button>
        </div>
    </div>
</div>

<!-- ── Modal Asignar Productos a Promoción ── -->
<div class="adm-modal-bg" id="modalAsignarProductosPromocion" style="z-index: 10000;">
    <div class="adm-modal" style="max-width: 800px; width: 90%; max-height: 90vh; display: flex; flex-direction: column; padding: 25px;">
        <button class="adm-modal-close" onclick="cerrarModalAsignarProductos()">✕</button>
        <h3 id="mapTitulo" style="margin-bottom: 5px;">Asignar Productos</h3>
        <p id="mapSubtitulo" style="font-size: 0.9rem; color: #666; margin-bottom: 20px;"></p>
        
        <input type="hidden" id="mapPromoId">

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; flex: 1; overflow: hidden; min-height: 0;">
            <!-- Izquierda: Productos disponibles -->
            <div style="display: flex; flex-direction: column; overflow: hidden;">
                <h4 style="margin-bottom: 10px; border-bottom: 2px solid #eee; padding-bottom: 5px;">Disponibles para agregar</h4>
                <input class="adm-input" id="buscadorProdPromo" placeholder="Buscar producto..." oninput="filtrarProductosDePromocion()" style="margin-bottom: 10px; padding: 6px 12px; font-size: 0.9rem;">
                <div id="listaDisponibles" style="flex: 1; overflow-y: auto; border: 1px solid #eee; border-radius: 6px; padding: 8px;">
                    <!-- Cargados dinámicamente -->
                </div>
            </div>

            <!-- Derecha: Productos ya en promoción -->
            <div style="display: flex; flex-direction: column; overflow: hidden;">
                <h4 style="margin-bottom: 10px; border-bottom: 2px solid #eee; padding-bottom: 5px;">Productos en esta oferta</h4>
                <div id="listaAsociados" style="flex: 1; overflow-y: auto; border: 1px solid #eee; border-radius: 6px; padding: 8px;">
                    <!-- Cargados dinámicamente -->
                </div>
            </div>
        </div>

        <div class="adm-modal-actions" style="margin-top: 20px; border-top: 1px solid #eee; padding-top: 15px; justify-content: flex-end;">
            <button class="adm-btn adm-btn-primary" onclick="cerrarModalAsignarProductos()">Aceptar</button>
        </div>
    </div>
</div>

<!-- ── Modal Detalle Pedido ── -->
<div class="adm-modal-bg" id="modalDetallePedido">
    <div class="adm-modal">
        <button class="adm-modal-close" onclick="document.getElementById('modalDetallePedido').classList.remove('open')">✕</button>
        <h3>Detalle del pedido</h3>
        <div id="detallePedidoContenido"></div>
    </div>
</div>


<!-- ── Modal Asignar Repartidor ── -->
<div class="adm-modal-bg" id="modalAsignarRep">
    <div class="adm-modal">
        <button class="adm-modal-close" onclick="cerrarModalAsignar()">✕</button>
        <h3 id="asignarRepTitulo">Asignar repartidor</h3>
        <input type="hidden" id="asignarDeliveryId">
        <div class="adm-fg">
            <label>Selecciona repartidor</label>
            <select class="adm-select" id="asignarRepSelect" style="width:100%;padding:.6rem .8rem;border-radius:8px;font-size:.9rem;"></select>
        </div>
        <div class="adm-modal-actions">
            <button class="adm-btn adm-btn-ghost" onclick="cerrarModalAsignar()">Cancelar</button>
            <button class="adm-btn adm-btn-primary" onclick="confirmarAsignarRepartidor()">Asignar</button>
        </div>
    </div>
</div>

<!-- ── Modal Roles Usuario ── -->
<div class="adm-modal-bg" id="modalRoles">
    <div class="adm-modal" style="max-width: 450px; border-radius: 12px; padding: 25px;">
        <button class="adm-modal-close" onclick="cerrarModalRoles()" style="top: 15px; right: 15px;">✕</button>
        <h3 style="margin-bottom: 5px; font-size: 1.25rem; color: #1e293b;">Gestionar Roles</h3>
        <p style="font-size: 0.9rem; color: #64748b; margin-bottom: 20px;">
            Asignando roles para: <strong style="color: #334155;" id="rolesUsuarioNombre"></strong>
        </p>
        <input type="hidden" id="rolesUsuarioId">
        
        <style>
            .role-card:hover { border-color: #cbd5e1 !important; background-color: #f8fafc !important; }
            .role-card input[type="checkbox"] { width: 20px; height: 20px; accent-color: #e30613; cursor: pointer; flex-shrink: 0; }
        </style>

        <div class="adm-fg" style="display:flex; flex-direction:column; gap:12px; margin-top:10px;">
            <label class="role-card" style="display:flex; align-items:center; justify-content:space-between; cursor:pointer; padding: 15px 20px; border: 1px solid #e2e8f0; border-radius: 10px; transition: all 0.2s ease; background: #fff;">
                <div style="display: flex; flex-direction: column; padding-right: 15px;">
                    <span style="font-weight: 600; color: #0f172a; font-size: 1rem;">Administrador</span>
                    <span style="font-size: 0.8rem; color: #64748b; line-height: 1.3;">Acceso total al sistema, panel y configuraciones.</span>
                </div>
                <input type="checkbox" id="rolAdmin" value="1">
            </label>
            
            <label class="role-card" style="display:flex; align-items:center; justify-content:space-between; cursor:pointer; padding: 15px 20px; border: 1px solid #e2e8f0; border-radius: 10px; transition: all 0.2s ease; background: #fff;">
                <div style="display: flex; flex-direction: column; padding-right: 15px;">
                    <span style="font-weight: 600; color: #0f172a; font-size: 1rem;">Cliente</span>
                    <span style="font-size: 0.8rem; color: #64748b; line-height: 1.3;">Acceso a la tienda virtual para compras y pedidos.</span>
                </div>
                <input type="checkbox" id="rolCliente" value="2">
            </label>
            
            <label class="role-card" style="display:flex; align-items:center; justify-content:space-between; cursor:pointer; padding: 15px 20px; border: 1px solid #e2e8f0; border-radius: 10px; transition: all 0.2s ease; background: #fff;">
                <div style="display: flex; flex-direction: column; padding-right: 15px;">
                    <span style="font-weight: 600; color: #0f172a; font-size: 1rem;">Repartidor</span>
                    <span style="font-size: 0.8rem; color: #64748b; line-height: 1.3;">Acceso exclusivo al panel de entregas y deliveries.</span>
                </div>
                <input type="checkbox" id="rolRepartidor" value="3">
            </label>

            <label class="role-card" style="display:flex; align-items:center; justify-content:space-between; cursor:pointer; padding: 15px 20px; border: 1px solid #e2e8f0; border-radius: 10px; transition: all 0.2s ease; background: #fff;">
                <div style="display: flex; flex-direction: column; padding-right: 15px;">
                    <span style="font-weight: 600; color: #0f172a; font-size: 1rem;">Almacenero</span>
                    <span style="font-size: 0.8rem; color: #64748b; line-height: 1.3;">Gestión de inventario, stock y movimientos de mercancía.</span>
                </div>
                <input type="checkbox" id="rolAlmacenero" value="4">
            </label>

            <label class="role-card" style="display:flex; align-items:center; justify-content:space-between; cursor:pointer; padding: 15px 20px; border: 1px solid #e2e8f0; border-radius: 10px; transition: all 0.2s ease; background: #fff;">
                <div style="display: flex; flex-direction: column; padding-right: 15px;">
                    <span style="font-weight: 600; color: #0f172a; font-size: 1rem;">Despachador (Picker)</span>
                    <span style="font-size: 0.8rem; color: #64748b; line-height: 1.3;">Recepción, preparación y empaque de pedidos en tienda.</span>
                </div>
                <input type="checkbox" id="rolDespachador" value="5">
            </label>

            <label class="role-card" style="display:flex; align-items:center; justify-content:space-between; cursor:pointer; padding: 15px 20px; border: 1px solid #e2e8f0; border-radius: 10px; transition: all 0.2s ease; background: #fff;">
                <div style="display: flex; flex-direction: column; padding-right: 15px;">
                    <span style="font-weight: 600; color: #0f172a; font-size: 1rem;">Cajero</span>
                    <span style="font-size: 0.8rem; color: #64748b; line-height: 1.3;">Ventas directas en mostrador y facturación rápida.</span>
                </div>
                <input type="checkbox" id="rolCajero" value="6">
            </label>

            <label class="role-card" style="display:flex; align-items:center; justify-content:space-between; cursor:pointer; padding: 15px 20px; border: 1px solid #e2e8f0; border-radius: 10px; transition: all 0.2s ease; background: #fff;">
                <div style="display: flex; flex-direction: column; padding-right: 15px;">
                    <span style="font-weight: 600; color: #0f172a; font-size: 1rem;">Soporte</span>
                    <span style="font-size: 0.8rem; color: #64748b; line-height: 1.3;">Atención al cliente y resolución de mensajes en el buzón.</span>
                </div>
                <input type="checkbox" id="rolSoporte" value="7">
            </label>
        </div>
        
        <div class="adm-modal-actions" style="margin-top: 25px; display: flex; justify-content: flex-end; gap: 10px;">
            <button class="adm-btn adm-btn-ghost" onclick="cerrarModalRoles()" style="padding: 10px 20px; border-radius: 8px;">Cancelar</button>
            <button class="adm-btn adm-btn-primary" id="btnGuardarRoles" onclick="guardarRolesUsuario()" style="padding: 10px 20px; border-radius: 8px; box-shadow: 0 4px 6px -1px rgba(227, 6, 19, 0.2);">
                <i class="fa fa-save" style="margin-right: 6px;"></i> Guardar
            </button>
        </div>
    </div>
</div>

<!-- ── Modal Colaborador (Trabajador) ── -->
<div class="adm-modal-bg" id="modalTrabajador">
    <div class="adm-modal" style="max-width: 520px; border-radius: 12px; padding: 25px;">
        <button class="adm-modal-close" onclick="cerrarModalTrabajador()" style="top: 15px; right: 15px;">✕</button>
        <h3 id="modalTrabajadorTitulo" style="margin-bottom: 6px; font-size: 1.25rem; color: #1e293b;">Nuevo Colaborador</h3>
        <p style="font-size: 0.85rem; color: #64748b; margin-bottom: 20px;">
            Asigna un cargo operativo de tienda y horario laboral a un usuario registrado.
        </p>
        <input type="hidden" id="trabajadorId">

        <form id="formTrabajador" onsubmit="guardarTrabajador(event)">
            <div class="adm-fg" id="grupoSelectUsuario">
                <label style="font-weight:600; font-size:0.85rem; color:#334155; margin-bottom:6px; display:block;">Seleccionar Usuario Registrado *</label>
                <select id="trabajadorUsuarioId" class="adm-input" required style="width:100%;">
                    <option value="">-- Elige un usuario por nombre o DNI --</option>
                </select>
                <small style="color:#64748b; font-size:0.75rem; margin-top:3px; display:block;">Si el colaborador no está en la lista, primero regístralo como usuario.</small>
            </div>

            <div class="adm-fg" id="infoUsuarioEdicion" style="display:none; background:#f8fafc; padding:12px; border-radius:8px; margin-bottom:14px; border:1px solid #e2e8f0;">
                <span style="font-size:0.85rem; color:#64748b;">Colaborador:</span>
                <strong id="labelNombreColaborador" style="display:block; color:#0f172a; font-size:1rem;"></strong>
            </div>

            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:12px;">
                <div class="adm-fg">
                    <label style="font-weight:600; font-size:0.85rem; color:#334155; margin-bottom:6px; display:block;">Cargo / Rol Operativo *</label>
                    <select id="trabajadorCargo" class="adm-input" required style="width:100%;">
                        <option value="Despachador">Despachador (Picker)</option>
                        <option value="Almacenero">Almacenero (Stock)</option>
                        <option value="Repartidor">Repartidor (Delivery)</option>
                        <option value="Cajero">Cajero (POS Mostrador)</option>
                        <option value="Soporte">Soporte (Atención al Cliente)</option>
                    </select>
                </div>
                <div class="adm-fg">
                    <label style="font-weight:600; font-size:0.85rem; color:#334155; margin-bottom:6px; display:block;">Turno Asignado *</label>
                    <select id="trabajadorTurno" class="adm-input" required style="width:100%;">
                        <option value="Mañana">Mañana (07:00 - 15:00)</option>
                        <option value="Tarde">Tarde (14:00 - 22:00)</option>
                        <option value="Noche">Noche (21:00 - 05:00)</option>
                        <option value="Completo">Horario Completo / Rotativo</option>
                    </select>
                </div>
            </div>

            <div class="adm-fg" style="margin-top:10px;">
                <label style="font-weight:600; font-size:0.85rem; color:#334155; margin-bottom:6px; display:block;">Sueldo Mensual (S/)</label>
                <input type="number" step="0.01" min="0" id="trabajadorSueldo" class="adm-input" placeholder="Ej. 1200.00" style="width:100%;">
            </div>

            <div class="adm-fg" style="margin-top:10px;">
                <label style="font-weight:600; font-size:0.85rem; color:#334155; margin-bottom:6px; display:block;">Observaciones / Notas Internas</label>
                <textarea id="trabajadorNotas" class="adm-input" rows="2" placeholder="Detalles de contrato, responsabilidades o área..." style="width:100%; resize:vertical;"></textarea>
            </div>

            <div class="adm-modal-actions" style="margin-top: 20px; display: flex; justify-content: flex-end; gap: 10px;">
                <button type="button" class="adm-btn adm-btn-ghost" onclick="cerrarModalTrabajador()" style="padding: 9px 18px; border-radius: 8px;">Cancelar</button>
                <button type="submit" class="adm-btn adm-btn-primary" id="btnGuardarTrabajador" style="padding: 9px 20px; border-radius: 8px;">
                    <i class="fa fa-check" style="margin-right: 6px;"></i> Guardar Colaborador
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Toast -->
<div class="adm-toast" id="admToast"></div>

<script src="<?= BASE_URL ?>/assets/js/admin.js?v=<?= @filemtime(__DIR__ . '/../../assets/js/admin.js') ?: time() ?>"></script>
<script src="<?= BASE_URL ?>/assets/js/soporte_admin.js"></script>

<!-- Modal Responder Soporte -->
<div class="adm-modal-bg" id="modalSoporte">
    <div class="adm-modal" style="max-width: 600px; padding: 0; display: flex; flex-direction: column; height: 80vh;">
        <div class="adm-modal-head" style="padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; background: linear-gradient(135deg, #e30613, #b9000b); border-radius: 8px 8px 0 0; color: white;">
            <div>
                <h3 style="margin: 0; color: white; display: flex; align-items: center; gap: 8px;"><i class="fa fa-headset"></i> Chat de Soporte</h3>
                <small id="soporteClienteNombreCorreo" style="color: #f1f5f9; opacity: 0.9;">Cargando...</small>
            </div>
            <button class="adm-modal-close" onclick="cerrarModalSoporte()" style="position: static; color: white; background: rgba(255,255,255,0.2); border-radius: 50%; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">✕</button>
        </div>
        
        <div id="soporteHistorialMensajes" style="flex: 1; overflow-y: auto; padding: 20px; background: #f1f5f9; display: flex; flex-direction: column; gap: 15px;">
            <!-- Chat bubbles will go here -->
            <div style="text-align: center; color: #94a3b8;">Cargando mensajes...</div>
        </div>

        <form id="formResponderSoporte" style="padding: 15px 20px; border-top: 1px solid #e2e8f0; background: #fff; border-radius: 0 0 8px 8px;">
            <input type="hidden" id="soporteId">
            <div style="display: flex; gap: 10px;">
                <textarea id="soporteRespuesta" rows="2" class="adm-input" required placeholder="Escribe tu mensaje..." style="flex: 1; resize: none; border-radius: 20px; padding: 10px 15px;"></textarea>
                <button type="submit" class="adm-btn adm-btn-primary" id="btnResponderSoporte" style="border-radius: 50%; width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; padding: 0;">
                    <i class="fa fa-paper-plane" style="margin: 0;"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Variables globales
    const IS_ADMIN = <?= esAdmin() ? 'true' : 'false' ?>;
    const DEFAULT_TAB = IS_ADMIN ? 'dashboard' : 'soporte';

// Abrir sección desde URL params
(function () {
    const p   = new URLSearchParams(window.location.search);
    const sec = p.get('sec') || DEFAULT_TAB;
    const editarId = p.get('editar');
    const elimId   = p.get('accion') === 'eliminar' ? p.get('id') : null;

    document.addEventListener('DOMContentLoaded', async function () {
        navegar(sec);
        if (sec === 'productos') {
            if (typeof cargarProductos === 'function') {
                await cargarProductos();
                if (editarId) {
                    const prod = typeof _productos !== 'undefined' ? _productos.find(pr => String(pr.id_producto) === editarId) : null;
                    if (prod) abrirModalProducto(prod);
                }
                if (elimId) {
                    const prod = typeof _productos !== 'undefined' ? _productos.find(pr => String(pr.id_producto) === elimId) : null;
                    if (prod) eliminarProducto(prod.id_producto, prod.nombre);
                }
            }
        }
    });
})();

// Lógica del temporizador de sesión
(function() {
    const timerSpan = document.getElementById('admSessionTimer');
    const timeLeftSpan = document.getElementById('admTimeLeft');
    if (!timerSpan || !timeLeftSpan) return;

    let remaining = parseInt(timerSpan.getAttribute('data-remaining'), 10);

    function updateDisplay() {
        if (remaining <= 0) {
            timeLeftSpan.textContent = "Expirada";
            timerSpan.style.color = "#ef4444";
            return;
        }
        
        const m = Math.floor(remaining / 60);
        const s = remaining % 60;
        timeLeftSpan.textContent = `${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`;

        if (remaining < 300) { // Menos de 5 minutos
            timerSpan.style.color = "#ef4444";
        }
    }

    updateDisplay();
    setInterval(() => {
        if (remaining > 0) {
            remaining--;
            updateDisplay();
        }
    }, 1000);
})();
</script>
</body>
</html>

