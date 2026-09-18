<?php

namespace Config;

require_once __DIR__ . '/App.php';

/**
 * Clase de conexión centralizada a la base de datos.
 * - Usa PDO con charset utf8mb4.
 * - Oculta errores de conexión al usuario final (los registra en error_log).
 * - Activa modo de excepciones PDO y emulación de prepared statements desactivada.
 */
class Database {

    private string $host     = 'localhost';
    private string $dbname   = 'bodegadb1';
    private string $user     = 'root';
    private string $password = '';
    private string $charset  = 'utf8mb4';

    /**
     * Crea y devuelve una conexión PDO configurada de forma segura.
     *
     * @return \PDO
     * @throws \RuntimeException Si no puede conectar (sin exponer detalles al cliente).
     */
    public function conectar(): \PDO {
        $dsn = "mysql:host={$this->host};dbname={$this->dbname};charset={$this->charset}";

        $opciones = [
            \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES   => false,   // Prepared statements reales (previene SQLi)
            \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
        ];

        try {
            return new \PDO($dsn, $this->user, $this->password, $opciones);
        } catch (\PDOException $e) {
            // Registrar el error real en el log, sin exponer detalles al usuario
            error_log('[DB] Error de conexión: ' . $e->getMessage());
            throw new \RuntimeException('No se pudo conectar a la base de datos. Por favor intenta más tarde.');
        }
    }
}