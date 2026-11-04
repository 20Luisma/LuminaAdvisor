# Workflow: load_context

## Objetivo
Cargar la memoria persistente del proyecto LuminaAdvisor al iniciar una nueva sesión.

## Pasos
1. Leer `MEMORY.md` en la raíz del proyecto
2. Verificar el estado actual (sección "Estado Actual")
3. Revisar los próximos pasos pendientes
4. Leer `AGENTS.md` para confirmar las reglas de arquitectura activas
5. Correr `git status` y `git log --oneline -3` para saber en qué punto está el repo

## Trigger
Al inicio de cualquier sesión nueva en este workspace.

