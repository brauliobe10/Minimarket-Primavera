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
    <title>Políticas y Términos - Market Primavera</title>

    <!-- GLOBAL -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/estilos.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/auth.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/carrito.css">

    <!-- PAGE CSS -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/politicas.css?v=2">

    <!-- ICONOS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>

<body>

<header class="header" id="site-header">
    <div class="header-left">
        <a href="<?= BASE_URL ?>/views/inicio.php" style="text-decoration:none; color:inherit; display:flex; align-items:center;">
            <div class="logo">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round" class="logo-icon">
                    <circle cx="9" cy="21" r="1"></circle>
                    <circle cx="20" cy="21" r="1"></circle>
                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"></path>
                </svg>
                <span>Market Primavera</span>
            </div>
        </a>
    </div>

    <div class="header-right">
        <a href="<?= BASE_URL ?>/views/inicio.php" class="header-item">
            <i class="fas fa-store"></i>
            <span class="header-label">Ir a la Tienda</span>
        </a>

        <a href="<?= BASE_URL ?>/views/AtencionCliente.php" class="header-item">
            <i class="fas fa-headset"></i>
            <span class="header-label">Ayuda</span>
        </a>
    </div>
</header>

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

    <main class="legal-main">
        <div class="legal-container">
            
            <section id="terminos" class="legal-section">
                <h2><i class="fas fa-file-contract"></i> Términos y Condiciones</h2>
                <p>Bienvenido a <strong>Market Primavera</strong>. Al acceder y utilizar nuestro sitio web y servicios, usted acepta cumplir con los siguientes términos y condiciones diseñados para asegurar una experiencia de compra segura y confiable.</p>

                <h3><i class="fas fa-laptop-house"></i> 1. Uso del Servicio</h3>
                <p>Market Primavera ofrece un servicio de venta minorista de productos de primera necesidad, alimentos frescos y artículos para el hogar. Nos reservamos el derecho de modificar, suspender o discontinuar cualquier aspecto del servicio en cualquier momento.</p>

                <h3><i class="fas fa-tags"></i> 2. Precios y Disponibilidad</h3>
                <ul>
                    <li>Todos los precios están expresados en <strong>Soles (S/)</strong> e incluyen IGV.</li>
                    <li>Las imágenes de los productos son referenciales. Nos esforzamos por mostrar colores y detalles con precisión.</li>
                    <li>Las ofertas "Más vendido" y promociones especiales están sujetas a stock limitado.</li>
                </ul>

                <h3><i class="fas fa-user-lock"></i> 3. Cuenta de Usuario</h3>
                <p>Para realizar compras, utilizamos un sistema de autenticación seguro (Firebase). Usted es responsable de mantener la confidencialidad de su cuenta y contraseña.</p>
            </section>


            <section id="envios" class="legal-section">
                <h2><i class="fas fa-shipping-fast"></i> Envío y Devoluciones</h2>
                
                <h3><i class="fas fa-truck"></i> Política de Envíos</h3>
                <ul>
                    <li><strong>Cobertura:</strong> Entregas en Lambayeque y zonas aledañas (Urb. Los Sauces).</li>
                    <li><strong>Tiempo de entrega:</strong> Express de 30 a 60 minutos en horario de atención.</li>
                    <li><strong>Horario:</strong> Lunes a Sábado 9am - 10:30pm | Domingos 10am - 8pm.</li>
                    <li><strong>Costo:</strong> S/ 3.00 a S/ 8.00 según distancia. <strong>Gratis</strong> en compras > S/ 80.00.</li>
                    <li><strong>Recojo en Tienda:</strong> Sin costo adicional en nuestra sede principal.</li>
                </ul>

                <h3><i class="fas fa-box-open"></i> Cambios y Devoluciones</h3>
                <p>Si no está 100% satisfecho con su compra, le respaldamos con nuestra garantía de calidad:</p>
                <ul>
                    <li><strong>Frescos (Frutas/Verduras):</strong> Reclamos dentro de las <strong>24 horas</strong> de recepción.</li>
                    <li><strong>Abarrotes/Limpieza:</strong> Cambios hasta <strong>48 horas</strong> post-entrega.</li>
                    <li><strong>Requisito:</strong> Presentar comprobante de pago y producto en empaque original.</li>
                </ul>
            </section>


            <section id="privacidad" class="legal-section">
                <h2><i class="fas fa-user-shield"></i> Políticas de Privacidad</h2>
                <p>En Market Primavera, la seguridad de sus datos es nuestra prioridad. Cumplimos estrictamente con la Ley de Protección de Datos Personales.</p>

                <h3><i class="fas fa-database"></i> 1. Recopilación de Datos</h3>
                <p>Recopilamos solo la información necesaria para procesar su pedido:</p>
                <ul>
                    <li>Nombre y Apellidos</li>
                    <li>Dirección de correo electrónico (para confirmaciones y boletas)</li>
                    <li>Dirección de entrega y teléfono de contacto</li>
                </ul>

                <h3><i class="fas fa-lock"></i> 2. Seguridad</h3>
                <p>Utilizamos tecnología SSL para encriptar su información. Sus datos de pago son procesados por pasarelas seguras y no se almacenan en nuestros servidores.</p>
                
                
                <a href="<?= BASE_URL ?>/views/inicio.php" class="back-to-top">
    <i class="fas fa-arrow-left"></i> Volver al inicio
</a>

            </section>

        </div>
    </main>
s
    <footer class="footer-primavera" id="footer">
        <div class="footer-inner">

            <div class="footer-brand">
                <svg xmlns="http://www.w3.org/2000/svg" class="footer-logo-icon"
                    width="32" height="32" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="9" cy="21" r="1"></circle>
                    <circle cx="20" cy="21" r="1"></circle>
                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"></path>
                </svg>
                <span class="footer-logo-text">MARKET PRIMAVERA</span>
            </div>

            <p class="footer-subtitle">Aceptamos los siguientes métodos de pago</p>
     <div class="footer-payments">
    <img src="<?= BASE_URL ?>/img/mastercard.png" alt="Mastercard">
    <img src="<?= BASE_URL ?>/img/visa.png" alt="Visa">
    <img src="<?= BASE_URL ?>/img/yape.avif" alt="Yape">
    <img src="<?= BASE_URL ?>/img/plin.png" alt="Plin">
</div>
            <div class="footer-help">
                <p class="footer-help-title">¿Necesitas ayuda?</p>
                <p>Visita <a href="<?= BASE_URL ?>/views/AtencionCliente.php" style="color: #ffd93d;">Atención al Cliente</a> o llámanos al</p>
                <a href="tel:+51979798082" style="color: #fff; font-weight: bold;">+51 979798082</a>
            </div>

            <div class="footer-links">
                <a href="#terminos">Términos y condiciones</a>
                <a href="#envios">Envío y devoluciones</a>
                <a href="#privacidad">Políticas de privacidad</a>
            </div>

            <p class="footer-address">Urb. Los Sauces 6448 – Lambayeque, Perú</p>
            <p class="footer-copy">&copy; 2026 Market Primavera. Todos los derechos reservados.</p>

        </div>
    </footer>

    <?php require __DIR__ . '/carrito.php'; ?>
    <?php require __DIR__ . '/login.php'; ?>
    <?php require __DIR__ . '/registro.php'; ?>

    <script src="<?= BASE_URL ?>/assets/js/categoria.js?v=999"></script>
    <script src="<?= BASE_URL ?>/assets/js/auth.js?v=999"></script>
    <script src="<?= BASE_URL ?>/assets/js/buscador.js?v=1"></script>

</body>
</html>




