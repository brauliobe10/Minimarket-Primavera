<?php
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../config/Security.php';
require __DIR__ . '/../controllers/InicioController.php';
require __DIR__ . '/../controllers/CategoriaController.php';

\Config\Security::setSecurityHeaders();

// Solo refrescar roles, sin redirigir — el repartidor también puede usar la tienda
if (estaLogueado()) {
    sincronizarRoles();
requireClienteOrGuest();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<script>window.BASE_URL = '<?= BASE_URL ?>';</script>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Market Primavera</title>
    <link rel="stylesheet" href="../assets/css/estilos.css?v=<?= time() ?>" />
    <link rel="stylesheet" href="../assets/css/auth.css?v=<?= time() ?>" />
    <link rel="stylesheet" href="../assets/css/carrito.css?v=<?= time() ?>" />
    <link rel="stylesheet" href="../assets/css/chatbot.css?v=<?= time() ?>" />
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .swal2-small-toast {
            font-size: 0.8rem !important;
            padding: 8px 12px !important;
            max-width: 320px !important;
        }
        .swal2-small-toast .swal2-title {
            font-size: 0.85rem !important;
            margin: 0 !important;
        }
        .swal2-container.swal2-bottom-end {
            bottom: 90px !important;
        }
    </style>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<body>

<?php require __DIR__ . '/login.php'; ?>
<?php require __DIR__ . '/registro.php'; ?>



    <!-- Encabezado principal -->
    <header class="header" id="site-header">
        <div class="header-left">
            <!-- Botón hamburguesa (móvil) -->
            <button class="hamburger-menu" aria-label="Abrir menú" aria-expanded="false" style="margin-right: 15px;">
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
            </button>
            <a href="inicio.php" class="logo">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="logo-icon">
                    <circle cx="9" cy="21" r="1"></circle>
                    <circle cx="20" cy="21" r="1"></circle>
                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"></path>
                </svg>
                <span>Market Primavera</span>
            </a>
        </div>

        <div class="header-center">
            <div class="search-container">
                <input class="buscador"
                    id="buscadorInput"
                    type="search"
                    placeholder="Buscar productos, marcas y más..."
                    aria-label="Buscar productos">
                <button class="search-btn" aria-label="Buscar">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle>
                        <path d="m21 21-4.35-4.35"></path>
                    </svg>
                </button>
                <div id="search-results" class="search-results"></div>
            </div>
        </div>

        <div class="header-right">




            <!-- Mis Pedidos -->
            <?php if (estaLogueado()): ?>
            <a href="<?= BASE_URL ?>/views/perfil.php#pedidos" class="header-item" id="misPedidosHeader">
            <?php else: ?>
            <a href="<?= BASE_URL ?>/views/rastrear_pedido.php" class="header-item" id="misPedidosHeader">
            <?php endif; ?>
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon-svg">
                    <path d="M16 3h5v5"></path>
                    <path d="M8 3H3v5"></path>
                    <path d="M12 22v-8"></path>
                    <path d="M16 12H8"></path>
                    <path d="M21 8v13H3V8"></path>
                </svg>
                <span class="header-label"><?= estaLogueado() ? 'Mis pedidos' : 'Rastrear pedido' ?></span>
            </a>


            
<!-- Mis Mensajes / Buzon -->
<?php if (estaLogueado()): ?>
<a href="<?= BASE_URL ?>/views/perfil.php#mensajes" class="header-item" id="misMensajesHeader">
    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon-svg">
        <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
        <polyline points="22,6 12,13 2,6"></polyline>
    </svg>
    <span class="header-label">Mensajes</span>
</a>
<?php endif; ?>

<!-- Mi Cuenta / Sesión -->
<?php if (estaLogueado()): ?>

<div class="has-dropdown-header">
    <button class="header-item account-toggle"
            type="button"
            aria-expanded="false"
            aria-controls="account-dropdown">

        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22"
             viewBox="0 0 24 24" fill="none" stroke="currentColor"
             stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
             class="icon-svg">
            <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
            <circle cx="12" cy="7" r="4"></circle>
        </svg>

        <span class="header-label" style="display: flex; align-items: center; gap: 6px;">
            <?= htmlspecialchars($_SESSION['usuario_nombre']) ?>
            <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" alt="Google" style="width: 14px; height: 14px; display: inline-block; transform: translateY(1px);" title="Cuenta">
        </span>
    </button>

    <ul id="account-dropdown" class="dropdown-header" role="menu">

        <?php if (esAdmin()): ?>
        <li>
            <a href="<?= BASE_URL ?>/views/admin/index.php">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                     viewBox="0 0 24 24" fill="none" stroke="currentColor"
                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
                    <rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>
                </svg>
                Panel Admin
            </a>
        </li>
        <?php endif; ?>

        <?php if (esRepartidor()): ?>
        <li>
            <a href="<?= BASE_URL ?>/views/panel_repartidor.php">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                     viewBox="0 0 24 24" fill="none" stroke="currentColor"
                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/>
                    <circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/>
                </svg>
                Panel de entregas
            </a>
        </li>
        <?php endif; ?>

        <li>
            <a href="<?= BASE_URL ?>/views/perfil.php">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                     viewBox="0 0 24 24" fill="none" stroke="currentColor"
                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
                Mi perfil
            </a>
        </li>

        <li>
            <a href="<?= BASE_URL ?>/controllers/AuthController.php?logout=1">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                     viewBox="0 0 24 24" fill="none" stroke="currentColor"
                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                    <polyline points="16 17 21 12 16 7"></polyline>
                    <line x1="21" y1="12" x2="9" y2="12"></line>
                </svg>
                Cerrar sesión
            </a>
        </li>

    </ul>
</div>

<?php else: ?>

<div class="has-dropdown-header">
    <button class="header-item account-toggle"
            type="button"
            onclick="abrirModalLogin(); return false;">

        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22"
             viewBox="0 0 24 24" fill="none" stroke="currentColor"
             stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
             class="icon-svg">
            <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
            <circle cx="12" cy="7" r="4"></circle>
        </svg>

        <span class="header-label">Iniciar sesión</span>
    </button>
</div>

<?php endif; ?>

         
         <!-- Ubicación--> 
<a href="acercaDe.php#location-section" class="header-item">
    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon-svg">
        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
        <circle cx="12" cy="10" r="3"></circle>
    </svg>
    <span class="header-label">Ubicación</span>
</a>
        <!-- Carrito -->
<div class="header-item" id="cart-icon">
    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon-svg">
        <circle cx="8" cy="21" r="1"></circle>
        <circle cx="19" cy="21" r="1"></circle>
        <path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"></path>
    </svg>
    <span class="header-label">Carrito <small class="cart-count">0</small></span>
</div>
        </div>
    </header>

  <!-- Barra de navegación principal -->
<nav class="main-nav" role="navigation" aria-label="Menú principal">
    <ul class="menu-items">

        <li><a href="inicio.php">Inicio</a></li>
        <li><a href="inicio.php#ofertas">Ofertas</a></li>

        <li class="dropdown">
            <a href="#">Comida</a>
            <ul class="dropdown-menu">
                <?php foreach ($comida as $cat): ?>
                    <li>
                        <a href="categoria.php?id=<?= $cat['id_categoria'] ?>">
                            <?= htmlspecialchars($cat['nombre']) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </li>

        <li class="dropdown">
            <a href="#">Bebidas</a>
            <ul class="dropdown-menu">
                <?php foreach ($bebidas as $cat): ?>
                    <li>
                        <a href="categoria.php?id=<?= $cat['id_categoria'] ?>">
                            <?= htmlspecialchars($cat['nombre']) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </li>

        <li class="dropdown">
            <a href="#">Limpieza del hogar</a>
            <ul class="dropdown-menu">
                <?php foreach ($limpieza as $cat): ?>
                    <li>
                        <a href="categoria.php?id=<?= $cat['id_categoria'] ?>">
                            <?= htmlspecialchars($cat['nombre']) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </li>

        <li class="dropdown">
            <a href="#">Cuidado personal</a>
            <ul class="dropdown-menu">
                <?php foreach ($cuidado as $cat): ?>
                    <li>
                        <a href="categoria.php?id=<?= $cat['id_categoria'] ?>">
                            <?= htmlspecialchars($cat['nombre']) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </li>

        <li><a href="inicio.php#mas-vendido">Más vendido</a></li>
        <li><a href="acercaDe.php">Acerca de</a></li>
        <li><a href="AtencionCliente.php" class="active">Atención al cliente</a></li>

    </ul>
</nav>

    <!-- Menú hamburguesa (móvil lateral) -->
    <nav class="menu-hamburguesa" aria-hidden="true" aria-label="Menú móvil">
        <div class="menu-ham-header">
            <a href="<?= BASE_URL ?>/views/inicio.php" class="menu-ham-logo">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="2"
                     stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="9" cy="21" r="1"></circle>
                    <circle cx="20" cy="21" r="1"></circle>
                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"></path>
                </svg>
                Market Primavera
            </a>
            <button class="close-menu" aria-label="Cerrar menú">✕</button>
        </div>
        <ul>
            <li><a href="inicio.php">Inicio</a></li>
            <li><a href="inicio.php#ofertas">Ofertas</a></li>

            <li class="accordion">
                <button class="accordion-toggle" aria-expanded="false">Comida</button>

                <ul class="accordion-panel">
                    <?php foreach ($comida as $cat): ?>
                        <li>
                            <a href="categoria.php?id=<?= $cat['id_categoria'] ?>">
                                <?= htmlspecialchars($cat['nombre']) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </li>

            <li class="accordion">
                <button class="accordion-toggle" aria-expanded="false">Bebidas</button>

                <ul class="accordion-panel">
                    <?php foreach ($bebidas as $cat): ?>
                        <li>
                            <a href="categoria.php?id=<?= $cat['id_categoria'] ?>">
                                <?= htmlspecialchars($cat['nombre']) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </li>

            <li class="accordion">
                <button class="accordion-toggle" aria-expanded="false">
                    Limpieza del hogar
                </button>

                <ul class="accordion-panel">
                    <?php foreach ($limpieza as $cat): ?>
                        <li>
                            <a href="categoria.php?id=<?= $cat['id_categoria'] ?>">
                                <?= htmlspecialchars($cat['nombre']) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </li>

            <li class="accordion">
                <button class="accordion-toggle" aria-expanded="false">
                    Cuidado Personal
                </button>

                <ul class="accordion-panel">
                    <?php foreach ($cuidado as $cat): ?>
                        <li>
                            <a href="categoria.php?id=<?= $cat['id_categoria'] ?>">
                                <?= htmlspecialchars($cat['nombre']) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </li>

            <li><a href="inicio.php#mas-vendido">Más vendido</a></li>
            <li><?php if (estaLogueado()): ?><a href="<?= BASE_URL ?>/views/perfil.php#pedidos" id="misPedidosHamburguesa" class="highlight">Mis pedidos</a><?php else: ?><a href="<?= BASE_URL ?>/views/rastrear_pedido.php" id="misPedidosHamburguesa" class="highlight">Rastrear pedido</a><?php endif; ?></li>
            <li><a href="acercaDe.php" class="highlight">Acerca de</a></li>
            <li><a href="AtencionCliente.php" class="highlight">Atención al cliente</a></li>
        </ul>
    </nav>

    <main>
        <!-- Carrusel de imágenes -->
        <div id="carrusel-contenido">
            <div id="carrusel-caja">
                <div class="carrusel-elemento">
                    <div class="imagen-contenedor">
                        <img class="imagenes" src="<?= BASE_URL ?>/img/imagen1.webp" alt="Imagen 1">
                    </div>
                </div>
                <div class="carrusel-elemento">
                    <div class="imagen-contenedor">
                        <img class="imagenes" src="<?= BASE_URL ?>/img/imagen2.jpg" alt="Imagen 2">
                    </div>
                </div>
                <div class="carrusel-elemento">
                    <div class="imagen-contenedor">
                        <img class="imagenes" src="<?= BASE_URL ?>/img/imagen3.webp" alt="Imagen 3">
                    </div>
                </div>
            </div>
        </div>

        <!-- Texto sobre el carrusel -->
        <div class="texto-overlay">
            <h1>Compra lo básico del día</h1>
            <p>Fácil, fresco y cómodo. Ahorra en grande en tus marcas favoritas.</p>
            <a href="#ofertas" class="btn-primario" onclick="scrollToOfertas(event)">Comprar ahora</a>
        </div>

        <!-- Servicios -->
        <section class="servicios">
            <div class="servicio-item">
                <div class="servicio-icon-container">
                    <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="servicio-icono-svg">
                        <path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"></path>
                        <path d="M15 18H9"></path>
                        <path d="M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.624l-3.48-4.35A1 1 0 0 0 17.52 8H14"></path>
                        <circle cx="17" cy="18" r="2"></circle>
                        <circle cx="7" cy="18" r="2"></circle>
                    </svg>
                </div>
                <div class="linea"></div>
                <div class="servicio-texto">
                    <h3>Entrega a domicilio</h3>
                    <p>Con costo adicional</p>
                </div>
            </div>
            <div class="servicio-item">
                <div class="servicio-icon-container">
                    <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="servicio-icono-svg">
                        <circle cx="8" cy="21" r="1"></circle>
                        <circle cx="19" cy="21" r="1"></circle>
                        <path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"></path>
                    </svg>
                </div>
                <div class="linea"></div>
                <div class="servicio-texto">
                    <h3>Recoge en tienda</h3>
                    <p>Sin costo adicional</p>
                </div>
            </div>
            <div class="servicio-item">
                <div class="servicio-icon-container">
                    <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="servicio-icono-svg">
                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                    </svg>
                </div>
                <div class="linea"></div>
                <div class="servicio-texto">
                    <h3>Atención 24/7</h3>
                    <p>Asistencia online</p>
                </div>
            </div>
            <div class="servicio-item">
                <div class="servicio-icon-container">
                    <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="servicio-icono-svg">
                        <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                        <line x1="8" y1="21" x2="16" y2="21"></line>
                        <line x1="12" y1="17" x2="12" y2="21"></line>
                    </svg>
                </div>
                <div class="linea"></div>
                <div class="servicio-texto">
                    <h3>Plataforma Web</h3>
                    <p>Compra fácil y seguro</p>
                </div>
            </div>
        </section>

<!-- Ofertas especiales -->
<section id="ofertas" class="seccion productos">
    <h2>Ofertas especiales</h2>
    <p>Descuentos por tiempo limitado en productos seleccionados. ¡Aprovecha antes de que se agoten!</p>

    <?php if (empty($productosOferta)): ?>
        <p>No hay ofertas activas por el momento.</p>
    <?php else: ?>

    <div class="contenedor-carrusel">

        <button class="btn-carrusel prev" data-target="grid-ofertas">&#10094;</button>

        <div class="grid-destacados" id="grid-ofertas">
            <?php foreach ($productosOferta as $p): ?>
            <?php $precioOferta = $p['precio'] * (1 - $p['porcentaje_descuento'] / 100); ?>
            <?php $stock = (int)($p['stock_actual'] ?? 0); ?>

                <div class="card-producto card-destacado"
                    id="producto-<?= $p['id_producto'] ?>"
                    data-nombre="<?= htmlspecialchars($p['nombre']) ?>"
                    data-precio="<?= $precioOferta ?>"
                    data-precio-original="<?= $p['precio'] ?>"
                    data-imagen="<?= htmlspecialchars($p['imagen']) ?>"
                    data-descripcion="<?= htmlspecialchars($p['descripcion'] ?? '') ?>"
                    data-stock="<?= $stock ?>">

                    <span class="badge-descuento">-<?= (int)$p['porcentaje_descuento'] ?>%</span>

                    <img src="<?= htmlspecialchars($p['imagen']) ?>"
                         alt="<?= htmlspecialchars($p['nombre']) ?>">

                    <h3><?= htmlspecialchars($p['nombre']) ?></h3>

                    <p class="precio">
                        S/ <?= number_format($precioOferta, 2) ?>
                        <small class="precio-original">S/ <?= number_format($p['precio'], 2) ?></small>
                    </p>

                    <p class="stock-info <?= $stock <= 0 ? 'agotado' : '' ?>">
                        <?= $stock > 0 ? 'Stock disponible: <span>' . $stock . '</span>' : 'Agotado' ?>
                    </p>

                        <div class="carrito-controles" onclick="event.stopPropagation();">

                            <div class="selector-cantidad">
                                <button type="button"
                                        class="btn-cantidad"
                                        onclick="event.stopPropagation(); cambiarCantidad(this, -1)">
                                    −
                                </button>

                                <input type="number"
                                       class="input-cantidad"
                                       value="1"
                                       min="1"
                                       max="<?= $stock ?>">

                                <button type="button"
                                        class="btn-cantidad"
                                        onclick="event.stopPropagation(); cambiarCantidad(this, 1)">
                                    +
                                </button>
                            </div>

                            <button class="btn-agregar"
                                    data-id="<?= $p['id_producto'] ?>"
                                    <?= $stock <= 0 ? 'disabled' : '' ?>
                                    onclick="event.stopPropagation(); agregarAlCarritoPorId(this)">
                                <?= $stock > 0 ? 'Agregar' : 'Agotado' ?>
                            </button>

                        </div>

                </div>

            <?php endforeach; ?>
        </div>

        <button class="btn-carrusel next" data-target="grid-ofertas">&#10095;</button>

    </div>

    <?php endif; ?>
</section>

<!-- Productos destacados -->
<section id="mas-vendido" class="seccion productos">
    <h2>Productos destacados</h2>
    <p>Los productos más populares y mejor valorados por nuestros clientes</p>

    <div class="contenedor-carrusel">

        <button class="btn-carrusel prev">&#10094;</button>

        <div class="grid-destacados">
            <?php foreach ($productosDestacados as $p): ?>
            <?php 
                $stock = (int)($p['stock_actual'] ?? 0);
                $tieneOferta = !empty($p['porcentaje_descuento']) && $p['porcentaje_descuento'] > 0;
                $precioOriginal = $p['precio'];
                $precioFinal = $tieneOferta ? $precioOriginal * (1 - $p['porcentaje_descuento'] / 100) : $precioOriginal;
            ?>

                <div class="card-producto card-destacado"
                    id="producto-<?= $p['id_producto'] ?>"
                    data-nombre="<?= htmlspecialchars($p['nombre']) ?>"
                    data-precio="<?= $precioFinal ?>"
                    data-precio-original="<?= $precioOriginal ?>"
                    data-imagen="<?= htmlspecialchars($p['imagen']) ?>"
                    data-descripcion="<?= htmlspecialchars($p['descripcion'] ?? '') ?>"
                    data-stock="<?= $stock ?>">

                    <?php if ($tieneOferta): ?>
                        <span class="badge-descuento">-<?= (int)$p['porcentaje_descuento'] ?>%</span>
                    <?php endif; ?>

                    <img src="<?= htmlspecialchars($p['imagen']) ?>"
                         alt="<?= htmlspecialchars($p['nombre']) ?>">

                    <h3><?= htmlspecialchars($p['nombre']) ?></h3>

                    <p class="precio">
                        S/ <?= number_format($precioFinal, 2) ?>
                        <?php if ($tieneOferta): ?>
                            <small class="precio-original">S/ <?= number_format($precioOriginal, 2) ?></small>
                        <?php endif; ?>
                    </p>

                    <p class="stock-info <?= $stock <= 0 ? 'agotado' : '' ?>">
                        <?= $stock > 0 ? 'Stock disponible: <span>' . $stock . '</span>' : 'Agotado' ?>
                    </p>

                        <div class="carrito-controles" onclick="event.stopPropagation();">

                            <div class="selector-cantidad">
                                <button type="button"
                                        class="btn-cantidad"
                                        onclick="event.stopPropagation(); cambiarCantidad(this, -1)">
                                    −
                                </button>

                                <input type="number"
                                       class="input-cantidad"
                                       value="1"
                                       min="1"
                                       max="<?= $stock ?>">

                                <button type="button"
                                        class="btn-cantidad"
                                        onclick="event.stopPropagation(); cambiarCantidad(this, 1)">
                                    +
                                </button>
                            </div>

                            <button class="btn-agregar"
                                    data-id="<?= $p['id_producto'] ?>"
                                    <?= $stock <= 0 ? 'disabled' : '' ?>
                                    onclick="event.stopPropagation(); agregarAlCarritoPorId(this)">
                                <?= $stock > 0 ? 'Agregar' : 'Agotado' ?>
                            </button>

                        </div>

                </div>

            <?php endforeach; ?>
        </div>

        <button class="btn-carrusel next">&#10095;</button>

    </div>
</section>


<!-- Categorías más populares -->
<section class="categorias">
    <h2>Categorías más populares</h2>

    <div class="categorias-grid">
        <?php foreach ($categorias as $cat): ?>
            <a href="categoria.php?id=<?= $cat['id_categoria'] ?>"
               class="categoria-item"
               style="text-decoration: none; color: #333;">

                <img src="<?= htmlspecialchars($cat['imagen']) ?>"
                     alt="<?= htmlspecialchars($cat['nombre']) ?>"
                     class="categoria-icono">

                <p><?= htmlspecialchars($cat['nombre']) ?></p>

            </a>
        <?php endforeach; ?>
    </div>
</section>

</main>

<!-- Modal producto -->
<div class="modal-overlay"
     id="modalOverlay"
     role="dialog"
     aria-modal="true"
     aria-hidden="true">

    <div class="modal-contenido">

        <button class="modal-cerrar"
                id="modalCerrar"
                aria-label="Cerrar">
            ✕
        </button>

        <div class="modal-cuerpo">

            <div class="modal-imagen-wrap">
                <img id="modalImagen" src="" alt="">
            </div>

            <div class="modal-info">

                <h2 id="modalNombre"></h2>

                <p class="modal-precio" id="modalPrecio"></p>

                <p class="modal-stock" id="modalStock"></p>

                <p class="modal-descripcion-titulo">
                    Descripción
                </p>

                <p id="modalDescripcion"
                   class="modal-descripcion-texto"></p>

                <div class="carrito-controles-modal">

                    <div class="selector-cantidad">

                        <button type="button"
                                class="btn-cantidad"
                                onclick="cambiarCantidad(this, -1)">
                            −
                        </button>

                        <input type="number"
                               class="input-cantidad modal-input-cantidad"
                               id="modalCantidad"
                               value="1"
                               min="1">

                        <button type="button"
                                class="btn-cantidad"
                                onclick="cambiarCantidad(this, 1)">
                            +
                        </button>

                    </div>

                    <button class="modal-agregar-carrito"
                            id="modalAgregarCarrito">

                        <svg xmlns="http://www.w3.org/2000/svg"
                             width="18"
                             height="18"
                             viewBox="0 0 24 24"
                             fill="none"
                             stroke="currentColor"
                             stroke-width="2">

                            <circle cx="8" cy="21" r="1"></circle>
                            <circle cx="19" cy="21" r="1"></circle>
                            <path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"></path>

                        </svg>

                        Agregar
                    </button>

                </div>

            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/carrito.php'; ?>

    <!-- FOOTER -->
    <footer class="footer-primavera">
        <div class="footer-inner">
            <a href="inicio.php" class="footer-brand">
                <svg xmlns="http://www.w3.org/2000/svg" class="footer-logo-icon" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="9" cy="21" r="1"></circle>
                    <circle cx="20" cy="21" r="1"></circle>
                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"></path>
                </svg>
                <span class="footer-logo-text">MARKET PRIMAVERA</span>
            </a>

            <p class="footer-subtitle">Aceptamos los siguientes métodos de pago</p>
            <div class="footer-payments">
                <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSrSgz318379divs84c9xhz_dhULTvVJJhyR4bSRgUVBA&s=10" alt="Mastercard">
                <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS-4lpjYz3PxT5AjeTDpe0FEaYlCnPFSeduWPBQEBl1-iXc2DyZsnQkgfAf&s=10" alt="Visa">
                <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTiCulWdYAlGe2IBrfrwd9dGB2MJ2-VwwcIMk7cAI_feQ&s=10" alt="Yape">
                <img src="https://www.bbva.pe/content/dam/public-web/peru/images/promo-sliders/promo-plin.png" alt="Plin">
            </div>

            <div class="footer-help">
                <p class="footer-help-title">¿Necesitas ayuda?</p>
                <p>Visita <a href="AtencionCliente.php">Atención al Cliente</a> o llámanos al</p>
                <a href="tel:+51979798082">+51 979798082</a>
            </div>

            <div class="footer-social">
                <a href="https://wa.me/51979798082" class="social-icon whatsapp" aria-label="WhatsApp" target="_blank">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                    </svg>
                </a>
                <a href="https://facebook.com/marketprimavera" class="social-icon facebook" aria-label="Facebook" target="_blank">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                    </svg>
                </a>
                <a href="https://instagram.com/marketprimavera" class="social-icon instagram" aria-label="Instagram" target="_blank">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                    </svg>
                </a>
                <a href="https://tiktok.com/@marketprimavera" class="social-icon tiktok" aria-label="TikTok" target="_blank">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64 2.93 2.93 0 0 1 .88.13V9.4a6.84 6.84 0 0 0-1-.05A6.33 6.33 0 0 0 5 20.1a6.34 6.34 0 0 0 10.86-4.43v-7a8.16 8.16 0 0 0 4.77 1.52v-3.4a4.85 4.85 0 0 1-1-.1z"/>
                    </svg>
                </a>
            </div>

            <div class="footer-links">
                <a href="Politicas.php#terminos">Términos y condiciones</a>
                <a href="Politicas.php#envios">Envío y devoluciones</a>
                <a href="Politicas.php#privacidad">Políticas de privacidad</a>
            </div>

            <p class="footer-address">Urb. Los Sauces 6448 – Lambayeque, Perú</p>
            <p class="footer-copy">&copy; 2026 Market Primavera. Todos los derechos reservados.</p>
        </div>
    </footer>



<?php if (!estaLogueado()): ?>
<script>
document.addEventListener('DOMContentLoaded', function () {

    <?php if (!empty($loginError)): ?>
        const login = document.getElementById('authOverlay');
        if (login) login.classList.add('activo');
    <?php endif; ?>

    <?php if (!empty($registroError)): ?>
        const registro = document.getElementById('registroOverlay');
        if (registro) registro.classList.add('activo');
    <?php endif; ?>

});
</script>
<?php endif; ?>


<script src="<?= BASE_URL ?>/assets/js/auth.js?v=<?= time() ?>"></script>
<script>
    // Si venimos redirigidos desde checkout por no tener sesión, abrir el login automáticamente
    if (new URLSearchParams(window.location.search).get('login') === '1') {
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof abrirModalLogin === 'function') abrirModalLogin();
        });
    }

    <?php if (isset($_SESSION['login_success'])): ?>
        <?php unset($_SESSION['login_success']); ?>
        document.addEventListener('DOMContentLoaded', function () {
            // Un bonito timeout para que cargue la interfaz antes del alert o toast
            setTimeout(() => {
                Swal.fire({
                    toast: true,
                    position: 'bottom-end',
                    icon: 'success',
                    title: '¡Sesión iniciada! Bienvenido/a <?= addslashes($_SESSION['usuario_nombre'] ?? '') ?>',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    background: '#ffffff',
                    color: '#333333',
                    iconColor: '#27ae60',
                    customClass: {
                        popup: 'swal2-small-toast'
                    },
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                    }
                });
            }, 500);
        });
    <?php endif; ?>

    <?php if (!empty($_SESSION['google_pendiente'])): ?>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof abrirCompletarGoogle === 'function') abrirCompletarGoogle();
        });
    <?php endif; ?>
</script>
<script src="<?= BASE_URL ?>/assets/js/categoria.js?v=<?= time() ?>"></script>
<script src="<?= BASE_URL ?>/assets/js/perfil.js?v=<?= time() ?>"></script>
<script type="module" src="<?= BASE_URL ?>/assets/js/auth-firebase.js?v=<?= time() ?>"></script>
<script src="<?= BASE_URL ?>/assets/js/buscador.js?v=<?= time() ?>"></script>
<?php require __DIR__ . '/chatbot.php'; ?>

<script src="<?= BASE_URL ?>/assets/js/chatbot.js"></script>
</body>
</html>




