<div class="space-y-6">

    {{-- Ficha de Baja --}}
    @if($system->status->value === 'decommissioned' && $system->decommission)
    @php $d = $system->decommission; @endphp
    <div class="rounded-xl border-2 border-red-300 dark:border-red-700 bg-red-50 dark:bg-red-900/20 overflow-hidden">
        {{-- Banner --}}
        <div class="bg-red-600 dark:bg-red-800 px-5 py-3 flex items-center justify-between gap-3">
            <div class="flex items-center gap-2.5">
                <svg class="w-5 h-5 text-white flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/>
                </svg>
                <span class="text-sm font-bold text-white uppercase tracking-wide">Sistema dado de baja</span>
            </div>
            <span class="text-xs font-semibold text-red-100 bg-red-700 dark:bg-red-900 px-3 py-1 rounded-full">
                Efectivo: {{ $d->effective_date->format('d/m/Y') }}
            </span>
        </div>

        <div class="p-5 space-y-5">

            {{-- Motivo --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3">
                <div class="flex gap-3 sm:col-span-2">
                    <span class="text-xs font-medium text-red-500 dark:text-red-400 w-36 flex-shrink-0 pt-0.5">Tipo de motivo</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-300">
                        {{ $d->reason_type->label() }}
                    </span>
                </div>
                <div class="flex gap-3 sm:col-span-2">
                    <span class="text-xs font-medium text-red-500 dark:text-red-400 w-36 flex-shrink-0 pt-0.5">Motivo detallado</span>
                    <span class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">{{ $d->reason }}</span>
                </div>
                @if($d->shutdown_date)
                <div class="flex gap-3">
                    <span class="text-xs font-medium text-red-500 dark:text-red-400 w-36 flex-shrink-0 pt-0.5">Apagado el</span>
                    <span class="text-sm text-gray-700 dark:text-gray-300">{{ $d->shutdown_date->format('d/m/Y') }}</span>
                </div>
                @endif
                @if($d->users_affected !== null)
                <div class="flex gap-3">
                    <span class="text-xs font-medium text-red-500 dark:text-red-400 w-36 flex-shrink-0 pt-0.5">Usuarios afectados</span>
                    <span class="text-sm text-gray-700 dark:text-gray-300">{{ number_format($d->users_affected) }}</span>
                </div>
                @endif
            </div>

            {{-- Sustento documental --}}
            @if($d->resolution_number || $d->memo_number)
            <div class="border-t border-red-200 dark:border-red-800 pt-4">
                <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-3">Sustento documental</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-2">
                    @if($d->resolution_number)
                    <div class="flex gap-3">
                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400 w-36 flex-shrink-0 pt-0.5">N° Resolución</span>
                        <span class="text-sm font-mono text-gray-800 dark:text-gray-200">{{ $d->resolution_number }}</span>
                    </div>
                    @endif
                    @if($d->resolution_date)
                    <div class="flex gap-3">
                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400 w-36 flex-shrink-0 pt-0.5">Fecha resolución</span>
                        <span class="text-sm text-gray-800 dark:text-gray-200">{{ $d->resolution_date->format('d/m/Y') }}</span>
                    </div>
                    @endif
                    @if($d->resolution_entity)
                    <div class="flex gap-3 sm:col-span-2">
                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400 w-36 flex-shrink-0 pt-0.5">Entidad emisora</span>
                        <span class="text-sm text-gray-800 dark:text-gray-200">{{ $d->resolution_entity }}</span>
                    </div>
                    @endif
                    @if($d->memo_number)
                    <div class="flex gap-3">
                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400 w-36 flex-shrink-0 pt-0.5">N° Memorando</span>
                        <span class="text-sm font-mono text-gray-800 dark:text-gray-200">{{ $d->memo_number }}</span>
                    </div>
                    @endif
                    @if($d->memo_date)
                    <div class="flex gap-3">
                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400 w-36 flex-shrink-0 pt-0.5">Fecha memorando</span>
                        <span class="text-sm text-gray-800 dark:text-gray-200">{{ $d->memo_date->format('d/m/Y') }}</span>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            {{-- Autorización --}}
            @if($d->authorized_by_name || $d->authorized_by_position)
            <div class="border-t border-red-200 dark:border-red-800 pt-4">
                <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-3">Autorización</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-2">
                    @if($d->authorized_by_name)
                    <div class="flex gap-3">
                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400 w-36 flex-shrink-0 pt-0.5">Autorizado por</span>
                        <span class="text-sm text-gray-800 dark:text-gray-200">{{ $d->authorized_by_name }}</span>
                    </div>
                    @endif
                    @if($d->authorized_by_position)
                    <div class="flex gap-3">
                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400 w-36 flex-shrink-0 pt-0.5">Cargo</span>
                        <span class="text-sm text-gray-800 dark:text-gray-200">{{ $d->authorized_by_position }}</span>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            {{-- Sistema sucesor y datos --}}
            @if($d->successorSystem || $d->successor_description || $d->data_migrated || $d->data_retention_until)
            <div class="border-t border-red-200 dark:border-red-800 pt-4">
                <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-3">Sistema sucesor y datos</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-2">
                    @if($d->successorSystem)
                    <div class="flex gap-3 sm:col-span-2">
                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400 w-36 flex-shrink-0 pt-0.5">Sistema sucesor</span>
                        <a href="{{ route('systems.show', $d->successorSystem) }}"
                           class="text-sm text-blue-600 dark:text-blue-400 hover:underline">
                            {{ $d->successorSystem->acronym ? "[{$d->successorSystem->acronym}] " : '' }}{{ $d->successorSystem->name }}
                        </a>
                    </div>
                    @elseif($d->successor_description)
                    <div class="flex gap-3 sm:col-span-2">
                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400 w-36 flex-shrink-0 pt-0.5">Sucesor</span>
                        <span class="text-sm text-gray-800 dark:text-gray-200">{{ $d->successor_description }}</span>
                    </div>
                    @endif
                    <div class="flex gap-3">
                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400 w-36 flex-shrink-0 pt-0.5">Data migrada</span>
                        <span class="text-sm {{ $d->data_migrated ? 'text-green-600 dark:text-green-400 font-medium' : 'text-gray-500 dark:text-gray-400' }}">
                            {{ $d->data_migrated ? 'Sí' : 'No' }}
                        </span>
                    </div>
                    @if($d->data_migrated && $d->data_migration_destination)
                    <div class="flex gap-3">
                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400 w-36 flex-shrink-0 pt-0.5">Destino de data</span>
                        <span class="text-sm text-gray-800 dark:text-gray-200">{{ $d->data_migration_destination }}</span>
                    </div>
                    @endif
                    @if($d->data_retention_until)
                    <div class="flex gap-3">
                        <span class="text-xs font-medium text-gray-500 dark:text-gray-400 w-36 flex-shrink-0 pt-0.5">Conservar hasta</span>
                        <span class="text-sm {{ $d->data_retention_until->isPast() ? 'text-red-600 dark:text-red-400' : 'text-gray-800 dark:text-gray-200' }}">
                            {{ $d->data_retention_until->format('d/m/Y') }}
                        </span>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            {{-- Notas de auditoría --}}
            @if($d->audit_notes || $d->reactivation_procedure)
            <div class="border-t border-red-200 dark:border-red-800 pt-4 space-y-3">
                @if($d->audit_notes)
                <div>
                    <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-1">Notas de auditoría</p>
                    <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">{{ $d->audit_notes }}</p>
                </div>
                @endif
                @if($d->reactivation_procedure)
                <div>
                    <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-1">Procedimiento de reactivación</p>
                    <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">{{ $d->reactivation_procedure }}</p>
                </div>
                @endif
            </div>
            @endif

            {{-- Pie: registrado por --}}
            <div class="border-t border-red-200 dark:border-red-800 pt-3 flex items-center gap-2 text-xs text-gray-400 dark:text-gray-500">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                Registrado por
                <span class="font-medium text-gray-500 dark:text-gray-400">{{ $d->registeredBy->name ?? 'Sistema' }}</span>
                el {{ $d->created_at->format('d/m/Y \a \l\a\s H:i') }}
            </div>
        </div>
    </div>
    @endif

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
