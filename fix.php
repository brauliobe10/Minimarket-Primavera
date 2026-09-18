<?php
$files = [
    'c:/xampp/htdocs/TecW_PAF/assets/js/repartidor.js',
    'c:/xampp/htdocs/TecW_PAF/assets/js/auth.js',
    'c:/xampp/htdocs/TecW_PAF/assets/js/admin.js'
];
foreach ($files as $f) {
    if(!file_exists($f)) continue;
    $c = file_get_contents($f);
    $map = [
        'NÂ°' => 'N°',
        'Â·' => '·',
        'â€”' => '—',
        'Ã³' => 'ó',
        'Ãº' => 'ú',
        'Ã©' => 'é',
        'Ã­' => 'í',
        'ðŸ’µ' => '💵',
        'ðŸ’³' => '💳',
        'ðŸ“±' => '📱',
        'ðŸ’°' => '💰',
        'aÃºn' => 'aún',
        'CrÃ©dito' => 'Crédito',
        'DÃ©bito' => 'Débito',
        'secciÃ³n' => 'sección',
        'estÃ¡s' => 'estás',
        'sesiÃ³n' => 'sesión',
        'Â¿' => '¿',
        'Ã±' => 'ñ'
    ];
    foreach ($map as $k => $v) {
        $c = str_replace($k, $v, $c);
    }
    file_put_contents($f, $c);
}
echo "OK";
