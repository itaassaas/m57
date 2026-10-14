# Corrección del buscador de M57 — 14 de octubre de 2026

**Responsable:** ING Fernando Guzman  
**Estado:** Resuelto en código, pendiente de despliegue en M57.

## Síntoma

Al buscar desde la cabecera de `https://m57.shop/`, productos que se veían en la portada podían retornar el mensaje **“No hay productos para ese filtro”**.

## Causa confirmada

La portada y el buscador no consumían el mismo conjunto de productos:

- La portada consume `GET /api/m57/storefront/home`, que contiene secciones curadas configuradas desde Hub.
- El buscador consumía solamente `GET /api/m57/catalog/products` y descartaba luego toda categoría fuera de una lista histórica de moda.

En consecuencia, un producto curado y visible en M57, pero no presente en la respuesta de catálogo o fuera de las categorías de moda, no se encontraba mediante la búsqueda.

## Cambio aplicado

- Cuando existe el parámetro `q`, M57 consulta el catálogo filtrado y la configuración actual de la portada.
- Agrega los productos curados que coinciden por nombre, SKU, descripción, categoría o tienda.
- Normaliza mayúsculas y tildes durante la coincidencia.
- Elimina duplicados por ID y pagina el resultado combinado.
- Mantiene el filtro de moda solo para la exploración normal de la portada, sin afectar una búsqueda solicitada por el usuario.

## Ajuste posterior de producción

La validación pública posterior detectó que una búsqueda podía continuar vacía aunque el mismo producto estuviera visible en inicio. La consulta de catálogo se ejecutaba antes de obtener la instantánea curada de la portada. Ahora M57 obtiene primero esa instantánea y la reutiliza como fuente prioritaria de la búsqueda; el catálogo complementa los resultados y no puede hacer desaparecer los productos ya visibles para el visitante.

## Búsqueda por empresa o marca

El buscador también reconoce la empresa que publica los productos. Cuando el término identifica una sola marca, M57 abre la categoría principal de esa tienda y añade un filtro interno de empresa, para que la persona vea solo ese catálogo. La comparación normaliza mayúsculas, tildes y tolera un error corto de escritura; por ejemplo, `cannabis` identifica `Cannabs Natural Fashion` y abre su categoría principal, como `/categories/14?store=ID`.

Hub aplica el filtro `owner_user_id` antes de entregar los productos y continúa aplicando las reglas de visibilidad, disponibilidad y categoría pública. Por tanto, el parámetro no permite consultar productos que no estén publicados en M57.

## Archivos

- `app/Http/Controllers/StorefrontController.php`
- `tests/Feature/ExampleTest.php`

## Prueba de regresión

La prueba `test_search_includes_matching_products_curated_on_the_storefront` simula el caso crítico: el catálogo responde vacío y un producto `LUXURY` existe en una sección curada. El resultado debe mostrar el producto, no el estado vacío. La prueba `test_store_search_redirects_to_its_category_with_products_limited_to_that_store` cubre la búsqueda de marca, su tolerancia de escritura y la redirección al catálogo limitado a esa empresa.

## Despliegue M57

```bash
git pull --ff-only origin main
php artisan optimize:clear
php artisan config:cache
php artisan view:cache
php artisan test tests/Feature/ExampleTest.php
```

## Riesgo remanente

La búsqueda agrega resultados de secciones configuradas en Hub, por lo que la caché de cinco minutos de `storefrontHome` puede retrasar hasta ese tiempo la visibilidad de una edición recién publicada. No expone datos privados: la API de Hub ya filtra disponibilidad y visibilidad antes de entregar cada tarjeta.
