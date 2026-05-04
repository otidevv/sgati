<div class="space-y-6">
    {{-- Descripción --}}
    @if($system->description)
    <div>
        <h3 class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-2">Descripción</h3>
        <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">{{ $system->description }}</p>
    </div>
    @endif

    {{-- Ficha técnica --}}
    <div>
        <h3 class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-3">Ficha Técnica</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="space-y-3">
                <div class="flex gap-3">
                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400 w-28 flex-shrink-0 pt-0.5">Nombre</span>
                    <span class="text-sm text-gray-800 dark:text-gray-200">{{ $system->name }}</span>
                </div>
                @if($system->acronym)
                <div class="flex gap-3">
                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400 w-28 flex-shrink-0 pt-0.5">Siglas</span>
                    <span class="text-sm font-mono text-gray-800 dark:text-gray-200">{{ $system->acronym }}</span>
                </div>
                @endif
                <div class="flex gap-3">
                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400 w-28 flex-shrink-0 pt-0.5">Estado</span>
                    <x-status-badge :status="$system->status" />
                </div>
                <div class="flex gap-3">
                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400 w-28 flex-shrink-0 pt-0.5">Área</span>
                    <span class="text-sm text-gray-800 dark:text-gray-200">{{ $system->area->name ?? '—' }}</span>
                </div>
                <div class="flex gap-3">
                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400 w-28 flex-shrink-0 pt-0.5">Responsable</span>
                    <span class="text-sm text-gray-800 dark:text-gray-200">{{ $system->responsible->name ?? '—' }}</span>
                </div>
            </div>
            <div class="space-y-3">
                @if(!empty($system->tech_stack))
                <div class="flex gap-3">
                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400 w-28 flex-shrink-0 pt-1">Stack</span>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach($system->tech_stack as $tag)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                     bg-blue-50 text-blue-700 border border-blue-200
                                     dark:bg-blue-900/30 dark:text-blue-300 dark:border-blue-700">
                            {{ $tag }}
                        </span>
                        @endforeach
                    </div>
                </div>
                @endif
                @if($system->repo_url)
                <div class="flex gap-3">
                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400 w-28 flex-shrink-0 pt-0.5">Repositorio</span>
                    <a href="{{ $system->repo_url }}" target="_blank"
                       class="text-sm text-blue-600 dark:text-blue-400 hover:underline truncate">{{ $system->repo_url }}</a>
                </div>
                @endif
                <div class="flex gap-3">
                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400 w-28 flex-shrink-0 pt-0.5">Registrado</span>
                    <span class="text-sm text-gray-800 dark:text-gray-200">{{ $system->created_at->format('d/m/Y') }}</span>
                </div>
                <div class="flex gap-3">
                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400 w-28 flex-shrink-0 pt-0.5">Actualizado</span>
                    <span class="text-sm text-gray-800 dark:text-gray-200">{{ $system->updated_at->diffForHumans() }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Procedencia --}}
    @if($system->origin)
    @php $origin = $system->origin; @endphp
    @php
    $originColors = [
        'donated'     => ['bg'=>'bg-purple-50 dark:bg-purple-900/20','border'=>'border-purple-200 dark:border-purple-800','title'=>'text-purple-700 dark:text-purple-300','badge'=>'bg-purple-100 text-purple-700 dark:bg-purple-900/40 dark:text-purple-300'],
        'third_party' => ['bg'=>'bg-orange-50 dark:bg-orange-900/20','border'=>'border-orange-200 dark:border-orange-800','title'=>'text-orange-700 dark:text-orange-300','badge'=>'bg-orange-100 text-orange-700 dark:bg-orange-900/40 dark:text-orange-300'],
        'internal'    => ['bg'=>'bg-teal-50 dark:bg-teal-900/20',   'border'=>'border-teal-200 dark:border-teal-800',   'title'=>'text-teal-700 dark:text-teal-300',   'badge'=>'bg-teal-100 text-teal-700 dark:bg-teal-900/40 dark:text-teal-300'],
        'state'       => ['bg'=>'bg-red-50 dark:bg-red-900/20',     'border'=>'border-red-200 dark:border-red-800',     'title'=>'text-red-700 dark:text-red-300',     'badge'=>'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300'],
    ];
    $oc = $originColors[$origin->origin_type->value] ?? $originColors['internal'];
    @endphp
    <div>
        <h3 class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-3">Procedencia</h3>
        <div class="rounded-lg border {{ $oc['border'] }} {{ $oc['bg'] }} p-4 space-y-3">
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $oc['badge'] }}">
                    {{ $origin->origin_type->label() }}
                </span>
            </div>

            {{-- Donado --}}
            @if($origin->origin_type->value === 'donated')
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-2 text-sm">
                @if($origin->donation_type)
                <div class="flex gap-2"><span class="text-xs text-gray-500 dark:text-gray-400 w-32 flex-shrink-0">Tipo</span><span class="text-gray-800 dark:text-gray-200">{{ $origin->donationTypeLabel() }}</span></div>
                @endif
                @if($origin->donor_name)
                <div class="flex gap-2"><span class="text-xs text-gray-500 dark:text-gray-400 w-32 flex-shrink-0">Donante</span><span class="text-gray-800 dark:text-gray-200">{{ $origin->donor_name }}</span></div>
                @endif
                @if($origin->donor_institution)
                <div class="flex gap-2"><span class="text-xs text-gray-500 dark:text-gray-400 w-32 flex-shrink-0">Institución</span><span class="text-gray-800 dark:text-gray-200">{{ $origin->donor_institution }}</span></div>
                @endif
                @if($origin->thesis_title)
                <div class="flex gap-2 sm:col-span-2"><span class="text-xs text-gray-500 dark:text-gray-400 w-32 flex-shrink-0">Título</span><span class="text-gray-800 dark:text-gray-200">{{ $origin->thesis_title }}</span></div>
                @endif
                @if($origin->thesis_author)
                <div class="flex gap-2 sm:col-span-2"><span class="text-xs text-gray-500 dark:text-gray-400 w-32 flex-shrink-0">Autor(es)</span><span class="text-gray-800 dark:text-gray-200">{{ $origin->thesis_author }}</span></div>
                @endif
                @if($origin->thesis_university)
                <div class="flex gap-2"><span class="text-xs text-gray-500 dark:text-gray-400 w-32 flex-shrink-0">Universidad</span><span class="text-gray-800 dark:text-gray-200">{{ $origin->thesis_university }}</span></div>
                @endif
                @if($origin->donation_date)
                <div class="flex gap-2"><span class="text-xs text-gray-500 dark:text-gray-400 w-32 flex-shrink-0">Fecha donación</span><span class="text-gray-800 dark:text-gray-200">{{ $origin->donation_date->format('d/m/Y') }}</span></div>
                @endif
                @if($origin->donation_document)
                <div class="flex gap-2"><span class="text-xs text-gray-500 dark:text-gray-400 w-32 flex-shrink-0">Resolución/Acta</span><span class="text-gray-800 dark:text-gray-200">{{ $origin->donation_document }}</span></div>
                @endif
            </div>
            @endif

            {{-- Terceros --}}
            @if($origin->origin_type->value === 'third_party')
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-2 text-sm">
                @if($origin->company_name)
                <div class="flex gap-2 sm:col-span-2"><span class="text-xs text-gray-500 dark:text-gray-400 w-32 flex-shrink-0">Empresa</span><span class="text-gray-800 dark:text-gray-200 font-medium">{{ $origin->company_name }}</span></div>
                @endif
                @if($origin->contact_name)
                <div class="flex gap-2"><span class="text-xs text-gray-500 dark:text-gray-400 w-32 flex-shrink-0">Contacto</span><span class="text-gray-800 dark:text-gray-200">{{ $origin->contact_name }}</span></div>
                @endif
                @if($origin->contact_email)
                <div class="flex gap-2"><span class="text-xs text-gray-500 dark:text-gray-400 w-32 flex-shrink-0">Email</span><a href="mailto:{{ $origin->contact_email }}" class="text-blue-600 dark:text-blue-400 hover:underline">{{ $origin->contact_email }}</a></div>
                @endif
                @if($origin->contact_phone)
                <div class="flex gap-2"><span class="text-xs text-gray-500 dark:text-gray-400 w-32 flex-shrink-0">Teléfono</span><span class="text-gray-800 dark:text-gray-200">{{ $origin->contact_phone }}</span></div>
                @endif
                @if($origin->contract_number)
                <div class="flex gap-2"><span class="text-xs text-gray-500 dark:text-gray-400 w-32 flex-shrink-0">N° Contrato</span><span class="text-gray-800 dark:text-gray-200 font-mono">{{ $origin->contract_number }}</span></div>
                @endif
                @if($origin->contract_date)
                <div class="flex gap-2"><span class="text-xs text-gray-500 dark:text-gray-400 w-32 flex-shrink-0">Fecha contrato</span><span class="text-gray-800 dark:text-gray-200">{{ $origin->contract_date->format('d/m/Y') }}</span></div>
                @endif
                @if($origin->contract_value)
                <div class="flex gap-2"><span class="text-xs text-gray-500 dark:text-gray-400 w-32 flex-shrink-0">Monto</span><span class="text-gray-800 dark:text-gray-200">S/. {{ number_format($origin->contract_value, 2) }}</span></div>
                @endif
                @if($origin->warranty_expiry)
                <div class="flex gap-2"><span class="text-xs text-gray-500 dark:text-gray-400 w-32 flex-shrink-0">Fin garantía</span>
                    <span class="{{ $origin->warranty_expiry->isPast() ? 'text-red-600 dark:text-red-400 font-medium' : 'text-gray-800 dark:text-gray-200' }}">
                        {{ $origin->warranty_expiry->format('d/m/Y') }}
                        @if($origin->warranty_expiry->isPast()) <span class="text-xs">(vencida)</span> @endif
                    </span>
                </div>
                @endif
            </div>
            @endif

            {{-- Interno --}}
            @if($origin->origin_type->value === 'internal')
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-2 text-sm">
                @if($origin->team_name)
                <div class="flex gap-2"><span class="text-xs text-gray-500 dark:text-gray-400 w-32 flex-shrink-0">Equipo</span><span class="text-gray-800 dark:text-gray-200">{{ $origin->team_name }}</span></div>
                @endif
                @if($origin->project_code)
                <div class="flex gap-2"><span class="text-xs text-gray-500 dark:text-gray-400 w-32 flex-shrink-0">Código proyecto</span><span class="text-gray-800 dark:text-gray-200 font-mono">{{ $origin->project_code }}</span></div>
                @endif
                @if($origin->dev_start_date)
                <div class="flex gap-2"><span class="text-xs text-gray-500 dark:text-gray-400 w-32 flex-shrink-0">Inicio desarrollo</span><span class="text-gray-800 dark:text-gray-200">{{ $origin->dev_start_date->format('d/m/Y') }}</span></div>
                @endif
                @if($origin->dev_end_date)
                <div class="flex gap-2"><span class="text-xs text-gray-500 dark:text-gray-400 w-32 flex-shrink-0">Fin desarrollo</span><span class="text-gray-800 dark:text-gray-200">{{ $origin->dev_end_date->format('d/m/Y') }}</span></div>
                @endif
                @if($origin->methodology)
                <div class="flex gap-2"><span class="text-xs text-gray-500 dark:text-gray-400 w-32 flex-shrink-0">Metodología</span><span class="text-gray-800 dark:text-gray-200">{{ $origin->methodologyLabel() }}</span></div>
                @endif
            </div>
            @endif

            {{-- Estado --}}
            @if($origin->origin_type->value === 'state')
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-2 text-sm">
                @if($origin->state_entity)
                <div class="flex gap-2"><span class="text-xs text-gray-500 dark:text-gray-400 w-32 flex-shrink-0">Entidad</span><span class="text-gray-800 dark:text-gray-200 font-medium">{{ $origin->state_entity }}</span></div>
                @endif
                @if($origin->state_entity_code)
                <div class="flex gap-2"><span class="text-xs text-gray-500 dark:text-gray-400 w-32 flex-shrink-0">Cód. entidad</span><span class="text-gray-800 dark:text-gray-200 font-mono">{{ $origin->state_entity_code }}</span></div>
                @endif
                @if($origin->state_system_code)
                <div class="flex gap-2"><span class="text-xs text-gray-500 dark:text-gray-400 w-32 flex-shrink-0">Cód. sistema</span><span class="text-gray-800 dark:text-gray-200 font-mono">{{ $origin->state_system_code }}</span></div>
                @endif
                @if($origin->state_implementation_date)
                <div class="flex gap-2"><span class="text-xs text-gray-500 dark:text-gray-400 w-32 flex-shrink-0">Implementado</span><span class="text-gray-800 dark:text-gray-200">{{ $origin->state_implementation_date->format('d/m/Y') }}</span></div>
                @endif
                @if($origin->state_official_url)
                <div class="flex gap-2 sm:col-span-2"><span class="text-xs text-gray-500 dark:text-gray-400 w-32 flex-shrink-0">URL oficial</span><a href="{{ $origin->state_official_url }}" target="_blank" class="text-blue-600 dark:text-blue-400 hover:underline truncate">{{ $origin->state_official_url }}</a></div>
                @endif
                @if($origin->legal_basis)
                <div class="flex gap-2 sm:col-span-2"><span class="text-xs text-gray-500 dark:text-gray-400 w-32 flex-shrink-0">Base legal</span><span class="text-gray-800 dark:text-gray-200">{{ $origin->legal_basis }}</span></div>
                @endif
            </div>
            @endif

            @if($origin->origin_notes)
            <div class="pt-2 border-t border-gray-200 dark:border-gray-700">
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Notas</p>
                <p class="text-sm text-gray-700 dark:text-gray-300">{{ $origin->origin_notes }}</p>
            </div>
            @endif
        </div>
    </div>
    @endif

    {{-- Observaciones --}}
    @if($system->observations)
    <div>
        <h3 class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-2">Observaciones</h3>
        <div class="p-4 bg-amber-50 dark:bg-amber-900/30 border border-amber-200 dark:border-amber-800 rounded-lg">
            <p class="text-sm text-amber-800 dark:text-amber-200 leading-relaxed">{{ $system->observations }}</p>
        </div>
    </div>
    @endif

    @if(!$system->description && !$system->observations && !$system->tech_stack && !$system->origin)
    <div class="text-center py-10 text-gray-400 dark:text-gray-500">
        <svg class="mx-auto w-10 h-10 text-gray-300 dark:text-gray-600 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        <p class="text-sm">Sin información adicional registrada.</p>
        @can('systems.edit')
        <a href="{{ route('systems.edit', $system) }}" class="mt-2 inline-block text-sm text-blue-600 dark:text-blue-400 hover:underline">Completar información →</a>
        @endcan
    </div>
    @endif
</div>
