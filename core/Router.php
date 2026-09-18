<?php

namespace Core;

use Config\Security;

class Router {

    private array $rutas = [];

    /**
     * Registra una ruta GET.
     */
    public function get(string $path, callable|string $handler, array $middlewares = []): void {
        $this->agregarRuta('GET', $path, $handler, $middlewares);
    }

    /**
     * Registra una ruta POST.
     */
    public function post(string $path, callable|string $handler, array $middlewares = []): void {
        $this->agregarRuta('POST', $path, $handler, $middlewares);
    }

    private function agregarRuta(string $metodo, string $path, callable|string $handler, array $middlewares): void {
        $this->rutas[] = [
            'metodo' => $metodo,
            'path' => rtrim($path, '/'),
            'handler' => $handler,
            'middlewares' => $middlewares
        ];
    }

    /**
     * Despacha la solicitud HTTP actual.
     */
    public function despachar(string $metodo, string $uri): void {
        $uriLimpia = rtrim(parse_url($uri, PHP_URL_PATH), '/');

        foreach ($this->rutas as $ruta) {
            if ($ruta['metodo'] === strtoupper($metodo) && $ruta['path'] === $uriLimpia) {

                // Ejecutar Middlewares
                foreach ($ruta['middlewares'] as $middleware) {
                    if ($middleware === 'csrf' && $metodo === 'POST') {
                        $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
                        if (!Security::validarCSRFToken($token)) {
                            http_response_code(403);
                            echo json_encode(['ok' => false, 'mensaje' => 'Error 403: Token CSRF inválido o expirado.']);
                            exit();
                        }
                    }

                    if ($middleware === 'auth') {
                        Security::initSession();
                        if (empty($_SESSION['usuario_id'])) {
                            header('Location: ' . BASE_URL . '/views/inicio.php?login=1');
                            exit();
                        }
                    }

                    if ($middleware === 'admin') {
                        Security::initSession();
                        if (empty($_SESSION['es_admin'])) {
                            header('Location: ' . BASE_URL . '/views/inicio.php');
                            exit();
                        }
                    }
                }

                // Ejecutar Handler
                if (is_callable($ruta['handler'])) {
                    call_user_func($ruta['handler']);
                } elseif (is_string($ruta['handler']) && file_exists($ruta['handler'])) {
                    require $ruta['handler'];
                }
                return;
            }
        }
    }
}
