@php $editing = !is_null($repository); @endphp

{{-- Identificación --}}
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div class="sm:col-span-2">
        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
            Nombre / Alias <span class="text-red-500">*</span>
        </label>
        <input type="text" id="name" name="name" required
               value="{{ old('name', $repository?->name) }}"
               class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
               placeholder="Ej: SGATI Backend">
        @error('name')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="provider" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
            Proveedor <span class="text-red-500">*</span>
        </label>
        <select id="provider" name="provider" required
                class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            @foreach($providers as $p)
            <option value="{{ $p->value }}" {{ old('provider', $repository?->provider?->value) === $p->value ? 'selected' : '' }}>
                {{ $p->label() }}
            </option>
            @endforeach
        </select>
        @error('provider')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="repo_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tipo</label>
        <select id="repo_type" name="repo_type"
                class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            <option value="organization" {{ old('repo_type', $repository?->repo_type ?? 'organization') === 'organization' ? 'selected' : '' }}>Organización</option>
            <option value="personal"     {{ old('repo_type', $repository?->repo_type) === 'personal' ? 'selected' : '' }}>Personal</option>
        </select>
    </div>

    <div class="sm:col-span-2">
        <label for="repo_url" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">URL del Repositorio</label>
        <input type="url" id="repo_url" name="repo_url"
               value="{{ old('repo_url', $repository?->clean_url) }}"
               class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm font-mono"
               placeholder="https://github.com/org/repo">
        @error('repo_url')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="default_branch" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Rama principal</label>
        <input type="text" id="default_branch" name="default_branch"
               value="{{ old('default_branch', $repository?->default_branch ?? 'main') }}"
               class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm font-mono"
               placeholder="main">
    </div>

    {{-- Toggles --}}
    <div class="flex flex-col gap-3 justify-center">
        <label class="flex items-center gap-3 cursor-pointer">
            <input type="hidden" name="is_private" value="0">
            <input type="checkbox" name="is_private" value="1"
                   {{ old('is_private', $repository?->is_private ?? true) ? 'checked' : '' }}
                   class="w-4 h-4 rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500">
            <span class="text-sm text-gray-700 dark:text-gray-300">Repositorio privado</span>
        </label>
        <label class="flex items-center gap-3 cursor-pointer">
            <input type="hidden" name="is_active" value="0">
            <input type="checkbox" name="is_active" value="1"
                   {{ old('is_active', $repository?->is_active ?? true) ? 'checked' : '' }}
                   class="w-4 h-4 rounded border-gray-300 dark:border-gray-600 text-blue-600 focus:ring-blue-500">
            <span class="text-sm text-gray-700 dark:text-gray-300">Activo</span>
        </label>
    </div>
</div>

{{-- Credenciales --}}
<div class="bg-gray-50 dark:bg-gray-700/40 rounded-lg border border-gray-200 dark:border-gray-600 p-4 space-y-4">
    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Credenciales de acceso</p>
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label for="credential_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tipo de credencial</label>
            <select id="credential_type" name="credential_type"
                    class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                <option value="token"      {{ old('credential_type', $repository?->credential_type ?? 'token') === 'token'      ? 'selected' : '' }}>Token (PAT)</option>
                <option value="password"   {{ old('credential_type', $repository?->credential_type) === 'password'   ? 'selected' : '' }}>Usuario + Contraseña</option>
                <option value="deploy_key" {{ old('credential_type', $repository?->credential_type) === 'deploy_key' ? 'selected' : '' }}>Deploy Key (SSH)</option>
                <option value="oauth"      {{ old('credential_type', $repository?->credential_type) === 'oauth'      ? 'selected' : '' }}>OAuth App</option>
            </select>
        </div>

        <div>
            <label for="username" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Usuario / Email</label>
            <input type="text" id="username" name="username"
                   value="{{ old('username', $repository?->username) }}"
                   class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                   placeholder="usuario@unamad.edu.pe">
        </div>

        <div class="sm:col-span-2">
            <label for="token" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Token / Contraseña / Clave
                @if($editing)
                <span class="ml-1 text-xs text-gray-400 font-normal">(dejar vacío para no cambiar)</span>
                @endif
            </label>
            <input type="password" id="token" name="token" autocomplete="new-password"
                   class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm font-mono"
                   placeholder="{{ $editing ? '••••••••••••' : 'ghp_xxxxxxxxxxxxx' }}">
            @error('token')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
            <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">
                El token se almacena encriptado en la base de datos.
            </p>
        </div>
    </div>
</div>

{{-- Notas --}}
<div>
    <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Notas</label>
    <textarea id="notes" name="notes" rows="2"
              class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
              placeholder="Observaciones adicionales…">{{ old('notes', $repository?->notes) }}</textarea>
</div>
