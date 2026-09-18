<?php
$dir = 'C:/xampp/htdocs/TecW_PAF/views/';
$files = [
    'acercaDe.php',
    'AtencionCliente.php',
    'categoria.php',
    'checkout.php',
    'rastrear_pedido.php',
    'carrito.php',
    'politicas.php'
];

$inicio = file_get_contents($dir . 'inicio.php');

$startSidebar = strpos($inicio, '<!-- Menú hamburguesa (móvil lateral) -->');
if ($startSidebar === false) {
    die("Could not find sidebar in inicio.php\n");
}
$endSidebar = strpos($inicio, '</nav>', $startSidebar);
if ($endSidebar === false) {
    die("Could not find end of sidebar in inicio.php\n");
}
$endSidebar += 6; // include </nav>

$sidebarHTML = substr($inicio, $startSidebar, $endSidebar - $startSidebar);

foreach ($files as $f) {
    $path = $dir . $f;
    if (!file_exists($path)) {
        echo "File not found: $f\n";
        continue;
    }
    
    $content = file_get_contents($path);
    
    if (strpos($content, '<nav class="menu-hamburguesa"') !== false) {
        echo "Skipping $f, already has sidebar\n";
        continue;
    }
    
    // Find where to insert. First try after </nav> of .main-nav
    $insertPos = false;
    $mainNavStart = strpos($content, '<nav class="main-nav"');
    if ($mainNavStart !== false) {
        $mainNavEnd = strpos($content, '</nav>', $mainNavStart);
        if ($mainNavEnd !== false) {
            $insertPos = $mainNavEnd + 6;
        }
    } else {
        // Try after </header>
        $headerEnd = strpos($content, '</header>');
        if ($headerEnd !== false) {
            $insertPos = $headerEnd + 9;
        }
    }
    
    if ($insertPos !== false) {
        $newContent = substr($content, 0, $insertPos) . "\n\n    " . $sidebarHTML . substr($content, $insertPos);
        file_put_contents($path, $newContent);
        echo "Injected sidebar into $f\n";
    } else {
        echo "Could not find insertion point in $f\n";
    }
}
echo "Done.\n";
