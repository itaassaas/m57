# Imágenes configuradas desde Superadmin para M57 — 14 de octubre de 2026

**Responsable:** ING Fernando Guzman  
**Estado:** Corregido en código, pendiente de despliegue en M57.

## Diagnóstico confirmado

La API privada de Hub entrega correctamente `hero.image_url` dentro de la configuración de storefront. Sin embargo, M57 solo leía ese valor y nunca lo incorporaba al HTML de la portada. Por esa razón una imagen guardada en **Superadmin → Hub → M57 → Portada → Imagen hero** no se veía en `m57.shop`.

Las imágenes de categorías se consumen desde `visual_categories.images`; al momento de la validación la API no tenía overrides de categorías guardados, por lo que M57 mostraba su imagen de compatibilidad. Cuando se guarden desde la pestaña **Imágenes**, M57 las recibirá por el mismo contrato.

## Corrección

- El primer hero de M57 muestra la URL configurada desde Superadmin como imagen de fondo.
- Se conserva una capa de contraste para proteger la legibilidad del título y los productos.
- Las tarjetas y controles se mantienen por encima de la imagen, sin bloquear clics ni navegación.
- Se añadió una prueba que comprueba la salida de una imagen hero y una imagen de categoría entregadas por Hub.

## Archivos

- `resources/views/storefront/index.blade.php`
- `resources/views/storefront/layout.blade.php`
- `tests/Feature/ExampleTest.php`

## Despliegue

```bash
git pull --ff-only origin main
php artisan optimize:clear
php artisan config:cache
php artisan view:cache
php artisan test tests/Feature/ExampleTest.php
```

No se requiere migración ni volver a cargar la imagen en Superadmin.
