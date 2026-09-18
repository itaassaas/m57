# Trabajo realizado en M57

## Resumen

## Categorias seleccionadas desde el admin (2026-09-18)

El admin del Hub controla visibilidad y orden mediante `m57_category_visibility`.
M57 recibe esa seleccion desde `/api/m57/catalog/categories` y la home muestra
las categorias recibidas, en el mismo orden. Se eliminaron los reemplazos por
categorias arbitrarias cuando un enlace del menu no tiene categoria disponible.
Las imagenes configuradas en el admin se conservan para los nombres coincidentes.

Para publicar, actualizar ambos repositorios. En el Hub ejecutar
`php artisan migrate` y `php artisan optimize:clear`. En M57 ejecutar
`php artisan optimize:clear` despues del pull de `main`.

M57 quedo como repositorio independiente dentro de `flumedrop1` usando Git submodule.

- Repo independiente: `https://github.com/itaassaas/m57.git`
- Repo padre: `https://github.com/itaassaas/flumedrop1.git`
- Ruta local del submodulo: `/Volumes/Data-Mac/Documents/Github/flumedrop1/m57`
- Rama de M57: `main`
- Rama de flumedrop1: `hub-main`

## Submodulo

Antes, `m57` era una carpeta normal versionada dentro de `flumedrop1`. Se convirtio a submodulo para que siga viviendo dentro de `flumedrop1`, pero con historial y push propios.

Archivo creado en `flumedrop1`:

```ini
[submodule "m57"]
	path = m57
	url = https://github.com/itaassaas/m57.git
```

Flujo correcto:

1. Editar archivos dentro de `flumedrop1/m57`.
2. Hacer commit y push en `m57`.
3. Volver a `flumedrop1`.
4. Hacer commit del puntero actualizado del submodulo.
5. Hacer push de `flumedrop1`.

## Commits importantes

En `m57`:

- `d5164e1` - `Test m57 submodule workflow`
- `a34b191` - `Add second submodule workflow test`
- `fbddbc0` - `Improve storefront loading performance`
- `b49ca0b` - `Improve mobile responsive storefront layout`
- `8f49cb9` - `Reduce initial storefront image requests`
- Pendiente de registrar: cambio para que la home cargue productos con una consulta paginada liviana en vez de esperar el catalogo completo.

En `flumedrop1`:

- `52462c88` - `Convert m57 to submodule`
- `96ba4011` - `Update m57 submodule pointer`
- `6b2ef179` - `Update m57 submodule to second test`
- `2cc5cb1d` - `Update m57 performance improvements`
- `73cea903` - `Update m57 responsive layout`
- `4a24db54` - `Update m57 initial load optimization`

## Mejoras de performance

Se reviso la primera carga de `https://m57.shop/`.

Mediciones observadas con `curl`:

- TTFB aproximado: `0.29s - 0.38s`
- Descarga HTML total aproximada: `0.60s - 0.62s`
- Tamano HTML aproximado: `200 KB`

Conclusion: el servidor responde rapido. La demora reportada de `25000 ms` viene principalmente de la carga completa del navegador por demasiados recursos externos, especialmente imagenes.

Problemas encontrados en la home publicada:

- Muchas imagenes externas desde `app.mihub.com.co`.
- Imagenes secundarias de hover cargando desde el HTML inicial.
- Fondos externos desde Unsplash.
- Fallback externo desde Picsum.

Cambios hechos:

- `StorefrontController` dejo de usar `freshAllProducts()` en home y paso a usar `allProducts()` con cache.
- `HubMarketplaceApi` ahora cachea:
  - categorias
  - productos paginados
  - catalogo completo
  - detalle de producto
  - departamentos
  - ciudades
- Timeout del cliente Hub reducido de `20s` a `8s` con retry corto.
- CSS de cookies cargado sin bloquear el primer render.
- JS de CookieConsent cargado con `defer`.
- Primera imagen critica del hero con `fetchpriority="high"`.
- Imagenes lazy con `decoding="async"`.
- Imagenes secundarias de tarjetas pasaron a `data-deferred-src` para cargarse solo en interaccion.
- Imagenes del mega menu se difieren hasta interaccion.
- Fondos de Unsplash/Picsum reemplazados por gradientes locales.

## Mejoras responsive

Se ajusto el responsive principal del storefront:

- Header movil mas compacto.
- Buscador en segunda linea en telefonos angostos.
- Carrito reducido a boton `Bolsa` con contador.
- Subnav sticky con scroll horizontal limpio.
- Mega menu oculto en movil para evitar ocupar toda la pantalla.
- Hero mas compacto y legible en movil.
- Grids de productos y tarjetas con menor padding.
- Breakpoint especial para pantallas de `430px` o menos.

## Validacion

Suite ejecutada:

```bash
php artisan test
```

Resultado:

```txt
10 passed, 48 assertions
```

Tambien se ejecuto:

```bash
./vendor/bin/pint --dirty
```

## Pendiente en servidor

Aunque los commits ya estan en GitHub, `https://m57.shop/` todavia servia HTML viejo al momento de la ultima medicion:

- `data-deferred-src`: `0`
- referencias Unsplash/Picsum: `6`
- imagenes directas desde `app.mihub.com.co`: `77`

Eso indica que falta actualizar el servidor o limpiar cache de Laravel.

El document de la home tambien se midio en frio con `Cache-Control: no-cache` y llego a `TTFB ~26.6s`. Eso confirmo que el bloqueo principal no era DNS, TLS ni descarga del HTML, sino trabajo de backend antes del primer byte.

Causa identificada:

- La home estaba usando `allProducts()`.
- `allProducts()` puede recorrer hasta 6 paginas de 240 productos desde Hub.
- En cache frio, la primera visita quedaba esperando esas llamadas seriales antes de recibir HTML.

Correccion aplicada despues:

- La home paso a usar `products()` con `page`, `per_page=72` y `spread=owners`.
- El HTML inicial ya no necesita construir el catalogo completo.
- El infinite scroll pide la siguiente pagina al endpoint `/home/products`.

Si el server apunta directo al repo `m57`:

```bash
cd /ruta/de/m57
git pull origin main
php artisan optimize:clear
php artisan view:clear
```

Si el server apunta a `flumedrop1` con `m57` como submodulo:

```bash
cd /ruta/de/flumedrop1
git pull origin hub-main
git submodule update --init --recursive
cd m57
php artisan optimize:clear
php artisan view:clear
```

Despues de eso se debe volver a medir `https://m57.shop/` y confirmar:

- que aparezcan `data-deferred-src` en el HTML;
- que no aparezcan referencias a Unsplash/Picsum;
- que baje el numero de imagenes iniciales con `src`;
- que la carga total del navegador mejore.
