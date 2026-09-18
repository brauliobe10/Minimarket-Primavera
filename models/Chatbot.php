<?php

namespace Models;

class Chatbot {

    private \PDO $conexion;

    public function __construct(\PDO $conexion) {
        $this->conexion = $conexion;
    }

    /**
     * Obtiene las preguntas recomendadas/sugeridas activas.
     */
    public function obtenerPreguntasRecomendadas(int $limite = 6): array {
        try {
            $sql = "SELECT id_faq, pregunta, categoria FROM chatbot_faq WHERE estado = 1 ORDER BY orden ASC LIMIT :limite";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(':limite', $limite, \PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            // Fallback si la tabla aún no se ha creado
            return [
                ['id_faq' => 1, 'pregunta' => '¿Cuáles son los métodos de pago?'],
                ['id_faq' => 2, 'pregunta' => '¿Cuánto cuesta el delivery?'],
                ['id_faq' => 3, 'pregunta' => '¿Cuáles son los horarios de atención?'],
                ['id_faq' => 4, 'pregunta' => '¿Cómo realizar un pedido?'],
                ['id_faq' => 5, 'pregunta' => '¿Cómo rastreo mi pedido?']
            ];
        }
    }

    /**
     * Procesa la consulta del usuario y encuentra la respuesta más adecuada.
     */
    public function procesarMensaje(string $mensaje): string {
        $mensajeLimpio = mb_strtolower(trim($mensaje));

        if (empty($mensajeLimpio)) {
            return "Por favor escribe una consulta o selecciona una de las preguntas frecuentes.";
        }

        // 0. Rastreo de pedido por DNI o Número directo
        $esSoloNumero = preg_match('/^#?\d{1,8}$/', str_replace(' ', '', $mensajeLimpio));
        $tienePalabraClave = preg_match('/(?:rastrear|seguimiento|rastreo|pedido|orden|estado)\s*#?(\d{1,8})/i', $mensajeLimpio, $matchesKw);

        if ($esSoloNumero || $tienePalabraClave) {
            $numero = $esSoloNumero ? ltrim(str_replace(' ', '', $mensajeLimpio), '#') : $matchesKw[1];
            try {
                if (strlen($numero) === 8) {
                    $sql = "SELECT p.id_pedido, p.estado_pedido, p.total, c.id_comprobante 
                            FROM pedido p 
                            LEFT JOIN comprobante c ON p.id_pedido = c.id_pedido
                            WHERE p.dni_cliente = :num OR p.id_usuario IN (SELECT id_usuario FROM usuario WHERE dni = :num2) 
                            ORDER BY p.fecha_pedido DESC LIMIT 1";
                    $params = [':num' => $numero, ':num2' => $numero];
                } else {
                    $sql = "SELECT p.id_pedido, p.estado_pedido, p.total, c.id_comprobante 
                            FROM pedido p 
                            LEFT JOIN comprobante c ON p.id_pedido = c.id_pedido
                            WHERE p.id_pedido = :num LIMIT 1";
                    $params = [':num' => $numero];
                }
                $stmt = $this->conexion->prepare($sql);
                $stmt->execute($params);
                $pedido = $stmt->fetch(\PDO::FETCH_ASSOC);
                
                if ($pedido) {
                    $extraDni = strlen($numero) === 8 ? '&dni=' . rawurlencode($numero) : '';
                    $resp = "Tu pedido **#{$pedido['id_pedido']}** se encuentra en estado: **{$pedido['estado_pedido']}**.<br>Monto total: S/ {$pedido['total']}.";
                    if (!empty($pedido['id_comprobante'])) {
                        $resp .= "<br><br><a href='" . BASE_URL . "/views/pdf_comprobante.php?id={$pedido['id_pedido']}{$extraDni}' target='_blank' style='display:inline-block; padding:5px 10px; background:#e30613; color:white; border-radius:4px; text-decoration:none; font-size:0.85rem;'>📄 Ver Boleta</a>";
                    }
                    return $resp;
                } else {
                    return "No encontré ningún pedido con ese número. Verifica que el DNI o número de pedido sea correcto.";
                }
            } catch (\PDOException $e) {
                // ignorar y seguir al siguiente filtro
            }
        }

        if (str_contains($mensajeLimpio, 'rastrear') || str_contains($mensajeLimpio, 'seguimiento') || str_contains($mensajeLimpio, 'rastreo')) {
            return "Para rastrear tu pedido por aquí, simplemente escríbeme 'rastrear' seguido de tu DNI o Número de Pedido. Ej: **rastrear 76345180** o **pedido 15**.<br><br>También puedes usar la sección de <a href='" . BASE_URL . "/views/rastrear_pedido.php'>Rastrear Pedido</a> en el menú superior.";
        }

        // 1. Intentar buscar en la tabla chatbot_faq
        try {
            // Coincidencia exacta o parcial por pregunta o palabras clave
            $sql = "SELECT respuesta FROM chatbot_faq 
                    WHERE estado = 1 AND (
                        LOWER(pregunta) LIKE :kw1 OR 
                        LOWER(palabras_clave) LIKE :kw2 OR
                        :kw3 LIKE CONCAT('%', LOWER(pregunta), '%')
                    )
                    ORDER BY id_faq ASC LIMIT 1";
            
            $stmt = $this->conexion->prepare($sql);
            $param = "%" . $mensajeLimpio . "%";
            $stmt->execute([
                ':kw1' => $param,
                ':kw2' => $param,
                ':kw3' => $mensajeLimpio
            ]);

            $faq = $stmt->fetch(\PDO::FETCH_ASSOC);
            if ($faq && !empty($faq['respuesta'])) {
                return $faq['respuesta'];
            }
        } catch (\PDOException $e) {
            // Ignorar y seguir al siguiente nivel de respuesta
        }

        // 2. Si preguntan por productos (ej: "tienen arroz", "precio de manzana", "cuanto cuesta la leche")
        $palabrasClaveProducto = ['precio', 'cuesta', 'tienen', 'venden', 'comprar', 'stock', 'hay', 'producto'];
        $esBusquedaProducto = false;
        foreach ($palabrasClaveProducto as $kw) {
            if (str_contains($mensajeLimpio, $kw)) {
                $esBusquedaProducto = true;
                break;
            }
        }

        if ($esBusquedaProducto) {
            // Extraer posibles términos de búsqueda quitando stop words
            $stopWords = ['precio', 'de', 'la', 'el', 'los', 'las', 'del', 'cuanto', 'cuesta', 'tienen', 'venden', 'hay', 'en', 'un', 'una', 'por', 'favor', 'hola'];
            $terminos = array_diff(explode(' ', $mensajeLimpio), $stopWords);
            $busqueda = trim(implode(' ', $terminos));

            if (!empty($busqueda) && strlen($busqueda) >= 3) {
                try {
                    $sqlProd = "SELECT nombre, precio, stock_actual FROM producto WHERE LOWER(nombre) LIKE :p_search AND estado = 1 LIMIT 3";
                    $stmtProd = $this->conexion->prepare($sqlProd);
                    $stmtProd->execute([':p_search' => "%" . $busqueda . "%"]);
                    $productos = $stmtProd->fetchAll(\PDO::FETCH_ASSOC);

                    if (!empty($productos)) {
                        $resp = "Encontré los siguientes productos relacionados con **" . htmlspecialchars($busqueda) . "**:<br><ul style='margin-top:5px; padding-left:18px;'>";
                        foreach ($productos as $p) {
                            $disponibilidad = $p['stock_actual'] > 0 ? "Disponible (Stock: {$p['stock_actual']})" : "Agotado";
                            $resp .= "<li><b>{$p['nombre']}</b> - S/ " . number_format($p['precio'], 2) . " <i>({$disponibilidad})</i></li>";
                        }
                        $resp .= "</ul><br>¡Puedes buscarlos usando el buscador principal en la parte superior!";
                        return $resp;
                    }
                } catch (\PDOException $e) {
                    // Ignorar errores de tabla producto
                }
            }
        }

        // 3. Saludos comunes
        $saludos = ['hola', 'buenas', 'buenos dias', 'buenas tardes', 'buenas noches', 'saludos', 'que tal'];
        foreach ($saludos as $saludo) {
            if (str_contains($mensajeLimpio, $saludo)) {
                return "¡Hola! 👋 Soy el Asistente Virtual de Market Primavera. ¿En qué te puedo ayudar hoy? Puedes seleccionar una de las preguntas sugeridas o escribirme tu consulta.";
            }
        }

        // 4. Respuesta por defecto
        return "Disculpa, no encontré una respuesta exacta para tu consulta. 😅<br><br>" .
               "Prueba seleccionando una de las **preguntas recomendadas** o ponte en contacto con nuestro equipo a través de la sección de **Atención al Cliente**.";
    }
}
