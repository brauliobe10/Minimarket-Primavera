<?php
$file = 'c:/xampp/htdocs/TecW_PAF/assets/js/admin.js';
$content = file_get_contents($file);

$bad = "function búsqueda server-side)\r\nâ• â• â• â• â• â• â• â• â• â• â• â• â• â• â• â• â• â• â• â• â• â• â• â• â• â• â• â• â• â• â• â• â• â• â• â• â• â• â• â• â• â• â• â• â• â•  */";
// I will just find the exact offset and use substring replace, or use a flexible regex.
$content = preg_replace("/function b.squeda server-side\).*\*\//su", "function badgePedido(e) {
    return { 'Pendiente':'badge-yellow','En preparación':'badge-orange',
             'En camino':'badge-blue','Entregado':'badge-green','Cancelado':'badge-red' }[e] || 'badge-gray';
}

/* ══════════════════════════════════════════════════════════════════════════════
   2. PRODUCTOS (Paginación + Búsqueda server-side)
══════════════════════════════════════════════════════════════════════════════ */", $content);

file_put_contents($file, $content);
echo "Fixed.\n";
