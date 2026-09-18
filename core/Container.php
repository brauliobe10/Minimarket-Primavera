<?php

namespace Core;

use Config\Database;
use Models\Producto;
use Models\Categoria;
use Models\Chatbot;

class Container {

    private static array $instancias = [];
    private static ?\PDO $conexionPDO = null;

    /**
     * Obtiene la conexión PDO singleton a la Base de Datos.
     */
    public static function getDatabase(): \PDO {
        if (self::$conexionPDO === null) {
            $db = new Database();
            self::$conexionPDO = $db->conectar();
        }
        return self::$conexionPDO;
    }

    /**
     * Registra o resuelve una instancia de servicio/modelo (Inyección de Dependencias).
     */
    public static function get(string $clase) {
        if (!isset(self::$instancias[$clase])) {
            $pdo = self::getDatabase();

            switch ($clase) {
                case Producto::class:
                case 'Models\Producto':
                    self::$instancias[$clase] = new Producto($pdo);
                    break;

                case Categoria::class:
                case 'Models\Categoria':
                    self::$instancias[$clase] = new Categoria($pdo);
                    break;

                case Chatbot::class:
                case 'Models\Chatbot':
                    self::$instancias[$clase] = new Chatbot($pdo);
                    break;

                default:
                    if (class_exists($clase)) {
                        self::$instancias[$clase] = new $clase($pdo);
                    } else {
                        throw new \Exception("Clase {$clase} no registrada en el contenedor de dependencias.");
                    }
                    break;
            }
        }

        return self::$instancias[$clase];
    }
}
