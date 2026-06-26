# M57

## Que es

M57 es el storefront marketplace estilo catalogo publico. No administra inventario ni crea ordenes por si mismo: consume `Hub` como cerebro operativo.

## Como funciona hoy

1. `M57` consulta a `Hub` para listar productos publicos disponibles.
2. El cliente navega, filtra, ve detalle y arma carrito.
3. En checkout, `M57` manda el carrito completo a `Hub`.
4. `Hub` separa el pedido por `owner_user_id`.
5. `Hub` crea una orden independiente por cada tienda dueña.
6. `M57` guarda el batch local y una copia resumida de las ordenes creadas.

## Piezas principales

- `app/Http/Controllers/StorefrontController.php`
- `app/Services/HubMarketplaceApi.php`
- `app/Models/MarketplaceOrder.php`
- `app/Models/MarketplaceOrderStore.php`
- `app/Models/MarketplaceOrderItem.php`
- `resources/views/storefront/*`
- `docs/ARCHITECTURE.md`
- `docs/openapi.yaml`

## Variables de entorno clave

- `APP_URL=http://127.0.0.1:8001`
- `DB_DATABASE=m57`
- `HUB_BASE_URL=http://127.0.0.1:8000`
- `HUB_M57_TOKEN=<token compartido>`

## Rutas web de M57

- `GET /`
- `GET /products/{productId}`
- `GET /cart`
- `POST /cart`
- `PATCH /cart/{itemKey}`
- `DELETE /cart/{itemKey}`
- `GET /checkout`
- `POST /checkout`
- `GET /success/{marketplaceOrder}`
- `GET /docs`
- `GET /docs/swagger`
- `GET /docs/openapi.yaml`

## Estado actual

- Catalogo publico funcionando
- Carrito funcionando
- Checkout separando ordenes por tienda en `Hub`
- Persistencia local del batch funcionando
- Swagger/OpenAPI generado

## Falta por construir

- Shipping real por tienda
- Reglas de pago por tienda/hub
- Estados sincronizados de vuelta desde `Hub`
- Wishlist persistente
- Busqueda instantanea AJAX
- Paginacion/infinite scroll real
- Login de clientes
- Recuperacion de carrito
