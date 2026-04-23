<#
.SYNOPSIS
    Configura y levanta Apache Guacamole en Docker para SGATI.

.DESCRIPTION
    Este script realiza los siguientes pasos:
      1. Verifica que Docker Desktop esté corriendo
      2. Crea los directorios necesarios
      3. Copia .env.example → .env si no existe
      4. Genera el SQL de inicialización de la BD de Guacamole
      5. (Opcional) Genera un certificado SSL auto-firmado
      6. Levanta los contenedores con docker compose

.PARAMETER GenerateCert
    Genera un certificado SSL auto-firmado para nginx (requiere openssl en PATH).

.PARAMETER Down
    Detiene y elimina los contenedores (no borra los volúmenes).

.PARAMETER Reset
    Detiene contenedores Y elimina el volumen de la base de datos (¡borra todos los datos!).

.EXAMPLE
    .\setup.ps1
    .\setup.ps1 -GenerateCert
    .\setup.ps1 -Down
    .\setup.ps1 -Reset
#>

param(
    [switch]$GenerateCert,
    [switch]$Down,
    [switch]$Reset
)

$ErrorActionPreference = 'Stop'
$GUACAMOLE_VERSION     = '1.5.5'
$SCRIPT_DIR            = $PSScriptRoot

# ── Helpers ───────────────────────────────────────────────────────────
function Write-Step  { param($msg) Write-Host "`n  ► $msg" -ForegroundColor Cyan }
function Write-OK    { param($msg) Write-Host "    ✓ $msg" -ForegroundColor Green }
function Write-Warn  { param($msg) Write-Host "    ⚠ $msg" -ForegroundColor Yellow }
function Write-Err   { param($msg) Write-Host "    ✗ $msg" -ForegroundColor Red; exit 1 }

Write-Host ""
Write-Host "  ═══════════════════════════════════════════════════" -ForegroundColor Blue
Write-Host "   SGATI — Apache Guacamole $GUACAMOLE_VERSION Setup  " -ForegroundColor Blue
Write-Host "  ═══════════════════════════════════════════════════" -ForegroundColor Blue

Set-Location $SCRIPT_DIR

# ── Verificar Docker ──────────────────────────────────────────────────
Write-Step "Verificando Docker Desktop..."
try {
    $dockerInfo = docker info 2>&1
    if ($LASTEXITCODE -ne 0) { throw "Docker no responde" }
    Write-OK "Docker está corriendo"
} catch {
    Write-Err "Docker Desktop no está iniciado. Ábrelo y vuelve a ejecutar este script."
}

# ── Modo: Down ────────────────────────────────────────────────────────
if ($Down) {
    Write-Step "Deteniendo contenedores..."
    docker compose down
    Write-OK "Contenedores detenidos (volúmenes conservados)"
    exit 0
}

# ── Modo: Reset ───────────────────────────────────────────────────────
if ($Reset) {
    Write-Warn "RESET: Se eliminarán los contenedores Y el volumen de base de datos."
    $confirm = Read-Host "  ¿Confirmas? Escribe 'si' para continuar"
    if ($confirm -ne 'si') { Write-Host "  Cancelado."; exit 0 }
    docker compose down -v
    Write-OK "Contenedores y volumen eliminados. Ejecuta el script sin -Reset para reiniciar."
    exit 0
}

# ── Crear estructura de directorios ───────────────────────────────────
Write-Step "Creando estructura de directorios..."

$dirs = @(
    'init',
    'data\drive',
    'data\record',
    'nginx\certs',
    'guacamole-home'
)

foreach ($d in $dirs) {
    $path = Join-Path $SCRIPT_DIR $d
    if (-not (Test-Path $path)) {
        New-Item -ItemType Directory -Path $path -Force | Out-Null
        Write-OK "Creado: $d"
    } else {
        Write-OK "Ya existe: $d"
    }
}

# ── Crear .env desde .env.example ─────────────────────────────────────
Write-Step "Configurando .env..."
$envFile     = Join-Path $SCRIPT_DIR '.env'
$envExample  = Join-Path $SCRIPT_DIR '.env.example'

if (-not (Test-Path $envFile)) {
    Copy-Item $envExample $envFile
    Write-Warn ".env creado desde .env.example"
    Write-Warn "IMPORTANTE: Edita .env y cambia GUAC_DB_PASSWORD antes de continuar."
    Write-Host ""
    Write-Host "  Abre el archivo: $envFile" -ForegroundColor Yellow
    $continue = Read-Host "  ¿Ya editaste el .env? Escribe 'si' para continuar"
    if ($continue -ne 'si') { Write-Host "  Edita .env y vuelve a ejecutar el script."; exit 0 }
} else {
    Write-OK ".env ya existe"
}

# Cargar variables del .env para validaciones
$envContent = Get-Content $envFile | Where-Object { $_ -match '^[^#].*=.*' }
$envVars = @{}
foreach ($line in $envContent) {
    $parts = $line -split '=', 2
    if ($parts.Count -eq 2) {
        $envVars[$parts[0].Trim()] = $parts[1].Trim()
    }
}

$password = $envVars['GUAC_DB_PASSWORD']
if ([string]::IsNullOrWhiteSpace($password) -or $password -eq 'cambia_esta_contrasena_segura') {
    Write-Err "GUAC_DB_PASSWORD en .env sigue siendo el valor por defecto. Cámbialo antes de continuar."
}
Write-OK "GUAC_DB_PASSWORD configurado"

# ── Generar SQL de inicialización ─────────────────────────────────────
Write-Step "Generando SQL de inicialización de Guacamole..."
$initSql = Join-Path $SCRIPT_DIR 'init\initdb.sql'

if (Test-Path $initSql) {
    Write-OK "init\initdb.sql ya existe — se omite la generación"
} else {
    Write-Host "    Descargando imagen guacamole/$GUACAMOLE_VERSION (puede tardar)..." -ForegroundColor Gray
    $sqlOutput = docker run --rm "guacamole/guacamole:$GUACAMOLE_VERSION" /opt/guacamole/bin/initdb.sh --postgresql 2>&1

    if ($LASTEXITCODE -ne 0) {
        Write-Err "No se pudo generar el SQL. Error: $sqlOutput"
    }

    # Filtrar solo líneas SQL (el script imprime algo de log al inicio)
    $sqlLines = $sqlOutput | Where-Object { $_ -notmatch '^\s*$' -or $_ -match ';$|^--' }
    $sqlLines | Out-File -FilePath $initSql -Encoding utf8

    Write-OK "init\initdb.sql generado correctamente"
}

# ── Generar certificado SSL auto-firmado (opcional) ───────────────────
if ($GenerateCert) {
    Write-Step "Generando certificado SSL auto-firmado..."
    $certDir  = Join-Path $SCRIPT_DIR 'nginx\certs'
    $certFile = Join-Path $certDir 'fullchain.pem'
    $keyFile  = Join-Path $certDir 'privkey.pem'

    if ((Test-Path $certFile) -and (Test-Path $keyFile)) {
        Write-OK "Certificados ya existen en nginx\certs\"
    } else {
        try {
            $serverName = if ($envVars['GUAC_SERVER_NAME']) { $envVars['GUAC_SERVER_NAME'] } else { 'guacamole.unamad.local' }
            & openssl req -x509 -nodes -days 365 -newkey rsa:2048 `
                -keyout $keyFile `
                -out $certFile `
                -subj "/CN=$serverName/O=UNAMAD/C=PE" 2>&1 | Out-Null

            if ($LASTEXITCODE -ne 0) { throw "openssl falló" }
            Write-OK "Certificado auto-firmado generado para: $serverName"
            Write-Warn "Para producción usa un certificado real (Let's Encrypt, institucional, etc.)"
            Write-Warn "Activa el bloque HTTPS en nginx\nginx.conf"
        } catch {
            Write-Warn "No se pudo generar el certificado. ¿Está openssl en el PATH?"
            Write-Warn "Instálalo con: winget install ShiningLight.OpenSSL.Light"
        }
    }
}

# ── Levantar servicios ────────────────────────────────────────────────
Write-Step "Levantando servicios con Docker Compose..."
docker compose up -d --build

if ($LASTEXITCODE -ne 0) {
    Write-Err "docker compose up falló. Revisa los logs con: docker compose logs"
}

# ── Resumen final ─────────────────────────────────────────────────────
$httpPort = if ($envVars['NGINX_HTTP_PORT']) { $envVars['NGINX_HTTP_PORT'] } else { '80' }
$serverName = if ($envVars['GUAC_SERVER_NAME']) { $envVars['GUAC_SERVER_NAME'] } else { 'localhost' }

Write-Host ""
Write-Host "  ═══════════════════════════════════════════════════" -ForegroundColor Green
Write-Host "   ✓  Guacamole levantado correctamente              " -ForegroundColor Green
Write-Host "  ═══════════════════════════════════════════════════" -ForegroundColor Green
Write-Host ""
Write-Host "  URL de acceso:  http://$serverName`:$httpPort/guacamole/" -ForegroundColor White
Write-Host "  Si accedes por IP local: http://localhost:$httpPort/guacamole/"  -ForegroundColor White
Write-Host ""
Write-Host "  Credenciales iniciales:" -ForegroundColor Yellow
Write-Host "    Usuario:    guacadmin" -ForegroundColor White
Write-Host "    Contraseña: guacadmin  ← CAMBIA ESTO DE INMEDIATO" -ForegroundColor Red
Write-Host ""
Write-Host "  Comandos útiles:" -ForegroundColor Gray
Write-Host "    Ver logs:         docker compose logs -f" -ForegroundColor Gray
Write-Host "    Ver logs guacd:   docker compose logs -f guacd" -ForegroundColor Gray
Write-Host "    Detener:          .\setup.ps1 -Down" -ForegroundColor Gray
Write-Host "    Reset completo:   .\setup.ps1 -Reset" -ForegroundColor Gray
Write-Host ""
Write-Host "  NOTA: La primera vez que se inicia puede tardar ~30 seg" -ForegroundColor Yellow
Write-Host "  mientras PostgreSQL aplica el esquema de base de datos." -ForegroundColor Yellow
Write-Host ""
