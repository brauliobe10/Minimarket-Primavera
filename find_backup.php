<?php
$historyDir = 'C:/Users/quiro/AppData/Roaming/Code/User/History';
if (!is_dir($historyDir)) {
    die("No history dir\n");
}

$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($historyDir));
$bestMatch = null;
$bestSize = 0;
$bestTime = 0;

foreach ($iterator as $file) {
    if ($file->isFile()) {
        $path = $file->getPathname();
        // Check if the file is likely a javascript file and has a reasonable size
        $size = filesize($path);
        if ($size > 80000 && $size < 100000) {
            $content = file_get_contents($path);
            if (strpos($content, 'function badgePedido') !== false && strpos($content, 'cargarDelivery') !== false) {
                $mtime = filemtime($path);
                if ($mtime > $bestTime) {
                    $bestTime = $mtime;
                    $bestMatch = $path;
                    $bestSize = $size;
                }
            }
        }
    }
}

if ($bestMatch) {
    echo "FOUND BACKUP: $bestMatch (Size: $bestSize bytes, Date: " . date('Y-m-d H:i:s', $bestTime) . ")\n";
    // Copy the backup to the project directory to restore it!
    $target = 'C:/xampp/htdocs/TecW_PAF/assets/js/admin.js';
    copy($bestMatch, $target);
    echo "Restored to $target successfully.\n";
} else {
    echo "No backup found in VS Code history.\n";
}
