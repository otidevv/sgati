# 📋 Checklist de Configuración para Producción SGATI

## ✅ Configuraciones Completadas

### 🔧 Archivos de Configuración Creados
- [x] `.env.production` - Configuración de producción
- [x] `deploy-production.sh` - Script de despliegue Linux/Mac
- [x] `deploy-production.bat` - Script de despliegue Windows
- [x] `config/logging-production.php` - Configuración de logs optimizada
- [x] `.htaccess-production` - Configuración Apache para producción

### 🔑 Seguridad Configurada
- [x] Nueva APP_KEY generada para producción
- [x] APP_DEBUG=false
- [x] APP_ENV=production
- [x] Configuración de logs optimizada (solo errores)

## ⚠️ TAREAS PENDIENTES CRÍTICAS

### 🔐 Credenciales por Configurar
Debes actualizar estas credenciales en `.env.production`:

```env
# Base de datos de producción
DB_HOST=tu-servidor-bd-produccion
DB_DATABASE=sgati_production
DB_USERNAME=usuario_prod
DB_PASSWORD=CAMBIAR_PASSWORD_SEGURO

# Email SMTP
MAIL_HOST=smtp.tu-dominio.com
MAIL_USERNAME=noreply@tu-dominio.com
MAIL_PASSWORD=CAMBIAR_PASSWORD_MAIL

# Guacamole
GUACAMOLE_URL=https://guacamole.tu-dominio.com:8080/guacamole
GUACAMOLE_USERNAME=admin_prod
GUACAMOLE_PASSWORD=CAMBIAR_PASSWORD_GUACAMOLE

# Dominio
APP_URL=https://sgati.tu-dominio.com
SESSION_DOMAIN=.tu-dominio.com
```

### 🌐 Configuración del Servidor Web

#### Apache
1. Copiar `.htaccess-production` a `public/.htaccess`
2. Configurar Virtual Host:
   ```apache
   <VirtualHost *:443>
       ServerName sgati.tu-dominio.com
       DocumentRoot /ruta/a/sgati/public
       
       SSLEngine on
       SSLCertificateFile /ruta/a/certificado.crt
       SSLCertificateKeyFile /ruta/a/private.key
       
       # Incluir configuraciones de seguridad
   </VirtualHost>
   ```

#### Nginx (Alternativa)
```nginx
server {
    listen 443 ssl;
    server_name sgati.tu-dominio.com;
    root /ruta/a/sgati/public;
    
    ssl_certificate /ruta/a/certificado.crt;
    ssl_certificate_key /ruta/a/private.key;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        include fastcgi_params;
    }
}
```

### 📊 Base de Datos
1. Crear base de datos de producción
2. Configurar usuario con permisos específicos
3. Ejecutar migraciones:
   ```bash
   php artisan migrate --force
   ```
4. Poblar datos iniciales si es necesario:
   ```bash
   php artisan db:seed --force
   ```

### 🔒 SSL/TLS
- [ ] Obtener certificado SSL (Let's Encrypt, comercial, etc.)
- [ ] Configurar HTTPS en servidor web
- [ ] Verificar configuración HSTS
- [ ] Probar redirección HTTP → HTTPS

### 🗂️ Permisos de Archivos
```bash
# Permisos recomendados
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
chown -R www-data:www-data storage/
chown -R www-data:www-data bootstrap/cache/
```

### 📈 Monitoreo y Logs
- [ ] Configurar rotación de logs
- [ ] Configurar alertas por email para errores críticos
- [ ] Configurar monitoreo de uptime
- [ ] Configurar backup automático de base de datos

### 🚀 Despliegue
Para desplegar ejecuta:
```bash
# Linux/Mac
./deploy-production.sh

# Windows
deploy-production.bat
```

### 🧪 Testing Post-Despliegue
- [ ] Verificar que la aplicación carga correctamente
- [ ] Probar login de usuarios
- [ ] Verificar conexión a base de datos PostgreSQL
- [ ] Probar integración con Guacamole
- [ ] Verificar logs se están generando correctamente
- [ ] Probar formularios y funcionalidades principales

### 🔧 Optimizaciones Adicionales
- [ ] Configurar CDN para assets estáticos
- [ ] Configurar Redis para cache (opcional)
- [ ] Configurar queue workers para procesamiento en background
- [ ] Implementar monitoreo de performance

## 📞 Soporte
En caso de problemas, revisar:
1. Logs en `storage/logs/`
2. Logs del servidor web
3. Logs de PHP
4. Estado de la base de datos PostgreSQL

---
**Generado automáticamente por Claude Code**