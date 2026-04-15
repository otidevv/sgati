#!/bin/bash

# Script de despliegue para producción SGATI
echo "🚀 Desplegando SGATI en producción..."

# Crear backup del .env actual si existe
if [ -f .env ]; then
    cp .env .env.backup.$(date +%Y%m%d_%H%M%S)
    echo "✅ Backup de .env creado"
fi

# Copiar configuración de producción
cp .env.production .env
echo "✅ Configuración de producción activada"

# Instalar dependencias de producción
echo "📦 Instalando dependencias..."
composer install --optimize-autoloader --no-dev --no-interaction

# Limpiar cachés
echo "🧹 Limpiando cachés..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Ejecutar migraciones (cuidado en producción)
echo "🗄️ Verificando migraciones..."
php artisan migrate:status

# Generar cachés optimizados
echo "⚡ Generando cachés optimizados..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Compilar assets
echo "🎨 Compilando assets..."
npm ci --production
npm run build

# Configurar permisos
echo "🔐 Configurando permisos..."
chmod -R 755 storage
chmod -R 755 bootstrap/cache

echo "✅ Despliegue completado!"
echo "⚠️  Recuerda:"
echo "   - Configurar las credenciales en .env"
echo "   - Verificar la configuración del servidor web"
echo "   - Configurar SSL/TLS"
echo "   - Configurar monitoreo de logs"