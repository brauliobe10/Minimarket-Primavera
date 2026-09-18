<?php
$inicio = file_get_contents('C:/xampp/htdocs/TecW_PAF/views/inicio.php');
$perfil = file_get_contents('C:/xampp/htdocs/TecW_PAF/views/perfil.php');
$startStr = '<!-- Encabezado principal -->';
$endStr = '<!-- Menú hamburguesa (móvil lateral) -->';
$start = strpos($inicio, $startStr);
$end = strpos($inicio, $endStr);
$block = substr($inicio, $start, $end - $start);

$pStart = strpos($perfil, $startStr);
$pEnd = strpos($perfil, $endStr);
if($pStart !== false && $pEnd !== false) {
    $new = substr($perfil, 0, $pStart) . $block . substr($perfil, $pEnd);
    file_put_contents('C:/xampp/htdocs/TecW_PAF/views/perfil.php', $new);
    echo "Success\n";
} else {
    echo "Failed\n";
}
