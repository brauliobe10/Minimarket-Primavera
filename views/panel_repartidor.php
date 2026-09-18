<?php
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../config/Security.php';
\Config\Security::setSecurityHeaders();

if (!estaLogueado()) {
    header('Location: ' . BASE_URL . '/views/inicio.php?login=1');
    exit;
}

// Refrescar roles desde BD
sincronizarRoles();
requireRepartidor();

// Verificar que sea repartidor
require_once __DIR__ . '/../config/Database.php';
use Config\Database;
$conn = (new Database())->conectar();
$stmt = $conn->prepare("SELECT r.nombres, r.placa_vehiculo FROM repartidor r WHERE r.id_usuario=? AND r.estado=1");
$stmt->execute([(int)$_SESSION['usuario_id']]);
$rep = $stmt->fetch(\PDO::FETCH_ASSOC);

// Fallback si no tiene id_usuario enlazado
if (!$rep) {
    $rolCheck = $conn->prepare("SELECT 1 FROM usuario_rol WHERE id_usuario=? AND id_rol=3");
    $rolCheck->execute([(int)$_SESSION['usuario_id']]);
    if ($rolCheck->fetch()) {
        $uStmt = $conn->prepare("SELECT CONCAT(nombres,' ',apellidos) AS nombre FROM usuario WHERE id_usuario=?");
        $uStmt->execute([(int)$_SESSION['usuario_id']]);
        $u = $uStmt->fetch(\PDO::FETCH_ASSOC);
        $rep = ['nombres' => $u['nombre'] ?? $_SESSION['usuario_nombre'], 'placa_vehiculo' => null];
    }
}

if (!$rep) {
    header('Location: ' . BASE_URL . '/views/inicio.php');
    exit;
}

if (empty($rep['placa_vehiculo'])) {
    header('Location: ' . BASE_URL . '/views/perfil.php#placa');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<script>window.BASE_URL = '<?= BASE_URL ?>';</script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Repartidor – Market Primavera</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/repartidor.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>
<body class="rep-page">

<!-- ══ SIDEBAR ══════════════════════════════════════ -->
<aside class="rep-sidebar" id="repSidebar">
    <div class="rep-sidebar-brand">
        <div class="rep-sidebar-logo">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                 fill="none" stroke="#e30613" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle>
                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"></path>
            </svg>
            <span class="rep-sidebar-brand-text">Market <strong>Primavera</strong></span>
        </div>
        <span class="rep-role-badge">
            <i class="fa fa-motorcycle"></i> Repartidor
        </span>
    </div>

    <!-- Avatar / nombre -->
    <div class="rep-sidebar-profile">
        <div class="rep-avatar">
            <i class="fa fa-user"></i>
        </div>
        <div class="rep-profile-info">
            <span class="rep-profile-name"><?= htmlspecialchars($rep['nombres']) ?></span>
            <span class="rep-profile-status">
                <span class="rep-status-dot"></span> En linea
            </span>
        </div>
    </div>

    <!-- Stats compactos en sidebar -->
    <div class="rep-sidebar-stats">
        <div class="rep-sstat">
            <strong id="repTotal">—</strong>
            <span>Total</span>
        </div>
        <div class="rep-sstat rep-sstat-blue">
            <strong id="repEnCamino">—</strong>
            <span>En camino</span>
        </div>
        <div class="rep-sstat rep-sstat-amber">
            <strong id="repPendientes">—</strong>
            <span>Pendientes</span>
        </div>
        <div class="rep-sstat rep-sstat-green">
            <strong id="repEntregados">—</strong>
            <span>Entregados</span>
        </div>
    </div>

    <!-- Ganancias destacado -->
    <div class="rep-earnings-card">
        <div class="rep-earnings-label">
            <i class="fa fa-wallet"></i> Ganancias hoy
        </div>
        <div class="rep-earnings-amount" id="repGanancias">S/ 0.00</div>
    </div>

    <!-- Navegacion -->
    <nav class="rep-sidebar-nav">
        <button class="rep-nav-item active" data-section="disponibles" onclick="cambiarSeccion('disponibles')">
            <span class="rep-nav-icon"><i class="fa fa-bell"></i></span>
            <span class="rep-nav-label">Disponibles</span>
            <span class="rep-nav-badge" id="badgeDisponibles" style="display:none;">0</span>
        </button>
        <button class="rep-nav-item" data-section="activas" onclick="cambiarSeccion('activas')">
            <span class="rep-nav-icon"><i class="fa fa-truck-fast"></i></span>
            <span class="rep-nav-label">Mis entregas</span>
            <span class="rep-nav-badge rep-nav-badge-blue" id="badgeActivas" style="display:none;">0</span>
        </button>
        <button class="rep-nav-item" data-section="asignados" onclick="cambiarSeccion('asignados')">
            <span class="rep-nav-icon"><i class="fa fa-box"></i></span>
            <span class="rep-nav-label">Asignados</span>
            <span class="rep-nav-badge rep-nav-badge-amber" id="badgeAsignados" style="display:none;">0</span>
        </button>
        <button class="rep-nav-item" data-section="historial" onclick="cambiarSeccion('historial')">
            <span class="rep-nav-icon"><i class="fa fa-clock-rotate-left"></i></span>
            <span class="rep-nav-label">Historial</span>
            <span class="rep-nav-badge rep-nav-badge-green" id="badgeHistorial" style="display:none;">0</span>
        </button>
    </nav>

    <div class="rep-sidebar-footer">
        <a href="<?= BASE_URL ?>/views/perfil.php" class="rep-footer-link">
            <i class="fa fa-user-circle"></i> Mi Perfil
        </a>
        <a href="<?= BASE_URL ?>/views/inicio.php" class="rep-footer-link">
            <i class="fa fa-store"></i> Ir a la tienda
        </a>
        <a href="<?= BASE_URL ?>/controllers/AuthController.php?logout=1" class="rep-footer-link rep-footer-link-danger">
            <i class="fa fa-right-from-bracket"></i> Cerrar sesion
        </a>
    </div>
</aside>

<!-- ══ CONTENIDO PRINCIPAL ══════════════════════════ -->
<div class="rep-layout">

    <!-- Topbar movil / desktop -->
    <header class="rep-topbar">
        <button class="rep-menu-toggle" onclick="toggleSidebar()" id="menuToggle">
            <i class="fa fa-bars"></i>
        </button>
        <span class="rep-topbar-title" id="repPageTitle">Pedidos Disponibles</span>
        <button class="rep-refresh-btn" onclick="cargarPanel()" title="Actualizar">
            <i class="fa fa-rotate-right" id="refreshIcon"></i>
        </button>
    </header>

    <!-- Area principal -->
    <main class="rep-main" id="repContenido">

        <!-- DNI warning placeholder -->
        <div id="repDniWarning" style="display:none;" class="rep-dni-warning">
            <i class="fa fa-triangle-exclamation"></i>
            <span>Tu DNI no esta registrado o es invalido. No podras tomar pedidos hasta completarlo en tu
            <a href="<?= BASE_URL ?>/views/perfil.php">perfil</a>.</span>
        </div>

        <!-- Seccion: Disponibles -->
        <section class="rep-section-content active" id="sec-disponibles">
            <div class="rep-section-header">
                <div>
                    <h1 class="rep-section-heading">
                        <i class="fa fa-bell" style="color:#e30613;"></i>
                        Pedidos Disponibles
                    </h1>
                    <p class="rep-section-sub">Sin repartidor asignado — tomalos antes que otro</p>
                </div>
                <div class="rep-auto-refresh">
                    <i class="fa fa-rotate-right fa-spin" style="color:#10b981;font-size:.75rem;"></i>
                    <span>Auto-actualiza cada 60 s</span>
                </div>
            </div>
            <div class="rep-cards-grid" id="listDisponibles">
                <div class="rep-skeleton-wrap">
                    <div class="rep-skeleton-card"></div>
                    <div class="rep-skeleton-card"></div>
                </div>
            </div>
        </section>

        <!-- Seccion: Activas -->
        <section class="rep-section-content" id="sec-activas">
            <div class="rep-section-header">
                <div>
                    <h1 class="rep-section-heading">
                        <i class="fa fa-truck-fast" style="color:#3b82f6;"></i>
                        Mis Entregas Activas
                    </h1>
                    <p class="rep-section-sub">Pedidos que llevas en camino actualmente</p>
                </div>
            </div>
            <div class="rep-cards-grid" id="listActivas">
                <div class="rep-empty-state">
                    <i class="fa fa-truck-fast"></i>
                    <p>Sin entregas activas</p>
                </div>
            </div>
        </section>

        <!-- Seccion: Asignados -->
        <section class="rep-section-content" id="sec-asignados">
            <div class="rep-section-header">
                <div>
                    <h1 class="rep-section-heading">
                        <i class="fa fa-box" style="color:#f59e0b;"></i>
                        Pedidos Asignados
                    </h1>
                    <p class="rep-section-sub">El admin te asigno estos pedidos — acepta o rechaza</p>
                </div>
            </div>
            <div class="rep-cards-grid" id="listAsignados">
                <div class="rep-empty-state">
                    <i class="fa fa-box-open"></i>
                    <p>Sin pedidos asignados</p>
                </div>
            </div>
        </section>

        <!-- Seccion: Historial -->
        <section class="rep-section-content" id="sec-historial">
            <div class="rep-section-header" style="display:flex; justify-content:space-between; flex-wrap:wrap; gap:10px;">
                <div>
                    <h1 class="rep-section-heading">
                        <i class="fa fa-clock-rotate-left" style="color:#10b981;"></i>
                        Historial de Entregas
                    </h1>
                    <p class="rep-section-sub">Pedidos que has entregado exitosamente</p>
                </div>
                <div style="display:flex; align-items:center; gap:10px;">
                    <label for="filtroFechaHistorial" style="font-size:0.85rem; font-weight:600; color:#475569;">Filtrar por fecha:</label>
                    <input type="date" id="filtroFechaHistorial" style="padding: 6px 10px; border:1px solid #cbd5e1; border-radius:6px; font-family:inherit; color:#1e293b;" onchange="renderHistorial(window._historialCache)">
                </div>
            </div>
            <div class="rep-cards-grid" id="listHistorial">
                <div class="rep-empty-state">
                    <i class="fa fa-clock-rotate-left"></i>
                    <p>Sin historial aun</p>
                </div>
            </div>
        </section>

    </main>
</div>

<!-- Overlay movil -->
<div class="rep-overlay" id="repOverlay" onclick="toggleSidebar()"></div>

<script src="<?= BASE_URL ?>/assets/js/repartidor.js?v=<?= @filemtime(__DIR__ . '/../assets/js/repartidor.js') ?: time() ?>"></script>
</body>
</html>



