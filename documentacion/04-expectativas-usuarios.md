# Expectativas de los Usuarios del Sistema

**Proyecto:** Market Primavera (TecW_PAF)
**Documento:** 04 — Lo que esperan los usuarios (inferencia a partir del sistema)
**Fecha:** Septiembre 2026

---

## 1. Propósito de este documento

Este documento **infiere** las expectativas de cada tipo de usuario a partir de las funcionalidades implementadas y del comportamiento observado del sistema. Sirve como base para validar criterios de aceptación, mejoras prioritarias y requisitos de calidad.

---

## 2. Expectativas generales (todos los usuarios)

### 2.1 Disponibilidad y acceso
- Espera que la tienda **funcione 24/7** y no esté "caída" en horarios de venta (el negocio opera de 7:00 a 22:00).
- Espera que cargue rápido en teléfonos móviles (la mayoría ingresará desde celular).
- Espera funcionar en cualquier navegador moderno sin instalaciones.

### 2.2 Confianza y seguridad
- Espera que su **información personal (DNI, correo, tarjetas) esté protegida**.
- Espera recibir una **boleta válida** con RUC, IGV y datos legales de la empresa.
- Espera que el monto cobrado coincida con el mostrado.
- Espera que sus contraseñas sean seguras y recuperables.

### 2.3 Soporte
- Espera que alguien **responda sus dudas o reclamos** y le dé seguimiento.
- Espera canales de contacto alternativos (correo, WhatsApp).

### 2.4 Claridad
- Espera entender el **estado de su pedido en todo momento** y qué sigue después.
- Espera conocer de antemano el costo de delivery y los tiempos estimados.
- Espera precios transparentes (descuentos visibles antes de pagar).

---

## 3. Expectativas por actor

### 3.1 Invitado (compra sin registro)

| Expectativa | Sustento en el sistema |
|---|---|
| Comprar sin crear cuenta ni complicarse | Flujo invitado con checkout directo |
| No perder el carrito durante la navegación | Carrito en `localStorage` mientras dure la sesión del navegador |
| Pagar rápido (efectivo, tarjeta o Yape/Plin) | 3 métodos de pago en checkout |
| Saber dónde está su pedido sin cuenta | Rastreo público por DNI + número de pedido |
| Recibir su boleta por correo | Comprobante B001 enviado por SMTP |
| Poder cancelar si cambia de opinión | Cancelación con motivo y restauración de stock |

### 3.2 Cliente registrado

| Expectativa | Sustento en el sistema |
|---|---|
| Recordar mis datos y no repetir formularios | Perfil con DNI, teléfono y direcciones guardadas |
| Ver mi historial completo de compras | Historial de pedidos con boletas en `perfil.php` |
| Guardar varias direcciones y elegir | Direcciones múltiples con una predeterminada |
| Guardar mi tarjeta para compras rápidas | Tarjetas cifradas con últimos 4 dígitos |
| Rastrear mi pedido y ver cada cambio de estado | Estados Pendiente → En preparación → En camino → Entregado |
| Entrar rápido con mi correo/cuenta Google | Login local + OAuth Google |
| Pedir ayuda desde mi cuenta | Tickets y buzón de mensajes con hilo de conversación |
| Recuperar mi contraseña si me olvido | Código de 6 dígitos por correo (15 min) |
| Tener mis promociones y ofertas visibles | Módulo de ofertas con descuento calculado |

### 3.3 Repartidor

| Expectativa | Sustento en el sistema |
|---|---|
| Ver qué entregas tengo asignadas | Listado de deliveries en panel del repartidor |
| Aceptar o rechazar entregas con libertad y explicación | Aceptar/rechazar con motivo de rechazo |
| Tomar entregas disponibles cuando quiera | Autoservicio del pool "Pendiente" |
| No saturen mi jornada | Límite de un reparto activo a la vez |
| Registrar la entrega fácilmente | Botones En camino → Entregado con hora registrada |
| Que los pedidos traigan datos claros de envío | Dirección + referencia del cliente en cada delivery |

### 3.4 Almacenero / Despachador / Cajero

| Expectativa | Sustento en el sistema |
|---|---|
| Registrar la mercadería que entra rápido | Restock + bitácora de movimientos (ENTRADA) |
| Conocer el stock disponible en tiempo real | Stock visible en producto/panel |
| Saber qué pedidos preparar o cobrar | Pedidos con estados en panel del trabajador |
| Evitar vender productos agotados | Descuento de stock al procesar pedido y stock mínimo sugerido |
| Control de horarios y turnos | Ficha laboral con cargo, turno y sueldo |

### 3.5 Administrador

| Expectativa | Sustento en el sistema |
|---|---|
| Ver la salud del negocio de un vistazo | Dashboard con ventas del día, pedidos y usuarios |
| Analizar la tendencia de ingresos | Gráfica "Ingresos 7 días" con Chart.js |
| Controlar el catálogo completo | CRUD de productos, categorías y promociones |
| Saber quién compra y habilitar/deshabilitar | Gestión de usuarios y asignación de roles |
| Gestionar al personal | Fichas de trabajadores y repartidores |
| Controlar el reparto | Módulo Delivery: asignar, cancelar y cambiar estados |
| Emitir comprobantes sin errores | Numeración correlativa B001 + IGV automatizado |
| Rendir cuentas | Reporte de ventas diarias y listado de comprobantes |
| Atender reclamos de forma ordenada | Buzón de soporte con priorización y notificaciones |
| No perder inventario | Historial de movimientos de stock con motivos |

### 3.6 Soporte (rol 7)

| Expectativa | Sustento en el sistema |
|---|---|
| Ver los tickets nuevo primero | Orden por estado (pendiente primero) y fecha desc |
| Dar seguimiento a cada caso | Hilo de mensajes por ticket |
| Responder y avisar al cliente | Respuesta + correo de notificación al cliente |
| Consultar datos del cliente | Ticket con datos del usuario y su correo |

---

## 4. Expectativas de calidad (requisitos no funcionales)

### 4.1 Usabilidad
- Que la compra se complete en **pocos pasos** (catálogo → carrito → pagar).
- Interfaz clara en español, sin tecnicismos, con mensajes de error comprensibles.
- Diseño responsivo: correcto en celular, tablet y desktop.

### 4.2 Fiabilidad
- Que un pedido exitoso **nunca quede incompleto** (transacciones atómicas).
- Que los estados de pedido y stock sean consistentes tras cancelaciones.
- **Respaldos:** el proyecto incluye utilidades de respaldo de la base (SQL de exportación).

### 4.3 Rendimiento
- Respuestas rápidas de catálogo y búsqueda (índices en BD).
- Paneles con paginación y límites (categorías 15/página, stock máx. 200) para no colgar el navegador.

### 4.4 Seguridad
- Contraseñas encriptadas y sesiones con caducidad.
- Protección de datos personales y tarjetas (cifrado de tarjetas guardadas).
- Validación de DNI real contra RENIEC para identidad confiable.
- Control de acceso estricto por rol (nadie externo ve paneles internos).

### 4.5 Mantenibilidad
- Código organizado por capas (views/controllers/models/config) y nombres de archivo descriptivos.
- Esquema de BD documentado y con relaciones vía claves foráneas.

---

## 5. Gaps / oportunidades detectadas (para próximas iteraciones)

1. **Email masivo y caché:** las notificaciones dependen del servidor SMTP; en horarios pico debe ser estable.
2. **Pagos online reales:** hoy el pago con tarjeta/Google Pay se registra a nivel local; el cliente espera que el cobro se ejecute de verdad (integración con pasarela PSE/Pasarela peruana como Niubiz, VisaNet o Izipay).
3. **Tiempos estimados:** el cliente espera un horario estimado de entrega, hoy no se muestra automáticamente.
4. **Notificaciones push / SMS:** los clientes esperan avisos cuando su pedido sale en camino.
5. **Multi-tienda / multi-sede:** a futuro el negocio podría expandirse y espera que la plataforma lo soporte.
6. **Deudas técnicas señaladas en el documento de arquitectura** (claves expuestas, `remember_me`, confianza en datos del cliente en Google login) deben resolverse para mantener la confianza del usuario.

---

## 6. Priorización sugerida de mejoras

| Prioridad | Mejora | Actor beneficiado |
|---|---|---|
| Alta | Pasarela de pago en línea (cobro real) | Cliente / Admin |
| Alta | Tiempos estimados de entrega | Cliente |
| Alta | Eliminar claves en texto plano y endurecer `remember_me` | Todos |
| Media | Notificaciones por SMS/WhatsApp cuando el pedido sale en camino | Cliente |
| Media | Sugerencias automáticas de reposición cuando stock < mínimo | Almacenero / Admin |
| Media | Reportes exportables (Excel/PDF) de ventas | Admin |
| Baja | Panel de métricas por repartidor (entregas, tiempos) | Admin / Repartidor |

---

*Fin del documento de expectativas.*