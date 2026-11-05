#!/usr/bin/env bash
# ==============================================================================
# LuminaAdvisor — Script de Despliegue Seguro a Hostinger VPS
# La Santísima Trinidad: Local ➔ GitHub ➔ Producción VPS (Regla 18 y 19)
# Cero secretos hardcodeados: lee variables exclusivamente de .env
# ==============================================================================

set -euo pipefail

DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
ENV_FILE="$DIR/.env"

if [ ! -f "$ENV_FILE" ]; then
    echo "❌ Error: No se encontró el archivo .env en $ENV_FILE"
    exit 1
fi

# Cargar variables de entorno ignorando comentarios
export $(grep -v '^#' "$ENV_FILE" | xargs)

if [ -z "${VPS_HOST:-}" ] || [ -z "${VPS_USER:-}" ] || [ -z "${VPS_PASS:-}" ] || [ -z "${VPS_REMOTE_PATH:-}" ]; then
    echo "❌ Error: Faltan variables de configuración VPS en .env"
    exit 1
fi

VPS_PORT="${VPS_PORT:-22}"

echo "========================================================"
echo "🚀 Iniciando despliegue de LuminaAdvisor a Hostinger"
echo "🌐 Destino: $VPS_USER@$VPS_HOST:$VPS_REMOTE_PATH"
echo "========================================================"

# 1. Validación estática previa (Local staging)
echo "🔍 1. Ejecutando verificación de sintaxis PHP..."
find "$DIR/src" "$DIR/public" "$DIR/templates" -name "*.php" -exec php -l {} \; > /dev/null
php -l "$DIR/index.php" > /dev/null
echo "✓ Sintaxis PHP 100% válida."

# 2. Crear carpeta remota si no existe
echo "📁 2. Verificando carpeta remota en VPS..."
sshpass -p "$VPS_PASS" ssh -o StrictHostKeyChecking=no -p "$VPS_PORT" "$VPS_USER@$VPS_HOST" "mkdir -p '$VPS_REMOTE_PATH'"

# 3. Sincronizar proyecto hacia el VPS (excluyendo git, env, backups locales)
echo "🔄 3. Sincronizando archivos con rsync..."
sshpass -p "$VPS_PASS" rsync -avz --delete \
    -e "ssh -p $VPS_PORT -o StrictHostKeyChecking=no" \
    --exclude=".git*" \
    --exclude=".env*" \
    --exclude="memory_backups/" \
    --exclude="docs/" \
    --exclude=".agents/" \
    --exclude="bin/" \
    "$DIR/" \
    "$VPS_USER@$VPS_HOST:$VPS_REMOTE_PATH/"

# 4. Asegurar permisos de seguridad y sincronizar credenciales runtime (.env con chmod 600)
echo "🔐 4. Configurando permisos y variables de entorno en VPS..."
sshpass -p "$VPS_PASS" ssh -o StrictHostKeyChecking=no -p "$VPS_PORT" "$VPS_USER@$VPS_HOST" "chmod 755 '$VPS_REMOTE_PATH' && find '$VPS_REMOTE_PATH' -type f -exec chmod 644 {} \;
cat << 'EOF' > '$VPS_REMOTE_PATH/.env'
APP_ENV=production
APP_NAME=LuminaAdvisor
APP_URL=https://contenido.creawebes.com/LuminaAdvisor
GEMINI_API_KEY=$GEMINI_API_KEY
EOF
chmod 600 '$VPS_REMOTE_PATH/.env'"

# 5. Verificación HTTP post-despliegue
echo "========================================================"
echo "📡 5. Verificando estado en https://contenido.creawebes.com/LuminaAdvisor/ ..."
sleep 1
HTTP_STATUS=$(curl -s -o /dev/null -w "%{http_code}" https://contenido.creawebes.com/LuminaAdvisor/)
echo "🌐 Código HTTP obtenido: $HTTP_STATUS"

if [ "$HTTP_STATUS" = "200" ]; then
    echo "✅ Despliegue completado con éxito (HTTP 200 OK)."
else
    echo "⚠️ Alerta: El servidor devolvió HTTP $HTTP_STATUS. Revisar logs o configuración."
fi
echo "========================================================"

