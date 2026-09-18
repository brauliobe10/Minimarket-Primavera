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
    <title>Atención al Cliente - Market Primavera</title>
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/estilos.css" />
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/AtencionCliente.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/auth.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/carrito.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/chatbot.css">

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<body class="atencion-cliente">


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
        <li><a href="acercaDe.php">Acerca de</a></li>
        <li><a href="AtencionCliente.php" class="active">Atención al cliente</a></li>

    </ul>
</nav>

<!-- MENÚ HAMBURGUESA (móvil) -->
<nav class="menu-hamburguesa" aria-hidden="true" aria-label="Menú móvil">
  <button class="close-menu" aria-label="Cerrar menú">✕</button>

  <ul>
    <li><a href="inicio.php">Inicio</a></li>
    <li><a href="ofertas.php#ofertas">Ofertas</a></li>
<!-- COMIDA -->
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

<!-- BEBIDAS -->
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

<!-- LIMPIEZA -->
<li class="accordion">
  <button class="accordion-toggle" aria-expanded="false">Limpieza del hogar</button>
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
  <button class="accordion-toggle" aria-expanded="false">Cuidado Personal</button>
  <ul class="accordion-panel">
    <?php foreach ($cuidado as $cat): ?>
      <li>
        <a href="categoria.php?id=<?= $cat['id_categoria'] ?>">
          <?= htmlspecialchars($cat['nombre']) ?>
        </a>
      </li>
    <?php endforeach; ?>
  </ul>
</li>          <!-- ✅ cierra el <li> de Cuidado Personal -->
</ul>          <!-- ✅ cierra el <ul class="menu-items"> hamburguesa -->
</nav>

    <!-- CONTENIDO PRINCIPAL -->
    <main class="main-content">
        <!-- Hero Section -->
        <section class="hero-section">
            <div class="hero-content">
                <div class="hero-text">
                    <h1>Atención al Cliente</h1>
                    <p>Estamos aquí para ayudarte. Encuentra respuestas a tus preguntas o contáctanos directamente.</p>
                </div>
            </div>
        </section>

        <!-- Opciones de Contacto -->
        <section class="contact-options">
            <div class="container">
                <h2>¿Cómo podemos ayudarte?</h2>
                <div class="options-grid">
                    <div class="option-card">
                        <div class="option-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <h3>Envíanos un mensaje</h3>
                        <p>Completa nuestro formulario y te responderemos en menos de 24 horas</p>
                        <button class="btn-option" onclick="scrollToSection('formulario-contacto')">Escribir mensaje</button>
                    </div>
                    
                    <div class="option-card">
                        <div class="option-icon">
                            <i class="fab fa-whatsapp"></i>
                        </div>
                        <h3>WhatsApp</h3>
                        <p>Chatea con nosotros por WhatsApp para una respuesta inmediata</p>
                        <a href="https://wa.me/51979798082" class="btn-option" target="_blank">Abrir WhatsApp</a>
                    </div>
                    
                    <div class="option-card">
                        <div class="option-icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <h3>Llámanos</h3>
                        <p>Disponible de Lunes a Sábado de 9:00 AM a 10:30 PM</p>
                        <a href="tel:+51979798082" class="btn-option">+51 979798082</a>
                    </div>
                    
                    <div class="option-card">
                        <div class="option-icon">
                            <i class="fas fa-question-circle"></i>
                        </div>
                        <h3>Preguntas Frecuentes</h3>
                        <p>Encuentra respuestas rápidas a las preguntas más comunes</p>
                        <button class="btn-option" onclick="scrollToSection('faq-section')">Ver FAQ</button>
                    </div>
                </div>
            </div>
        </section>



<section id="formulario-contacto" class="contact-form-section">
    <div class="container">

        <div class="form-header">
            <h2>Formulario de Contacto</h2>
            <p>Completa todos los campos y nos pondremos en contacto contigo pronto</p>
        </div>

        <div class="form-container">

            <form id="contactForm" class="contact-form" novalidate>

                <div class="form-row">
                    <div class="form-group">
                        <label for="asunto">Asunto *</label>
                        <select id="asunto" name="asunto" required>
                            <option value="">Selecciona un asunto</option>
                            <option value="pedido">Consulta sobre pedido</option>
                            <option value="producto">Información de producto</option>
                            <option value="devolucion">Devolución o cambio</option>
                            <option value="entrega">Problemas de entrega</option>
                            <option value="facturacion">Facturación</option>
                            <option value="sugerencia">Sugerencia</option>
                            <option value="otro">Otro</option>
                        </select>
                        <input type="text" id="asunto_otro" name="asunto_otro" placeholder="Especifica tu asunto" style="display: none; margin-top: 10px; width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 8px; font-family: 'Inter', sans-serif; transition: border-color 0.3s ease;">
                        <span class="error-message" id="error-asunto"></span>
                    </div>
                </div>

                <div class="form-group">
                    <label for="mensaje">Mensaje *</label>
                    <textarea id="mensaje" name="mensaje" rows="6"
                    placeholder="Describe tu consulta o problema en detalle" required></textarea>
                    <span class="error-message" id="error-mensaje"></span>
                </div>

                <div class="form-group checkbox-group">
                    <label class="checkbox-label">
                        <input type="checkbox" id="acepto-terminos" name="acepto-terminos" required>
                        <span class="checkmark"></span>

    Acepto los 
    <a href="<?= BASE_URL ?>/views/politicas.php#terminos" target="_blank">
        términos y condiciones
    </a>
    y la 
    <a href="<?= BASE_URL ?>/views/politicas.php#privacidad" target="_blank">
        política de privacidad
    </a>
    *
</label>
            
                    <span class="error-message" id="error-acepto-terminos"></span>
                </div>

                <button type="submit" class="btn-submit">
                    <span class="btn-text">Enviar mensaje</span>
                    <div class="btn-loader" style="display: none;">
                        <i class="fas fa-spinner fa-spin"></i>
                    </div>
                </button>

            </form>
        </div>

 <div id="success-message" class="success-message" style="display: none;">
    <div class="success-icon">
        <i class="fas fa-check-circle"></i>
    </div>
    <h3>¡Mensaje enviado con éxito!</h3>
    <p>Gracias por contactarnos. Nos pondremos en contacto contigo en menos de 24 horas.</p>
    <button class="btn-nuevo-mensaje" onclick="resetForm()">Enviar otro mensaje</button>
   </div>
</div>

</section> <!-- cierra formulario-contacto -->


        <!-- Preguntas Frecuentes -->
        <section id="faq-section" class="faq-section">
            <div class="container">
                <div class="faq-header">
                    <h2>Preguntas Frecuentes</h2>
                    <p>Encuentra respuestas rápidas a las preguntas más comunes</p>
                </div>
                
                <div class="faq-categories">
                    <button class="faq-category active" data-category="general">General</button>
                    <button class="faq-category" data-category="pedidos">Pedidos</button>
                    <button class="faq-category" data-category="entrega">Entrega</button>
                    <button class="faq-category" data-category="pagos">Pagos</button>
                    <button class="faq-category" data-category="cuenta">Mi Cuenta</button>
                </div>
                
                <div class="faq-container">
                    <!-- FAQ General -->
                    <div class="faq-category-content active" data-category="general">
                        <div class="faq-item">
                            <div class="faq-question">
                                <span>¿Cuáles son los horarios de atención?</span>
                                <i class="fas fa-chevron-down"></i>
                            </div>
                            <div class="faq-answer">
                                <p>Estamos disponibles de Lunes a Sábado de 9:00 AM a 10:30 PM y Domingos de 10:00 AM a 8:00 PM. Nuestro servicio de atención al cliente por WhatsApp está disponible 24/7.</p>
                            </div>
                        </div>
                        
                        <div class="faq-item">
                            <div class="faq-question">
                                <span>¿En qué zonas realizan entregas?</span>
                                <i class="fas fa-chevron-down"></i>
                            </div>
                            <div class="faq-answer">
                                <p>Realizamos entregas en La victoria, JLO, Pimentel y Chiclayo. Para verificar si llegamos a tu zona específica, puedes consultar en el momento de realizar tu pedido o contactarnos directamente.</p>
                            </div>
                        </div>
                        
                        <div class="faq-item">
                            <div class="faq-question">
                                <span>¿Cómo puedo contactar con Market Primavera?</span>
                                <i class="fas fa-chevron-down"></i>
                            </div>
                            <div class="faq-answer">
                                <p>Puedes contactarnos a través de WhatsApp (+51 912 345 678), por teléfono, mediante nuestro formulario de contacto en esta página, o visitándonos directamente en nuestra tienda física.</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- FAQ Pedidos -->
                    <div class="faq-category-content" data-category="pedidos">
                        <div class="faq-item">
                            <div class="faq-question">
                                <span>¿Cómo puedo realizar un pedido?</span>
                                <i class="fas fa-chevron-down"></i>
                            </div>
                            <div class="faq-answer">
                                <p>Puedes realizar tu pedido a través de nuestra página web, agregando productos al carrito y completando el proceso de compra. También puedes llamarnos o escribirnos por WhatsApp para hacer tu pedido telefónicamente.</p>
                            </div>
                        </div>
                        
                        <div class="faq-item">
                            <div class="faq-question">
                                <span>¿Puedo modificar o cancelar mi pedido?</span>
                                <i class="fas fa-chevron-down"></i>
                            </div>
                            <div class="faq-answer">
                                <p>Sí, puedes modificar o cancelar tu pedido hasta 30 minutos después de haberlo realizado. Una vez que el pedido esté en preparación, no podrá ser modificado. Contáctanos inmediatamente si necesitas hacer cambios.</p>
                            </div>
                        </div>
                        
                        <div class="faq-item">
                            <div class="faq-question">
                                <span>¿Cuál es el monto mínimo para realizar un pedido?</span>
                                <i class="fas fa-chevron-down"></i>
                            </div>
                            <div class="faq-answer">
                                <p>El monto mínimo para pedidos con entrega a domicilio es de S/ 30. Para recoger en tienda no hay monto mínimo. Este monto puede variar según la zona de entrega.</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- FAQ Entrega -->
                    <div class="faq-category-content" data-category="entrega">
                        <div class="faq-item">
                            <div class="faq-question">
                                <span>¿Cuánto tiempo tarda la entrega?</span>
                                <i class="fas fa-chevron-down"></i>
                            </div>
                            <div class="faq-answer">
                                <p>El tiempo estimado de entrega es de 30 a 60 minutos, dependiendo de la distancia y la disponibilidad de nuestros repartidores. Te notificaremos cuando tu pedido esté en camino.</p>
                            </div>
                        </div>
                        
                        <div class="faq-item">
                            <div class="faq-question">
                                <span>¿Cuánto cuesta la entrega a domicilio?</span>
                                <i class="fas fa-chevron-down"></i>
                            </div>
                            <div class="faq-answer">
                                <p>El costo de entrega varía según la distancia. Generalmente oscila entre S/ 3 y S/ 8. En pedidos superiores a S/ 80, la entrega es gratuita en zonas cercanas a nuestra tienda.</p>
                            </div>
                        </div>
                        
                        <div class="faq-item">
                            <div class="faq-question">
                                <span>¿Puedo programar mi entrega para una hora específica?</span>
                                <i class="fas fa-chevron-down"></i>
                            </div>
                            <div class="faq-answer">
                                <p>Sí, ofrecemos entregas programadas. Puedes elegir una ventana de tiempo de 2 horas durante el proceso de pedido. Este servicio tiene un costo adicional de S/ 2.</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- FAQ Pagos -->
                    <div class="faq-category-content" data-category="pagos">
                        <div class="faq-item">
                            <div class="faq-question">
                                <span>¿Qué métodos de pago aceptan?</span>
                                <i class="fas fa-chevron-down"></i>
                            </div>
                            <div class="faq-answer">
                                <p>Aceptamos efectivo contra entrega, tarjetas Visa y Mastercard, y Yape o Plin. También puedes pagar con transferencia bancaria para pedidos grandes.</p>
                            </div>
                        </div>
                        
                        <div class="faq-item">
                            <div class="faq-question">
                                <span>¿Es seguro pagar con tarjeta en línea?</span>
                                <i class="fas fa-chevron-down"></i>
                            </div>
                            <div class="faq-answer">
                                <p>Sí, utilizamos un sistema de pago seguro con encriptación SSL. Tus datos financieros están protegidos y nunca almacenamos información de tarjetas de crédito en nuestros servidores.</p>
                            </div>
                        </div>
                        
                        <div class="faq-item">
                            <div class="faq-question">
                                <span>¿Puedo obtener una factura?</span>
                                <i class="fas fa-chevron-down"></i>
                            </div>
                            <div class="faq-answer">
                                <p>Sí, emitimos boletas electrónicas automáticamente. Si necesitas factura, proporciona tu RUC durante el proceso de compra. Las facturas se envían por correo electrónico en formato PDF.</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- FAQ Mi Cuenta -->
                    <div class="faq-category-content" data-category="cuenta">
                        <div class="faq-item">
                            <div class="faq-question">
                                <span>¿Cómo creo una cuenta?</span>
                                <i class="fas fa-chevron-down"></i>
                            </div>
                            <div class="faq-answer">
                                <p>Puedes crear una cuenta haciendo clic en "Mi Cuenta" en la parte superior de la página y seleccionando "Iniciar sesión". Luego elige "Crear nueva cuenta" y completa el formulario de registro.</p>
                            </div>
                        </div>
                        
                        <div class="faq-item">
                            <div class="faq-question">
                                <span>¿Cómo puedo ver el historial de mis pedidos?</span>
                                <i class="fas fa-chevron-down"></i>
                            </div>
                            <div class="faq-answer">
                                <p>Una vez que inicies sesión en tu cuenta, podrás ver todos tus pedidos anteriores en la sección "Mis Pedidos". Allí encontrarás el detalle, estado y fecha de cada pedido.</p>
                            </div>
                        </div>
                        
                        <div class="faq-item">
                            <div class="faq-question">
                                <span>¿Cómo actualizo mi información personal?</span>
                                <i class="fas fa-chevron-down"></i>
                            </div>
                            <div class="faq-answer">
                                <p>En tu cuenta, ve a "Mi Perfil" donde podrás editar tu información personal, direcciones de entrega, número de teléfono y preferencias de comunicación.</p>
                            </div>
                        </div>
                    </div>
                </div>
                
    
            </div>
        </section>
    </main>


<!-- Carrito -->
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
<script src="<?= BASE_URL ?>/assets/js/AtencionCliente.js?v=999"></script>
<script src="<?= BASE_URL ?>/assets/js/auth.js?v=999"></script>
<script type="module" src="<?= BASE_URL ?>/assets/js/auth-firebase.js"></script>
<script src="<?= BASE_URL ?>/assets/js/buscador.js?v=1"></script>
<?php require __DIR__ . '/chatbot.php'; ?>
<script src="<?= BASE_URL ?>/assets/js/chatbot.js"></script>

</body>
</html>




