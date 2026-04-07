<?php

namespace App\Services;

use App\Models\Server;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GuacamoleService
{
    private string $baseUrl;
    private string $username;
    private string $password;

    public function __construct()
    {
        $this->baseUrl  = rtrim(config('guacamole.url'), '/');
        $this->username = config('guacamole.username');
        $this->password = config('guacamole.password');
    }

    // ── Autenticación ────────────────────────────────────────────────

    /**
     * Obtiene un token de sesión de Guacamole.
     * Retorna ['authToken' => '...', 'dataSource' => '...']
     */
    public function authenticate(): array
    {
        $response = Http::timeout(10)
            ->asForm()
            ->post("{$this->baseUrl}/api/tokens", [
                'username' => $this->username,
                'password' => $this->password,
            ]);

        if (! $response->successful()) {
            throw new \RuntimeException(
                'No se pudo autenticar con Guacamole. Verifica GUACAMOLE_URL, GUACAMOLE_USERNAME y GUACAMOLE_PASSWORD.'
            );
        }

        $data = $response->json();

        return [
            'authToken'  => $data['authToken'],
            'dataSource' => $data['dataSource'],
        ];
    }

    // ── Conexiones ───────────────────────────────────────────────────

    /**
     * Crea una conexión en Guacamole para el servidor.
     * El protocolo (rdp / ssh) se detecta automáticamente desde el OS del servidor.
     */
    public function createConnection(Server $server, string $token, string $dataSource): string
    {
        $protocol = $server->guacamole_protocol; // 'rdp' | 'ssh'
        $payload  = $this->buildPayload($server, $protocol);

        $response = Http::timeout(10)
            ->asJson()
            ->post(
                "{$this->baseUrl}/api/session/data/{$dataSource}/connections?token={$token}",
                $payload
            );

        if (! $response->successful()) {
            throw new \RuntimeException(
                "Error al crear la conexión [{$protocol}] en Guacamole para [{$server->name}]: " . $response->body()
            );
        }

        return (string) $response->json('identifier');
    }

    /**
     * Actualiza la conexión existente con los datos actuales del servidor.
     */
    public function updateConnection(Server $server, string $connectionId, string $token, string $dataSource): void
    {
        $protocol = $server->guacamole_protocol;
        $payload  = $this->buildPayload($server, $protocol);

        $response = Http::timeout(10)
            ->asJson()
            ->put(
                "{$this->baseUrl}/api/session/data/{$dataSource}/connections/{$connectionId}?token={$token}",
                $payload
            );

        if (! $response->successful()) {
            Log::warning("No se pudo actualizar conexión Guacamole [{$server->name}]: " . $response->body());
        }
    }

    /**
     * Elimina la conexión del servidor en Guacamole.
     */
    public function deleteConnection(string $connectionId, string $token, string $dataSource): void
    {
        Http::timeout(10)
            ->delete(
                "{$this->baseUrl}/api/session/data/{$dataSource}/connections/{$connectionId}?token={$token}"
            );
    }

    // ── URL de cliente ───────────────────────────────────────────────

    /**
     * Construye la URL del cliente Guacamole para abrir la conexión directamente.
     * Guacamole codifica el identificador como: base64("{id}\0c\0{dataSource}")
     */
    public function buildClientUrl(string $connectionId, string $token, string $dataSource): string
    {
        $identifier = base64_encode("{$connectionId}\0c\0{$dataSource}");

        return "{$this->baseUrl}/#/client/{$identifier}?token={$token}&GUAC_DATA_SOURCE={$dataSource}";
    }

    // ── Helpers privados ─────────────────────────────────────────────

    /**
     * Construye el payload de conexión según el protocolo.
     */
    private function buildPayload(Server $server, string $protocol): array
    {
        $ip   = $this->resolveIp($server);
        $port = (string) ($server->rdp_port ?? $server->default_remote_port);

        $parameters = match ($protocol) {
            'rdp' => [
                // Red
                'hostname'           => $ip,
                'port'               => $port,

                // Autenticación
                'username'           => $server->ssh_user ?? '',
                'password'           => $server->ssh_password ?? '',
                'security'           => 'any',
                'ignore-cert'        => 'true',
                'cert-tofu'          => 'true',   // Trust host certificate on first use

                // Pantalla
                'color-depth'        => '16',

                // Rendimiento — mismos flags que la conexión que funciona
                'enable-wallpaper'             => 'true',
                'enable-theming'               => '',
                'enable-font-smoothing'        => '',
                'enable-full-window-drag'      => '',
                'enable-desktop-composition'   => 'true',  // Aero
                'enable-menu-animations'       => 'true',
                'disable-bitmap-caching'       => '',
                'disable-offscreen-caching'    => '',
                'disable-glyph-caching'        => '',
                'disable-gfx'                  => '',
            ],
            'ssh' => [
                'hostname' => $ip,
                'port'     => $port,
                'username' => $server->ssh_user ?? '',
                'password' => $server->ssh_password ?? '',
            ],
            default => throw new \RuntimeException("Protocolo Guacamole no soportado: {$protocol}"),
        };

        return [
            'parentIdentifier' => 'ROOT',
            'name'             => $server->name,
            'protocol'         => $protocol,
            'parameters'       => $parameters,
            'attributes'       => [
                'max-connections'          => '',
                'max-connections-per-user' => '',
                'weight'                   => '',
                'failover-only'            => '',
                'guacd-port'               => '',
                'guacd-hostname'           => '',
                'guacd-encryption'         => '',
            ],
        ];
    }

    private function resolveIp(Server $server): string
    {
        $ip = $server->primaryIp?->ip_address
            ?? $server->ips->where('type', 'private')->first()?->ip_address
            ?? $server->ips->first()?->ip_address;

        if (! $ip) {
            throw new \RuntimeException(
                "El servidor [{$server->name}] no tiene ninguna IP configurada."
            );
        }

        return $ip;
    }
}
