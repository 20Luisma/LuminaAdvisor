#!/bin/bash
# guardar_memoria.sh — Backup automático de MEMORY.md con timestamp
# Uso: bash .agents/scripts/guardar_memoria.sh

TIMESTAMP=$(date +"%Y-%m-%d_%H-%M")
DEST="memory_backups/MEMORY_${TIMESTAMP}.md"

cp MEMORY.md "$DEST"
echo "✅ Backup guardado en: $DEST"

