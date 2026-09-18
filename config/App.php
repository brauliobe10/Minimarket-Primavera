<?php

/**
 * Rutas base del proyecto.
 *
 * Calcula de forma dinámica la URL base de la aplicación a partir de la ruta
 * física del proyecto y del DOCUMENT_ROOT del servidor, de modo que la app
 * funcione desde cualquier subcarpeta del document root (o desde la raíz).
 *
 * Define la constante global BASE_URL, por ejemplo:
 *   '/CALIDAD-DE-SOFTWARE/TecW_PAF'   (proyecto en subcarpeta)
 *   ''                                (proyecto en la raíz del document root)
 */

if (!defined('BASE_URL')) {

    $raizProyecto = str_replace('\\', '/', realpath(__DIR__ . '/..') ?: __DIR__);
    $raizProyecto = rtrim($raizProyecto, '/');

    $baseUrl = '';

    if (isset($_SERVER['DOCUMENT_ROOT']) && $_SERVER['DOCUMENT_ROOT'] !== '') {
        $docRoot = rtrim(str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']), '/');

        $proyNorm = strtolower($raizProyecto);
        $docNorm  = strtolower($docRoot);

        if ($proyNorm !== $docNorm && strpos($proyNorm, $docNorm . '/') === 0) {
            $relativo = substr($raizProyecto, strlen($docRoot));
            $baseUrl  = '/' . ltrim($relativo, '/');
        }
    }

    define('BASE_URL', $baseUrl);
}