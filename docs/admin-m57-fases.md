# Administrador M57 por fases

## Estado 2026-09-12

Se implemento la version completa inicial de las 7 fases:

- Hub ahora tiene tablas para configuracion editorial, secciones, items, visibilidad de tiendas, visibilidad de productos, snapshots de publicacion y eventos analiticos.
- Hub expone:
  - `GET /api/m57/storefront/config`
  - `GET /api/m57/storefront/home`
- `GET /api/m57/storefront/sections`
- `GET /api/m57/storefront/sections/{code}`
- `POST /api/m57/analytics`
- Hub mantiene compatibilidad con el catalogo actual: si las tablas nuevas no existen o no hay secciones, M57 sigue mostrando productos con las reglas anteriores.
- La API de catalogo ya respeta ocultamiento por producto y por tienda cuando exista configuracion del admin.
- Superadmin tiene una pantalla inicial en:
  - `/superadmin/erp/m57`
- Desde esa pantalla se puede editar marca/hero, tiendas visibles/destacadas, productos visibles/destacados, secciones manuales, secciones automaticas por regla, publicar snapshot y restaurar publicaciones anteriores.
- Las reglas automaticas soportadas son: destacados, nuevos, mas vistos, mejor desempeno, stock alto, precio menor y precio mayor, con filtros por categoria/tienda.
- M57 consume `storefront/home` para la portada cuando no hay filtros activos, y usa fallback automatico al listado anterior.
- Los bloques principales de M57 (`featured_products`, `new_collection`, `flash_sale`, `trending`, `premium`) toman productos de las secciones publicadas en Hub.
- El hero de M57 ya lee titulo/subtitulo configurados desde Hub.
- M57 registra eventos de home, vistas de producto, clicks, intentos de carrito, agregados al carrito y compra por item.
- El admin muestra un resumen de eventos, producto con mas interaccion y snapshots recientes.

Validaciones locales:

- Hub: `php -l` en archivos nuevos/modificados OK.
- Hub: `php artisan route:list --path=m57` OK.
- Hub: `php artisan view:cache` OK y luego `php artisan view:clear`.
- Hub: `php artisan migrate --pretend` no corrio porque MySQL local rechazo conexion.
- M57: `php artisan test` OK, 10 tests, 50 assertions.

Pendiente para servidor:

```bash
cd /ruta/de/flumedrop1
git pull
php artisan migrate
php artisan optimize:clear
```

Despues de migrar, entrar como superadmin a `/superadmin/erp/m57`, guardar configuracion y presionar `Publicar`.

## Decision principal

El administrador de M57 debe vivir principalmente en `Hub`, no en `M57`.

Motivo:

- `Hub` ya es la fuente de verdad de tiendas, productos, stock, precios, categorias y duenios.
- Las decisiones de que tienda aparece o no aparece afectan catalogo, ordenes, visibilidad y reglas comerciales.
- Si esa configuracion vive solo en `M57`, se duplican datos y se corre el riesgo de mostrar productos que Hub ya no quiere vender.
- `M57` debe seguir siendo storefront publico: rapido, simple, cacheable y enfocado en experiencia de compra.

La mejor arquitectura es hibrida:

- `Hub` administra la configuracion.
- `Hub` expone endpoints publicos/privados para M57.
- `M57` consume esa configuracion y solo renderiza.
- `M57` puede tener cache local para velocidad, pero no debe ser la fuente de verdad.

## Que se debe poder administrar

### Tiendas

- Tiendas visibles en M57.
- Tiendas ocultas temporalmente.
- Orden/prioridad de tiendas.
- Tiendas destacadas.
- Tiendas bloqueadas por incumplimiento, falta de stock o decision comercial.

### Productos

- Productos destacados en home.
- Productos destacados por categoria.
- Productos ocultos solo en M57, aunque existan en Hub.
- Productos para hero.
- Productos para secciones como:
  - Super ofertas
  - Nueva coleccion
  - Tendencias
  - Premium
  - Mas vendidos
  - Recomendados

### Categorias y colecciones

- Categorias visibles.
- Categorias ocultas.
- Orden de categorias.
- Imagen visual por categoria.
- Alias de categorias.
- Colecciones manuales.
- Colecciones automaticas por reglas.

### Home

- Hero principal.
- Slides del hero.
- Textos del hero.
- CTA del hero.
- Secciones activas/inactivas.
- Orden de secciones.
- Cantidad de productos por seccion.
- Banners editoriales.

### Reglas comerciales

- Mostrar productos solo con stock.
- Mostrar productos solo de tiendas aprobadas.
- Excluir tiendas o productos por categoria.
- Priorizar tiendas con mejor cumplimiento.
- Priorizar productos con mejor margen o venta.

## Arquitectura recomendada

### En Hub

Crear un modulo:

```txt
M57 Admin / Marketplace Admin
```

Responsabilidades:

- Guardar configuracion comercial.
- Validar que productos/tiendas existan.
- Publicar configuracion para M57.
- Exponer endpoints para storefront.
- Mantener auditoria de cambios.

### En M57

Responsabilidades:

- Consumir endpoints de Hub.
- Cachear configuracion publicada.
- Renderizar home, categorias y secciones.
- No administrar tiendas/productos directamente.
- No duplicar reglas comerciales profundas.

## Modelo de datos sugerido en Hub

### `m57_storefront_settings`

Configuracion general del storefront.

Campos sugeridos:

- `id`
- `is_enabled`
- `home_title`
- `home_meta_title`
- `home_meta_description`
- `theme`
- `published_at`
- `created_by`
- `updated_by`

### `m57_storefront_sections`

Secciones de la home.

Campos sugeridos:

- `id`
- `key`
- `title`
- `subtitle`
- `type`
- `position`
- `is_enabled`
- `config_json`

Tipos posibles:

- `hero`
- `manual_products`
- `category_products`
- `featured_stores`
- `banner`
- `collection`
- `automatic_rules`

### `m57_storefront_section_items`

Items dentro de cada seccion.

Campos sugeridos:

- `id`
- `section_id`
- `product_id`
- `category_id`
- `owner_user_id`
- `title_override`
- `image_override`
- `url_override`
- `position`
- `is_enabled`

### `m57_store_visibility`

Control de tiendas visibles.

Campos sugeridos:

- `id`
- `owner_user_id`
- `is_visible`
- `is_featured`
- `priority`
- `reason`
- `starts_at`
- `ends_at`

### `m57_product_visibility`

Control especifico de productos para M57.

Campos sugeridos:

- `id`
- `product_id`
- `is_visible`
- `is_featured`
- `priority`
- `reason`
- `starts_at`
- `ends_at`

## Endpoints necesarios en Hub

### Publicos para M57

```txt
GET /api/m57/storefront/config
GET /api/m57/storefront/home
GET /api/m57/storefront/sections
GET /api/m57/storefront/sections/{key}
```

Estos endpoints deben devolver informacion ya resuelta y lista para renderizar.

Ejemplo:

```json
{
  "data": {
    "sections": [
      {
        "key": "hero",
        "type": "hero",
        "title": "Nueva temporada",
        "items": []
      },
      {
        "key": "featured-products",
        "type": "manual_products",
        "title": "Destacados",
        "products": []
      }
    ]
  }
}
```

### Privados para admin en Hub

```txt
GET /admin/m57/storefront
POST /admin/m57/storefront/sections
PATCH /admin/m57/storefront/sections/{section}
DELETE /admin/m57/storefront/sections/{section}
POST /admin/m57/storefront/sections/{section}/items
PATCH /admin/m57/storefront/sections/{section}/items/{item}
DELETE /admin/m57/storefront/sections/{section}/items/{item}
PATCH /admin/m57/stores/{owner}/visibility
PATCH /admin/m57/products/{product}/visibility
POST /admin/m57/storefront/publish
```

## Fases de implementacion

## Fase 1 - Base de configuracion en Hub

Objetivo:

Crear la estructura minima para decidir que tiendas y productos aparecen en M57.

Incluye:

- Migraciones en Hub.
- Modelos para visibilidad de tiendas.
- Modelos para visibilidad de productos.
- Campos de prioridad.
- Endpoints internos basicos.
- Validacion de permisos de admin.

Resultado esperado:

- Desde Hub se puede activar/desactivar una tienda para M57.
- Desde Hub se puede activar/desactivar un producto para M57.
- M57 sigue funcionando igual, pero Hub ya tiene la capa de decision.

## Fase 2 - Endpoint publico de configuracion

Objetivo:

Exponer a M57 una configuracion ya filtrada y cacheable.

Incluye:

- `GET /api/m57/storefront/config`
- `GET /api/m57/storefront/home`
- Cache en Hub.
- Cache en M57.
- Fallback si Hub no responde.

Resultado esperado:

- M57 puede cargar configuracion de home desde Hub.
- Si Hub esta lento, M57 puede usar la ultima configuracion cacheada.

## Fase 3 - Admin visual en Hub

Objetivo:

Crear pantalla de administracion para usuarios internos.

Incluye:

- Pantalla "M57 Storefront".
- Lista de secciones.
- Crear/editar/ordenar secciones.
- Activar/desactivar secciones.
- Seleccionar productos manualmente.
- Seleccionar tiendas destacadas.
- Buscador de productos dentro del admin.
- Preview basico de la home.

Resultado esperado:

- Un admin puede configurar la home sin tocar codigo.

## Fase 4 - M57 consume secciones dinamicas

Objetivo:

Quitar hardcode progresivamente de `resources/views/storefront/index.blade.php`.

Incluye:

- Adaptar `StorefrontController@index`.
- Agregar metodo en `HubMarketplaceApi` para storefront home.
- Renderizar secciones desde configuracion.
- Mantener fallback a secciones actuales si Hub no devuelve configuracion.

Resultado esperado:

- La home de M57 se controla desde Hub.
- El codigo de M57 queda mas simple y menos rigido.

## Fase 5 - Reglas automaticas

Objetivo:

Permitir secciones automaticas sin elegir producto por producto.

Reglas posibles:

- Productos con mas ventas.
- Productos nuevos.
- Productos con stock alto.
- Productos por categoria.
- Productos por tienda.
- Productos con margen alto.
- Productos en promocion.

Resultado esperado:

- Una seccion puede ser manual o automatica.
- Hub resuelve los productos y M57 solo renderiza.

## Fase 6 - Auditoria, publicacion y rollback

Objetivo:

Evitar que cambios malos rompan la home publica.

Incluye:

- Borradores.
- Boton publicar.
- Historial de cambios.
- Usuario que publico.
- Fecha de publicacion.
- Rollback a version anterior.
- Preview antes de publicar.

Resultado esperado:

- El equipo puede probar cambios antes de afectar `m57.shop`.
- Se puede volver a una version anterior de la home.

## Fase 7 - Analitica y decisiones comerciales

Objetivo:

Usar datos para decidir que mostrar.

Incluye:

- Clicks por seccion.
- Productos mas vistos.
- Productos mas agregados al carrito.
- Conversion por tienda.
- Conversion por producto.
- Secciones con mejor rendimiento.

Resultado esperado:

- El admin no solo configura, tambien ayuda a decidir que vender mas.

## Cambios necesarios en M57

Archivos probables:

- `app/Services/HubMarketplaceApi.php`
- `app/Http/Controllers/StorefrontController.php`
- `resources/views/storefront/index.blade.php`
- `resources/views/storefront/partials/*`
- `docs/openapi.yaml`

Nuevos metodos sugeridos:

```php
public function storefrontHome(): array
public function storefrontConfig(): array
```

Cache sugerido:

- `m57:storefront:config`
- `m57:storefront:home`
- TTL inicial: `5 a 15 minutos`
- O cache hasta `published_at` si Hub entrega version/hash.

## Riesgos

- Duplicar reglas en Hub y M57.
- Que M57 dependa demasiado de llamadas lentas a Hub.
- Que una mala configuracion deje la home vacia.
- Que productos ocultos sigan apareciendo por cache viejo.
- Que se permita destacar productos sin stock.

Mitigaciones:

- Hub debe entregar payload ya validado.
- M57 debe tener fallback cacheado.
- Toda configuracion debe tener preview.
- Toda publicacion debe guardar version.
- Las secciones deben ignorar productos sin stock o invisibles.

## Recomendacion final

Construir el admin en `Hub`.

No construir un admin completo dentro de `M57` todavia.

`M57` debe tener solo lo necesario para consumir configuracion, cachearla y renderizarla rapido. Si mas adelante se necesita una pantalla tecnica local en M57, que sea solo para diagnostico, no para ser fuente de verdad.
