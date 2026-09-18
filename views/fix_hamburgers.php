<?php
$dir = 'C:/xampp/htdocs/TecW_PAF/views/';
$files = glob($dir . '*.php');

$buttonToAdd = <<<HTML
            <!-- Botón hamburguesa (móvil) -->
            <button class="hamburger-menu" aria-label="Abrir menú" aria-expanded="false" style="margin-right: 15px;">
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
            </button>
HTML;

$buttonToRemove = <<<HTML

HTML;

$buttonToRemove2 = <<<HTML

HTML;

foreach ($files as $file) {
    if (strpos($file, 'panel_repartidor.php') !== false || strpos($file, 'fix.php') !== false) {
        continue;
    }
    
    $content = file_get_contents($file);
    if (strpos($content, '<header class="header"') === false) {
        continue;
    }
    
    // Check if it already has the button on the left
    if (strpos($content, 'style="margin-right: 15px;"') === false) {
        // Insert on left
        $content = preg_replace('/(<div class="header-left">\s*)(<a href="[^"]+" class="logo">)/', "$1" . $buttonToAdd . "\n            $2", $content);
    }
    
    // Remove from right
    $content = str_replace($buttonToRemove, '', $content);
    $content = str_replace($buttonToRemove2, '', $content);
    
    file_put_contents($file, $content);
    echo "Updated " . basename($file) . "\n";
}
echo "Done.\n";
