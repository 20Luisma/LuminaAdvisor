# AGENTS.md — LuminaAdvisor

## Identidad del Proyecto
**LuminaAdvisor** — Asesor inteligente de iluminación para el hogar.  
Calcula el tipo y cantidad de luminarias necesarias para cualquier habitación basándose en estándares europeos (UNE-EN 12464-1).

---

## Stack Tecnológico
- **Backend:** PHP 8.4+ estricto — Clean Architecture (sin frameworks)
- **Frontend:** HTML5 + CSS3 (Design Tokens, Dark Mode, Glassmorphism) + Vanilla JS
- **Persistencia:** SQLite (local/MVP) → MySQL Hostinger (producción)
- **Servidor:** Hostinger (`contenido.creawebes.com`)
- **Sin npm, sin Composer obligatorio, sin frameworks JS**

---

## Reglas de Arquitectura Específicas

### Separación estricta (Regla 17)
- HTML semántico en templates/vistas únicamente
- CSS en `public/css/` con Design Tokens (variables CSS)
- JS en `public/js/` como módulos desacoplados
- PHP de negocio SOLO en `src/` — cero SQL en vistas

### Pipeline de Deploy (La Santísima Trinidad — Regla 18)
1. **Local Mac:** `php -l` en todos los .php antes de mover nada
2. **GitHub:** commit atómico (`feat:`, `fix:`, `refactor:`) + push inmediato
3. **Hostinger:** SCP o `bin/deploy.sh` — NUNCA copiar a mano sin script

### Credenciales (Regla 19)
- `.env` en `.gitignore` siempre — verificar antes de cada push
- API keys solo desde variables de entorno

---

## Dominio de Negocio — Restricciones Core

### Estándar aplicado
**Norma UNE-EN 12464-1** para niveles de iluminancia por tipo de espacio (España/UE)

### Parámetros de entrada obligatorios
- Tipo de habitación (cocina, baño, dormitorio, salón, despacho, pasillo, garaje)
- Color de paredes (factor de reflexión)
- Metros cuadrados
- Altura del techo (m)
- Ventanas / entradas de luz natural

### Parámetros de salida
- Lúmenes totales necesarios
- Temperatura de color recomendada (Kelvin)
- Número de puntos de luz sugeridos
- Tipo de luminaria (LED integrado, E27, GU10, etc.)
- Consumo estimado (kWh/mes + €/mes)

### Fórmula base
```
Lúmenes = (Lux × m²) / reflexión_paredes / factor_luz_natural
```

---

## Design System UI
- **Paleta base:** Igual a GastosMartin (Dark Mode Fintech)
  - Fondo: `#0a0a0f`
  - Surface: `#13131a`
  - Accent: `#f59e0b` (ámbar — luminosa, cálida, apropiada para iluminación)
  - Text: `#e2e8f0`
- **Tipografía:** Inter / System UI
- **Componentes:** Cards glassmorphism, wizard paso a paso, resultado visual destacado
- **Sin diseños aburridos** — micro-animaciones, transiciones suaves

---

## Comandos de Verificación Post-Deploy
```bash
# Verificar HTTP
curl -s -o /dev/null -w "%{http_code}" https://contenido.creawebes.com/LuminaAdvisor/

# Lint PHP local
find src public -name "*.php" -exec php -l {} \;
```

---

## Memoria Persistente
Lee `MEMORY.md` antes de cualquier decisión técnica.  
Actualiza `MEMORY.md` al completar cada hito o resolver bugs críticos.  
Backup automático en `memory_backups/` con `bin/guardar_memoria.sh`.
