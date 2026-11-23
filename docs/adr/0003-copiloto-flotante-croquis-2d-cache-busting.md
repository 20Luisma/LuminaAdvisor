# ADR 0003 — Copiloto Flotante, Croquis 2D y Cache-Busting Dinámico

**Fecha:** 2026-11-05  
**Estado:** Aprobado

## Contexto
Las primeras pruebas en producción mostraron tres fricciones de experiencia de usuario:
1. Las recomendaciones en texto plano obligaban al usuario a imaginar la ubicación física de los focos.
2. Un chat embebido al final de la página se perdía en el scroll y no estaba disponible durante el wizard.
3. Al desplegar cambios en `style.css` y `app.js`, los navegadores servían versiones obsoletas desde caché local.

## Decisión
1. **Croquis 2D esquemático:** renderizar en `templates/result.php` un plano cenital dinámico con halos de luz proporcionales al número de puntos calculados.
2. **Copiloto IA en Drawer flotante (`templates/copilot.php`):** botón FAB fijo en la esquina inferior derecha + drawer deslizable accesible desde cualquier pantalla, conectado a `ChatController` (`POST /index.php?action=chat`) con contexto serializado de la habitación.
3. **Cache-busting automático:** en `templates/layout.php` inyectar `?v=filemtime(...)` sobre `css/style.css` y `js/app.js`.

## Consecuencias
- ✅ UX moderna con feedback visual inmediato y asistente contextual en 1 clic.
- ✅ Cero incidencias de caché tras cada despliegue a Hostinger.
- ⚠️ Requiere mantener la compatibilidad del contrato JSON de `POST /index.php?action=chat`.
