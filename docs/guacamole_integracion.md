# Integración Apache Guacamole con SGATI

Guía completa de cómo se integró Apache Guacamole al sistema SGATI para permitir acceso remoto (RDP/SSH) a los servidores de la OTI directamente desde el navegador.

---

## ¿Qué es Apache Guacamole?

Apache Guacamole es un gateway de escritorio remoto **clientless** (sin instalar nada en el cliente). Permite conectarse a servidores via **RDP**, **SSH** o **VNC** desde cualquier navegador web. Se comunica a través de su propio demonio `guacd` y expone una API REST para administrar conexiones y sesiones.

---

## Arquitectura de la integración

```
[Navegador del usuario]
        │
        │ Click "Conectar" en tabla de servidores
        ▼
[Laravel SGATI]
  ├── ServerController::connect()
  │     ├── POST /api/tokens  →  obtiene authToken
  │     └── Redirige a URL del cliente Guacamole
        │
        ▼
[Apache Guacamole]  http://40.0.0.126:8080/guacamole
  └── Abre escritorio remoto en el navegador
        │
        ▼
[Servidor destino]  RDP (Windows) / SSH (Linux)
```

**Flujo al crear un servidor:**
```
Formulario → store() → GuacamoleService::createConnection() → guarda guacamole_connection_id en BD
```

**Flujo al conectarse:**
```
Click "Conectar" → connect() → GuacamoleService::authenticate() → URL con token → nueva pestaña
```

---

## Requisitos previos

- Apache Guacamole corriendo y accesible (en este caso `http://40.0.0.126:8080/guacamole`)
- Usuario administrador de Guacamole con permisos para crear/eliminar conexiones via API
- PHP con extensión `curl` habilitada (para Laravel HTTP Client)
- Los servidores destino deben tener habilitado RDP (puerto 3389) o SSH (puerto 22)

---

## Archivos creados / modificados

### Archivos nuevos

| Archivo | Descripción |
|---|---|
| `config/guacamole.php` | Configuración centralizada (URL, credenciales) |
| `app/Services/GuacamoleService.php` | Toda la lógica de integración con la API REST |

### Archivos modificados

| Archivo | Qué se agregó |
|---|---|
| `.env` | Variables `GUACAMOLE_URL`, `GUACAMOLE_USERNAME`, `GUACAMOLE_PASSWORD` |
| `app/Models/Server.php` | Campos en `$fillable`, accessor `guacamole_protocol`, accessor `default_remote_port` |
| `app/Http/Controllers/Admin/ServerController.php` | Método `connect()`, llamadas a `syncGuacamoleConnection()` en store/update/destroy |
| `routes/web.php` | Ruta `GET admin/servers/{server}/connect` |
| `database/migrations/..._create_servers_table.php` | Columnas `guacamole_connection_id` y `rdp_port` |
| `resources/views/admin/servers/index.blade.php` | Botón monitor + función JS `guacConnect()` |
| `resources/views/admin/servers/form.blade.php` | Sección "Acceso Remoto" dinámica con detección de protocolo |

---

## Configuración

### 1. Variables de entorno (`.env`)

```env
GUACAMOLE_URL=http://40.0.0.126:8080/guacamole
GUACAMOLE_USERNAME=guacadmin
GUACAMOLE_PASSWORD=tu_contraseña_aqui
```

### 2. Archivo de configuración (`config/guacamole.php`)

```php
return [
    'url'      => env('GUACAMOLE_URL', 'http://localhost:8080/guacamole'),
    'username' => env('GUACAMOLE_USERNAME', 'guacadmin'),
    'password' => env('GUACAMOLE_PASSWORD', 'guacadmin'),
];
```

---

## Base de datos

Se agregaron dos columnas a la tabla `servers`:

| Columna | Tipo | Default | Descripción |
|---|---|---|---|
| `guacamole_connection_id` | `string` nullable | `null` | ID de la conexión en Guacamole (se guarda al crear el servidor) |
| `rdp_port` | `unsignedSmallInteger` | `3389` | Puerto de conexión remota (RDP o SSH) |

---

## GuacamoleService

Ubicación: `app/Services/GuacamoleService.php`

### Métodos públicos

#### `authenticate(): array`
Autentica contra la API REST de Guacamole y devuelve el token de sesión.

```php
$auth = $guac->authenticate();
// ['authToken' => '...', 'dataSource' => 'postgresql']
```

Llama a:
```
POST /api/tokens
Content-Type: application/x-www-form-urlencoded
Body: username=guacadmin&password=...
```

#### `createConnection(Server $server, string $token, string $dataSource): string`
Crea una conexión RDP o SSH en Guacamole para el servidor. El protocolo se detecta automáticamente desde el campo `operating_system` del servidor.

Devuelve el `identifier` de la conexión creada (se guarda en `guacamole_connection_id`).

#### `updateConnection(Server $server, string $connectionId, ...): void`
Actualiza la conexión existente en Guacamole con los datos actuales del servidor (IP, credenciales, nombre).

#### `deleteConnection(string $connectionId, ...): void`
Elimina la conexión de Guacamole cuando el servidor es eliminado en SGATI.

#### `buildClientUrl(string $connectionId, string $token, string $dataSource): string`
Construye la URL directa al cliente Guacamole para abrir la sesión remota.

El identificador se codifica en base64 siguiendo el formato de Guacamole:
```
base64("{connectionId}\0c\0{dataSource}")
```

URL resultante:
```
http://40.0.0.126:8080/guacamole/#/client/{base64_id}?token={authToken}&GUAC_DATA_SOURCE={dataSource}
```

---

## Detección automática de protocolo

El sistema detecta el protocolo según el sistema operativo registrado:

| Sistema Operativo | Protocolo | Puerto por defecto |
|---|---|---|
| Contiene `windows` (cualquier capitalización) | **RDP** | 3389 |
| Cualquier otro (Ubuntu, Debian, CentOS...) | **SSH** | 22 |

Implementado en `app/Models/Server.php`:

```php
public function getGuacamoleProtocolAttribute(): string
{
    return str_contains(strtolower($this->operating_system ?? ''), 'windows')
        ? 'rdp'
        : 'ssh';
}
```

El formulario de creación/edición también refleja esto en tiempo real: cuando escribes el SO, el badge del protocolo cambia dinámicamente entre **RDP** (azul) y **SSH** (verde), y el puerto se ajusta automáticamente.

---

## Parámetros RDP configurados

Para conexiones Windows, se envían los siguientes parámetros a Guacamole (basados en una conexión funcionando verificada):

```php
'security'                   => 'any',          // Modo seguridad: Cualquier
'ignore-cert'                => 'true',          // Ignorar certificado del servidor
'cert-tofu'                  => 'true',          // Trust host certificate on first use
'color-depth'                => '16',
'enable-wallpaper'           => 'true',          // Fondo de pantalla activado
'enable-desktop-composition' => 'true',          // Composición Aero activada
'enable-menu-animations'     => 'true',          // Animaciones de menú activadas
```

---

## Ciclo de vida de una conexión Guacamole

```
┌─────────────────────────────────────────────────────┐
│ SGATI crea servidor                                  │
│   → GuacamoleService::createConnection()            │
│   → Guacamole crea conexión (identifier: "3")       │
│   → Server::guacamole_connection_id = "3"           │
└─────────────────────────────────────────────────────┘
                        │
                        ▼
┌─────────────────────────────────────────────────────┐
│ SGATI edita servidor                                 │
│   → GuacamoleService::updateConnection()            │
│   → Guacamole actualiza IP, nombre, credenciales    │
└─────────────────────────────────────────────────────┘
                        │
                        ▼
┌─────────────────────────────────────────────────────┐
│ Usuario hace click en "Conectar"                     │
│   → GuacamoleService::authenticate()  (token fresco)│
│   → GuacamoleService::buildClientUrl()              │
│   → JS abre nueva pestaña con escritorio remoto     │
└─────────────────────────────────────────────────────┘
                        │
                        ▼
┌─────────────────────────────────────────────────────┐
│ SGATI elimina servidor                               │
│   → GuacamoleService::deleteConnection()            │
│   → Guacamole elimina la conexión                   │
│   → Server se elimina de la BD                      │
└─────────────────────────────────────────────────────┘
```

---

## Crear un servidor Windows para conectarse por RDP

1. Ir a **Servidores → Nuevo Servidor**
2. Completar:
   - **Nombre**: nombre descriptivo (ej. `COMEDOR`, `PRODUCCION`)
   - **Sistema Operativo**: `Windows Server 2022` (o similar con la palabra "Windows")
   - **Tipo de Servidor**: Físico
   - **Usuario Windows**: `Administrador` (o el usuario del equipo)
   - **Contraseña**: contraseña del usuario Windows
   - **Puerto RDP**: `3389` (por defecto)
   - **Direcciones IP**: agregar la IP privada del servidor (ej. `192.168.254.33`)
3. Guardar → SGATI crea automáticamente la conexión en Guacamole
4. En la tabla de servidores aparecerá el icono **monitor verde** en la columna Acciones
5. Click en el monitor → se abre el escritorio remoto en una nueva pestaña

> **Nota:** Si el icono del monitor aparece apagado (gris), significa que el servidor no tiene `guacamole_connection_id`. Esto puede ocurrir si Guacamole no estaba disponible al crear el servidor. Solución: editar el servidor y guardar sin cambios.

---

## Solución de problemas

### La conexión en Guacamole se crea con nombre incorrecto
**Causa:** El payload HTTP no se enviaba como `application/json`.  
**Solución:** Usar `->asJson()` explícito en todas las llamadas POST/PUT a la API de Guacamole:
```php
Http::timeout(10)->asJson()->post($url, $payload);
```

### El botón "Conectar" no aparece (monitor gris)
El servidor no tiene `guacamole_connection_id`. Causas posibles:
- Guacamole no estaba disponible al crear el servidor
- El servidor no tiene IP registrada
- Error de autenticación con Guacamole

**Solución:** Revisar `storage/logs/laravel.log` para ver el error exacto, luego editar y guardar el servidor.

### Error de autenticación con Guacamole
Verificar en `.env`:
```env
GUACAMOLE_URL=http://40.0.0.126:8080/guacamole   # Sin trailing slash
GUACAMOLE_USERNAME=guacadmin
GUACAMOLE_PASSWORD=contraseña_correcta
```
Ejecutar `php artisan config:clear` después de modificar el `.env`.

### No se puede conectar al servidor destino
- Verificar que el firewall del servidor Windows permita RDP (puerto 3389)
- Verificar que el servicio "Escritorio remoto" esté habilitado en Windows
- Confirmar que la IP registrada en SGATI sea accesible desde el servidor Guacamole

---

## Seguridad

- Las contraseñas SSH/RDP se almacenan **encriptadas** en la BD usando el cast `'encrypted'` de Laravel.
- El token de Guacamole se genera en cada click (no se almacena), por lo que expira con la sesión.
- Las credenciales de Guacamole admin se guardan solo en `.env` (nunca en código).
- El endpoint `/admin/servers/{server}/connect` está protegido por el middleware `role:admin`.
