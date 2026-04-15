@echo off
REM Script de despliegue para producción SGATI (Windows)
echo 🚀 Desplegando SGATI en producción...

REM Crear backup del .env actual si existe
if exist .env (
    set timestamp=%date:~-4,4%%date:~-10,2%%date:~-7,2%_%time:~0,2%%time:~3,2%%time:~6,2%
    set timestamp=%timestamp: =0%
    copy .env .env.backup.%timestamp%
    echo ✅ Backup de .env creado
)

REM Copiar configuración de producción
copy .env.production .env
echo ✅ Configuración de producción activada

REM Instalar dependencias de producción
echo 📦 Instalando dependencias...
call composer install --optimize-autoloader --no-dev --no-interaction

REM Limpiar cachés
echo 🧹 Limpiando cachés...
call php artisan config:clear
call php artisan cache:clear
call php artisan route:clear
call php artisan view:clear

REM Verificar migraciones
echo 🗄️ Verificando migraciones...
call php artisan migrate:status

REM Generar cachés optimizados
echo ⚡ Generando cachés optimizados...
call php artisan config:cache
call php artisan route:cache
call php artisan view:cache
call php artisan event:cache

REM Compilar assets
echo 🎨 Compilando assets...
call npm ci --production
call npm run build

echo ✅ Despliegue completado!
echo ⚠️  Recuerda:
echo    - Configurar las credenciales en .env
echo    - Verificar la configuración del servidor web
echo    - Configurar SSL/TLS
echo    - Configurar monitoreo de logs

pause