# Selector de colores sin imagen — M57

**Fecha:** 14 de octubre de 2026  
**Responsable:** ING Fernando Guzman  
**Versión Hub:** `v1.0.0.0.8.5`

## Comportamiento implementado

La ficha de producto agrupa las variaciones por el atributo cuyo nombre sea `Color`, sin depender de mayúsculas/minúsculas. Para cada color:

1. Si una talla de ese color tiene imagen, se muestra dicha miniatura.
2. Si ninguna talla de ese color tiene imagen, se muestra un círculo de color.
3. Se usan nombres comunes en español (`Azul`, `Rojo`, `Verde`, `Beige`, `Negro`, etc.) y valores hexadecimales válidos, como `#123ABC`.
4. Un valor de color desconocido muestra un gris neutro; no se genera una imagen ficticia ni se bloquea la compra.

## Alcance técnico

- Vista: `resources/views/storefront/product.blade.php`.
- El selector conserva `data-color`, por lo que la carga del precio, talla, inventario y galería de la variación continúa usando el flujo existente.
- Si una misma tonalidad tiene varias tallas, se utiliza la primera URL de imagen válida disponible entre ellas.

## Validación

La prueba `test_product_page_renders_redesigned_pdp` agrega una variación con atributo `color: Azul` y sin imagen. Comprueba que el HTML incluya el modificador de círculo de respaldo y el color azul esperado.

## Riesgo remanente

La conversión semántica depende de que el atributo contenga un nombre de color comprensible o un hexadecimal. Para tonos comerciales no estándar se mostrará el gris neutro hasta que se agregue su equivalencia al catálogo de colores.
