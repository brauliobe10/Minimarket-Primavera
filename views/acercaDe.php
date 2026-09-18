<?php
require_once __DIR__ . '/../controllers/AuthController.php';
requireClienteOrGuest();
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/Categoria.php';

use Config\Database;
use Models\Categoria;

$db = new Database();
$conexion = $db->conectar();

$categoriaModel = new Categoria($conexion);

$comida = $categoriaModel->obtenerPorGrupo('Comida');
$bebidas = $categoriaModel->obtenerPorGrupo('Bebidas');
$limpieza = $categoriaModel->obtenerPorGrupo('Limpieza del hogar');
$cuidado = $categoriaModel->obtenerPorGrupo('Cuidado personal');
?>

<!DOCTYPE html>
<html lang="es">
<head>
<script>window.BASE_URL = '<?= BASE_URL ?>';</script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acerca de - Market Primavera</title>
   <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/estilos.css" />
   <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/acercaDe.css">
   <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/auth.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/carrito.css">

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<body>

    <!-- Encabezado principal -->
    <header class="header" id="site-header">
        <div class="header-left">
                        <!-- Botón hamburguesa (móvil) -->
            <button class="hamburger-menu" aria-label="Abrir menú" aria-expanded="false" style="margin-right: 15px;">
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
            </button>
            <a href="<?= BASE_URL ?>/views/inicio.php" class="logo">
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
                id="buscadorInput" type="search" placeholder="Buscar productos, marcas y más..." aria-label="Buscar productos">
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
            <!-- Icono Mis Pedidos -->
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

        <span class="header-label">
            <?= htmlspecialchars($_SESSION['usuario_nombre']) ?>
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
            <!-- Icono Ubicación -->
            <a href="acercaDe.php#location-section" class="header-item">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon-svg">
                     <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                     <circle cx="12" cy="10" r="3"></circle>
                </svg>
                <span class="header-label">Ubicación</span>
            </a>

            <!-- Icono Carrito -->
<button class="header-item" id="cart-icon" type="button" aria-label="Ver carrito de compras">
    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon-svg">
        <circle cx="8" cy="21" r="1"></circle>
        <circle cx="19" cy="21" r="1"></circle>
        <path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"></path>
    </svg>
    <span class="header-label">Carrito <small class="cart-count">0</small></span>
</button>

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
        <li><a href="acercaDe.php" class="active">Acerca de</a></li>
        <li><a href="AtencionCliente.php">Atención al cliente</a></li>

    </ul>
</nav>
<!-- MENÚ HAMBURGUESA (móvil) -->
<nav class="menu-hamburguesa" aria-hidden="true" aria-label="Menú móvil">
    <button class="close-menu" aria-label="Cerrar menú">✕</button>

    <ul>
        <li><a href="inicio.php">Inicio</a></li>
        <li><a href="inicio.php#ofertas">Ofertas</a></li>

        <!-- COMIDA -->
        <li class="accordion">
            <button class="accordion-toggle">Comida</button>
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

        <!-- BEBIDAS -->
        <li class="accordion">
            <button class="accordion-toggle">Bebidas</button>
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

        <!-- LIMPIEZA -->
        <li class="accordion">
            <button class="accordion-toggle">Limpieza del hogar</button>
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

        <!-- CUIDADO PERSONAL -->
        <li class="accordion">
            <button class="accordion-toggle">Cuidado personal</button>
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
        <!-- Sección Nuestra Historia -->
        <section class="nosotros">
            <div class="nosotros-container">
                <div class="nosotros-imagen">
                    <div class="imagen-frame">
                        <img src="<?= BASE_URL ?>/img/NuestraHistoria.jpg" alt="Market Primavera">
                        <div class="imagen-overlay"></div>
                    </div>
                </div>
                <div class="nosotros-contenido">
                    <h2>Nuestra Historia</h2>
                    <p>En Market Primavera, comenzamos con una misión simple pero poderosa: <strong>llevar productos frescos y de calidad a las puertas de cada familia de nuestro país.</strong></p>
                    <p>Fundada en 2015, nuestra empresa ha crecido desde un pequeño mercado local hasta convertirse en un referente de confianza en el comercio minorista. Cada día, miles de clientes confían en nosotros para sus necesidades diarias.</p>
                    <p>No solo vendemos productos, <strong>construimos relaciones</strong> con nuestros clientes basadas en la honestidad, la calidad y el servicio excepcional.</p>
                </div>
            </div>
        </section>

        <!-- Valores y Misión -->
        <section class="valores">
            <div class="valores-header">
                <h2>Nuestros Valores y Misión</h2>
                <p>Principios que guían cada decisión que tomamos</p>
            </div>
            
            <div class="valores-grid">
                <!-- Valor 1 -->
                <div class="valor-card">
                    <div class="valor-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                            <path d="M9 12l2 2 4-4"></path>
                        </svg>
                    </div>
                    <h3>Calidad Garantizada</h3>
                    <p>Seleccionamos cuidadosamente cada producto para asegurar que llegues lo mejor a tu mesa.</p>
                </div>

                <!-- Valor 2 -->
                <div class="valor-card">
                    <div class="valor-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                    </div>
                    <h3>Compromiso Social</h3>
                    <p>Apoyamos a comunidades locales y promotores sostenibles de nuestro país.</p>
                </div>

                <!-- Valor 3 -->
                <div class="valor-card">
                    <div class="valor-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <path d="M8 14s1.5 2 4 2 4-2 4-2"></path>
                            <line x1="9" y1="9" x2="9.01" y2="9"></line>
                            <line x1="15" y1="9" x2="15.01" y2="9"></line>
                        </svg>
                    </div>
                    <h3>Satisfacción del Cliente</h3>
                    <p>Tu sonrisa es nuestro éxito. Trabajamos para que cada compra sea una experiencia positiva.</p>
                </div>

                <!-- Valor 4 -->
                <div class="valor-card">
                    <div class="valor-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 2v20M2 12h20"></path>
                            <circle cx="12" cy="12" r="10"></circle>
                            <circle cx="12" cy="12" r="3"></circle>
                        </svg>
                    </div>
                    <h3>Sostenibilidad</h3>
                    <p>Cuidamos nuestro planeta implementando prácticas ecológicas en nuestras operaciones.</p>
                </div>

                <!-- Valor 5 -->
                <div class="valor-card">
                    <div class="valor-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                            <circle cx="9" cy="10" r="1"></circle>
                            <circle cx="12" cy="10" r="1"></circle>
                            <circle cx="15" cy="10" r="1"></circle>
                        </svg>
                    </div>
                    <h3>Comunicación Abierta</h3>
                    <p>Escuchamos a nuestros clientes y estamos siempre disponibles para ayudarte.</p>
                </div>

                <!-- Valor 6 -->
                <div class="valor-card">
                    <div class="valor-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                        </svg>
                    </div>
                    <h3>Atención 24/7</h3>
                    <p>Nuestro equipo está disponible para resolver tus dudas en cualquier momento.</p>
                </div>
            </div>
        </section>

        <!-- Estadísticas -->
        <section class="estadisticas">
            <h2>Nuestro Impacto</h2>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number" data-target="1000">0</div>
                    <p class="stat-label">Clientes Satisfechos</p>
                </div>
                <div class="stat-card">
                    <div class="stat-number" data-target="500">0</div>
                    <p class="stat-label">Productos Ofrecidos</p>
                </div>
                <div class="stat-card">
                    <div class="stat-number" data-target="20">0</div>
                    <p class="stat-label">Proveedores Locales</p>
                </div>
                <div class="stat-card">
                    <div class="stat-number" data-target="365">0</div>
                    <p class="stat-label">Días de Servicio al Año</p>
                </div>
            </div>
        </section>

        <section class="equipo">
            <h2>Nuestro Equipo</h2>
            <p class="equipo-subtitle">Profesionales dedicados a tu servicio</p>
            
           <div class="equipo-grid">

    <!-- Miembro 1 -->
    <div class="miembro-card">
        <div class="miembro-img">
            <img src="<?= BASE_URL ?>/img/persona1.jpg" alt="Carlos Mendoza" class="miembro-photo">
        </div>
        <h3>Carlos Mendoza</h3>
        <p class="cargo">Gerente General</p>
        <p class="descripcion">
            Más de 15 años en el sector retail, apasionado por la excelencia en el servicio.
        </p>
    </div>

    <!-- Miembro 2 -->
    <div class="miembro-card">
        <div class="miembro-img">
            <img src="<?= BASE_URL ?>/img/persona2.jpg" alt="María Sánchez" class="miembro-photo">
        </div>
        <h3>María Sánchez</h3>
        <p class="cargo">Directora de Operaciones</p>
        <p class="descripcion">
            Especialista en logística y gestión de inventarios con eficiencia garantizada.
        </p>
    </div>

    <!-- Miembro 3 -->
    <div class="miembro-card">
        <div class="miembro-img">
            <img src="<?= BASE_URL ?>/img/persona3.jpg" alt="Patricia Gómez" class="miembro-photo">
        </div>
        <h3>Patricia Gómez</h3>
        <p class="cargo">Atención al Cliente</p>
        <p class="descripcion">
            Dedicada a resolver cualquier inquietud y garantizar tu satisfacción total.
        </p>
    </div>

</div>
</section>
       <!--Nuestra Ubicacion-->
        <section class="location-section" id="location-section">
            <div class="container">
                <div class="location-header">
                    <h2>Nuestra Ubicación</h2>
                    <p>Visítanos en nuestra tienda física o encuentra nuestras zonas de entrega</p>
                </div>
                
                <div class="location-content">
                    <div class="location-info">
                        <div class="info-card">
                            <div class="info-icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div class="info-text">
                                <h3>Dirección</h3>
                                <p>Urb. Los Sauces 6448<br>Pimentel, Perú</p>
                            </div>
                        </div>
                        
                        <div class="info-card">
                            <div class="info-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="info-text">
                                <h3>Horarios</h3>
                                <p>Lunes a Sábado<br>9:00 AM - 10:30 PM</p>
                                <p>Domingos<br>10:00 AM - 8:00 PM</p>
                            </div>
                        </div>
                        
                        <div class="info-card">
                            <div class="info-icon">
                                <i class="fas fa-truck"></i>
                            </div>
                            <div class="info-text">
                                <h3>Entrega a Domicilio</h3>
                                <p>Cobertura en todo Lambayeque<br>Tiempo estimado: 30-60 min</p>
                            </div>
                        </div>
                        
                        <div class="info-card">
                            <div class="info-icon">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <div class="info-text">
                                <h3>Protocolos de Seguridad</h3>
                                <p>Cumplimos con todos los protocolos de bioseguridad vigentes</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- MAPA COMPLETO: iframe + botón para abrir Google Maps -->
<div class="map-container">

  <!-- IFRAME DEL MAPA -->
  <div id="map-placeholder" class="map-placeholder">
    <iframe 
      id="my-map-iframe"
      src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3920.40231288904!2d-79.8873733!3d-6.7938281!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x904cefa17b6684f5%3A0xd9aff3c0976700a6!2sMarket%20Primavera!5e0!3m2!1ses!2spe!4v1733260000000!5m2!1ses!2spe"
      width="100%" 
      height="400" 
      style="border:0;" 
      allowfullscreen 
      loading="lazy"
      referrerpolicy="no-referrer-when-downgrade"
      title="Mapa">
    </iframe>
  </div>

  <!-- BOTÓN (sin estilos, tú lo das en tu archivo CSS) -->
  <div class="map-controls">
    <a id="open-map-link"
       href="https://www.google.com/maps/place/Market+Primavera/@-6.7936576,-79.885646,17z/data=!4m6!3m5!1s0x904cefa17b6684f5:0xd9aff3c0976700a6!8m2!3d-6.7938281!4d-79.8847984!16s%2Fg%2F11kj_5gx1n?entry=ttu"
       target="_blank"
       rel="noopener noreferrer">
       Abrir en Google Maps
    </a>
  </div>

</div>
</section>


<?php require __DIR__ . '/carrito.php'; ?>

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
                <a href="<?= BASE_URL ?>/views/politicas.php#terminos">Términos y condiciones</a>
                <a href="<?= BASE_URL ?>/views/politicas.php#envios">Envío y devoluciones</a>
                <a href="<?= BASE_URL ?>/views/politicas.php#privacidad">Políticas de privacidad</a>
            </div>

            <p class="footer-address">Urb. Los Sauces 6448 – Lambayeque, Perú</p>
            <p class="footer-copy">&copy; 2026 Market Primavera. Todos los derechos reservados.</p>
        </div>

    </footer>

<?php require __DIR__ . '/login.php'; ?>
<?php require __DIR__ . '/registro.php'; ?>

<script src="<?= BASE_URL ?>/assets/js/categoria.js?v=999"></script>
<script src="<?= BASE_URL ?>/assets/js/acercaDe.js?v=999"></script>
<script src="<?= BASE_URL ?>/assets/js/auth.js?v=999"></script>
<script type="module" src="<?= BASE_URL ?>/assets/js/auth-firebase.js"></script>
<script src="<?= BASE_URL ?>/assets/js/buscador.js?v=1"></script>

</body>
</html>





