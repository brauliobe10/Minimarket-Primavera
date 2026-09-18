<?php
$files = [
    'c:/xampp/htdocs/TecW_PAF/assets/js/admin.js',
    'c:/xampp/htdocs/TecW_PAF/assets/js/auth.js',
    'c:/xampp/htdocs/TecW_PAF/assets/js/repartidor.js',
    'c:/xampp/htdocs/TecW_PAF/assets/js/perfil.js',
    'c:/xampp/htdocs/TecW_PAF/assets/js/carrito.js',
    'c:/xampp/htdocs/TecW_PAF/assets/js/categoria.js',
    'c:/xampp/htdocs/TecW_PAF/assets/js/checkout.js',
    'c:/xampp/htdocs/TecW_PAF/assets/js/main.js'
];

foreach ($files as $file) {
    if (!file_exists($file)) continue;
    
    $content = file_get_contents($file);
    $original = $content;
    
    // Fix Edit/Delete buttons (admin.js)
    $content = preg_replace("/btnEditar\.innerHTML\s*=\s*'[^']*Editar';/u", "btnEditar.innerHTML = '✏️ Editar';", $content);
    $content = preg_replace("/btnElim\.innerHTML\s*=\s*'[^']*Eliminar';/u", "btnElim.innerHTML = '🗑️ Eliminar';", $content);
    
    // Fix SweetAlert messages in admin.js
    $content = preg_replace("/`[^`]*Eliminar \"\\$\\{nombre\\}\"\?[^`]* deshacer\.`/u", "`¿Eliminar \"\${nombre}\"? Esta acción no se puede deshacer.`", $content);
    $content = preg_replace("/`[^`]*Eliminar la promoci[^`]* \"\\$\\{nombre\\}\"\?[^`]* descuento\.`/u", "`¿Eliminar la promoción \"\${nombre}\"? Los productos asignados a ella ya no tendrán descuento.`", $content);
    $content = preg_replace("/`[^`]*Eliminar a \"\\$\\{nombre\\}\" de la lista de repartidores\?[^`]* deshacer\.`/u", "`¿Eliminar a \"\${nombre}\" de la lista de repartidores? Esta acción no se puede deshacer.`", $content);
    $content = preg_replace("/'S[^']*,\s*eliminar'/u", "'Sí, eliminar'", $content);
    
    // Fix Modals titles in admin.js
    $content = preg_replace("/'Editar categor[^']*'/u", "'Editar categoría'", $content);
    $content = preg_replace("/'Nueva categor[^']*'/u", "'Nueva categoría'", $content);
    $content = preg_replace("/'Editar promoci[^']*'/u", "'Editar promoción'", $content);
    $content = preg_replace("/'Nueva promoci[^']*'/u", "'Nueva promoción'", $content);
    
    // Fix auth.js
    $content = preg_replace("/'[^']*xito!'/u", "'¡Éxito!'", $content);
    $content = preg_replace("/'[^']*YZ% '/u", "'✅ '", $content);
    $content = preg_replace("/'[^']*s[^']* Corrige:'/u", "'¡Oops! Corrige:'", $content);
    $content = preg_replace("/contrase[^a]*a/u", "contraseña", $content);
    $content = preg_replace("/Est[^s]*s seguro/u", "Estás seguro", $content);
    
    // Fix repartidor.js
    $content = preg_replace("/volver[^ ]* a estar/u", "volverá a estar", $content);
    
    // Generic fixes (safe fallbacks)
    $content = preg_replace("/gr[^a]*fico/u", "gráfico", $content);
    $content = preg_replace("/Gr[^a]*fico/u", "Gráfico", $content);
    $content = preg_replace("/b[^s]*squeda/u", "búsqueda", $content);
    $content = preg_replace("/acci[^n]*n/u", "acción", $content);
    $content = preg_replace("/opci[^n]*n/u", "opción", $content);
    
    if ($content !== $original) {
        file_put_contents($file, $content);
        echo "Fixed: $file\n";
    }
}
echo "Done.\n";
