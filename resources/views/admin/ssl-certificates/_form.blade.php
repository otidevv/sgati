@php $cert ??= null; @endphp

<div x-data="{
    fileMode: '{{ old('file_mode', $cert?->pfx_file_path ? 'pfx' : 'separate') }}',
    pfxPassword: '',
    showPassword: false,
    parsing: false,
    parsed: false,
    parseError: null,
    fields: {
        name:        @js(old('name',        $cert?->name)),
        common_name: @js(old('common_name', $cert?->common_name)),
        issuer:      @js(old('issuer',      $cert?->issuer)),
        valid_from:  @js(old('valid_from',  $cert?->valid_from?->format('Y-m-d'))),
        valid_until: @js(old('valid_until', $cert?->valid_until?->format('Y-m-d'))),
    },

    async onCertFile(event, isPfx) {
        const file = event.target.files[0];
        if (!file) return;

        this.parsing    = true;
        this.parsed     = false;
        this.parseError = null;

        const form = new FormData();
        form.append('cert_file', file);
        form.append('_token', document.querySelector('meta[name=csrf-token]').content);
        if (isPfx && this.pfxPassword) {
            form.append('pfx_password', this.pfxPassword);
        }

        try {
            const res  = await fetch('{{ route('admin.ssl-certificates.parse-cert') }}', { method: 'POST', body: form });
            const data = await res.json();

            if (!res.ok) {
                this.parseError = data.error ?? 'Error al analizar el certificado.';
                return;
            }

            if (data.common_name && !this.fields.common_name) this.fields.common_name = data.common_name;
            if (data.issuer      && !this.fields.issuer)      this.fields.issuer      = data.issuer;
            if (data.valid_from  && !this.fields.valid_from)  this.fields.valid_from  = data.valid_from;
            if (data.valid_until && !this.fields.valid_until) this.fields.valid_until = data.valid_until;
            if (data.name_suggestion && !this.fields.name)    this.fields.name        = data.name_suggestion;

            this.parsed = true;
        } catch (e) {
            this.parseError = 'No se pudo conectar con el servidor.';
        } finally {
            this.parsing = false;
        }
    }
}" class="space-y-5">

{{-- Toggle: modo de carga --}}
<div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-700/30">
        <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-200">¿Cómo tienes el certificado?</h2>
        <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Al subirlo se leerán los datos automáticamente</p>
    </div>
    <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-3">

        {{-- Opción: archivos separados --}}
        <label class="relative flex items-start gap-3 p-4 rounded-xl border-2 cursor-pointer transition-all"
               :class="fileMode === 'separate'
                   ? 'border-blue-400 dark:border-blue-600 bg-blue-50 dark:bg-blue-900/20'
                   : 'border-gray-200 dark:border-gray-600 hover:border-gray-300 dark:hover:border-gray-500 bg-white dark:bg-gray-700/30'">
            <input type="radio" name="file_mode" value="separate" x-model="fileMode" class="sr-only">
            <div class="p-2 rounded-lg flex-shrink-0 mt-0.5"
                 :class="fileMode === 'separate' ? 'bg-blue-100 dark:bg-blue-900/40' : 'bg-gray-100 dark:bg-gray-700'">
                <svg class="w-5 h-5"
                     :class="fileMode === 'separate' ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500'"
                     fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div>
                <p class="text-sm font-semibold"
                   :class="fileMode === 'separate' ? 'text-blue-700 dark:text-blue-300' : 'text-gray-700 dark:text-gray-300'">
                    Archivos separados
                </p>
                <p class="text-xs mt-0.5"
                   :class="fileMode === 'separate' ? 'text-blue-500 dark:text-blue-400' : 'text-gray-400 dark:text-gray-500'">
                    <span class="font-mono">.crt</span> + <span class="font-mono">.key</span> + <span class="font-mono">CA.crt</span>
                </p>
            </div>
        </label>

        {{-- Opción: PFX --}}
        <label class="relative flex items-start gap-3 p-4 rounded-xl border-2 cursor-pointer transition-all"
               :class="fileMode === 'pfx'
                   ? 'border-violet-400 dark:border-violet-600 bg-violet-50 dark:bg-violet-900/20'
                   : 'border-gray-200 dark:border-gray-600 hover:border-gray-300 dark:hover:border-gray-500 bg-white dark:bg-gray-700/30'">
            <input type="radio" name="file_mode" value="pfx" x-model="fileMode" class="sr-only">
            <div class="p-2 rounded-lg flex-shrink-0 mt-0.5"
                 :class="fileMode === 'pfx' ? 'bg-violet-100 dark:bg-violet-900/40' : 'bg-gray-100 dark:bg-gray-700'">
                <svg class="w-5 h-5"
                     :class="fileMode === 'pfx' ? 'text-violet-600 dark:text-violet-400' : 'text-gray-400 dark:text-gray-500'"
                     fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
            <div>
                <p class="text-sm font-semibold"
                   :class="fileMode === 'pfx' ? 'text-violet-700 dark:text-violet-300' : 'text-gray-700 dark:text-gray-300'">
                    Archivo PFX / P12
                </p>
                <p class="text-xs mt-0.5"
                   :class="fileMode === 'pfx' ? 'text-violet-500 dark:text-violet-400' : 'text-gray-400 dark:text-gray-500'">
                    Todo en un solo archivo <span class="font-mono">.pfx</span>
                </p>
            </div>
        </label>

    </div>
</div>

{{-- Archivos del certificado --}}
<div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-700/30 flex items-center justify-between">
        <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-200">Archivos</h2>
        <div class="flex items-center gap-3">
            {{-- Spinner --}}
            <div x-show="parsing" class="flex items-center gap-1.5 text-xs text-blue-600 dark:text-blue-400">
                <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
                Analizando…
            </div>
            {{-- OK --}}
            <div x-show="parsed && !parsing" x-transition
                 class="flex items-center gap-1.5 text-xs text-emerald-600 dark:text-emerald-400 font-medium">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
                Datos completados
            </div>
        </div>
    </div>

    {{-- Error parse --}}
    <div x-show="parseError" x-transition
         class="mx-6 mt-4 flex items-start gap-2 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
        <svg class="w-4 h-4 text-red-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        <p class="text-xs text-red-700 dark:text-red-300" x-text="parseError"></p>
    </div>

    {{-- ── Modo PFX ── --}}
    <div x-show="fileMode === 'pfx'" x-transition class="p-6 space-y-4">

        {{-- Contraseña (antes del upload para que esté disponible) --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Contraseña del PFX
                <span class="ml-1 text-xs font-normal text-gray-400 dark:text-gray-500">(dejar vacío si no tiene)</span>
            </label>
            <div class="relative w-full sm:w-72">
                <input :type="showPassword ? 'text' : 'password'"
                       x-model="pfxPassword"
                       autocomplete="off"
                       class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700
                              dark:text-gray-100 shadow-sm focus:ring-violet-500 focus:border-violet-500 sm:text-sm pr-10"
                       placeholder="Contraseña opcional">
                <button type="button" @click="showPassword = !showPassword"
                        class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg x-show="!showPassword" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    <svg x-show="showPassword" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Upload PFX --}}
        <div>
            <label for="pfx_file"
                   class="flex items-center gap-4 w-full px-4 py-4 rounded-xl border-2 border-dashed cursor-pointer transition-colors
                          border-violet-200 dark:border-violet-800 bg-violet-50/50 dark:bg-violet-900/10
                          hover:bg-violet-50 dark:hover:bg-violet-900/20 hover:border-violet-300 dark:hover:border-violet-700">
                <div class="p-2.5 bg-violet-100 dark:bg-violet-900/40 rounded-xl flex-shrink-0">
                    <svg class="w-6 h-6 text-violet-600 dark:text-violet-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-violet-700 dark:text-violet-300">Subir archivo PFX</p>
                    <p class="text-xs text-violet-500 dark:text-violet-400 mt-0.5">
                        Ej: <span class="font-mono">mi_certificado.pfx</span> — se leerán los datos automáticamente
                    </p>
                </div>
                <svg class="w-4 h-4 text-violet-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                </svg>
            </label>
            <input type="file" id="pfx_file" name="pfx_file"
                   accept=".pfx,.p12"
                   class="sr-only"
                   @change="onCertFile($event, true)">
            @error('pfx_file')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
        </div>

    </div>

    {{-- ── Modo archivos separados ── --}}
    <div x-show="fileMode === 'separate'" x-transition class="p-6 space-y-4">

        {{-- Certificado principal --}}
        <div>
            <label for="cert_file" class="flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Certificado principal
                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium
                             bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300">.crt / .pem</span>
            </label>
            <label for="cert_file"
                   class="flex items-center gap-4 w-full px-4 py-3 rounded-xl border-2 border-dashed cursor-pointer transition-colors
                          border-blue-200 dark:border-blue-800 bg-blue-50/50 dark:bg-blue-900/10
                          hover:bg-blue-50 dark:hover:bg-blue-900/20 hover:border-blue-300 dark:hover:border-blue-700">
                <div class="p-2 bg-blue-100 dark:bg-blue-900/40 rounded-lg flex-shrink-0">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-blue-700 dark:text-blue-300">Subir certificado</p>
                    <p class="text-xs text-blue-500 dark:text-blue-400 mt-0.5">
                        Ej: <span class="font-mono">wildcard_unamad_edu_pe.crt</span>
                    </p>
                </div>
                <svg class="w-4 h-4 text-blue-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                </svg>
            </label>
            <input type="file" id="cert_file" name="cert_file"
                   accept=".pem,.crt,.cer"
                   class="sr-only"
                   @change="onCertFile($event, false)">
            @error('cert_file')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            {{-- Llave privada --}}
            <div>
                <label for="key_file" class="flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Llave privada
                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium
                                 bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300">.key</span>
                </label>
                <label for="key_file"
                       class="flex items-center gap-3 w-full px-3 py-2.5 rounded-xl border-2 border-dashed cursor-pointer transition-colors
                              border-amber-200 dark:border-amber-800 bg-amber-50/50 dark:bg-amber-900/10
                              hover:bg-amber-50 dark:hover:bg-amber-900/20">
                    <svg class="w-4 h-4 text-amber-500 dark:text-amber-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                    </svg>
                    <span class="text-xs text-amber-600 dark:text-amber-400">
                        Ej: <span class="font-mono">wildcard_unamad_edu_pe.key</span>
                    </span>
                </label>
                <input type="file" id="key_file" name="key_file" accept=".key,.pem" class="sr-only">
                @error('key_file')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
            </div>

            {{-- Cadena intermedia --}}
            <div>
                <label for="chain_file" class="flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Cadena intermedia (CA)
                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium
                                 bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300">.crt</span>
                </label>
                <label for="chain_file"
                       class="flex items-center gap-3 w-full px-3 py-2.5 rounded-xl border-2 border-dashed cursor-pointer transition-colors
                              border-purple-200 dark:border-purple-800 bg-purple-50/50 dark:bg-purple-900/10
                              hover:bg-purple-50 dark:hover:bg-purple-900/20">
                    <svg class="w-4 h-4 text-purple-500 dark:text-purple-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                    </svg>
                    <span class="text-xs text-purple-600 dark:text-purple-400">
                        Ej: <span class="font-mono">DigiCertCA.crt</span>
                    </span>
                </label>
                <input type="file" id="chain_file" name="chain_file" accept=".crt,.pem,.cer" class="sr-only">
                @error('chain_file')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
            </div>
        </div>

    </div>
</div>

{{-- Información del certificado (auto-completada) --}}
<div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden transition-all"
     :class="parsed ? 'ring-2 ring-emerald-300 dark:ring-emerald-700' : ''">
    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-700/30 flex items-center justify-between">
        <div>
            <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-200">Información del Certificado</h2>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Se completa al subir el archivo — puedes editar los valores</p>
        </div>
        <div x-show="parsed" x-transition
             class="text-xs text-emerald-600 dark:text-emerald-400 font-medium flex items-center gap-1">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
            </svg>
            Auto-completado
        </div>
    </div>
    <div class="p-6 space-y-4">

        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Nombre descriptivo <span class="text-red-500">*</span>
            </label>
            <input type="text" id="name" name="name"
                   x-model="fields.name"
                   required maxlength="150"
                   class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700
                          dark:text-gray-100 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                   placeholder="Certificado Wildcard UNAMAD 2025">
            @error('name')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="common_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Common Name (CN)</label>
                <input type="text" id="common_name" name="common_name"
                       x-model="fields.common_name"
                       maxlength="255"
                       class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700
                              dark:text-gray-100 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm font-mono"
                       placeholder="*.unamad.edu.pe">
                @error('common_name')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="issuer" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Emisor (CA)</label>
                <input type="text" id="issuer" name="issuer"
                       x-model="fields.issuer"
                       maxlength="255"
                       class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700
                              dark:text-gray-100 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                       placeholder="DigiCert / Let's Encrypt / RENIEC">
                @error('issuer')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="valid_from" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Válido desde</label>
                <input type="date" id="valid_from" name="valid_from"
                       x-model="fields.valid_from"
                       class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700
                              dark:text-gray-100 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                @error('valid_from')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="valid_until" class="flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Vence el
                    <template x-if="fields.valid_until">
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium"
                              :class="new Date(fields.valid_until) < new Date()
                                  ? 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300'
                                  : (new Date(fields.valid_until) < new Date(Date.now() + 30*86400000)
                                      ? 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300'
                                      : 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300')"
                              x-text="new Date(fields.valid_until) < new Date() ? 'Vencido'
                                  : (new Date(fields.valid_until) < new Date(Date.now() + 30*86400000) ? 'Por vencer' : 'Vigente')">
                        </span>
                    </template>
                </label>
                <input type="date" id="valid_until" name="valid_until"
                       x-model="fields.valid_until"
                       class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700
                              dark:text-gray-100 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                @error('valid_until')<p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
            </div>
        </div>

        <div>
            <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Notas</label>
            <textarea id="notes" name="notes" rows="2"
                      class="block w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700
                             dark:text-gray-100 shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                      placeholder="Observaciones adicionales…">{{ old('notes', $cert?->notes) }}</textarea>
        </div>
    </div>
</div>

</div>{{-- end x-data --}}
