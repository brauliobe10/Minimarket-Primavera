# Diseño Conceptual de la Arquitectura del Sistema

**Proyecto:** Market Primavera (TecW_PAF)
**Empresa:** Market Primavera S.A.C. — RUC 20612345678, Chiclayo/Lambayeque, Perú
**Documento:** 01 — Arquitectura del sistema
**Fecha:** Septiembre 2026

---

## 1. Propósito del sistema

Market Primavera es una **tienda virtual con reparto a domicilio (delivery)** para un mercado / bodega peruana. Permite a los clientes navegar el catálogo de productos, armar un carrito, pagar por distintos medios y recibir su pedido en casa o recogerlo en tienda. Además, la plataforma concentra la **operación interna del negocio**: gestión de productos, stock, promociones, pedidos, repartidores, trabajadores, soporte al cliente y reportes de ventas.

El sistema atiende tres grandes frentes:

| Frente | Audiencia | Modelo de acceso |
|---|---|---|
| Tienda pública | Clientes e invitados | Navegador web, sin login |
| Operación interna | Trabajadores, repartidores, soporte | Paneles restringidos por rol |
| Administración | Administrador | Panel de control integral |

---

## 2. Vista general de la arquitectura

### 2.1 Estilo arquitectónico

La aplicación es una **aplicación web clásica en PHP** que sigue un estilo **"controlador de página"** (cada vista se auto-inicializa) con una organización por capas similar al **MVC ligero**:

```
┌─────────────────────────────────────────────────────────────────┐
│                         NAVEGADOR WEB                           │
│          (HTML · CSS · JavaScript · jQuery · Bootstrap)         │
└───────────────────────────────┬─────────────────────────────────┘
                                │  HTTP (GET/POST)  +  fetch()/AJAX
                                ▼
┌─────────────────────────────────────────────────────────────────┐
│                       CAPA DE PRESENTACIÓN                       │
│                     views/  (*.php)  y  views/admin/             │
│        inicio · login · registro · carrito · checkout ·          │
│        perfil · rastreo · chat · paneles · dashboard admin       │
└───────────────────────────────┬─────────────────────────────────┘
                                │
                                ▼
┌─────────────────────────────────────────────────────────────────┐
│                    CAPA DE CONTROL (lógica)                      │
│                  controllers/  y  controllers/admin/             │
│    Reciben ?accion= / POST / JSON · validan sesión y rol ·       │
│    orquestan modelos · responden JSON o redirigen                │
└───────────────────────────────┬─────────────────────────────────┘
                                │
                                ▼
┌─────────────────────────────────────────────────────────────────┐
│                        CAPA DE MODELO                            │
│                        models/  (*.php)                          │
│         Producto · Categoria · CarritoModel · Chatbot ·          │
│         AtencionCliente                                         │
└───────────────────────────────┬─────────────────────────────────┘
                                │  PDO (prepared statements)
                                ▼
┌─────────────────────────────────────────────────────────────────┐
│                     CAPA DE DATOS                                │
│           MariaDB/MySQL · base bodegadb1  (23 tablas)            │
└─────────────────────────────────────────────────────────────────┘
```

### 2.2 Capas y responsabilidades

| Capa | Carpeta | Responsabilidad |
|---|---|---|
| Presentación | `views/`, `views/admin/` | Renderizado de páginas HTML, formularios, llamadas AJAX al backend |
| Control | `controllers/`, `controllers/admin/` | Orquestan la lógica: autenticación, checkout, stock, reparto, reportes |
| Modelo | `models/` | Acceso a datos y reglas de negocio (Producto, Categoria, Carrito, Chatbot, Soporte) |
| Configuración | `config/` | Base URL, conexión PDO, seguridad, encriptación, correo |
| Núcleo | `core/`, `lib/` | Router/Container (esqueleto) y librería PHPMailer |
| Datos | `bodegadb1.sql` | Esquema y datos semilla de la base de datos |

---

## 3. Componentes tecnológicos

| Componente | Tecnología | Uso |
|---|---|---|
| Lenguaje | PHP 8 (>= 8 por `str_contains`) | Lógica de servidor, sin frameworks |
| Base de datos | MariaDB/MySQL (`bodegadb1`) | Persistencia, 23 tablas |
| Frontend | HTML5, CSS3, JavaScript vanilla, jQuery, Bootstrap 5 | Interfaz de usuario |
| Gráficos | Chart.js | Dashboard: ingresos 7 días, estados de pedidos |
| Correo | PHPMailer (`lib/PHPMailer`) + SMTP Gmail | Comprobantes y notificaciones |
| Autenticación | Sesiones PHP + OAuth Google | Login local y con cuenta Google |
| API externa | API RENIEC (Codart) | Validación de DNI en registro/perfil |
| Encriptación | OpenSSL AES‑256‑CBC | Tarjetas de crédito guardadas |
| Servidor | Apache/XAMPP (entorno de desarrollo) | Ejecución de la aplicación |

---

## 4. Punto de entrada y flujo de una petición

### 4.1 Arranque

1. `index.php` redirige a `views/inicio.php` (tienda principal).
2. Cada `views/*.php` incluye sus propios controladores/modelos/conexión según la página.
3. `config/App.php` calcula `BASE_URL` dinámicamente según la ubicación del proyecto en el `DOCUMENT_ROOT` (funciona en cualquier subcarpeta).

### 4.2 Flujo de una acción (ej.: procesar pedido)

```
Inicio → carrito.php → checkout.php → ProcesarPedidoController.php
   │
   ├─ Validación: sesión/rol (cliente o invitado) + token CSRF cuando aplica
   ├─ Transacción PDO: pago → comprobante → pedido → detalle_pedido → delivery
   ├─ Regla de negocio: descuento de stock + registro en movimiento_stock
   └─ Salida: envío de comprobante por correo + redirección a comprobante.php
```

### 4.3 Patrón de comunicación

- Navegación tradicional: formularios HTML + redirección a vistas.
- Operaciones dinámicas: `fetch()` de JavaScript hacia controladores que responden **JSON** (`Content-Type: application/json`).
- Los controladores reciben la acción mediante `$_GET['accion']` o `$_POST['accion']`.

---

## 5. Modelo de datos (23 tablas)

```
usuario ──< usuario_rol >── rol              (7 roles)
usuario ──< tarjeta_guardada                 (tarjetas cifradas)
usuario ──< direccion                        (direcciones de entrega)
usuario ──< carrito ──< detalle_carrito >── producto
usuario ──< pedido ──< detalle_pedido >── producto
pedido ──< pago ── metodo_pago
pedido │──1── comprobante                   (serie B001, IGV 18%)
pedido ──< delivery ── repartidor            (estados de reparto)
usuario ──< trabajador                      (cargos operativos)
producto >── categoria
promocion >─ producto_promocion ─< producto
producto ──< movimiento_stock
usuario ──< soporte_cliente ──< soporte_mensajes
chatbot_faq                                (preguntas frecuentes)

Leyenda: ──<  uno-a-muchos · >──  muchos-a-uno · >─  N-М
```

### 5.1 Principales entidades

| Entidad | Descripción |
|---|---|
| `usuario` | Clientes y personal. Contiene credenciales, DNI único, estado y datos de verificación |
| `rol` | Administrador, Cliente, Repartidor, Almacenero, Despachador, Cajero, Soporte |
| `producto` | Catálogo: nombre, precio, stock, stock mínimo (5), código de barras, vencimiento |
| `categoria` | Agrupación por familia (Comida, Bebidas, Limpieza, Cuidado personal) con imagen |
| `promocion` / `producto_promocion` | Descuentos por porcentaje con vigencia, vinculados a productos |
| `pedido` | Subtotal, descuento, total, estado, tipo de entrega, datos de contacto del cliente |
| `detalle_pedido` | Líneas del pedido con precio capturado en el momento (snapshot) |
| `pago` | Monto, monto recibido, vuelto, método de pago |
| `comprobante` | Boleta (B001) con numeración correlativa y cálculo de IGV 18% |
| `delivery` | Reparto: dirección, costo S/ 5.00, repartidor asignado, hora salida/entrega |
| `repartidor` | Personal de reparto: datos, vehículo (placa) y estado |
| `movimiento_stock` | Bitácora de ENTRADAS/SALIDAS de stock con motivo |
| `soporte_cliente` / `soporte_mensajes` | Tickets de atención con hilo de mensajes cliente-administrador |
| `trabajador` | Ficha laboral: cargo, turno, sueldo, fecha de ingreso |
| `chatbot_faq` | Base de conocimiento del asistente virtual |
| `carrito` / `detalle_carrito` | Carrito persistido para usuarios registrados |
| `metodo_pago` | Efectivo, Tarjeta, Yape/Plin |
| `tarjeta_guardada` | Tarjetas guardadas con número cifrado (AES‑256‑CBC) |

### 5.2 Integridad referencial

- FKs con `ON DELETE CASCADE` para dependencias (carrito, direcciones, detalles, comprobante, soporte).
- `ON DELETE SET NULL` en relación paciente/operario (trabajador, repartidor) para preservar el histórico de pedidos.
- Índices en columnas de filtro frecuente (correo, DNI, estado, fechas).

---

## 6. Aspectos de seguridad

| Mecanismo | Detalle |
|---|---|
| Sesiones | `Security::initSession()`: cookies HttpOnly + SameSite=Lax, timeout de inactividad de 20 minutos |
| CSRF | Validación de token en login, registro y completar cuenta Google |
| Contraseñas | `password_hash()` (bcrypt) + `password_verify()` |
| Inyección SQL | PDO con *prepared statements* y `EMULATE_PREPARES=false` |
| Captcha de fuerza bruta | Bloqueo temporal de login tras 3 intentos fallidos |
| Recordar sesión | Cookie `remember_me` con token (30 días) |
| Encriptación | Tarjetas cifradas con AES‑256‑CBC |
| Sanitización | Filtrado de entradas (`Security::sanitizeInput`) y validación de DNI (8 dígitos) |
| Recuperación de acceso | Código de 6 dígitos temporal (15 min) enviado por SMTP |
| Borrado lógico | Productos, usuarios y repartidores se desactivan con `estado` en lugar de eliminar |

> **Hallazgos a corregir (anotación de arquitectura):** existen claves en texto plano (AES, SMTP Gmail, token RENIEC), el login con Google confía en datos enviados por el cliente, `remember_me` no verifica el token contra la BD y CSRF solo cubre las acciones de autenticación. Estas deudas técnicas deben tratarse antes de pasar a producción.

---

## 7. Requisitos operativos

- **Servidor web:** Apache con módulo PHP 8+.
- **Base de datos:** MariaDB 10.x / MySQL 8.x.
- **Carga inicial:** importar `bodegadb1.sql` (esquema + catálogo + datos semilla).
- **Correo:** credenciales SMTP de Gmail en `config/MailHelper.php`.
- **Estructura de carpetas:** no se usa Composer; las clases se cargan por `require_once` y *namespaces*.

---

## 8. Decisiones de diseño relevantes

1. **Cero dependencias de terceros** (sin frameworks): la app es autocontenida y fácil de desplegar en XAMPP.
2. **Soporte para invitados:** se permite comprar sin registro (los pedidos guardan datos de contacto en el propio pedido).
3. **Carrito dual:** registrados usan tablas `carrito/detalle_carrito`; invitados usan `localStorage`.
4. **Snapshot de precios:** `detalle_pedido` guarda el precio al momento de comprar, independiente de cambios posteriores del catálogo.
5. **Estados de reparto explícitos:** Asignado → En camino → Entregado (o rechazo con motivo que libera el pedido al pool).
6. **Módulos desacoplados:** la tienda pública no depende del panel administrativo; ambos se comunican por base de datos.

---

*Fin del documento de arquitectura.*