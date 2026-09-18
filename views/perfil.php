
<?php
require_once __DIR__ . '/../controllers/PerfilController.php';
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/CategoriaController.php';
require_once __DIR__ . '/../config/Security.php';

\Config\Security::setSecurityHeaders();

sincronizarRoles();
requireClienteOrGuest();
?>
<html lang="es">
<head>
<script>window.BASE_URL = '<?= BASE_URL ?>';</script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - Market Primavera</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/estilos.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/perfil.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/auth.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/carrito.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/chatbot.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
        <button class="close-menu" aria-label="Cerrar menú">✕</button>
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
            <li><a href="perfil.php#pedidos" id="misPedidosHamburguesa" class="highlight">Mis pedidos</a></li>
            <li><a href="acercaDe.php" class="highlight">Acerca de</a></li>
            <li><a href="AtencionCliente.php" class="highlight">Atención al cliente</a></li>
        </ul>
    </nav>










    <!-- Contenedor principal del perfil -->
    <div class="perfil-container">
        <!-- Sidebar -->
        <aside class="perfil-sidebar">
         <div id="sidebarAvatar" class="perfil-avatar-grande">
    <?= strtoupper(substr($usuario['nombres'] ?? 'U', 0, 1)) ?>
</div>

<div id="sidebarNombre" class="perfil-nombre-usuario">
    <?= htmlspecialchars($usuario['nombres']) ?>
</div>

<div id="sidebarEmail" class="perfil-email-usuario">
    <?= htmlspecialchars($usuario['correo']) ?>
</div>

            <nav>
    <ul class="perfil-menu">
        <li class="perfil-menu-item">
            <a href="#" class="perfil-menu-link active" data-seccion="perfil">
                <i class="fas fa-user"></i>
                <span>Perfil</span>
            </a>
        </li>
        
        <li class="perfil-menu-item">
            <a href="#" class="perfil-menu-link" data-seccion="direcciones">
                <i class="fas fa-map-marker-alt"></i>
                <span>Direcciones</span>
            </a>
        </li>
        <li class="perfil-menu-item">
            <a href="#" class="perfil-menu-link" data-seccion="gestion">
                <i class="fas fa-cog"></i>
                <span>Gestión de la cuenta</span>
            </a>
        </li>
        <li class="perfil-menu-item">
            <a href="#" class="perfil-menu-link" data-seccion="pedidos">
                <i class="fas fa-shopping-bag"></i>
                <span>Pedidos</span>
            </a>
        </li>
        <li class="perfil-menu-item">
            <a href="#" class="perfil-menu-link" data-seccion="mensajes">
                <i class="fas fa-envelope"></i>
                <span>Mis Mensajes</span>
            </a>
        </li>
        <li class="perfil-menu-item">
            <a href="<?= BASE_URL ?>/controllers/AuthController.php?logout=1"
   class="perfil-menu-link">
                <i class="fas fa-sign-out-alt"></i>
                <span>Cerrar sesión</span>
            </a>
        </li>
    </ul>
</nav>
        </aside>

        <!-- Contenido principal -->
        <main class="perfil-contenido">
    <div class="perfil-header">
        <button class="btn-volver" onclick="volverPaginaAnterior()">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="15 18 9 12 15 6"></polyline>
            </svg>
            Volver
        </button>
        <div>
            <h1 id="mainTitle">Perfil</h1>
            <p class="perfil-header-subtitle" id="mainSubtitle">Actualiza tus datos de sesión, correo electrónico y contraseña</p>
        </div>
    </div>




<div id="seccionMensajes" class="perfil-seccion-contenido" style="display: none;">
    <section class="perfil-seccion">
        <h2>Buzón de Atención al Cliente</h2>
        <div style="margin-top: 15px; display: flex; gap: 10px; flex-wrap: wrap; align-items: center;">
            <input type="text" id="buscadorMensajesCliente" placeholder="Buscar por asunto, contenido..." oninput="filtrarMensajesCliente()" style="flex: 1; min-width: 200px; padding: 10px 15px; border-radius: 8px; border: 1px solid #cbd5e1; outline: none;">
            
            <input type="date" id="filtroFechaCliente" onchange="filtrarMensajesCliente()" style="padding: 10px; border-radius: 8px; border: 1px solid #cbd5e1; outline: none; color: #475569;" title="Filtrar por fecha">
            
            <select id="filtroMotivoCliente" onchange="filtrarMensajesCliente()" style="padding: 10px; border-radius: 8px; border: 1px solid #cbd5e1; outline: none; color: #475569;">
                <option value="">Todos los motivos</option>
            </select>
            
            <button type="button" onclick="limpiarFiltrosClienteSoporte()" style="background: #475569; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; color: white; display: flex; align-items: center; gap: 8px; font-weight: bold; box-shadow: 0 2px 4px rgba(0,0,0,0.1); transition: transform 0.2s, background 0.2s;" onmouseover="this.style.background='#334155'; this.style.transform='translateY(-2px)';" onmouseout="this.style.background='#475569'; this.style.transform='translateY(0)';">
                <i class="fa fa-eraser"></i> Limpiar
            </button>
        </div>
        <div id="listaMensajesSoporte" style="margin-top: 20px;">
            <p>Cargando tus mensajes...</p>
        </div>
    </section>
</div>

<!-- Modal Cancelar Pedido -->
<div class="adm-modal-bg" id="modalCancelarPedido" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; justify-content: center; align-items: center;">
    <div class="adm-modal" style="background: #fff; width: 90%; max-width: 450px; padding: 25px; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.2);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
            <h3 style="margin: 0; color: #1e293b; display: flex; align-items: center; gap: 8px;">
                <i class="fa fa-exclamation-triangle" style="color: #e30613;"></i> Cancelar Pedido
            </h3>
            <button onclick="cerrarModalCancelar()" style="background: transparent; border: none; font-size: 20px; cursor: pointer; color: #64748b;">✕</button>
        </div>
        <p style="color: #475569; margin-bottom: 20px;">¿Estás seguro de que deseas cancelar este pedido? Esta acción no se puede deshacer.</p>
        
        <div style="margin-bottom: 20px;">
            <label style="display: block; font-weight: 600; color: #1e293b; margin-bottom: 8px;">Motivo de cancelación (Opcional)</label>
            <textarea id="motivoCancelacionText" rows="3" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #cbd5e1; outline: none; resize: vertical; font-family: inherit;" placeholder="Cuéntanos por qué deseas cancelar el pedido..."></textarea>
        </div>
        
        <div style="display: flex; justify-content: flex-end; gap: 10px;">
            <button onclick="cerrarModalCancelar()" style="padding: 10px 15px; border-radius: 8px; border: 1px solid #cbd5e1; background: #f8fafc; color: #475569; cursor: pointer; font-weight: 600;">Volver</button>
            <button id="btnConfirmarCancelacion" style="padding: 10px 15px; border-radius: 8px; border: none; background: #e30613; color: white; cursor: pointer; font-weight: 600; display: flex; align-items: center; gap: 5px;">
                Confirmar Cancelación
            </button>
        </div>
    </div>
</div>

<!-- Modal Chat Cliente -->
<div class="adm-modal-bg" id="modalChatCliente" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; justify-content: center; align-items: center;">
    <div class="adm-modal" style="background: #fff; width: 90%; max-width: 600px; padding: 0; display: flex; flex-direction: column; height: 80vh; border-radius: 8px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.2);">
        <div class="adm-modal-head" style="padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; background: linear-gradient(135deg, #e30613, #b9000b); border-radius: 8px 8px 0 0; color: white;">
            <div>
                <h3 style="margin: 0; color: white; display: flex; align-items: center; gap: 8px;"><i class="fa fa-headset"></i> Chat de Soporte</h3>
                <small id="chatClienteAsunto" style="color: #f1f5f9; opacity: 0.9;">Asunto</small>
            </div>
            <button class="adm-modal-close" onclick="cerrarModalChatCliente()" style="background: rgba(255,255,255,0.2); border: none; font-size: 20px; cursor: pointer; color: white; width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">✕</button>
        </div>
        
        <div id="chatClienteHistorial" style="flex: 1; overflow-y: auto; padding: 20px; background: #f1f5f9; display: flex; flex-direction: column; gap: 15px;">
            <div style="text-align: center; color: #94a3b8;">Cargando mensajes...</div>
        </div>

        <form id="formChatCliente" style="padding: 15px 20px; border-top: 1px solid #e2e8f0; background: #fff;">
            <input type="hidden" id="chatClienteId">
            <div style="display: flex; gap: 10px;">
                <textarea id="chatClienteRespuesta" rows="2" required placeholder="Escribe tu mensaje..." style="flex: 1; resize: none; border-radius: 20px; padding: 10px 15px; border: 1px solid #cbd5e1; outline: none;"></textarea>
                <button type="submit" id="btnChatClienteResponder" style="background: #e30613; color: white; border: none; border-radius: 50%; width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; cursor: pointer; box-shadow: 0 2px 5px rgba(227,6,19,0.3);">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M15.854.146a.5.5 0 0 1 .11.54l-5.819 14.547a.75.75 0 0 1-1.329.124l-3.178-4.995L.643 7.184a.75.75 0 0 1 .124-1.33L15.314.037a.5.5 0 0 1 .54.11ZM6.636 10.07l2.761 4.338L14.13 2.576 6.636 10.07Zm6.787-8.201L1.591 6.602l4.339 2.76 7.494-7.493Z"/>
                    </svg>
                </button>
            </div>
        </form>
    </div>
</div>

<div id="seccionPerfil" class="perfil-seccion-contenido">

<?php
$direccionPrincipal = $direcciones[0]['direccion'] ?? '';
?>

<form id="perfilForm"
      method="POST"
      action="<?= BASE_URL ?>/controllers/PerfilController.php">

    <input type="hidden" name="accion" value="actualizar">

    <section class="perfil-seccion">
        <h2>Datos personales</h2>


     <?php if (empty($usuario['telefono']) || empty($direccionPrincipal) || empty($usuario['dni']) || (esRepartidor() && empty($placaVehiculo))): ?>
    <div class="perfil-alert" id="perfilAlerta">
        ⚠️ Completa tu
        <?= empty($usuario['dni']) ? '<strong>DNI</strong>, ' : '' ?>
        <?= empty($usuario['telefono']) ? '<strong>teléfono</strong>' : '' ?>
        <?= empty($usuario['telefono']) && empty($direccionPrincipal) ? ' y ' : '' ?>
        <?= empty($direccionPrincipal) ? '<strong>dirección</strong>' : '' ?>
        <?= (esRepartidor() && empty($placaVehiculo)) ? ((!empty($usuario['telefono']) || !empty($direccionPrincipal)) ? ' y ' : '') . '<strong>placa del vehículo</strong>' : '' ?>
        para poder operar correctamente.
    </div>
<?php endif; ?>

        <div class="perfil-grid">

            <div class="perfil-campo">
                <label>Nombre</label>
                <input type="text"
                       name="nombre"
                       value="<?= htmlspecialchars($usuario['nombres']) ?>"
                       <?= !empty(trim($usuario['nombres'])) ? 'data-locked="true"' : '' ?>
                       readonly
                       required>
            </div>

            <div class="perfil-campo">
                <label>Apellido</label>
                <input type="text"
                       name="apellido"
                       value="<?= htmlspecialchars($usuario['apellidos']) ?>"
                       <?= !empty(trim($usuario['apellidos'])) ? 'data-locked="true"' : '' ?>
                       readonly
                       required>
            </div>
<div class="perfil-campo">
    <label>DNI</label>
    <div style="display:flex; gap:10px; align-items:center;">
        <input type="text"
               name="dni"
               id="perfilDni"
               value="<?= htmlspecialchars($usuario['dni'] ?? '') ?>"
               maxlength="8"
               inputmode="numeric"
               pattern="\d{8}"
               oninput="this.value=this.value.replace(/\D/g,'').slice(0,8)"
               <?= !empty($usuario['dni']) ? 'data-locked="true"' : '' ?>
               readonly
               placeholder="Ej: 76345180"
               <?= empty($usuario['dni']) ? 'required' : '' ?>
               style="flex: 1;">
        
        <?php if (empty($usuario['dni'])): ?>
            <button type="button" class="btn-perfil" id="btnEditarDni" onclick="document.getElementById('perfilDni').readOnly = false; document.getElementById('perfilDni').focus(); this.style.display='none'; document.getElementById('perfilDni').value='';" style="padding: 12px 15px; margin: 0; background: #6c757d; display: none;">Editar</button>
        <?php endif; ?>
    </div>

    <?php if (empty($usuario['dni'])): ?>
        <span class="perfil-campo-info" style="color:#e74c3c;">
            Debes completar tu DNI para poder realizar pedidos.
        </span>
    <?php endif; ?>
</div>
            <div class="perfil-campo">
                <label>Email</label>
                <input type="email"
                       value="<?= htmlspecialchars($usuario['correo']) ?>"
                       disabled>
            </div>

            <div class="perfil-campo">
                <label>Teléfono</label>
                <input type="text"
                       id="perfilTelefono"
                       name="telefono"
                       maxlength="9"
                       inputmode="numeric"
                       oninput="this.value=this.value.replace(/\D/g,'').slice(0,9)"
                       value="<?= htmlspecialchars($usuario['telefono'] ?? '') ?>"
                       readonly>
            </div>

            <?php if (esRepartidor()): ?>
            <div class="perfil-campo">
                <label>Placa de Vehículo</label>
                <input type="text"
                       id="perfilPlaca"
                       name="placa"
                       maxlength="10"
                       style="text-transform: uppercase;"
                       value="<?= htmlspecialchars($placaVehiculo ?? '') ?>"
                       <?= empty($placaVehiculo) ? 'required' : '' ?>
                       readonly>
            </div>
            <?php endif; ?>

            <div class="perfil-campo perfil-campo-full">
                <label>Dirección</label>
                <input type="text"
                       id="perfilDireccion"
                       name="direccion"
                       value="<?= htmlspecialchars($direccionPrincipal ?? '') ?>"
                       readonly>
            </div>

        </div>
    </section>

    <div class="perfil-acciones">
        <button type="button" id="btnEditarPerfil" class="btn-perfil btn-editar" onclick="activarEdicionPerfil()">
            <i class="fa fa-pencil-alt"></i> Editar datos
        </button>

        <button type="submit" id="btnGuardarPerfil" class="btn-perfil btn-guardar" style="display:none;">
            Guardar cambios
        </button>

        <button type="button"
                id="btnCancelarPerfil"
                class="btn-perfil btn-cancelar"
                onclick="cancelarEdicionPerfil()"
                style="display:none;">
            Cancelar
        </button>
    </div>

<script>
    const originalValues = {
        telefono: document.getElementById('perfilTelefono').value,
        direccion: document.getElementById('perfilDireccion').value,
        nombre: document.querySelector('input[name="nombre"]').value,
        apellido: document.querySelector('input[name="apellido"]').value,
        dni: document.getElementById('perfilDni').value,
        placa: document.getElementById('perfilPlaca') ? document.getElementById('perfilPlaca').value : ''
    };

    function activarEdicionPerfil() {
        document.getElementById('btnEditarPerfil').style.display = 'none';
        document.getElementById('btnGuardarPerfil').style.display = 'inline-block';
        document.getElementById('btnCancelarPerfil').style.display = 'inline-block';
        
        const inputs = [
            document.getElementById('perfilTelefono'),
            document.getElementById('perfilDireccion'),
            document.querySelector('input[name="nombre"]'),
            document.querySelector('input[name="apellido"]'),
            document.getElementById('perfilDni'),
            document.getElementById('perfilPlaca')
        ];

        inputs.forEach(input => {
            if (input && input.getAttribute('data-locked') !== 'true') {
                input.removeAttribute('readonly');
            }
        });
        
        // Poner foco en el primer campo editable (teléfono o nombre)
        document.getElementById('perfilTelefono').focus();
    }
    
    function cancelarEdicionPerfil() {
        document.getElementById('perfilTelefono').value = originalValues.telefono;
        document.getElementById('perfilDireccion').value = originalValues.direccion;
        document.querySelector('input[name="nombre"]').value = originalValues.nombre;
        document.querySelector('input[name="apellido"]').value = originalValues.apellido;
        document.getElementById('perfilDni').value = originalValues.dni;
        if (document.getElementById('perfilPlaca')) document.getElementById('perfilPlaca').value = originalValues.placa;
        
        const inputs = [
            document.getElementById('perfilTelefono'),
            document.getElementById('perfilDireccion'),
            document.querySelector('input[name="nombre"]'),
            document.querySelector('input[name="apellido"]'),
            document.getElementById('perfilDni'),
            document.getElementById('perfilPlaca')
        ];

        inputs.forEach(input => {
            if (input) input.setAttribute('readonly', 'true');
        });

        document.getElementById('btnEditarPerfil').style.display = 'inline-block';
        document.getElementById('btnGuardarPerfil').style.display = 'none';
        document.getElementById('btnCancelarPerfil').style.display = 'none';
    }

    // Si redirigieron desde el panel por falta de placa, auto-activar edición y hacer scroll
    if (window.location.hash === '#placa') {
        setTimeout(() => {
            activarEdicionPerfil();
            const placa = document.getElementById('perfilPlaca');
            if (placa) {
                placa.scrollIntoView({ behavior: 'smooth', block: 'center' });
                placa.focus();
                placa.style.borderColor = '#e30613';
                placa.style.boxShadow = '0 0 0 3px rgba(227,6,19,.15)';
                document.getElementById('perfilAlerta')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }, 300);
    }
</script>

</form>
</div>
<!-- SECCIÓN DIRECCIONES -->
<div id="seccionDirecciones" class="perfil-seccion-contenido" style="display:none;">

    <section class="perfil-seccion">

        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
            <h2>Mis direcciones</h2>

            <button type="button"
                    class="btn-perfil btn-guardar"
                    onclick="mostrarFormularioDireccion()">
                <i class="fas fa-plus"></i>
                Agregar dirección
            </button>
        </div>

        <!-- Formulario nueva dirección -->
        <div id="formularioDireccion" style="display:none;">




<form id="formNuevaDireccion"
      method="POST"
      action="<?= BASE_URL ?>/controllers/PerfilController.php">

    <input type="hidden" name="accion" id="accionDireccion" value="guardarDireccion">
    <input type="hidden" name="id_direccion" id="dirId" value="">

    <div class="perfil-grid full-width">

        <!-- Etiqueta -->
        <div class="perfil-campo">
            <label>Etiqueta *</label>
            <input type="text"
                   id="dirEtiqueta"
                   name="etiqueta"
                   placeholder="Casa, Trabajo, etc."
                   required>
        </div>

        <!-- Distrito -->
        <div class="perfil-campo">
            <label>Distrito *</label>
            <select id="dirDistrito" name="distrito" required style="width:100%;padding:10px;border:1px solid #ccc;border-radius:4px;font-family:inherit;">
                <option value="Chiclayo">Chiclayo</option>
                <option value="José Leonardo Ortiz">José Leonardo Ortiz</option>
                <option value="La Victoria">La Victoria</option>
                <option value="Pimentel">Pimentel</option>
            </select>
        </div>

        <!-- Dirección -->
        <div class="perfil-campo">
            <label>Dirección completa *</label>
            <input type="text"
                   id="dirCompleta"
                   name="direccion"
                   placeholder="Av. Principal 123, Urb. Los Sauces"
                   required>
        </div>

        <!-- Referencia -->
        <div class="perfil-campo">
            <label>Referencia</label>
            <input type="text"
                   id="dirReferencia"
                   name="referencia"
                   placeholder="Frente al parque, casa verde">
        </div>

    </div>

    <!-- Predeterminada -->
    <div class="perfil-checkbox">
        <input type="checkbox"
               id="dirPredeterminada"
               name="predeterminada">

        <label for="dirPredeterminada">
            Establecer como dirección predeterminada
        </label>
    </div>

    <!-- Botones -->
    <div class="perfil-acciones">

        <button type="submit" class="btn-perfil btn-guardar">
            <i class="fas fa-save"></i>
            Guardar dirección
        </button>

        <button type="button"
                class="btn-perfil btn-cancelar"
                onclick="ocultarFormularioDireccion()">
            <i class="fas fa-times"></i>
            Cancelar
        </button>

    </div>

</form>
        </div>

    
        <!-- Lista -->
<div id="listaDirecciones">

    <?php if (empty($direcciones)): ?>

        <div class="direcciones-empty" id="direccionesEmpty">
            <i class="fas fa-map-marker-alt"
               style="font-size:3rem; color:#ddd; margin-bottom:15px;"></i>

            <p>No tienes direcciones guardadas</p>

            <p style="font-size:0.9rem; color:#999;">
                Agrega una dirección para tus entregas
            </p>
        </div>

    <?php else: ?>

        <?php foreach ($direcciones as $d): ?>
            <div class="direccion-card <?= $d['predeterminada'] ? 'predeterminada' : '' ?>">

                <div class="direccion-header">

                    <!-- Etiqueta -->
                    <div class="direccion-etiqueta">
                        <i class="fas fa-map-marker-alt"></i>
                        <?= htmlspecialchars($d['etiqueta']) ?>

                        <?php if ($d['predeterminada']): ?>
                            <span class="badge-predeterminada">
                                Predeterminada
                            </span>
                        <?php endif; ?>
                    </div>

                    <!-- Acciones -->
                    <div class="direccion-acciones">

                        <!-- Editar -->
                        <button type="button"
                                class="btn-icon editar"
                                title="Editar"
                                onclick="editarDireccion({
                                    id_direccion: <?= (int)$d['id_direccion'] ?>,
                                    etiqueta: '<?= htmlspecialchars($d['etiqueta'], ENT_QUOTES) ?>',
                                    direccion: '<?= htmlspecialchars($d['direccion'], ENT_QUOTES) ?>',
                                    referencia: '<?= htmlspecialchars($d['referencia'] ?? '', ENT_QUOTES) ?>',
                                    predeterminada: <?= (int)$d['predeterminada'] ?>
                                })">
                            <i class="fas fa-pen"></i>
                        </button>

                        <!-- Eliminar -->
                        <form method="POST"
                              action="<?= BASE_URL ?>/controllers/PerfilController.php"
                              onsubmit="return confirm('¿Eliminar esta dirección?')"
                              style="display:inline;">

                            <input type="hidden"
                                   name="accion"
                                   value="eliminarDireccion">

                            <input type="hidden"
                                   name="id_direccion"
                                   value="<?= (int)$d['id_direccion'] ?>">

                            <button type="submit"
                                    class="btn-icon eliminar"
                                    title="Eliminar">
                                <i class="fas fa-trash"></i>
                            </button>

                        </form>

                    </div>

                </div>

                <!-- Información -->
                <div class="direccion-info">

                    <p>
                        <strong><?= htmlspecialchars($d['direccion']) ?></strong>
                    </p>

                    <p>
                        <?= htmlspecialchars($d['distrito']) ?>,
                        <?= htmlspecialchars($d['provincia']) ?>,
                        <?= htmlspecialchars($d['departamento']) ?>
                    </p>

                    <?php if (!empty($d['referencia'])): ?>
                        <p>
                            Ref: <?= htmlspecialchars($d['referencia']) ?>
                        </p>
                    <?php endif; ?>

                </div>

            </div>
        <?php endforeach; ?>

    <?php endif; ?>

</div>

</section>
</div>



<!-- SECCIÓN GESTIÓN DE CUENTA -->
<div id="seccionGestion" class="perfil-seccion-contenido" style="display:none;">
    
    <section class="perfil-seccion">

        <?php $tienePassword = !empty($usuario['password_hash']); ?>
        <h2><?= $tienePassword ? 'Cambiar contraseña' : 'Establecer contraseña' ?></h2>
        <p style="color: #666; margin-bottom: 20px;">
            <?= $tienePassword ? 'Actualiza tu contraseña para mantener tu cuenta segura' : 'Crea una contraseña para poder iniciar sesión con tu correo' ?>
        </p>

        <form id="formCambiarPassword"
              method="POST"
              action="<?= BASE_URL ?>/controllers/PerfilController.php">

            <input type="hidden" name="accion" value="cambiarPassword">

            <div class="perfil-grid">

                <?php if ($tienePassword): ?>
                <!-- Contraseña actual -->
                <div class="perfil-campo">
                    <label>Contraseña actual *</label>

                    <div class="password-input-group">
                        <input type="password"
                               id="passwordActual"
                               name="password_actual"
                               placeholder="••••••••"
                               required>

                        <button type="button"
                                class="toggle-password"
                                onclick="togglePasswordVisibility('passwordActual')">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="perfil-campo"></div>
                <?php endif; ?>

                <!-- Nueva contraseña -->
                <div class="perfil-campo">
                    <label>Nueva contraseña *</label>

                    <div class="password-input-group">
                        <input type="password"
                               id="passwordNueva"
                               name="password_nueva"
                               placeholder="••••••••"
                               required
                               minlength="6">

                        <button type="button"
                                class="toggle-password"
                                onclick="togglePasswordVisibility('passwordNueva')">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>

                    <span class="perfil-campo-info">
                        Mínimo 6 caracteres
                    </span>
                </div>

                <!-- Confirmar contraseña -->
                <div class="perfil-campo">
                    <label>Confirmar nueva contraseña *</label>

                    <div class="password-input-group">
                        <input type="password"
                               id="passwordConfirmar"
                               name="password_confirmar"
                               placeholder="••••••••"
                               required
                               minlength="6">

                        <button type="button"
                                class="toggle-password"
                                onclick="togglePasswordVisibility('passwordConfirmar')">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

            </div>
<!-- Botones -->
<div class="perfil-acciones">

    <button type="submit" class="btn-perfil btn-guardar">
        <i class="fas fa-key"></i>
        <?= $tienePassword ? 'Cambiar contraseña' : 'Establecer contraseña' ?>
    </button>

<button type="button"
        class="btn-perfil btn-cancelar"
        onclick="
            document.getElementById('formCambiarPassword').reset();
            cambiarSeccion('perfil');
        ">
    <i class="fas fa-times"></i>
    Cancelar
</button>

</div>

</form>
        <!-- Info -->
        <?php if (!$tienePassword): ?>
        <div class="alerta-info" style="margin-top: 30px;">
            <i class="fas fa-info-circle"></i>
            <div>
                <strong>Nota importante:</strong>
                <p>
                    Al establecer una contraseña, podrás iniciar sesión con tu correo (<?= htmlspecialchars($usuario['correo']) ?>) y esta contraseña, además de seguir usando Google.
                </p>
            </div>
        </div>
        <?php else: ?>
        <div class="alerta-info" style="margin-top: 30px;">
            <i class="fas fa-info-circle"></i>

            <div>
                <strong>Nota importante:</strong>
                <p>
                    Si iniciaste sesión con Google, no puedes cambiar la contraseña aquí.
                    Debes hacerlo desde tu cuenta de Google.
                </p>
            </div>
        </div>
        <?php endif; ?>

    </section>

</div>
    <!-- SECCIÓN PEDIDOS -->
<div id="seccionPedidos" class="perfil-seccion-contenido" style="display:none;">
    <section class="perfil-seccion">
        <h2>Mis pedidos</h2>
        <p style="color: #666; margin-bottom: 25px;">Historial completo de tus compras</p>

        <!-- Filtros de Pedidos Cliente -->
        <div style="margin-bottom: 20px; display: flex; gap: 10px; flex-wrap: wrap; align-items: center;">
            <input type="text" id="buscadorPedidosCliente" placeholder="Buscar por producto, #pedido..." oninput="filtrarPedidosCliente()" style="flex: 1; min-width: 200px; padding: 10px 15px; border-radius: 8px; border: 1px solid #cbd5e1; outline: none;">
            
            <input type="date" id="filtroFechaPedidosCliente" onchange="filtrarPedidosCliente()" style="padding: 10px; border-radius: 8px; border: 1px solid #cbd5e1; outline: none; color: #475569;" title="Filtrar por fecha">
            
            <select id="filtroEstadoPedidosCliente" onchange="filtrarPedidosCliente()" style="padding: 10px; border-radius: 8px; border: 1px solid #cbd5e1; outline: none; color: #475569;">
                <option value="">Todos los estados</option>
                <option value="Pendiente">Pendiente</option>
                <option value="En camino">En camino</option>
                <option value="Entregado">Entregado</option>
                <option value="Cancelado">Cancelado</option>
            </select>
            
            <button type="button" onclick="limpiarFiltrosPedidosCliente()" style="background: #475569; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; color: white; display: flex; align-items: center; gap: 8px; font-weight: bold; box-shadow: 0 2px 4px rgba(0,0,0,0.1); transition: transform 0.2s, background 0.2s;" onmouseover="this.style.background='#334155'; this.style.transform='translateY(-2px)';" onmouseout="this.style.background='#475569'; this.style.transform='translateY(0)';">
                <i class="fa fa-eraser"></i> Limpiar
            </button>
        </div>

            <!-- Lista de pedidos -->
            <div id="listaPedidos" class="pedidos-lista">
                <!-- Los pedidos se cargarán aquí -->
            </div>

            <!-- Estado vacío -->
            <div id="pedidosEmpty" class="pedidos-empty" style="display:none;">
                <i class="fas fa-shopping-bag" style="font-size: 3rem; color: #ddd; margin-bottom: 15px;"></i>
                <p>No tienes pedidos realizados</p>
                <p style="font-size: 0.9rem; color: #999;">Cuando realices una compra, aparecerá aquí</p>
                <button class="btn-perfil btn-guardar" onclick="window.location.href='inicio.php'" style="margin-top: 20px;">
                    <i class="fas fa-shopping-cart"></i> Ir a comprar
                </button>
            </div>
        </section>
    </div>
</main>

<!-- MODAL LOGIN / REGISTRO -->
<div id="authModal" class="auth-modal">
  <div class="auth-content">
    <button class="auth-close" id="closeAuth">&times;</button>
    <div class="auth-header-icon">
      <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64"
        viewBox="0 0 24 24" fill="none" stroke="currentColor"
        stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="12" cy="8" r="4"></circle>
        <path d="M4 20c0-4 4-6 8-6s8 2 8 6"></path>
      </svg>
    </div>

    <h2 id="authTitle">Iniciar sesión</h2>
    <p class="auth-subtitle">Accede a tu cuenta Market Primavera</p>

    <form id="loginForm">
      <label>Email</label>
      <input type="email" name="email" placeholder="tu@email.com" required>

      <label>Contraseña</label>
      <input type="password" name="password" placeholder="••••••••" required>

      <button type="submit" class="btn-primary">Iniciar sesión</button>
    </form>

    <form id="registerForm" style="display:none;">
      <label>Nombre completo</label>
      <input type="text" name="nombre" placeholder="Juan Pérez" required>

      <label>Email</label>
      <input type="email" name="email" placeholder="tu@email.com" required>

      <label>Teléfono</label>
      <input type="tel" name="telefono" placeholder="999 888 777">

      <label>Contraseña</label>
      <input type="password" name="password" required minlength="6">

      <label>Confirmar contraseña</label>
      <input type="password" name="confirmPassword" required>

      <button type="submit" class="btn-primary">Crear cuenta</button>

      <p class="auth-switch">
        ¿Ya tienes cuenta?
        <a href="#" id="showLogin">Inicia sesión</a>
      </p>
    </form>

    <div class="login-only">
      <div class="auth-divider">o</div>

      <button class="btn-google" onclick="loginGoogle()">
        <img src="https://www.svgrepo.com/show/475656/google-color.svg">
        Continuar con Google
      </button>

      <p class="auth-switch auth-login">
        ¿No tienes cuenta? 
        <a href="#" id="showRegister">Regístrate</a>
      </p>
    </div>
  </div>
</div>

<!-- CARRITO LATERAL -->
<div class="cart-sidebar" id="cartSidebar">
    <div class="cart-header">
        <h2>
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="8" cy="21" r="1"></circle>
                <circle cx="19" cy="21" r="1"></circle>
                <path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"></path>
            </svg>
            Mi Carrito
        </h2>
        <button class="cart-close" id="cartClose">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </button>
    </div>

    <div class="cart-body" id="cartBody">
    </div>
</div>

<script src="<?= BASE_URL ?>/assets/js/perfil.js?v=1"></script>
<script type="module" src="<?= BASE_URL ?>/assets/js/auth-firebase.js?v=1"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>


<script>
document.getElementById('perfilDni')?.addEventListener('blur', async function () {
    const dni = this.value.trim();

    // Validar formato y evitar si ya está bloqueado
    if (!/^\d{8}$/.test(dni) || this.readOnly) return;

    try {
        const res = await fetch(`<?= BASE_URL ?>/controllers/ConsultaDniController.php?dni=${dni}`);
        const data = await res.json();

        if (data.success) {
            const nombreInput = document.querySelector('input[name="nombre"]');
            const apellidoInput = document.querySelector('input[name="apellido"]');

            // Solo autocompletar si están vacíos
            if (nombreInput.value.trim() === '') {
                nombreInput.value = data.nombres;
            }

            if (apellidoInput.value.trim() === '') {
                apellidoInput.value = data.apellidos;
            }
            
            // Bloquear el campo y mostrar el botón editar
            this.readOnly = true;
            const btnEditar = document.getElementById('btnEditarDni');
            if (btnEditar) {
                btnEditar.style.display = 'block';
            }

        } else {
            Swal.fire({icon: 'error', title: 'Error', text: data.error || "DNI no encontrado", confirmButtonColor: '#e30613'});
        }

    } catch (e) {
        console.error("Error consultando DNI:", e);
    }
});
</script>

<?php require __DIR__ . '/carrito.php'; ?>
<?php require __DIR__ . '/login.php'; ?>
<?php require __DIR__ . '/registro.php'; ?>

<script src="<?= BASE_URL ?>/assets/js/categoria.js?v=999"></script>
<script src="<?= BASE_URL ?>/assets/js/auth.js?v=999"></script>
<script src="<?= BASE_URL ?>/assets/js/buscador.js?v=1"></script>
<?php require __DIR__ . '/chatbot.php'; ?>
<script src="<?= BASE_URL ?>/assets/js/chatbot.js"></script>

</body>
</html>




