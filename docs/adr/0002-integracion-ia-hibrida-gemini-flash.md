# ADR 0002 — Arquitectura Híbrida: Cálculo Determinista + IA Gemini 3.8 Flash

**Fecha:** 2026-11-05  
**Estado:** Aprobado

## Contexto
Los modelos de lenguaje generativos (LLMs) pueden alucinar cálculos numéricos físicos y generan latencia/coste si se usan para aritmética determinista. Sin embargo, una fórmula fotométrica por sí sola no ofrece consejos de distribución espacial ni diseño de interiores en lenguaje claro.

## Decisión
Separar estrictamente la **capa física determinista** de la **capa cualitativa de IA**:
1. `CalculateLightingUseCase` ejecuta el cálculo exacto de la norma **UNE-EN 12464-1** en PHP puro (0 ms de latencia, 0 € de API, 100% reproducible).
2. `GeminiInteriorAdvisor` consume **Google Gemini 3.8 Flash** únicamente sobre los resultados ya validados, usando `responseMimeType: application/json` y `thinkingBudget: 0` para latencia < 2 s.
3. **Fallback silencioso obligatorio:** si falta `GEMINI_API_KEY` o falla la red/cuota (HTTP 429/504), la aplicación registra el aviso vía `ErrorLogger` y muestra el resultado matemático sin romperse.
4. **Transporte inyectable:** se permite inyectar un `callable` HTTP en `GeminiInteriorAdvisor` para pruebas unitarias 100% aisladas sin red.

## Consecuencias
- ✅ Cero alucinaciones en lúmenes, vatios, kWh y €/mes.
- ✅ Consejos de interiorismo en español terrenal sin jerga de laboratorio.
- ✅ Resiliencia total ante caídas de API externa y testabilidad unitaria completa.
