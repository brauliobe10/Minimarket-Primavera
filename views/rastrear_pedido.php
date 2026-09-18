<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../config/Security.php';
require_once __DIR__ . '/../controllers/AuthController.php';

\Config\Security::initSession();
\Config\Security::setSecurityHeaders();

// Refrescar roles y verificar acceso
sincronizarRoles();
requireClienteOrGuest();

$title = "Rastrear Pedido";
// Validamos sesión si existe
$isLogged = isset($_SESSION['usuario_id']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<script>window.BASE_URL = '<?= BASE_URL ?>';</script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rastrear Pedido – Market Primavera</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/estilos.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .rastreo-page { background-color: #f4f6f8; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; padding: 0; }
        .header { background: #e30613; padding: 15px 30px; display: flex; align-items: center; justify-content: space-between; color: white; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .logo { display: flex; align-items: center; gap: 10px; color: white; text-decoration: none; font-size: 1.2rem; font-weight: bold; }
        .header-item { color: white; text-decoration: none; font-weight: bold; }
        .header-item:hover { text-decoration: underline; }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<body class="rastreo-page">

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
                 stroke-linecap="round" stroke-linejoin="round">
                <circle cx="9" cy="21" r="1"></circle>
                <circle cx="20" cy="21" r="1"></circle>
                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"></path>
            </svg>
            <span>Market Primavera</span>
        </a>
    </div>
    <div class="header-right">
        <a href="<?= BASE_URL ?>/views/inicio.php" class="header-item">Volver a la tienda</a>
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
                    <li><a href="inicio.php#comida">Ver opciones de Comida</a></li>
                </ul>
            </li>

            <li class="accordion">
                <button class="accordion-toggle" aria-expanded="false">Bebidas</button>
                <ul class="accordion-panel">
                    <li><a href="inicio.php#bebidas">Ver opciones de Bebidas</a></li>
                </ul>
            </li>

            <li class="accordion">
                <button class="accordion-toggle" aria-expanded="false">Limpieza del hogar</button>
                <ul class="accordion-panel">
                    <li><a href="inicio.php#limpieza">Ver opciones de Limpieza</a></li>
                </ul>
            </li>

            <li class="accordion">
                <button class="accordion-toggle" aria-expanded="false">Cuidado Personal</button>
                <ul class="accordion-panel">
                    <li><a href="inicio.php#cuidado">Ver opciones de Cuidado Personal</a></li>
                </ul>
            </li>

            <li><a href="inicio.php#mas-vendido">Más vendido</a></li>
            <li><?php if (estaLogueado()): ?><a href="<?= BASE_URL ?>/views/perfil.php#pedidos" id="misPedidosHamburguesa" class="highlight">Mis pedidos</a><?php else: ?><a href="<?= BASE_URL ?>/views/rastrear_pedido.php" id="misPedidosHamburguesa" class="highlight">Rastrear pedido</a><?php endif; ?></li>
            <li><a href="acercaDe.php" class="highlight">Acerca de</a></li>
            <li><a href="AtencionCliente.php" class="highlight">Atención al cliente</a></li>
        </ul>
    </nav>


<div style="max-width: 600px; margin: 40px auto; padding: 20px; background: white; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
    <h2 style="margin-bottom: 20px; color: #e30613; text-align: center;">Rastrear mi Pedido</h2>
    <p style="text-align: center; color: #555; margin-bottom: 30px;">
        Ingresa tu DNI y el Número de Pedido para conocer el estado de tu compra.
    </p>

    <form id="formRastrear" onsubmit="rastrearPedido(event)">
        <div style="margin-bottom: 15px;">
            <label style="display: block; font-weight: 600; margin-bottom: 5px;">DNI *</label>
            <input type="text" id="rastreoDni" required maxlength="8" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;" placeholder="Ingresa los 8 dígitos de tu DNI">
        </div>

        <div style="margin-bottom: 25px;">
            <label style="display: block; font-weight: 600; margin-bottom: 5px;">Número de Pedido (Opcional)</label>
            <input type="number" id="rastreoId" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;" placeholder="Ejemplo: 123">
            <small style="color: #666; display: block; margin-top: 5px;">Si no recuerdas el número, déjalo en blanco para ver todos tus pedidos.</small>
        </div>

        <button type="submit" style="width: 100%; padding: 12px; background: #e30613; color: white; border: none; border-radius: 4px; font-size: 1rem; font-weight: bold; cursor: pointer;">
            Buscar Pedido
        </button>
    </form>

    <div id="rastreoResultado" style="margin-top: 30px; display: none;">
        <!-- Resultado dinámico -->
    </div>
</div>

<script>
async function rastrearPedido(e) {
    e.preventDefault();
    const dni = document.getElementById('rastreoDni').value.trim();
    const id = document.getElementById('rastreoId').value.trim();
    const resultDiv = document.getElementById('rastreoResultado');
    const btn = document.querySelector('#formRastrear button');

    if(dni.length !== 8) {
        Swal.fire({icon: 'warning', title: 'Atención', text: 'El DNI debe tener 8 dígitos.', confirmButtonColor: '#e30613'});
        return;
    }

    btn.textContent = "Buscando...";
    btn.disabled = true;
    resultDiv.style.display = 'none';

    try {
        const resp = await fetch(`<?= BASE_URL ?>/controllers/RastrearPedidoController.php?dni=${dni}&id_pedido=${id}`);
        const data = await resp.json();

        btn.textContent = "Buscar Pedido";
        btn.disabled = false;

        if(!data.success) {
            resultDiv.innerHTML = `<div style="padding: 15px; background: #fde8e8; border-left: 4px solid #e30613; color: #c0392b;">${data.error}</div>`;
            resultDiv.style.display = 'block';
            return;
        }

        const pedidos = Array.isArray(data.pedidos) ? data.pedidos : [data.pedido];
        
        if(pedidos.length === 0) {
            resultDiv.innerHTML = `<div style="padding: 15px; background: #fde8e8; border-left: 4px solid #e30613; color: #c0392b;">No se encontraron pedidos.</div>`;
            resultDiv.style.display = 'block';
            return;
        }

        const esRegistrado = data.registrado || false;

        let html = ``;
        pedidos.forEach(p => {
            html += `
                <div style="padding: 20px; border: 1px solid #eee; border-radius: 6px; background: #fafafa; margin-bottom: 20px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #ccc; padding-bottom: 10px; margin-bottom: 15px;">
                        <h3 style="margin: 0;">Pedido #${p.id_pedido}</h3>
                        <div style="display: flex; gap: 10px;">
                            <a href="<?= BASE_URL ?>/views/pdf_comprobante.php?id=${p.id_pedido}" target="_blank" style="background: #334155; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 0.85rem; text-decoration: none; display: flex; align-items: center; gap: 5px;">
                                <i class="fa fa-file-pdf"></i> Ver Boleta
                            </a>
                            ${p.estado_pedido === 'Pendiente' ? 
                                (esRegistrado ? 
                                    `<button onclick="typeof abrirModalLogin === 'function' ? abrirModalLogin() : alert('Por favor, inicia sesión para cancelar este pedido.');" style="background: #64748b; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 0.85rem;" title="Este DNI tiene una cuenta registrada. Inicia sesión."><i class="fa fa-lock"></i> Inicia sesión para cancelar</button>` 
                                :
                                    `<button onclick="cancelarPedidoInvitado(${p.id_pedido})" style="background: #e30613; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 0.85rem;"><i class="fa fa-times"></i> Cancelar Pedido</button>`
                                )
                                : ''}
                        </div>
                    </div>
                    <p><strong>Fecha:</strong> ${p.fecha}</p>
                    <p><strong>Total:</strong> S/ ${p.total}</p>
                    <p><strong>Entrega:</strong> ${p.metodo_entrega}</p>
                    <p><strong>Pago:</strong> ${p.metodo_pago}</p>
                    <p><strong>Dirección:</strong> ${p.direccion}</p>
                    
                    <div style="margin-top: 20px; padding: 15px; background: #e8f5e9; border-left: 4px solid #4caf50;">
                        <h4 style="margin: 0 0 10px 0; color: #2e7d32;">Estado del Pedido</h4>
                        <p style="margin:0; font-size: 1.1rem; font-weight: bold; color: #1b5e20;">${p.estado_pedido}</p>
                    </div>
                </div>
            `;
        });
        
        resultDiv.innerHTML = html;
        resultDiv.style.display = 'block';

    } catch (error) {
        btn.textContent = "Buscar Pedido";
        btn.disabled = false;
        resultDiv.innerHTML = `<div style="padding: 15px; background: #fde8e8; border-left: 4px solid #e30613; color: #c0392b;">Error de conexión al buscar el pedido.</div>`;
        resultDiv.style.display = 'block';
    }
}

let pedidoACancelarInvitado = null;

function cancelarPedidoInvitado(id_pedido) {
    pedidoACancelarInvitado = id_pedido;
    const modal = document.getElementById('modalCancelarPedidoInvitado');
    if(modal) {
        modal.style.display = 'flex';
        document.getElementById('motivoCancelacionTextInvitado').value = '';
        
        const btnConfirmar = document.getElementById('btnConfirmarCancelacionInvitado');
        btnConfirmar.onclick = () => procesarCancelacionInvitado();
    }
}

function cerrarModalCancelarInvitado() {
    pedidoACancelarInvitado = null;
    const modal = document.getElementById('modalCancelarPedidoInvitado');
    if(modal) modal.style.display = 'none';
}

async function procesarCancelacionInvitado() {
    if (!pedidoACancelarInvitado) return;
    
    const motivo = document.getElementById('motivoCancelacionTextInvitado').value.trim();
    const btn = document.getElementById('btnConfirmarCancelacionInvitado');
    btn.innerHTML = 'Cancelando...';
    btn.disabled = true;
    
    try {
        const resp = await fetch('<?= BASE_URL ?>/controllers/CancelarPedidoController.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id_pedido: pedidoACancelarInvitado, guest: true, motivo: motivo })
        });
        const data = await resp.json();
        
        btn.innerHTML = 'Confirmar Cancelación';
        btn.disabled = false;
        
        if (data.success) {
            Swal.fire({icon: 'success', title: 'Éxito', text: 'El pedido ha sido cancelado exitosamente.', confirmButtonColor: '#e30613'});
            cerrarModalCancelarInvitado();
            document.querySelector('#formRastrear button').click(); // Recargar la lista
        } else {
            Swal.fire({icon: 'error', title: 'Error', text: data.message || 'No se pudo cancelar el pedido.', confirmButtonColor: '#e30613'});
        }
    } catch (err) {
        btn.innerHTML = 'Confirmar Cancelación';
        btn.disabled = false;
        Swal.fire({icon: 'error', title: 'Error de conexión', text: 'Error de conexión al intentar cancelar el pedido.', confirmButtonColor: '#e30613'});
    }
}
</script>

<!-- Modal Cancelar Pedido Invitado -->
<div id="modalCancelarPedidoInvitado" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; justify-content: center; align-items: center;">
    <div style="background: #fff; width: 90%; max-width: 450px; padding: 25px; border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.2);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
            <h3 style="margin: 0; color: #1e293b; display: flex; align-items: center; gap: 8px;">
                <i class="fa fa-exclamation-triangle" style="color: #e30613;"></i> Cancelar Pedido
            </h3>
            <button onclick="cerrarModalCancelarInvitado()" style="background: transparent; border: none; font-size: 20px; cursor: pointer; color: #64748b;">✕</button>
        </div>
        <p style="color: #475569; margin-bottom: 20px;">¿Estás seguro de que deseas cancelar este pedido? Esta acción no se puede deshacer.</p>
        
        <div style="margin-bottom: 20px;">
            <label style="display: block; font-weight: 600; color: #1e293b; margin-bottom: 8px;">Motivo de cancelación (Opcional)</label>
            <textarea id="motivoCancelacionTextInvitado" rows="3" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #cbd5e1; outline: none; resize: vertical; font-family: inherit;" placeholder="Cuéntanos por qué deseas cancelar el pedido..."></textarea>
        </div>
        
        <div style="display: flex; justify-content: flex-end; gap: 10px;">
            <button onclick="cerrarModalCancelarInvitado()" style="padding: 10px 15px; border-radius: 8px; border: 1px solid #cbd5e1; background: #f8fafc; color: #475569; cursor: pointer; font-weight: 600;">Volver</button>
            <button id="btnConfirmarCancelacionInvitado" style="padding: 10px 15px; border-radius: 8px; border: none; background: #e30613; color: white; cursor: pointer; font-weight: 600; display: flex; align-items: center; gap: 5px;">
                Confirmar Cancelación
            </button>
        </div>
    </div>
</div>

<script src="<?= BASE_URL ?>/assets/js/categoria.js?v=999"></script>
</body>
</html>





