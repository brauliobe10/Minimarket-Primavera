<?php
$file = 'c:/xampp/htdocs/TecW_PAF/assets/js/repartidor.js';
$content = file_get_contents($file);
$content = preg_replace("/title:\s*'[^']*seguro\?'/u", "title: '¿Estás seguro?'", $content);
$content = preg_replace("/text:\s*'[^']*cancelar esta entrega\?[^']*volver[^']* a estar disponible para otros repartidores\.'/u", "text: '¿Seguro que deseas cancelar esta entrega? El pedido volverá a estar disponible para otros repartidores.'", $content);
file_put_contents($file, $content);
echo "Fixed repartidor.js encoding.\n";
