# 🧠 MEMORY.md — LuminaAdvisor

## 🌍 Contexto General del Proyecto

**LuminaAdvisor** es un asesor inteligente de iluminación para el hogar.  
Permite al usuario describir una habitación (tipo, color de paredes, metros cuadrados, altura del techo, ventanas/luz natural) y recibe una recomendación precisa de:
- Cuántos lúmenes necesita esa habitación
- Qué tipo de bombilla/luminaria es la más adecuada (temperatura de color)
- Cuántos puntos de luz instalar
- Consumo estimado en kWh y coste mensual en €

**Estándares aplicados:** Norma UNE-EN 12464-1 (niveles de iluminancia por tipo de espacio)  
**Mercado objetivo:** España (precios en €, estándares europeos)  
**Propietario:** Martín (Esparreguera, Barcelona)

---

## 🏗️ Arquitectura Técnica

- **Backend:** PHP 8.4+ estricto — Clean Architecture (Domain / Application / Infrastructure / Presentation)
- **Frontend:** HTML5 semántico + CSS3 (Design Tokens, Dark Mode, Glassmorphism) + Vanilla JS modular
- **Persistencia:** A definir (SQLite local para MVP, MySQL Hostinger para producción)
- **IA:** Posible integración futura con Gemini para recomendaciones avanzadas
- **Servidor destino:** Hostinger (`contenido.creawebes.com`)
- **Repositorio:** A crear en GitHub (privado)
- **Sin frameworks PHP ni JS** — filosofía del proyecto GastosMartin

### Estructura de carpetas
```
LuminaAdvisor/
├── AGENTS.md
├── MEMORY.md
├── .env.example
├── .gitignore
├── README.md
├── memory_backups/
├── docs/adr/
├── .agents/
│   ├── AGENTS.md
│   ├── rules/
│   ├── workflows/
│   └── scripts/guardar_memoria.sh
├── src/
│   ├── Domain/Entities/        ← Room.php, LightRecommendation.php
│   ├── Application/UseCases/   ← CalculateLightingUseCase.php
│   ├── Infrastructure/         ← PDO, Storage
│   └── Presentation/           ← Controllers
├── public/
│   ├── css/
│   ├── js/
│   └── index.php               ← Front Controller
├── templates/
├── bin/
└── storage/
```

---

## 📌 Estado Actual del Proyecto

- [x] Bootstrap completo del proyecto (estructura, MEMORY.md, AGENTS.md, .gitignore)
- [x] Definir entidad de dominio `Room.php` con todos los parámetros de entrada
- [x] Implementar `CalculateLightingUseCase.php` con lógica UNE-EN 12464
- [x] Crear tabla de lúmenes y temperatura de color por tipo de habitación
- [x] Diseñar UI: wizard de 2 pasos + glassmorphism ámbar + resultado animado
- [x] Integrar factor de reflexión de paredes y factor de luz natural
- [x] Calcular consumo €/mes y kWh/mes del setup
- [x] Repositorio GitHub conectado y sincronizado (`20Luisma/LuminaAdvisor`)
- [x] Despliegue en producción Hostinger activo (`contenido.creawebes.com/LuminaAdvisor/`)
- [ ] Posible integración opcional con IA (Gemini) para consejos de estilismo/interiorismo

---

## 🧮 Lógica de Negocio Core (Fórmula base)

```
Lúmenes necesarios = (Lux requeridos × m²) / Factor de reflexión de paredes / Factor de luz natural

Lux por tipo de habitación (UNE-EN 12464):
- Dormitorio:    100–200 lux
- Salón:         200–300 lux
- Cocina:        300–500 lux
- Baño:          200–500 lux
- Despacho:      500–750 lux
- Pasillo:       100–150 lux
- Garaje:        75–100 lux

Factor de reflexión de paredes:
- Blanco/crema:  0.80
- Beige/ocre:    0.60
- Gris medio:    0.40
- Azul/verde:    0.30
- Rojo/marrón:   0.20
- Negro/oscuro:  0.10

Factor luz natural (ventanas):
- Sin ventanas:    1.00 (necesitás todo artificial)
- Ventana pequeña: 0.85
- Ventana grande:  0.65
- Muchas ventanas: 0.50

Temperatura de color por uso:
- Dormitorio/Salón: 2700K–3000K (cálida, relax)
- Cocina/Baño:      3500K–4000K (neutra, funcional)
- Despacho:         4000K–5000K (fría, concentración)
```

---

## 🚀 Próximos Pasos Prioritarios

1. Crear entidad `Room.php` y `LightRecommendation.php`
2. Implementar `CalculateLightingUseCase.php`
3. Diseñar UI formulario paso a paso (wizard de 3 pasos)
4. Implementar resultado visual con recomendación clara
5. Primer commit + push a GitHub
6. Deploy a Hostinger

---

## 🕰️ Historial de Sesiones

### 2026-11-05 (Sesión 2 — MVP Completo y Deploy Producción)
- Implementación de entidades: `Room.php` y `LightRecommendation.php`.
- Caso de uso `CalculateLightingUseCase.php` con física exacta UNE-EN 12464-1 (cero alucinaciones).
- Interfaz completa: Front Controller `index.php`, `LightingController.php`, wizard de 2 pasos, glassmorphism con acento ámbar (`style.css`), animaciones de contadores (`app.js`).
- Configuración de Apache: `index.php` raíz y `.htaccess` con rewrite a `public/`.
- Pipeline de deploy: `bin/deploy.sh` blindado (lee credenciales de `.env` ignorado en git).
- Despliegue completado y verificado en Hostinger: `https://contenido.creawebes.com/LuminaAdvisor/` (HTTP 200).

### 2026-11-04 (Sesión 1 — Bootstrap)
- Concepción del proyecto: asesor de luminarias para el hogar
- Decisión de stack: PHP 8.4+ · España · proyecto independiente
- Bootstrap completo ejecutado (Regla 22 God-Mode)
- Lógica de negocio core definida (fórmula UNE-EN 12464)
- Parámetros de entrada definidos: tipo de habitación, color de paredes, m², altura techo, ventanas
- Parámetros de salida: lúmenes totales, temperatura de color, nº de puntos de luz, kWh/mes + €/mes

