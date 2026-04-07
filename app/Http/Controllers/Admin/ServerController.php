<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Server;
use App\Models\ServerIp;
use App\Models\ServerContainer;
use App\Enums\ServerFunction;
use Illuminate\Http\Request;
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
            'ips'                => 'nullable|array',
            'ips.*.ip_address'   => 'required|string|max:45',
            'ips.*.type'         => 'required|in:private,public',
            'ips.*.interface'    => 'nullable|string|max:50',
            'ips.*.is_primary'   => 'nullable|boolean',
        ]);

        $data['slug']               = Str::slug($data['name']);
        $data['is_active']          = $request->boolean('is_active', true);
        $data['installed_services'] = $this->parseServices($request->input('installed_services'));

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

        return redirect()->route('admin.servers.show', $server)
            ->with('success', 'Servidor actualizado correctamente.');
    }

    public function destroy(Server $server)
    {
        $server->delete();

        return redirect()->route('admin.servers.index')
            ->with('success', 'Servidor eliminado.');
    }

    private function parseServices(?string $input): array
    {
        if (empty($input)) return [];
        return array_values(array_filter(array_map('trim', explode(',', $input))));
    }
}
