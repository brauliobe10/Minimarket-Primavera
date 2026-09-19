# Usuarios y Roles del Sistema

**Proyecto:** Market Primavera (TecW_PAF)
**Documento:** 02 — Usuarios que participan en el sistema
**Fecha:** Septiembre 2026

---

## 1. Introducción

El sistema reconoce **siete roles** definidos en la tabla `rol` de la base de datos y un actor especial **"invitado"** (cliente que compra sin registrarse). Cada rol agrupa un conjunto de permisos que determinan qué paneles y acciones puede ejecutar la persona.

| ID | Rol | Ámbito |
|---|---|---|
| 1 | Administrador | Control total de la plataforma |
| 2 | Cliente | Compra en la tienda y seguimiento propio |
| 3 | Repartidor | Gestión de entregas a domicilio |
| 4 | Almacenero | Control de inventario y entrada de mercadería |
| 5 | Despachador | Preparación y despacho de pedidos |
| 6 | Cajero | Cobros y ventas en mostrador |
| 7 | Soporte | Atención de reclamos y buzón de ayuda |
| — | Invitado | Compra rápida sin registro |

---

## 2. Mapa de actores

```
                 ┌──────────────────────────┐
                 │      INVITADO (Guest)    │  Compra sin cuenta
                 └────────────┬─────────────┘
                              │
                 ┌────────────▼─────────────┐
                 │  CLIENTE (usuario + rol2)│  Cuenta, perfil, historial
                 └────────────┬─────────────┘
                              │
   ┌──────────────────────────┼──────────────────────────┐
   │        TRABAJADORES      │        SOPORTE (rol 7)   │
   │  ┌────────┐ ┌─────────┐  │   Buzón de tickets +     │
   │  │ALMACE- │ │DESPACH. │  │   acceso al panel admin  │
   │  │NERO(4) │ │(5)      │  │   (solo sección Soporte) │
   │  └───┬────┘ └────┬────┘  └────────────┬─────────────┘
   │  ┌─────────┐ ┌───┴───────┐            │
   │  │ CAJERO  │ │ REPARTI-  │            │
   │  │ (6)     │ │ DOR (3)   │            │
   │  └─────────┘ └───────────┘            │
   └────────────────────────────────────────┘
                 ┌──────────────────────────┐
                 │  ADMINISTRADOR (rol 1)   │  Acceso total
                 └──────────────────────────┘
```

---

## 3. Descripción de cada actor

### 3.1 Invitado (sin cuenta)

- Persona que navega y compra **sin registrarse ni iniciar sesión**.
- Puede: ver catálogo, ofertas y detalles; armar carrito (guardado en `localStorage` del navegador); hacer checkout dejando nombre, DNI, teléfono y correo; pagar y recibir su boleta por correo; rastrear su pedido por **DNI + Número de pedido**.
- **No puede:** ver historial persistente, guardar tarjetas, chatear con soporte ni usar los paneles internos.

### 3.2 Cliente (rol 2)

- Usuario registrado con cuenta (credenciales locales y/o cuenta de Google).
- Además de todo lo del invitado, puede: guardar su perfil (DNI validado vía RENIEC), registrar múltiples direcciones de entrega, tener tarjetas guardadas (cifradas), ver su historial completo de pedidos con sus boletas, cancelar pedidos, abrir tickets de atención y mantener conversaciones con el buzón de soporte.
- El carrito se **persiste en base de datos** entre sesiones y dispositivos.

### 3.3 Administrador (rol 1)

- Propietario/gestor del negocio. Acceso total al **panel administrativo** (`views/admin/index.php`).
- Gestiona: dashboard financiero, productos, categorías, promociones, pedidos (cambiar estados), usuarios (activar/desactivar y asignar roles), trabajadores, soporte, stock, ventas diarias, comprobantes, deliveries (asignar repartidores) y el registro de repartidores.
- Es quien configura la operación completa del local.

### 3.4 Repartidor (rol 3)

- Personal de reparto a domicilio registrado en la tabla `repartidor` (con placa de vehículo).
- Usa el **panel de repartidor** (`views/panel_repartidor.php`).
- Puede: ver los deliveries asignados, **aceptar o rechazar** con motivo, **tomar** entregas del pool de pendientes, marcar **en camino** y **entregar**, o cancelar la entrega liberándola al pool.
- Soporta a lo sumo **un reparto activo** ("En camino") a la vez.

### 3.5 Almacenero (rol 4)

- Trabajador de inventario.
- Prioriza tareas de control de stock: registrar **entradas** de mercadería (restock) y consultar la bitácora de `movimiento_stock`.
- Opera desde el **panel del trabajador** (`views/panel_trabajador.php`).

### 3.6 Despachador (rol 5)

- Encargado de preparar/packear los pedidos para salida (a delivery o ventanilla).
- Usa el panel de trabajador para el estado "En preparación" de los pedidos, lo que dispara la preparación física.

### 3.7 Cajero (rol 6)

- Atiende cobros en mostrador y pedidos de recojo en tienda.
- Opera desde el panel de trabajador; verifica pedidos, cobros y entrega a clientes que retiran en el local.

### 3.8 Soporte (rol 7)

- Persona de atención al cliente que gestiona el **buzón de soporte**.
- Accede al panel administrativo, pero **únicamente a la sección "Buzón (Soporte)"**: ve los tickets en orden de prioridad, responde hilos de mensajes y notifica al cliente por correo.
- Resuelve dudas, reclamos y consultas de pedidos.

---

## 4. Guardas y permisos por rol

La aplicación valida el acceso mediante funciones en `controllers/AuthController.php` (`requireAdmin`, `requireSoporteOrAdmin`, `requireRepartidor`, `requireTrabajador`, `requireClienteOrGuest`). Los flags de rol se cargan en sesión al iniciar sesión y se sincronizan al modificar roles.

| Vista / Acción | Invitado | Cliente | Almacenero | Despachador | Cajero | Repartidor | Soporte | Admin |
|---|---|---|---|---|---|---|---|---|
| Catálogo, ofertas, detalle, búsqueda | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Carrito (localStorage) | ✅ | — | — | — | — | — | — | — |
| Carrito en BD | — | ✅ | — | — | — | — | — | ✅* |
| Checkout / pagar | ✅ | ✅ | — | — | — | — | — | — |
| Pago con tarjeta guardada | — | ✅ | — | — | — | — | — | — |
| Comprobante / boleta (B001) | ✅ | ✅ | — | — | — | — | — | ✅ |
| Rastrear pedido | ✅ | ✅ | — | — | — | — | — | ✅ |
| Cancelar pedido propio | ✅(DNI+ID) | ✅ | — | — | — | — | — | ✅ |
| Perfil / direcciones / tarjetas | — | ✅ | — | — | — | — | — | ✅ |
| Soporte (abrir ticket, hilo) | — | ✅ | — | — | — | — | ✅(responder) | ✅ |
| Chatbot / FAQ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Panel trabajador (stock, pedidos) | — | — | ✅ | ✅ | ✅ | — | — | ✅* |
| Panel repartidor (deliveries) | — | — | — | — | — | ✅ | — | — |
| Panel admin — Dashboard | — | — | — | — | — | — | — | ✅ |
| Panel admin — Buzón Soporte | — | — | — | — | — | — | ✅ | ✅ |
| Panel admin — Productos/Categorías/Promos | — | — | — | — | — | — | — | ✅ |
| Panel admin — Pedidos/Estados | — | — | — | — | — | — | — | ✅ |
| Panel admin — Usuarios/Roles | — | — | — | — | — | — | — | ✅ |
| Panel admin — Trabajadores | — | — | — | — | — | — | — | ✅ |
| Panel admin — Stock / Movimientos | — | — | — | — | — | — | — | ✅ |
| Panel admin — Ventas diarias | — | — | — | — | — | — | — | ✅ |
| Panel admin — Comprobantes | — | — | — | — | — | — | — | ✅ |
| Panel admin — Delivery / Repartidores | — | — | — | — | — | — | — | ✅ |

\* Funciones administrativas/con cargos: el admin también puede operar como si fuera cualquier trabajador.

---

## 5. Registro y autenticación de usuarios

- **Registro local:** DNI (8 dígitos, único), correo (único), contraseña ≥ 6 caracteres; se crea usuario + rol Cliente + una dirección por defecto (Chiclayo/Lambayeque).
- **Login local:** validación de contraseña con bcrypt, bloqueo temporal tras 3 intentos fallidos, opción "Recordarme" (cookie 30 días) y redirección según rol.
- **Login con Google (OAuth):** el sistema iguala por correo o `google_uid`; si el usuario es nuevo, deben completarse DNI y teléfono.
- **Recuperación de contraseña:** código de 6 dígitos (validez 15 min) enviado a correo SMTP; validación previa con correo + teléfono.
- **Validación de DNI:** consulta a la API RENIEC desde el perfil y al completar el registro.

---

## 6. Cuadro resumen de acceso a paneles

| Rol | Panel principal |
|---|---|
| Administrador | `views/admin/index.php` (13 secciones) |
| Soporte | `views/admin/index.php` (solo Buzón/Soporte) |
| Almacenero / Despachador / Cajero | `views/panel_trabajador.php` |
| Repartidor | `views/panel_repartidor.php` |
| Cliente / Invitado | Tienda pública + `views/perfil.php` (clientes) |
| Público general | `views/inicio.php`, catálogo, rastreo, chat |

---

*Fin del documento de usuarios.*