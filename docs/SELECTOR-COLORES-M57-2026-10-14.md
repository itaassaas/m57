# Selector de colores sin imagen — M57

**Fecha:** 14 de octubre de 2026  
**Responsable:** ING Fernando Guzman  
**Versión Hub:** `v1.0.0.0.9.2`

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

## Galería al seleccionar una variación

Al elegir un color o una talla, M57 consulta el estado de la variación seleccionada y pone su galería propia antes que cualquier foto general. La imagen principal cambia inmediatamente a la primera foto de esa variación. Si no tiene fotos asignadas, la galería general del producto funciona como respaldo; el selector de color conserva el círculo visual.

## Tallas compactas

El selector se presenta como **Color** y después **Tallas**. Cada botón de talla contiene solo su valor (`S`, `M`, `L`, etc.). Como compatibilidad, una variación antigua llamada `Color: Gris · Talla: S` se interpreta internamente como color `Gris` y talla `S`.

También se admite el formato legado sin separador, como `Talla: 36 Color: Gris`; tras elegir Gris, se muestra solo `36` en el selector de tallas.

## Riesgo remanente

La conversión semántica depende de que el atributo contenga un nombre de color comprensible o un hexadecimal. Para tonos comerciales no estándar se mostrará el gris neutro hasta que se agregue su equivalencia al catálogo de colores.
