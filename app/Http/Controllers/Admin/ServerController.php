<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Server;
use App\Models\ServerIp;
use App\Models\ServerContainer;
use App\Enums\ServerFunction;
use App\Services\GuacamoleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ServerController extends Controller
{
    public function index()
    {
        $servers = Server::withCount(['deployments', 'activeContainers', 'databaseServers'])
            ->with(['primaryIp', 'ips'])
            ->orderBy('name')
            ->get();

        return view('admin.servers.index', compact('servers'));
    }

    public function create()
    {
        $functions = ServerFunction::cases();
        return view('admin.servers.form', [
            'server'    => new Server,
            'functions' => $functions,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'               => 'required|string|max:100|unique:servers,name',
            'operating_system'   => 'nullable|string|max:150',
            'function'           => 'required|in:' . implode(',', array_column(ServerFunction::cases(), 'value')),
            'host_type'          => 'required|in:physical,virtual,cloud',
            'cpu_cores'          => 'nullable|integer|min:1|max:512',
            'ram_gb'             => 'nullable|integer|min:1|max:65536',
            'storage_gb'         => 'nullable|integer|min:1',
            'cloud_provider'     => 'nullable|required_if:host_type,cloud|in:aws,gcp,azure,digitalocean,linode,other',
            'cloud_region'       => 'nullable|string|max:50',
            'cloud_instance'     => 'nullable|string|max:100',
            'ssh_user'           => 'nullable|string|max:100',
            'ssh_password'       => 'nullable|string',
            'web_root'           => 'nullable|string|max:255',
            'installed_services' => 'nullable|string',
            'is_active'          => 'boolean',
            'notes'              => 'nullable|string',
            'rdp_port'           => 'nullable|integer|min:1|max:65535',
            'ips'                => 'nullable|array',
            'ips.*.ip_address'   => 'required|string|max:45',
            'ips.*.type'         => 'required|in:private,public',
            'ips.*.interface'    => 'nullable|string|max:50',
            'ips.*.is_primary'   => 'nullable|boolean',
        ]);

        $data['slug']               = Str::slug($data['name']);
        $data['is_active']          = $request->boolean('is_active', true);
        $data['installed_services'] = $this->parseServices($request->input('installed_services'));
        $data['rdp_port']           = $data['rdp_port'] ?? 3389;

        $ips = $data['ips'] ?? [];
        unset($data['ips']);

        $server = Server::create($data);

        foreach ($ips as $i => $ip) {
            if (empty($ip['ip_address'])) continue;
            $server->ips()->create([
                'ip_address' => $ip['ip_address'],
                'type'       => $ip['type'],
                'interface'  => $ip['interface'] ?? null,
                'is_primary' => isset($ip['is_primary']) || $i === 0,
            ]);
        }

        // ── Crear conexión en Guacamole ───────────────────────────────
        $this->syncGuacamoleConnection($server, 'create');

        return redirect()->route('admin.servers.show', $server)
            ->with('success', 'Servidor registrado correctamente.');
    }

    public function show(Server $server)
    {
        $server->load([
            'ips',
            'activeContainers.system',
            'databaseServers.databases.system',
            'deployments.system',
        ]);

        return view('admin.servers.show', compact('server'));
    }

    public function edit(Server $server)
    {
        $server->load('ips');
        $functions = ServerFunction::cases();

        return view('admin.servers.form', compact('server', 'functions'));
    }

    public function update(Request $request, Server $server)
    {
        $data = $request->validate([
            'name'               => 'required|string|max:100|unique:servers,name,' . $server->id,
            'operating_system'   => 'nullable|string|max:150',
            'function'           => 'required|in:' . implode(',', array_column(ServerFunction::cases(), 'value')),
            'host_type'          => 'required|in:physical,virtual,cloud',
            'cpu_cores'          => 'nullable|integer|min:1|max:512',
            'ram_gb'             => 'nullable|integer|min:1|max:65536',
            'storage_gb'         => 'nullable|integer|min:1',
            'cloud_provider'     => 'nullable|required_if:host_type,cloud|in:aws,gcp,azure,digitalocean,linode,other',
            'cloud_region'       => 'nullable|string|max:50',
            'cloud_instance'     => 'nullable|string|max:100',
            'ssh_user'           => 'nullable|string|max:100',
            'ssh_password'       => 'nullable|string',
            'web_root'           => 'nullable|string|max:255',
            'installed_services' => 'nullable|string',
            'is_active'          => 'boolean',
            'notes'              => 'nullable|string',
            'rdp_port'           => 'nullable|integer|min:1|max:65535',
            'ips'                => 'nullable|array',
            'ips.*.ip_address'   => 'required|string|max:45',
            'ips.*.type'         => 'required|in:private,public',
            'ips.*.interface'    => 'nullable|string|max:50',
            'ips.*.is_primary'   => 'nullable|boolean',
        ]);

        $data['is_active']          = $request->boolean('is_active', true);
        $data['installed_services'] = $this->parseServices($request->input('installed_services'));

        $ips = $data['ips'] ?? [];
        unset($data['ips']);

        // No sobrescribir password si viene vacío
        if (empty($data['ssh_password'])) {
            unset($data['ssh_password']);
        }

        $server->update($data);

        // Reemplazar IPs
        $server->ips()->delete();
        foreach ($ips as $i => $ip) {
            if (empty($ip['ip_address'])) continue;
            $server->ips()->create([
                'ip_address' => $ip['ip_address'],
                'type'       => $ip['type'],
                'interface'  => $ip['interface'] ?? null,
                'is_primary' => isset($ip['is_primary']) || $i === 0,
            ]);
        }

        // ── Sincronizar conexión en Guacamole ─────────────────────────
        $server->refresh()->load('ips', 'primaryIp');
        $this->syncGuacamoleConnection($server, 'update');

        return redirect()->route('admin.servers.show', $server)
            ->with('success', 'Servidor actualizado correctamente.');
    }

    public function destroy(Server $server)
    {
        // ── Eliminar conexión en Guacamole primero ────────────────────
        if ($server->guacamole_connection_id) {
            try {
                $guac   = new GuacamoleService();
                $auth   = $guac->authenticate();
                $guac->deleteConnection(
                    $server->guacamole_connection_id,
                    $auth['authToken'],
                    $auth['dataSource']
                );
            } catch (\Throwable $e) {
                Log::warning("No se pudo eliminar la conexión Guacamole del servidor [{$server->name}]: {$e->getMessage()}");
            }
        }

        $server->delete();

        return redirect()->route('admin.servers.index')
            ->with('success', 'Servidor eliminado.');
    }

    /**
     * Genera un token de sesión fresco en Guacamole y redirige al cliente RDP.
     * Se abre en una nueva pestaña desde el frontend.
     */
    public function connect(Server $server)
    {
        if (! $server->guacamole_connection_id) {
            return back()->with('error', "El servidor [{$server->name}] no tiene conexión Guacamole configurada. Edítalo y guárdalo para generarla.");
        }

        try {
            $guac  = new GuacamoleService();
            $auth  = $guac->authenticate();
            $url   = $guac->buildClientUrl(
                $server->guacamole_connection_id,
                $auth['authToken'],
                $auth['dataSource']
            );

            // Devolvemos la URL como JSON para que el JS la abra en nueva pestaña
            if (request()->expectsJson()) {
                return response()->json(['url' => $url]);
            }

            return redirect($url);

        } catch (\Throwable $e) {
            Log::error("Error al conectar con Guacamole [{$server->name}]: {$e->getMessage()}");

            if (request()->expectsJson()) {
                return response()->json(['error' => $e->getMessage()], 502);
            }

            return back()->with('error', "No se pudo conectar: {$e->getMessage()}");
        }
    }

    // ── Helpers ──────────────────────────────────────────────────────

    private function parseServices(?string $input): array
    {
        if (empty($input)) return [];
        return array_values(array_filter(array_map('trim', explode(',', $input))));
    }

    /**
     * Crea o actualiza la conexión RDP del servidor en Guacamole.
     * Los errores se loguean pero NO interrumpen el flujo principal.
     */
    private function syncGuacamoleConnection(Server $server, string $action): void
    {
        try {
            $guac = new GuacamoleService();
            $auth = $guac->authenticate();

            if ($action === 'update' && $server->guacamole_connection_id) {
                $guac->updateConnection(
                    $server,
                    $server->guacamole_connection_id,
                    $auth['authToken'],
                    $auth['dataSource']
                );
            } else {
                $connectionId = $guac->createConnection($server, $auth['authToken'], $auth['dataSource']);
                $server->update(['guacamole_connection_id' => $connectionId]);
            }
        } catch (\Throwable $e) {
            Log::warning("No se pudo sincronizar Guacamole para [{$server->name}]: {$e->getMessage()}");
        }
    }
}
