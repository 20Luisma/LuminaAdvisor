# ADR 0001 — Arquitectura Inicial LuminaAdvisor

**Fecha:** 2026-11-04  
**Estado:** Aprobado

## Contexto
Nuevo proyecto independiente de asesoría de iluminación para el hogar. Se necesita definir la arquitectura base antes de escribir código.

## Decisión
Usar **PHP 8.4+ con Clean Architecture** (mismo stack que GastosMartin), sin frameworks, con frontend en Vanilla JS + CSS3.

**Motivos:**
- Mismo stack = mismo servidor Hostinger sin coste adicional
- Martín ya domina este stack
- Sin dependencias externas = cero vulnerabilidades de supply chain
- Despliegue inmediato sin pipelines complejos

## Estructura de capas
- **Domain:** Entidades puras (`Room`, `LightRecommendation`) sin dependencias externas
- **Application:** Use Cases con lógica UNE-EN 12464-1
- **Infrastructure:** PDO (SQLite local / MySQL Hostinger)
- **Presentation:** Front Controller PHP + Templates HTML

## Consecuencias
- ✅ Despliegue simple vía SCP a Hostinger
- ✅ Sin npm, sin Composer obligatorio para MVP
- ✅ Cero frameworks = cero breaking changes futuros
- ⚠️ Para escalar a multi-usuario real, se necesitará auth layer (sesiones PHP)

