<?php

namespace App\Http\Controllers;

use App\Models\Repository;
use App\Models\System;
use App\Enums\RepoProvider;
use Illuminate\Http\Request;

class SystemRepositoryController extends Controller
{
    public function index()
    {
        $repositories = Repository::with('system')
            ->orderBy('provider')
            ->orderBy('name')
            ->get();

        return view('systems.repositories.index', compact('repositories'));
    }

    public function create(System $system)
    {
        $providers = RepoProvider::cases();
        return view('systems.repositories.create', compact('system', 'providers'));
    }

    public function store(Request $request, System $system)
    {
        $data = $request->validate([
            'name'            => 'required|string|max:150',
            'provider'        => 'required|in:github,gitlab,bitbucket,gitea,other',
            'repo_url'        => 'nullable|url|max:255',
            'username'        => 'nullable|string|max:150',
            'token'           => 'nullable|string',
            'credential_type' => 'required|in:token,password,deploy_key,oauth',
            'default_branch'  => 'nullable|string|max:100',
            'repo_type'       => 'required|in:personal,organization',
            'is_private'      => 'boolean',
            'is_active'       => 'boolean',
            'notes'           => 'nullable|string',
        ]);

        $data['is_private'] = $request->boolean('is_private', true);
        $data['is_active']  = $request->boolean('is_active', true);
        $data['system_id']  = $system->id;

        Repository::create($data);

        return redirect(route('systems.show', $system) . '#repositories')
            ->with('success', 'Repositorio registrado correctamente.');
    }

    public function edit(System $system, Repository $repository)
    {
        $providers = RepoProvider::cases();
        return view('systems.repositories.edit', compact('system', 'repository', 'providers'));
    }

    public function update(Request $request, System $system, Repository $repository)
    {
        $data = $request->validate([
            'name'            => 'required|string|max:150',
            'provider'        => 'required|in:github,gitlab,bitbucket,gitea,other',
            'repo_url'        => 'nullable|url|max:255',
            'username'        => 'nullable|string|max:150',
            'token'           => 'nullable|string',
            'credential_type' => 'required|in:token,password,deploy_key,oauth',
            'default_branch'  => 'nullable|string|max:100',
            'repo_type'       => 'required|in:personal,organization',
            'is_private'      => 'boolean',
            'is_active'       => 'boolean',
            'notes'           => 'nullable|string',
        ]);

        $data['is_private'] = $request->boolean('is_private');
        $data['is_active']  = $request->boolean('is_active');

        if (empty($data['token'])) {
            unset($data['token']);
        }

        $repository->update($data);

        return redirect(route('systems.show', $system) . '#repositories')
            ->with('success', 'Repositorio actualizado correctamente.');
    }

    public function destroy(System $system, Repository $repository)
    {
        $repository->delete();

        return redirect(route('systems.show', $system) . '#repositories')
            ->with('success', 'Repositorio eliminado.');
    }
}
