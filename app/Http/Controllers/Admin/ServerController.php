<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Persona;
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
        return view('admin.servers.form', [
            'server'    => new Server,
            'functions' => ServerFunction::cases(),
            'personas'  => Persona::orderBy('apellido_paterno')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->serverRules());

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

        return redirect()->route('admin.servers.show', $server)
            ->with('success', 'Servidor registrado. Puedes habilitar el acceso remoto desde esta vista.');
    }

    public function show(Server $server)
    {
        $server->load([
            'ips',
            'activeContainers.system',
            'databaseServers.databases.system',
            'deployments.system',
            'responsibles.persona',
            'responsibles.documents',
        ]);

        return view('admin.servers.show', compact('server'));
    }

    public function edit(Server $server)
    {
        $server->load(['ips', 'responsibles.persona']);

        return view('admin.servers.form', [
            'server'    => $server,
            'functions' => ServerFunction::cases(),
            'personas'  => Persona::orderBy('apellido_paterno')->get(),
        ]);
    }

    public function update(Request $request, Server $server)
    {
        $rules = $this->serverRules('update', $server->id);
        $data  = $request->validate($rules);

        $data['is_active']          = $request->boolean('is_active', true);
        $data['installed_services'] = $this->parseServices($request->input('installed_services'));

        $ips = $data['ips'] ?? [];
        unset($data['ips']);

        if (empty($data['ssh_password'])) {
            unset($data['ssh_password']);
        }

        $server->update($data);

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

        $server->refresh()->load('ips', 'primaryIp');

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

    /**
     * Elimina la conexión Guacamole existente y crea una nueva.
     * Útil cuando la conexión aparece como "sin conexión" en Guacamole.
     */
    public function reconnect(Server $server)
    {
        try {
            $guac = new GuacamoleService();
            $auth = $guac->authenticate();

            // Eliminar conexión anterior si existe
            if ($server->guacamole_connection_id) {
                try {
                    $guac->deleteConnection($server->guacamole_connection_id, $auth['authToken'], $auth['dataSource']);
                } catch (\Throwable $e) {
                    Log::warning("No se pudo eliminar la conexión Guacamole antigua [{$server->name}]: {$e->getMessage()}");
                }
            }

            // Crear nueva conexión
            $connectionId = $guac->createConnection($server, $auth['authToken'], $auth['dataSource']);
            $server->update(['guacamole_connection_id' => $connectionId]);

            if (request()->expectsJson()) {
                return response()->json(['success' => true, 'connection_id' => $connectionId]);
            }

            return back()->with('success', 'Conexión Guacamole restablecida correctamente.');

        } catch (\Throwable $e) {
            Log::error("Error al restablecer conexión Guacamole [{$server->name}]: {$e->getMessage()}");

            if (request()->expectsJson()) {
                return response()->json(['error' => $e->getMessage()], 502);
            }

            return back()->with('error', "No se pudo restablecer la conexión: {$e->getMessage()}");
        }
    }

    // ── Helpers ──────────────────────────────────────────────────────

    private function serverRules(string $mode = 'create', ?int $id = null): array
    {
        $uniqueName = $mode === 'update' ? "unique:servers,name,{$id}" : 'unique:servers,name';

        return [
            'name'               => "required|string|max:100|{$uniqueName}",
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
        ];
    }

    private function parseServices(?string $input): array
    {
        if (empty($input)) return [];
        return array_values(array_filter(array_map('trim', explode(',', $input))));
    }

}
