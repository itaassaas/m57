# Arquitectura M57 + Hub

## Resumen

`M57` es la cara publica tipo SHEIN.

`Hub` es el sistema central:

- fuente de verdad de productos
- stock
- precio final publico
- separacion por tienda
- creacion de ordenes

## Relacion entre sistemas

### M57

Responsabilidades:

- mostrar catalogo publico
- presentar UI de producto, carrito y checkout
- capturar datos del cliente
- enviar el pedido a `Hub`
- guardar batch local y ordenes resultantes

No hace:

- no descuenta stock local
- no calcula reglas profundas de negocio
- no decide a que tienda pertenece cada item

### Hub

Responsabilidades:

- filtrar productos activos y visibles en catalogo
- resolver precio final por producto o variacion
- validar stock disponible
- agrupar carrito por `owner_user_id`
- crear una orden por tienda
- descontar stock

## Flujo de checkout actual

1. `M57` arma un payload con cliente + items.
2. `POST /api/m57/checkout` en `Hub`.
3. `Hub` bloquea productos, valida stock y agrupa por dueño.
4. `Hub` crea una orden por cada dueño.
5. `Hub` devuelve:
   - `batch_code`
   - lista de ordenes creadas
   - total global
6. `M57` persiste:
   - `marketplace_orders`
   - `marketplace_order_stores`
   - `marketplace_order_items`

## Tablas locales de M57

### `marketplace_orders`

Cabecera del batch enviado a `Hub`.

### `marketplace_order_stores`

Una fila por tienda dueña que recibio orden.

### `marketplace_order_items`

Snapshot minimo de los items del batch.

## Autenticacion entre sistemas

- `M57` llama a `Hub` con Bearer token
- `Hub` valida con `M57_SHARED_TOKEN`
- `M57` usa `HUB_M57_TOKEN`

## Endpoints usados en Hub

- `GET /api/m57/catalog/categories`
- `GET /api/m57/catalog/products`
- `GET /api/m57/catalog/products/{productId}`
- `POST /api/m57/checkout`
- `GET /api/m57/orders/{orderNumber}`

## Decisiones actuales

- carrito multi-tienda permitido
- `Hub` separa ordenes por tienda
- `M57` usa precio final publico ya resuelto por `Hub`
- checkout minimalista primero, sin pasarela nueva

## Riesgos abiertos

- el shipping aun no se prorratea por tienda
- no hay sync reverso automatico de estados desde `Hub` a `M57`
- no hay reserva temporal de inventario previa al pago
- no se estan reusando aun las pantallas web de checkout por website; se esta reusando la logica central de orden

## Siguiente corte recomendado

1. Shipping por tienda y total consolidado.
2. Sync de estados desde `Hub` a `M57`.
3. PDP mejorado: quick view, recomendaciones, recientemente vistos.
4. Busqueda instantanea y paginacion scroll.
5. Checkout con reglas de pago por tienda/hub.
