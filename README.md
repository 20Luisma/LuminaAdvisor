# 💡 LuminaAdvisor

[![CI — LuminaAdvisor (ITV 7/7)](https://github.com/20Luisma/LuminaAdvisor/actions/workflows/ci.yml/badge.svg)](https://github.com/20Luisma/LuminaAdvisor/actions/workflows/ci.yml)

> **Asesor inteligente de iluminación para el hogar** basado en la norma europea **UNE-EN 12464-1** e impulsado por **Google Gemini 3.8 Flash**.

🌐 **En producción:** [https://contenido.creawebes.com/LuminaAdvisor/](https://contenido.creawebes.com/LuminaAdvisor/)  
📦 **Repositorio GitHub:** [https://github.com/20Luisma/LuminaAdvisor](https://github.com/20Luisma/LuminaAdvisor)  
⚙️ **GitHub Actions (CI):** [https://github.com/20Luisma/LuminaAdvisor/actions](https://github.com/20Luisma/LuminaAdvisor/actions)  
🔒 **GitHub Security:** [https://github.com/20Luisma/LuminaAdvisor/security](https://github.com/20Luisma/LuminaAdvisor/security)  
🏷️ **Releases / Tags:** [https://github.com/20Luisma/LuminaAdvisor/tags](https://github.com/20Luisma/LuminaAdvisor/tags)  
🐛 **Issues:** [https://github.com/20Luisma/LuminaAdvisor/issues](https://github.com/20Luisma/LuminaAdvisor/issues)

---

## 📸 ¿Qué es LuminaAdvisor?

**LuminaAdvisor** es una plataforma web rápida, accesible (WCAG 2.1 AA) y precisa que ayuda a propietarios, electricistas y diseñadores a planificar la iluminación ideal para cada habitación.

Combina dos mundos:
1. **Matemática determinista (Cero alucinaciones):** Aplica la fórmula física oficial de la norma europea **UNE-EN 12464-1** teniendo en cuenta lux requeridos, metros cuadrados, altura de techos, reflectancia según el color de pared y aporte de luz natural por ventanas.
2. **Copiloto IA de Interiorismo (Lumina):** Asistente conversacional flotante impulsada por **Google Gemini 3.8 Flash** que traduce la física a lenguaje 100% humano y práctico, con fallback silencioso si no hay API key o falla la red.

---

## ✨ Características Principales

- 🧮 **Cálculo Fotométrico Automático:** Lúmenes totales, temperatura de color recomendada (K), cantidad de puntos de luz y consumo eléctrico (€/mes y kWh/mes a tarifa media de España).
- 🗺️ **Croquis Esquemático 2D:** Plano de planta cenital generado en tiempo real con distribución visual de los puntos de luz y halos interactivos.
- 💬 **Copiloto IA Flotante (Lumina):** Drawer deslizable accesible desde toda la app con chat en vivo, memoria del cálculo y chips de preguntas rápidas.
- ♿ **Accesibilidad WCAG 2.1 AA:** Semántica `<fieldset>`/`<legend>`, etiquetas `<label>` + `aria-label`, `<html lang="es">` y alertas accesibles `role="alert"`.
- 📡 **Observabilidad Fail-Safe:** `ErrorLogger` con `set_exception_handler`, logs JSON estructurados locales y soporte opcional de `SENTRY_DSN` desde `.env`.
- 🛡️ **Seguridad y Cache-Busting:** Cabeceras HTTP (`nosniff`, `SAMEORIGIN`, `Referrer-Policy`), bloqueo de archivos internos en `.htaccess` y versionado automático `?v=filemtime` en CSS/JS.

---

## 🛠️ Stack Tecnológico

| Capa | Tecnología |
|---|---|
| **Backend (Runtime)** | PHP 8.4+ estricto — Clean Architecture (sin frameworks) |
| **Frontend** | HTML5 semántico + CSS3 (Design Tokens, Glassmorphism Dark Mode) + Vanilla JS |
| **Inteligencia Artificial** | Google Gemini 3.8 Flash (`responseMimeType: application/json`, `thinkingBudget: 0`) |
| **Calidad y Testing (Dev)** | PHPStan nivel 6 · PHPUnit 11 + Xdebug Coverage · TypeScript (`checkJs`) · Playwright E2E |
| **CI/CD y Hosting** | GitHub Actions ([`ci.yml`](https://github.com/20Luisma/LuminaAdvisor/blob/main/.github/workflows/ci.yml)) · Hostinger VPS (`bin/deploy.sh`) |

---

## 🏛️ Decisiones Arquitectónicas (ADRs)

Todas las decisiones técnicas relevantes están documentadas en [`docs/adr/`](https://github.com/20Luisma/LuminaAdvisor/tree/main/docs/adr):

| ADR | Título | Estado | Enlace GitHub |
|---|---|---|---|
| **0001** | Arquitectura Inicial Clean Architecture | Aprobado | [0001-arquitectura-inicial-clean-architecture.md](https://github.com/20Luisma/LuminaAdvisor/blob/main/docs/adr/0001-arquitectura-inicial-clean-architecture.md) |
| **0002** | Integración IA Híbrida (UNE-EN 12464-1 + Gemini 3.8 Flash) | Aprobado | [0002-integracion-ia-hibrida-gemini-flash.md](https://github.com/20Luisma/LuminaAdvisor/blob/main/docs/adr/0002-integracion-ia-hibrida-gemini-flash.md) |
| **0003** | Copiloto Flotante, Croquis 2D y Cache-Busting Dinámico | Aprobado | [0003-copiloto-flotante-croquis-2d-cache-busting.md](https://github.com/20Luisma/LuminaAdvisor/blob/main/docs/adr/0003-copiloto-flotante-croquis-2d-cache-busting.md) |
| **0004** | Arnés de Calidad ITV 7/7, Accesibilidad WCAG 2.1 AA y Observabilidad | Aprobado | [0004-calidad-itv-7-de-7-observabilidad-ci.md](https://github.com/20Luisma/LuminaAdvisor/blob/main/docs/adr/0004-calidad-itv-7-de-7-observabilidad-ci.md) |

---

## 📂 Arquitectura del Proyecto

```
LuminaAdvisor/
├── .github/workflows/ci.yml   ← Pipeline CI real (sin falsos verdes)
├── docs/adr/                  ← Architecture Decision Records (0001–0004)
├── bin/deploy.sh              ← Script de despliegue automatizado a Hostinger VPS
├── src/
│   ├── Autoloader.php         ← Autoloader PSR-4 nativo (runtime sin vendor/)
│   ├── Domain/Entities/       ← Room.php, LightRecommendation.php
│   ├── Application/UseCases/  ← CalculateLightingUseCase.php (UNE-EN 12464-1)
│   ├── Infrastructure/
│   │   ├── AI/                ← GeminiInteriorAdvisor.php (con transporte inyectable)
│   │   └── Observability/     ← ErrorLogger.php (logs JSON + Sentry fail-safe)
│   └── Presentation/
│       ├── Controllers/       ← LightingController.php, ChatController.php
│       └── View/              ← WizardViewData.php
├── public/                    ← Front Controller, CSS Design System y JS modular
├── templates/                 ← Vistas HTML semánticas y partials WCAG 2.1 AA
├── tests/
│   ├── Unit/                  ← Suite PHPUnit aislada (cobertura >= 75%)
│   └── e2e/                   ← Pruebas Playwright en navegador real
├── composer.json / .lock      ← PHPStan + PHPUnit (solo dev)
└── package.json / .lock       ← TypeScript checkJs + Playwright (solo dev)
```

---

## 🚀 Instalación y Uso en Local (desde clon limpio)

```bash
# 1. Clonar el repositorio
git clone https://github.com/20Luisma/LuminaAdvisor.git
cd LuminaAdvisor

# 2. Configurar variables de entorno
cp .env.example .env

# 3. Levantar el servidor local PHP (runtime sin dependencias externas)
php -S 127.0.0.1:8099 -t public/
```

Abrí `http://127.0.0.1:8099` en tu navegador.

---

## ✅ Calidad, Tests e ITV 7/7

Para ejecutar toda la batería de calidad en local:

```bash
# Instalar herramientas de desarrollo (PHPStan, PHPUnit, TypeScript, Playwright)
composer install
npm ci
npx playwright install chromium

# Estación 1: Sintaxis PHP y JS
composer lint && node --check public/js/app.js

# Estación 2: Tipos estáticos (PHPStan nivel 6 + TypeScript checkJs)
composer stan && npm run typecheck

# Estación 3: Tests unitarios + cobertura real (Xdebug)
composer test:coverage

# Estación 4: Auditoría de dependencias (0 CVEs)
composer audit && npm audit

# Estación 5: Pruebas E2E en navegador real
npm run test:e2e
```

---

## 📡 Pipeline de Despliegue (Local ➔ GitHub ➔ Hostinger)

1. **Local (Mac):** Validación ITV 7/7 en verde.
2. **GitHub:** Commit atómico + push a `main` (dispara [GitHub Actions CI](https://github.com/20Luisma/LuminaAdvisor/actions)).
3. **Producción Hostinger:** Despliegue blindado excluyendo tooling de desarrollo:
```bash
bash bin/deploy.sh
```

---

## ⚖️ Licencia y Créditos

Desarrollado por **Martín** (Barcelona) © 2026 — Licencia propietaria.  
Cumple con los estándares fotométricos europeos **UNE-EN 12464-1**.
