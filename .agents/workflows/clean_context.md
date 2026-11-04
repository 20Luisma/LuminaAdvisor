# Workflow: clean_context

## Objetivo
Limpiar y sintetizar MEMORY.md cuando se vuelva muy extenso, preservando solo lo relevante.

## Pasos
1. Crear backup ANTES de limpiar: `bash .agents/scripts/guardar_memoria.sh`
2. En "Historial de Sesiones": comprimir sesiones antiguas en un resumen de 2-3 líneas por sesión
3. Eliminar tareas ya completadas de "Próximos Pasos"
4. Mantener intactos: Contexto General, Arquitectura, Lógica de Negocio, Estado Actual

## Trigger
Cuando MEMORY.md supere las 200 líneas o Martín pida `/clean_context`.
