# Workflow: save_context

## Objetivo
Guardar el estado actual del proyecto en MEMORY.md y crear backup con timestamp.

## Pasos
1. Actualizar la sección "Estado Actual" en MEMORY.md con los cambios recientes
2. Añadir entrada en "Historial de Sesiones" con fecha y resumen de lo hecho
3. Correr: `bash .agents/scripts/guardar_memoria.sh`
4. Confirmar que el backup existe en `memory_backups/`

## Trigger
Al completar un hito, resolver un bug crítico o cerrar una sesión de trabajo.

