<?php
require_once __DIR__ . '/../controllers/CheckoutController.php';
require_once __DIR__ . '/../config/Security.php';
\Config\Security::setSecurityHeaders();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<script>window.BASE_URL = '<?= BASE_URL ?>';</script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finalizar compra – Market Primavera</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/estilos.css?v=<?= time() ?>">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/checkout.css?v=<?= time() ?>">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/chatbot.css?v=<?= time() ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<body class="checkout-page">

<!-- Header igual al de inicio -->
<header class="header" id="site-header">
    <div class="header-left">
                    <!-- Botón hamburguesa (móvil) -->
            <button class="hamburger-menu" aria-label="Abrir menú" aria-expanded="false" style="margin-right: 15px;">
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
            </button>
            <a href="<?= BASE_URL ?>/views/inicio.php" class="logo">
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24"
                 fill="none" stroke="currentColor" stroke-width="2"
                 stroke-linecap="round" stroke-linejoin="round" class="logo-icon">
                <circle cx="9" cy="21" r="1"></circle>
                <circle cx="20" cy="21" r="1"></circle>
                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"></path>
            </svg>
            <span>Market Primavera</span>
        </a>
    </div>

    <div class="header-right">
        <?php if (estaLogueado()): ?>
        <a href="<?= BASE_URL ?>/views/perfil.php#pedidos" class="header-item">
        <?php else: ?>
        <a href="<?= BASE_URL ?>/views/rastrear_pedido.php" class="header-item">
        <?php endif; ?>
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24"
                 fill="none" stroke="currentColor" stroke-width="2"
                 stroke-linecap="round" stroke-linejoin="round" class="icon-svg">
                <path d="M16 3h5v5"></path><path d="M8 3H3v5"></path>
                <path d="M12 22v-8"></path><path d="M16 12H8"></path>
                <path d="M21 8v13H3V8"></path>
            </svg>
            <span class="header-label"><?= estaLogueado() ? 'Mis pedidos' : 'Rastrear pedido' ?></span>
        </a>

        <?php if (estaLogueado()): ?>
        <div class="has-dropdown-header">
            <button class="header-item account-toggle" type="button"
                    aria-expanded="false" aria-controls="account-dropdown">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="2"
                     stroke-linecap="round" stroke-linejoin="round" class="icon-svg">
                    <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
                <span class="header-label"><?= htmlspecialchars($_SESSION['usuario_nombre'] ?? '') ?></span>
            </button>
            <ul id="account-dropdown" class="dropdown-header" role="menu">
                <?php if (esAdmin()): ?>
                <li><a href="<?= BASE_URL ?>/views/admin/index.php">Panel Admin</a></li>
                <?php endif; ?>
                <?php if (esRepartidor()): ?>
                <li><a href="<?= BASE_URL ?>/views/panel_repartidor.php">Panel de entregas</a></li>
                <?php endif; ?>
                <li><a href="<?= BASE_URL ?>/views/perfil.php">Mi perfil</a></li>
                <li><a href="<?= BASE_URL ?>/controllers/AuthController.php?logout=1">Cerrar sesión</a></li>
            </ul>
        </div>
        <?php else: ?>
        <div class="has-dropdown-header">
            <a href="<?= BASE_URL ?>/views/inicio.php?login=1&redirect=checkout" class="header-item" style="text-decoration:none;">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="2"
                     stroke-linecap="round" stroke-linejoin="round" class="icon-svg">
                    <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
                <span class="header-label">Iniciar sesión</span>
            </a>
        </div>
        <?php endif; ?>
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

<div class="checkout-body">

    <!-- Volver -->
    <a href="#" onclick="checkoutVolver(event)" class="checkout-back">
        <i class="fa fa-arrow-left"></i> Volver
    </a>

    <!-- Stepper -->
    <div class="checkout-steps">
        <div class="step active" id="step1">
            <div class="step-circle">1</div>
            <div class="step-label">Entrega</div>
        </div>
        <div class="step-line" id="line12"></div>
        <div class="step" id="step2">
            <div class="step-circle">2</div>
            <div class="step-label">Dirección</div>
        </div>
        <div class="step-line" id="line23"></div>
        <div class="step" id="step3">
            <div class="step-circle">3</div>
            <div class="step-label">Pago</div>
        </div>
    </div>

    <!-- Éxito -->
    <div class="checkout-exito" id="checkoutExito">
        <i class="fa fa-check-circle exito-icon"></i>
        <h2>¡Pedido Confirmado!</h2>
        <p>Tu pedido ha sido procesado correctamente.</p>
        <p style="background: #fff3cd; color: #856404; padding: 10px; border-radius: 4px; border: 1px solid #ffeeba; display: inline-block; margin: 15px 0;">
            <strong>Importante:</strong> Guarda tu <strong style="font-size: 1.1em;">Número de Pedido <span id="compNumPedido"></span></strong> y tu DNI para <a href="<?= BASE_URL ?>/views/rastrear_pedido.php" style="color: #856404; text-decoration: underline;">rastrear tu compra</a>.
        </p>
        <a href="<?= BASE_URL ?>/views/inicio.php" class="btn-volver-inicio">Volver al inicio</a>
    </div>

    <!-- Contenido principal -->
    <div id="checkoutContenido">
        <div class="checkout-grid">

            <!-- Columna izquierda: pasos -->
            <div>

                <!-- PASO 1: Entrega -->
                <div id="paso1">
                    <div class="checkout-card">
                        <h2>Método de entrega</h2>

                        <div class="entrega-opciones">
                            <label class="entrega-opcion selected" data-metodo="domicilio">
                                <input type="radio" name="entrega" value="domicilio" checked>
                                <div class="entrega-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22"
                                         viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                         stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="1" y="3" width="15" height="13"></rect>
                                        <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon>
                                        <circle cx="5.5" cy="18.5" r="2.5"></circle>
                                        <circle cx="18.5" cy="18.5" r="2.5"></circle>
                                    </svg>
                                </div>
                                <div class="entrega-info">
                                    <h4>Entrega a domicilio</h4>
                                    <p>Recibe tu pedido en casa (costo adicional S/ 5.00)</p>
                                </div>
                            </label>

                            <label class="entrega-opcion" data-metodo="tienda">
                                <input type="radio" name="entrega" value="tienda">
                                <div class="entrega-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22"
                                         viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                         stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                                        <polyline points="9 22 9 12 15 12 15 22"></polyline>
                                    </svg>
                                </div>
                                <div class="entrega-info">
                                    <h4>Recoge en tienda</h4>
                                    <p>Sin costo adicional · Urb. Los Sauces 6448, Lambayeque</p>
                                </div>
                            </label>
                        </div>

                        <button class="btn-checkout-primary" id="btnContinuarEntrega" style="display:none;">
                            Continuar
                        </button>
                    </div>
                </div>

                <!-- PASO 2: Dirección -->
                <div id="paso2" style="display:none;">
                    <div class="checkout-card">
                        <h2 id="tituloPaso2Main" style="display:none;">Dirección de entrega</h2>


                        <?php if (empty($direcciones)): ?>
                            <!-- Sin direcciones guardadas o Invitado -->
                            <h2 id="tituloPaso2" style="margin-bottom:15px;">Dirección de entrega</h2>
                            
                            <?php if (!$esInvitado): ?>
                            <p style="color:#888;font-size:.9rem;margin-bottom:16px;">
                                No tienes direcciones guardadas.
                                <a href="<?= BASE_URL ?>/views/perfil.php" style="color:#e30613;font-weight:600;">
                                    Agrégalas en tu perfil
                                </a>
                                y vuelve aquí.
                            </p>
                            <?php else: ?>
                            <p id="subtituloPaso2" style="color:#888;font-size:.9rem;margin-bottom:16px;">
                                Ingresa tus datos para continuar con el pedido.
                            </p>
                            <?php endif; ?>
                            <div class="form-checkout">
                                <?php if ($esInvitado): ?>
                                <div class="form-row-checkout">
                                    <div class="fg-checkout" style="flex:1;">
                                        <label>DNI *</label>
                                        <div style="display:flex; gap:10px;">
                                            <input type="text" id="dirDNI" maxlength="8" inputmode="numeric" oninput="this.value=this.value.replace(/\D/g,'').slice(0,8)" placeholder="Ej: 76345180" required style="flex:1;">
                                            <button type="button" class="btn-checkout-primary" id="btnBuscarDniCheckout" style="margin-top:0; width:auto; padding:0 15px;">Buscar</button>
                                        </div>
                                        <small id="dniEstadoCheckout" style="display:block; margin-top:5px; font-weight:600;"></small>
                                    </div>
                                </div>
                                <?php endif; ?>
                                <div class="form-row-checkout">
                                    <div class="fg-checkout">
                                        <label>Nombres *</label>
                                        <input type="text" id="dirNombres"
                                               value="<?= htmlspecialchars($usuarioDatos['nombres'] ?? '') ?>"
                                               required>
                                    </div>
                                    <div class="fg-checkout">
                                        <label>Apellidos *</label>
                                        <input type="text" id="dirApellidos"
                                               value="<?= htmlspecialchars($usuarioDatos['apellidos'] ?? '') ?>"
                                               required>
                                    </div>
                                </div>
                                <div class="fg-checkout campos-delivery">
                                    <label>Dirección *</label>
                                    <input type="text" id="dirDireccion" placeholder="Av. Los Sauces 123" required>
                                </div>
                                <div class="form-row-checkout">
                                    <div class="fg-checkout campos-delivery">
                                        <label>Distrito *</label>
                                        <select id="dirDistrito" required style="width:100%;padding:10px;border:1px solid #ccc;border-radius:4px;">
                                            <option value="Chiclayo">Chiclayo</option>
                                            <option value="José Leonardo Ortiz">José Leonardo Ortiz</option>
                                            <option value="La Victoria">La Victoria</option>
                                            <option value="Pimentel">Pimentel</option>
                                        </select>
                                    </div>
                                    <div class="fg-checkout">
                                        <label>Teléfono *</label>
                                        <input type="tel" id="dirTelefono"
                                               value="<?= htmlspecialchars($usuarioDatos['telefono'] ?? '') ?>"
                                               maxlength="9" inputmode="numeric"
                                               oninput="this.value=this.value.replace(/\D/g,'').slice(0,9)"
                                               required>
                                    </div>
                                </div>
                                <div class="fg-checkout campos-delivery">
                                    <label>Referencia</label>
                                    <textarea id="dirReferencia" placeholder="Frente al parque, portón azul..."></textarea>
                                </div>
                            </div>

                        <?php else: ?>
                            <!-- Selector de dirección guardada -->
                            <div class="direcciones-guardadas" id="listaDirecciones">
                                <?php foreach ($direcciones as $dir): ?>
                                <label class="dir-opcion <?= $dir['predeterminada'] ? 'selected' : '' ?>"
                                       data-id="<?= $dir['id_direccion'] ?>"
                                       data-direccion="<?= htmlspecialchars($dir['direccion']) ?>"
                                       data-distrito="<?= htmlspecialchars($dir['distrito']) ?>"
                                       data-referencia="<?= htmlspecialchars($dir['referencia'] ?? '') ?>">
                                    <input type="radio" name="dir_seleccionada"
                                           value="<?= $dir['id_direccion'] ?>"
                                           <?= $dir['predeterminada'] ? 'checked' : '' ?>>
                                    <div class="dir-icono">
                                        <i class="fa fa-map-marker-alt"></i>
                                    </div>
                                    <div class="dir-info">
                                        <strong><?= htmlspecialchars($dir['etiqueta']) ?></strong>
                                        <?php if ($dir['predeterminada']): ?>
                                            <span class="dir-badge">Principal</span>
                                        <?php endif; ?>
                                        <p><?= htmlspecialchars($dir['direccion']) ?>,
                                           <?= htmlspecialchars($dir['distrito']) ?>,
                                           <?= htmlspecialchars($dir['provincia']) ?></p>
                                        <?php if ($dir['referencia']): ?>
                                            <p style="color:#aaa;font-size:.8rem;">
                                                <?= htmlspecialchars($dir['referencia']) ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                </label>
                                <?php endforeach; ?>
                            </div>

                            <a href="<?= BASE_URL ?>/views/perfil.php#direcciones" class="link-agregar-dir">
                                <i class="fa fa-plus"></i> Agregar nueva dirección
                            </a>

                            <!-- Campos ocultos para validar si no hay dir seleccionada -->
                            <input type="hidden" id="dirDireccion" value="">
                            <input type="hidden" id="dirDistrito"  value="">
                            <input type="hidden" id="dirNombres"   value="<?= htmlspecialchars($usuarioDatos['nombres'] ?? '') ?>">
                            <input type="hidden" id="dirApellidos" value="<?= htmlspecialchars($usuarioDatos['apellidos'] ?? '') ?>">
                            <input type="hidden" id="dirTelefono"  value="<?= htmlspecialchars($usuarioDatos['telefono'] ?? '') ?>">
                            <input type="hidden" id="dirDNI"       value="<?= htmlspecialchars($usuarioDatos['dni'] ?? '') ?>">
                            <input type="hidden" id="dirReferencia" value="">

                        <?php endif; ?>

                        <div style="display:flex;gap:10px;margin-top:20px;">
                            <button class="btn-checkout-back" onclick="mostrarPaso(1)">
                                <i class="fa fa-arrow-left"></i> Volver
                            </button>
                            <button class="btn-checkout-primary" id="btnContinuarDireccion" style="margin-top:0;">
                                Continuar
                            </button>
                        </div>
                    </div>
                </div>

                <!-- PASO 3: Pago -->
                <div id="paso3" style="display:none;">
                    <div class="checkout-card" style="margin-bottom:16px;">
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
                            <h2 style="margin:0;font-size:1rem;">Dirección seleccionada</h2>
                            <button type="button" id="btnCambiarDireccion" style="background:none;border:none;color:#e30613;font-size:.85rem;font-weight:600;cursor:pointer;">Cambiar</button>
                        </div>
                        <div id="resumenDireccion" style="font-size:.88rem;color:#555;line-height:1.6;"></div>
                    </div>
                    <div class="checkout-card">
                        <h2>Método de pago</h2>

                        <div class="pago-opciones">
                            <label class="pago-opcion" data-pago="tarjeta">
                                <input type="radio" name="pago" value="tarjeta">
                                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28"
                                     viewBox="0 0 24 24" fill="none" stroke="#e30613"
                                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
                                    <line x1="1" y1="10" x2="23" y2="10"></line>
                                </svg>
                                <span>Tarjeta de crédito / débito</span>
                                <small class="pago-resumen" id="resumenTarjeta"></small>
                            </label>

                            <label class="pago-opcion" data-pago="yape">
                                <input type="radio" name="pago" value="yape">
                                <span class="pago-icon-dual">
                                    <img src="<?= BASE_URL ?>/img/yape.avif" alt="Yape" onerror="this.style.display='none'">
                                    <img src="<?= BASE_URL ?>/img/plin.png" alt="Plin" onerror="this.style.display='none'">
                                </span>
                                <span>Yape / Plin</span>
                                <small class="pago-resumen" id="resumenYp"></small>
                            </label>

                            <label class="pago-opcion" data-pago="efectivo">
                                <input type="radio" name="pago" value="efectivo">
                                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28"
                                     viewBox="0 0 24 24" fill="none" stroke="#27ae60"
                                     stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="12" y1="1" x2="12" y2="23"></line>
                                    <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                                </svg>
                                <span>Pago en efectivo al recibir</span>
                                <small class="pago-resumen" id="resumenEfectivo"></small>
                            </label>
                        </div>

                        <!-- ══════ MODAL DE PAGO (overlay) ══════ -->
                        <div class="pm-overlay" id="pmOverlay">
                          <div class="pm-modal" id="pmModal">
                            <button type="button" class="pm-close" id="pmClose" aria-label="Cerrar">&times;</button>

                            <!-- ── Contenido: Tarjeta ── -->
                            <div class="pm-content" id="pmTarjeta">
                                <h3 class="pm-title">Tarjeta de crédito / débito</h3>

                                <?php if (!$esInvitado && !empty($tarjetasGuardadas)): ?>
                                <div class="fg-checkout saved-cards-selector" style="margin-bottom: 20px;">
                                    <label class="premium-label">Tus tarjetas guardadas</label>
                                    <div class="custom-dropdown" id="customCardDropdown">
                                        <div class="custom-dropdown-trigger" id="dropdownCardTrigger">
                                            <div class="dropdown-selected-content">
                                                <i class="fa fa-plus-circle"></i> Usar una tarjeta nueva
                                            </div>
                                            <i class="fa fa-chevron-down arrow-icon"></i>
                                        </div>
                                        <div class="custom-dropdown-options" id="dropdownCardOptions">
                                            <div class="custom-option active" data-value="">
                                                <i class="fa fa-plus-circle"></i> Usar una tarjeta nueva
                                            </div>
                                            <?php foreach($tarjetasGuardadas as $t): ?>
                                                <div class="custom-option" data-value="<?= $t['id_tarjeta'] ?>">
                                                    <?php if(strtolower($t['marca']) == 'visa'): ?>
                                                        <i class="fa-brands fa-cc-visa" style="color:#1434CB; font-size:1.4rem;"></i>
                                                    <?php elseif(strtolower($t['marca']) == 'mastercard'): ?>
                                                        <i class="fa-brands fa-cc-mastercard" style="color:#EB001B; font-size:1.4rem;"></i>
                                                    <?php else: ?>
                                                        <i class="fa fa-credit-card" style="color:#666; font-size:1.2rem;"></i>
                                                    <?php endif; ?>
                                                    <span><?= htmlspecialchars(ucfirst($t['marca'])) ?> term. en •••• <?= htmlspecialchars($t['ultimos_cuatro']) ?></span>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        <input type="hidden" id="selTarjetaGuardada" value="">
                                    </div>
                                </div>
                                <?php endif; ?>

                                <div class="cc-visual" id="ccVisual">
                                    <div class="cc-visual-inner" id="ccVisualInner">
                                        <div class="cc-face cc-front">
                                            <div class="cc-top-row">
                                                <div class="cc-chip"></div>
                                                <span class="cc-brand" id="ccBrandLogo">VISA</span>
                                            </div>
                                            <div class="cc-number" id="ccNumberDisplay">•••• •••• •••• ••••</div>
                                            <div class="cc-bottom-row">
                                                <div class="cc-holder-block">
                                                    <span class="cc-mini-label">Titular</span>
                                                    <span class="cc-value" id="ccNameDisplay">NOMBRE APELLIDO</span>
                                                </div>
                                                <div class="cc-expiry-block">
                                                    <span class="cc-mini-label">Vence</span>
                                                    <span class="cc-value" id="ccExpiryDisplay">MM/AA</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="cc-face cc-back">
                                            <div class="cc-stripe"></div>
                                            <div class="cc-cvv-row">
                                                <span class="cc-cvv-label">CVV</span>
                                                <div class="cc-cvv-box" id="ccCvvDisplay">•••</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="fg-checkout">
                                    <label>Número de tarjeta</label>
                                    <input type="text" id="ccNumeroInput" placeholder="1234 5678 9012 3456"
                                           maxlength="19" inputmode="numeric">
                                </div>
                                <div class="form-row-checkout">
                                    <div class="fg-checkout">
                                        <label>Vencimiento</label>
                                        <input type="text" id="ccVencInput" placeholder="MM/AA" maxlength="5" inputmode="numeric">
                                    </div>
                                    <div class="fg-checkout">
                                        <label>CVV</label>
                                        <input type="password" id="ccCvvInput" placeholder="•••" maxlength="3" inputmode="numeric" autocomplete="cc-csc">
                                    </div>
                                </div>
                                <div class="fg-checkout">
                                    <label>Nombre en la tarjeta</label>
                                    <input type="text" id="ccNombreInput" placeholder="JUAN PÉREZ">
                                </div>

                                <?php if (!$esInvitado): ?>
                                <div class="save-card-toggle">
                                    <label class="toggle-switch">
                                        <input type="checkbox" id="ccGuardarInput">
                                        <span class="slider round"></span>
                                    </label>
                                    <span class="toggle-text">Guardar esta tarjeta para futuras compras de forma segura</span>
                                </div>
                                <?php endif; ?>

                                <p class="pm-error" id="ccError"></p>

                                <button type="button" class="btn-pm-pagar" id="btnGuardarTarjeta">
                                    <i class="fa fa-lock" style="margin-right:.4rem;"></i> Pagar S/ <span id="ccMonto">0.00</span>
                                </button>
                            </div>

                            <!-- ── Contenido: Yape / Plin ── -->
                            <div class="pm-content" id="pmYp">
                                <h3 class="pm-title">Yape / Plin</h3>

                                <div class="yp-opciones">
                                    <label class="yp-opcion" data-yp="yape">
                                        <input type="radio" name="yp" value="yape">
                                        <img src="<?= BASE_URL ?>/img/yape.avif" alt="Yape" onerror="this.style.display='none'">
                                        <span>Yape</span>
                                    </label>
                                    <label class="yp-opcion" data-yp="plin">
                                        <input type="radio" name="yp" value="plin">
                                        <img src="<?= BASE_URL ?>/img/plin.png" alt="Plin" onerror="this.style.display='none'">
                                        <span>Plin</span>
                                    </label>
                                </div>

                                <!-- Panel Yape -->
                                <div class="yp-panel" id="yapePanel">
                                    <div class="yp-inner yp-inner-yape">
                                        <div class="yp-modal-brand">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                                 fill="none" stroke="#e30613" stroke-width="2.2"
                                                 stroke-linecap="round" stroke-linejoin="round">
                                                <circle cx="9" cy="21" r="1"></circle>
                                                <circle cx="20" cy="21" r="1"></circle>
                                                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"></path>
                                            </svg>
                                            <span>Market Primavera</span>
                                        </div>

                                        <div class="fg-checkout">
                                            <label>Ingresa tu celular Yape</label>
                                            <input type="tel" id="yapeCelular" placeholder="Ej: 987654321" maxlength="9" inputmode="numeric" oninput="this.value=this.value.replace(/\D/g,'').slice(0,9)">
                                        </div>
                                        <div class="fg-checkout">
                                            <label>Ingresa el código de aprobación</label>
                                            <input type="text" id="yapeCodigo" placeholder="123 456" maxlength="7" inputmode="numeric" oninput="let v = this.value.replace(/\D/g,'').slice(0,6); if(v.length > 3) v = v.slice(0,3) + ' ' + v.slice(3); this.value = v;">
                                        </div>

                                        <p class="pm-error" id="yapeError"></p>

                                        <button type="button" class="btn-checkout-primary" id="btnYapear">
                                            Pagar con Yape S/ <span class="monto-modal-yape" id="yapeMonto">0.00</span>
                                        </button>
                                    </div>
                                </div>

                                <!-- Panel Plin -->
                                <div class="yp-panel" id="plinPanel">
                                    <div class="yp-inner yp-inner-plin">
                                        <div class="yp-modal-brand">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                                 fill="none" stroke="#e30613" stroke-width="2.2"
                                                 stroke-linecap="round" stroke-linejoin="round">
                                                <circle cx="9" cy="21" r="1"></circle>
                                                <circle cx="20" cy="21" r="1"></circle>
                                                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"></path>
                                            </svg>
                                            <span>Market Primavera</span>
                                        </div>

                                        <div class="fg-checkout">
                                            <label>Ingresa tu celular Plin</label>
                                            <input type="tel" id="plinCelular" placeholder="Ej: 987654321" maxlength="9" inputmode="numeric" oninput="this.value=this.value.replace(/\D/g,'').slice(0,9)">
                                        </div>
                                        <div class="fg-checkout">
                                            <label>Ingresa el código de aprobación</label>
                                            <input type="text" id="plinCodigo" placeholder="123 456" maxlength="7" inputmode="numeric" oninput="let v = this.value.replace(/\D/g,'').slice(0,6); if(v.length > 3) v = v.slice(0,3) + ' ' + v.slice(3); this.value = v;">
                                        </div>

                                        <p class="pm-error" id="plinError"></p>

                                        <button type="button" class="btn-checkout-primary yp-plin-btn" id="btnPlinear">
                                            Pagar con Plin S/ <span class="monto-modal-plin" id="plinMonto">0.00</span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- ── Contenido: Efectivo ── -->
                            <div class="pm-content" id="pmEfectivo">
                                <h3 class="pm-title">Pago en efectivo al recibir</h3>
                                <p class="pm-sub">Indícanos con cuánto billete vas a pagar para que el repartidor lleve tu vuelto listo.</p>

                                <div class="fg-checkout">
                                    <label>¿Con cuánto vas a pagar?</label>
                                    <input type="text" id="efectivoMonto" placeholder="Ej. 50" inputmode="decimal">
                                </div>

                                <div class="efectivo-vuelto" id="efectivoVueltoBox" style="display:none;">
                                    Tu vuelto será <strong id="efectivoVuelto">S/ 0.00</strong>
                                </div>

                                <p class="pm-error" id="efectivoError"></p>

                                <button type="button" class="btn-pm-pagar" id="btnGuardarEfectivo">
                                    Confirmar
                                </button>
                            </div>

                          </div>
                        </div>

                        <div style="display:flex;gap:10px;margin-top:20px;">
                            <button class="btn-checkout-back" id="btnVolverPago">
                                <i class="fa fa-arrow-left"></i> Volver
                            </button>
                            <button class="btn-checkout-primary" id="btnConfirmarPago" style="margin-top:0;">
                                <i class="fa fa-lock" style="margin-right:.4rem;"></i> Confirmar pedido
                            </button>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Columna derecha: resumen -->
            <div>
                <div class="checkout-card">
                    <p class="resumen-titulo">Resumen del pedido</p>

                    <div id="resumenItems" class="resumen-items"></div>

                    <hr class="resumen-divider">

                    <div class="resumen-fila">
                        <span>Subtotal</span>
                        <span id="resSubtotal">S/ 0.00</span>
                    </div>
                    <div class="resumen-fila descuento" id="lineaDescuentoRes" style="display:none;">
                        <span>Descuento</span>
                        <span id="resDescuento">S/ 0.00</span>
                    </div>
                    <div class="resumen-fila">
                        <span>IGV (18%)</span>
                        <span id="resIgv">S/ 0.00</span>
                    </div>
                    <div class="resumen-fila" id="lineaEnvioRes">
                        <span>Envío</span>
                        <span id="resEnvio">S/ 5.00</span>
                    </div>
                    <div class="resumen-fila total">
                        <span>Total</span>
                        <span id="resTotal">S/ 0.00</span>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>

<?php require __DIR__ . '/comprobante.php'; ?>

<script>
window._usuarioId = <?= json_encode($_SESSION['usuario_id'] ?? 0) ?>;
window._carritoCache = <?= json_encode($carritoItems ?? []) ?>;
window._checkoutNombre = "<?= htmlspecialchars(($_SESSION['usuario_nombre'] ?? '') . ' ' . ($usuarioDatos['apellidos'] ?? '')) ?>";
window._checkoutDNI = "<?= htmlspecialchars($usuarioDatos['dni'] ?? '') ?>";
window._checkoutTelefono = "<?= htmlspecialchars($usuarioDatos['telefono'] ?? '') ?>";
window._tarjetasGuardadas = <?= json_encode($tarjetasGuardadas ?? []) ?>;
</script>
<script src="<?= BASE_URL ?>/assets/js/categoria.js?v=<?= time() ?>"></script>
<script src="<?= BASE_URL ?>/assets/js/checkout.js?v=<?= time() ?>"></script>
<?php require __DIR__ . '/chatbot.php'; ?>
<script src="<?= BASE_URL ?>/assets/js/chatbot.js"></script>
</body>
</html>





