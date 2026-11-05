# 💡 LuminaAdvisor

> **Asesor inteligente de iluminación para el hogar** basado en la norma europea **UNE-EN 12464-1** e impulsado por **Google Gemini 3.8 Flash**.

🌐 **En producción:** [https://contenido.creawebes.com/LuminaAdvisor/](https://contenido.creawebes.com/LuminaAdvisor/)  
📦 **Repositorio:** [github.com/20Luisma/LuminaAdvisor](https://github.com/20Luisma/LuminaAdvisor)

---

## 📸 ¿Qué es LuminaAdvisor?

**LuminaAdvisor** es una plataforma web rápida, moderna y precisa que ayuda a propietarios, electricistas y diseñadores a planificar la iluminación perfecta para cada habitación.

Combina dos mundos:
1. **Matemática determinista (Cero alucinaciones):** Aplica la fórmula física oficial de la norma europea **UNE-EN 12464-1** teniendo en cuenta lux requeridos, metros cuadrados, altura de techos, reflectancia según el color de pared y aporte de luz natural por ventanas.
2. **Copiloto IA de Interiorismo (Lumina):** Una asistente conversacional flotante impulsada por **Google Gemini 3.8 Flash** que traduce la física a lenguaje 100% humano y práctico: qué bombilla pedir en la tienda (casquillos GU10, E27, E14, vatios LED), cómo distribuir los focos sin sombras y cómo regular la intensidad.

---

## ✨ Características Principales

- 🧮 **Cálculo Fotométrico Automático:** Lúmenes totales, temperatura de color recomendada (K), cantidad de puntos de luz y consumo eléctrico (€/mes y kWh/mes a tarifa de España).
- 🗺️ **Croquis Esquemático 2D:** Plano de planta cenital generado en tiempo real con distribución visual de los puntos de luz y halos interactivos.
- 💬 **Copiloto IA Flotante (Lumina):** Drawer deslizable con chat en vivo, memoria del cálculo de la habitación y chips de preguntas rápidas (ej: *"¿Qué bombilla pido en la tienda?"*).
- 🎨 **Diseño Dark Fintech:** Glassmorphism de alto nivel con acento ámbar lumínico (`#f59e0b`), micro-animaciones, contadores en vivo y diseño responsive móvil/desktop.
- ⚡ **Ultra Rápido y Ligero:** Sin frameworks pesados (cero Composer, cero npm, PHP 8.4 nativo y Vanilla JS). Carga en menos de 200 ms.
- 🛡️ **Cache-Busting Dinámico:** Inyección de versiones automáticas por timestamp en CSS y JS para despliegues instantáneos sin problemas de caché.

---

## 🛠️ Stack Tecnológico

| Capa | Tecnología |
|---|---|
| **Backend** | PHP 8.4+ estricto — Clean Architecture (Domain, Application, Infrastructure, Presentation) |
| **Frontend** | HTML5 semántico + CSS3 (Design Tokens, Glassmorphism, Dark Mode) + Vanilla JS |
| **Inteligencia Artificial** | Google Gemini 3.8 Flash (`responseMimeType: application/json`, `thinkingBudget: 0`) |
| **Normativa** | UNE-EN 12464-1 (España / Unión Europea) |
| **Hosting & Deploy** | Hostinger VPS (`contenido.creawebes.com`) vía `bin/deploy.sh` blindado |

---

## 📂 Arquitectura del Proyecto

```
LuminaAdvisor/
├── AGENTS.md                  ← Protocolo y reglas específicas del proyecto
├── MEMORY.md                  ← Memoria persistente técnica y roadmap
├── README.md                  ← Documentación oficial del repositorio
├── index.php                  ← Front Controller raíz con rewrite seguro
├── .htaccess                  ← Ruteo Apache y protección de carpetas
├── bin/
│   └── deploy.sh              ← Script de despliegue automatizado a Hostinger VPS
├── src/
│   ├── Autoloader.php         ← Autoloader PSR-4 nativo sin Composer
│   ├── Domain/
│   │   └── Entities/          ← Room.php, LightRecommendation.php
│   ├── Application/
│   │   └── UseCases/          ← CalculateLightingUseCase.php (UNE-EN 12464-1)
│   ├── Infrastructure/
│   │   └── AI/                ← GeminiInteriorAdvisor.php (API Google Gemini)
│   └── Presentation/
│       └── Controllers/       ← LightingController.php, ChatController.php
├── public/
│   ├── index.php              ← Punto de entrada web
│   ├── css/style.css          ← Design System Dark Mode Fintech con acento ámbar
│   └── js/app.js              ← Wizard, animaciones y Copiloto IA flotante
├── templates/
│   ├── layout.php             ← Shell HTML con inyección de cache busters
│   ├── wizard.php             ← Formulario paso a paso (Habitación y Características)
│   ├── result.php             ← Dashboard de resultados y croquis 2D
│   └── copilot.php            ← Drawer flotante del agente conversacional Lumina
└── memory_backups/            ← Respaldos inmutables con timestamp
```

---

## 🚀 Cómo Arrancar en Local

### 1. Clonar el repositorio
```bash
git clone https://github.com/20Luisma/LuminaAdvisor.git
cd LuminaAdvisor
```

### 2. Configurar variables de entorno
```bash
cp .env.example .env
```
Editá `.env` y agregá tu `GEMINI_API_KEY` (de Google AI Studio) y los datos de acceso si vas a desplegar.

### 3. Levantar el servidor PHP
```bash
php -S localhost:8099 -t public/
```
Abrí `http://localhost:8099` en tu navegador.

---

## 📡 Pipeline de Despliegue (La Santísima Trinidad)

Siguiendo el protocolo estricto de ingeniería:
1. **Local (Mac):** Validación estática con `php -l` en todos los archivos.
2. **GitHub:** Commit atómico y push a la rama `main`.
3. **Producción Hostinger:** Ejecución del script blindado:
```bash
bash bin/deploy.sh
```

---

## ⚖️ Licencia y Créditos

Desarrollado con rigor técnico y pasión por **Martín** (Barcelona) © 2026.  
Cumple con los estándares fotométricos europeos **UNE-EN 12464-1**.
