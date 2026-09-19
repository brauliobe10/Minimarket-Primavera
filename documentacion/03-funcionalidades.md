# Funcionalidades del Sistema

**Proyecto:** Market Primavera (TecW_PAF)
**Documento:** 03 — Cómo funciona el sistema (funcionalidades)
**Fecha:** Septiembre 2026

---

## 1. Introducción

Este documento describe los **módulos funcionales** del sistema ordenados por área: tienda pública, cuenta del cliente, compra y pago, seguimiento, atención al cliente, operación interna (trabajadores, repartidores) y administración.

```
┌────────────────────────────────────────────────────────────────┐
│                    MARKET PRIMAVERA                          │
├───────────────┬───────────────────────────────┬───────────────┤
│   TIENDA      │        OPERACIÓN              │  ADMINISTRA-  │
│   PÚBLICA     │                               │  CIÓN         │
│  · Catálogo   │  · Panel trabajador           │  · Dashboard  │
│  · Ofertas    │  · Panel repartidor           │  · Catálogo   │
│  · Búsqueda   │  · Flujo de reparto           │  · Pedidos    │
│  · Carrito    │  · Stock/almacén              │  · Usuarios   │
│  · Checkout   │  · Soporte (buzón)            │  · Ventas     │
│  · Rastreo    │                               │  · Delivery   │
│  · Chatbot    │                               │  · Comprob.   │
└───────────────┴───────────────────────────────┴───────────────┘
```

---

## 2. Módulo de la tienda pública

### 2.1 Página de inicio
- Hero de bienvenida y acceso directo a login/registro de la tienda.
- **Productos destacados:** los 8 más vendidos según el histórico de `detalle_pedido` (Modelo `Producto::obtenerDestacados`).
- **Productos en oferta:** los que tienen `promocion` activa dentro de sus fechas de vigencia, con descuento calculado (Modelo `Producto::obtenerEnOferta`).
- Familias de categorías para navegación por grupos (Comida, Bebidas, Limpieza del hogar, Cuidado personal).

### 2.2 Catálogo por categoría (`categoria.php`)
- Listado/grid de productos por categoría con **interrumpible vista lista o cuadrícula**; la preferencia se guarda en cookie (`user_prefs`).
- Los productos muestran precio, descuento si aplica, imagen, stock y botón de agregar al carrito.

### 2.3 Búsqueda y detalle de producto
- Búsqueda libre por nombre (`BuscarController` → `Producto::buscarPorNombre`).
- Ficha de detalle con descripción, disponibilidad y fecha de vencimiento visible.

### 2.4 Chatbot "Asistente Primavera" (`chatbot.php`)
- Botón flotante siempre disponible.
- **Preguntas frecuentes recomendadas** provenientes de `chatbot_faq` (medios de pago, costo de delivery S/ 5.00, horario 7:00–22:00, cómo pedir, cómo rastrear, direcciones, reclamos).
- Motor de respuestas por **palabras clave** (Modelo `Chatbot::procesarMensaje`).
- También rastrea pedidos escribiendo su número/DNI en el chat y deriva a Atención al Cliente si no entiende.

---

## 3. Carrito de compras

| Escenario | Comportamiento |
|---|---|
| Invitado | Carrito en `localStorage` del navegador (no persiste entre dispositivos) |
| Cliente registrado | Carrito persistido en BD (`carrito` + `detalle_carrito`), una carrito activo por usuario |

- Agregar, actualizar cantidades y quitar productos (`CarritoController`, `CarritoModel`).
- Resumen con subtotales y montos actualizados en tiempo real.
- Al iniciar sesión el carrito se mantiene consistente (un solo carrito activo por usuario).

---

## 4. Proceso de compra (Checkout)

### 4.1 Pantalla de checkout (`checkout.php`)
- Verificación de sesión: cliente o invitado (`requireClienteOrGuest`).
- **Datos de entrega:** elegir dirección guardada (clientes) o llenar dirección manual (invitados).
- **Tipo de entrega:**
  - **Delivery a domicilio:** costo S/ 5.00 dentro de Chiclayo/metropolitano.
  - **Recojo en tienda:** sin cargo de delivery.
- **Métodos de pago** (tabla `metodo_pago`): **Efectivo** (cobro contra entrega), **Tarjeta** (con opción de guardar tarjeta cifrada) y **Yape/Plin**.

### 4.2 Procesamiento del pedido (`ProcesarPedidoController`)
Ejecuta una **transacción de base de datos atómica**:

```
1. Registrar PAGO        (monto, método, monto recibido, vuelto)
2. Emitir COMPROBANTE    (Boleta B001, correlativo, subtotal + IGV 18% = total)
3. Crear PEDIDO          (estado "Pendiente", tipo de entrega, datos cliente)
4. Registrar DETALLE     (líneas con precio snapshot)
5. Si es delivery →      crear DELIVERY (asignado al pool, costo S/ 5.00)
6. Descontar STOCK       (menos cantidad vendida)
7. Registrar movimiento  (SALIDA en movimiento_stock, motivo "Pedido #N")
8. Enviar por correo     (comprobante HTML al correo del cliente)
```

- Si la dirección es de la base de datos, se usa la **predeterminada** del cliente.
- Ante cualquier error, la transacción revierte (nada de pagos sin stock).

### 4.3 Comprobante (`comprobante.php` / `pdf_comprobante.php`)
- Boleta electrónica con datos de la empresa (RUC 20612345678, dirección, teléfono), correlativo B001-000000XX, IGV 18% y QR.
- Vista **imprimible/PDF**; accesible al propietario del pedido, administradores, trabajadores habilitados o invitado con DNI coincidente.

---

## 5. Seguimiento y gestión de pedidos

### 5.1 Rastreo público (`rastrear_pedido.php`)
- Cualquier persona puede ingresar **DNI + ID/Número de pedido** y ver estado y detalle.
- Indica si el pedido está registrado (`registrado` flag) y su historial de estados.

### 5.2 Estados de pedido (flujo)
```
Pendiente → En preparación → En camino → Entregado
              │
              └── Cancelado  (con motivo de cancelación y reposición de stock)
```

### 5.3 Cancelación
- El cliente (dueño del pedido) o el admin puede cancelar.
- Al cancelar se restaura el stock y se registra `movimiento_stock` tipo ENTRADA con motivo "Cancelación Pedido #N".

### 5.4 Historial del cliente
- En `perfil.php`: lista completa de pedidos propios, boletas, estado actual y acceso al comprobante.

---

## 6. Cuenta del cliente

### 6.1 Registro y autenticación
- **Registro local:** DNI 8 dígitos (único), correo (único), contraseña mínimo 6 caracteres; autologin al terminar.
- **Registro con Google:** completa DNI y teléfono cuando la cuenta Google es nueva.
- **Login**: bloqueo temporal (15 s) tras 3 intentos fallidos; opción "Recordarme" (30 días).
- **Recuperar contraseña:** verificación correo+teléfono → código 6 dígitos por SMTP (15 min) → nuevo password con bcrypt.

### 6.2 Perfil (`perfil.php`)
- Datos personales: nombres, apellidos, DNI (bloqueado una vez vinculado, validable contra RENIEC), teléfono.
- Cambio de contraseña.
- **Direcciones de entrega:** varias, una marcada como predeterminada.
- **Tarjetas guardadas:** números cifrados (AES‑256‑CBC), se muestra solo los últimos 4 dígitos.
- **Historial de pedidos y comprobantes.**

### 6.3 Soporte al cliente (Atención al Cliente)
- Formulario de contacto con asunto (≤ 200 caracteres), mensaje (≤ 3000) y aceptación de términos.
- **Buzón de mensajes:** hilo de conversación cliente ↔ admin dentro del perfil.
- Acceso a WhatsApp (wa.me/51979798082) como canal alternativo.

---

## 7. Operación interna — Panel del trabajador (`panel_trabajador.php`)

Para **almacenero, despachador y cajero** (y el admin con rol operativo):

- Ficha del trabajador (cargo, turno, sueldo, fecha de ingreso, estado) — el admin ve una ficha virtual.
- **Control de stock:** registrar **entradas** de mercadería (restock) con `movimiento_stock`.
- **Pedidos operativos:** consultar y mover pedidos a "En preparación".

---

## 8. Panel del repartidor (`panel_repartidor.php`)

Flujo de entregas a domicilio:

```
                     ┌──────────────────────────────┐
                     │  DELIVERY asignado / pool    │
                     └──────────┬───────────────────┘
          ┌─────────────────────┼─────────────────────┐
          ▼                     ▼                     ▼
   ACEPTAR (→ En camino)   TOMAR del pool      RECHAZAR
   pedido → "En camino"    (autoselección)     motiva liberación
          │                                       │
          ▼                                       ▼
   ENTREGAR  → delivery "Entregado"        CANCELAR ENTREGA
   + hora_entrega + pedido "Entregado"     (regresa al pool)
```

- Con **un solo reparto activo** a la vez.
- Validación de DNI del repartidor al tomar entregas.
- Registro de hora de salida y hora de entrega para trazabilidad.

---

## 9. Panel administrativo (`views/admin/index.php`)

Panel con 13 módulos accesibles por pestañas (SPA con JS + Chart.js). El soporte solo ve la sección de buzón.

| Módulo | Funcionalidades |
|---|---|
| **Dashboard** | Totales: pedidos, ventas del día, usuarios activos, productos activos, pedidos pendientes; gráficas "Ingresos 7 días" (Chart.js) y "Estado de pedidos"; últimos 8 pedidos |
| **Productos** | Crear, editar y desactivar productos (nombre, precio, stock, stock mínimo, categoría, código de barras, vencimiento, imagen) |
| **Categorías** | CRUD de categorías con paginación (15/página); borrado protegido contra productos asociados |
| **Promociones** | CRUD con porcentaje de descuento y vigencia; vincula/desvincula productos |
| **Pedidos** | Cambio de estado con lista blanca (Pendiente, En preparación, En camino, Entregado, Cancelado); sincroniza delivery al entregar/cancelar |
| **Usuarios** | Activar/desactivar usuarios y **asignar roles** |
| **Trabajadores** | Alta de fichas laborales con cargo (Admin, Repartidor, Almacenero, Despachador, Cajero) y turno |
| **Buzón (Soporte)** | Listado de tickets por prioridad, hilo de mensajes, respuesta con notificación por correo |
| **Stock** | Historial de `movimiento_stock` con filtro por Entrada/Salida y rango de fechas (máx. 200 registros) |
| **Ventas diarias** | Reporte por rango de fechas con filtro de estado de pedido |
| **Comprobantes** | Listado de todas las boletas emitidas en orden descendente |
| **Delivery** | Asignar repartidor a pedidos elegibles, cancelar asignación, cambiar estado, crear delivery (S/ 5.00) |
| **Repartidores** | CRUD: asignar/quitar rol de repartidor, actualizar datos, activar/desactivar, eliminar (con protección si hay reparto en curso) |

---

## 10. Notificaciones y mensajería

| Canal | Uso |
|---|---|
| **Correo SMTP (PHPMailer)** | Comprobante de compra (Boleta HTML B001), confirmación de recuperación de contraseña con código, notificación de respuesta del buzón de soporte, aviso de cambio de estado |
| **WhatsApp** | Canal complementario de atención al cliente (número del negocio) |
| **Chatbot** | Resolución inmediata de dudas frecuentes + derivación a soporte |
| **Buzón interno** | Puede ser hilo de mensajes (cliente ↔ soporte) reutilizable |

---

## 11. Reglas de negocio relevantes

1. **Stock mínimo de reabastecimiento:** `producto.stock_minimo = 5` por defecto.
2. **Costo de delivery:** S/ 5.00 para Delivery dentro de Chiclayo; el recojo en tienda no lo paga.
3. **IGV:** 18% aplicado en el comprobante (subtotal + IGV = total).
4. **Numeración:** serie B001 con número correlativo por boleta.
5. **Un pedido activo de carrito por usuario** (carrito ACTIVE reutilizado).
6. **Precio snapshot** en `detalle_pedido`: el precio final no cambia aunque el catálogo cambie después.
7. **Repartidor:** solo un delivery "En camino" por vez; rechazos visible al pool con motivo.
8. **Cancelación restaura stock** y deja registro en el historial de movimientos.
9. **Ventas del día/informes** excluyen pedidos cancelados.
10. **Borrado lógico** (estado 0/1) para productos, usuarios y repartidores.

---

*Fin del documento de funcionalidades.*