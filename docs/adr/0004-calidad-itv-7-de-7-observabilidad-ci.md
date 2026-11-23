# ADR 0004 — Arnés de Calidad ITV 7/7, Accesibilidad WCAG 2.1 AA y Observabilidad Fail-Safe

**Fecha:** 2026-11-23  
**Estado:** Aprobado

## Contexto
La inspección inicial de la ITV de Código reportó `APTO_CON_OBSERVACIONES`: faltaba análisis estático de tipos (Estación 2), suite de tests con cobertura >= 75% (Estación 3), pruebas E2E de navegador (Estación 5), etiquetas accesibles en radios dinámicos del wizard (Estación 6), y observabilidad + reducción de complejidad ciclomática en `templates/wizard.php` (Estación 7).

## Decisión
Adoptar un arnés de calidad completo sin frameworks en runtime (las herramientas de desarrollo viven solo en `require-dev` / `devDependencies` y se excluyen del deploy a producción):
1. **Tipos estáticos:** `phpstan/phpstan` nivel 6 sin `ignoreErrors` para PHP (`phpstan.neon`) y `tsc --noEmit` con `checkJs: true` para `public/js/`.
2. **Tests aislados y cobertura real:** `phpunit/phpunit` 11 con Xdebug (`coverage/clover.xml`) probando dominio, caso de uso, controladores, ViewModel, observabilidad y cliente IA con transporte inyectado.
3. **Pruebas E2E reales:** `@playwright/test` contra `php -S 127.0.0.1:8099 -t public`, cubriendo flujo completo y caminos negativos HTTP 400/405/422.
4. **WCAG 2.1 AA y deuda técnica:** extracción de `WizardViewData` y partición de `templates/wizard.php` en partials (`wizard_step1.php`, `wizard_step2.php`) con `<fieldset>`, `<legend>` y `aria-label` explícitos.
5. **Observabilidad fail-safe:** `ErrorLogger` con `set_exception_handler`, logs JSON estructurados en `storage/logs/app.log` y soporte opcional `SENTRY_DSN` desde `.env`.
6. **CI en GitHub Actions:** `.github/workflows/ci.yml` ejecutando las estaciones en cada `push` y `pull_request` sin falsos verdes.

## Consecuencias
- ✅ 7/7 estaciones de la ITV en verde y 6/6 controles automáticos de seguridad (100%).
- ✅ Runtime en Hostinger sigue libre de `vendor/` y `node_modules/` (excluidos en `bin/deploy.sh`).
- ✅ Cero regresiones gracias a CI automático en GitHub Actions.
