<?php

namespace App\Models;

use App\Enums\SystemOriginType;
use Illuminate\Database\Eloquent\Model;

class SystemOrigin extends Model
{
    protected $fillable = [
        'system_id', 'origin_type',
        // donado
        'donor_name', 'donor_institution', 'donation_type',
        'thesis_title', 'thesis_author', 'thesis_university',
        'donation_date', 'donation_document',
        // terceros
        'company_name', 'contact_name', 'contact_email', 'contact_phone',
        'contract_number', 'contract_date', 'contract_value', 'warranty_expiry',
        // interno
        'team_name', 'dev_start_date', 'dev_end_date', 'methodology', 'project_code',
        // estado
        'state_entity', 'state_entity_code', 'state_system_code',
        'state_official_url', 'legal_basis', 'state_implementation_date',
        // común
        'origin_notes',
    ];

    protected function casts(): array
    {
        return [
            'origin_type'              => SystemOriginType::class,
            'donation_date'            => 'date',
            'contract_date'            => 'date',
            'warranty_expiry'          => 'date',
            'dev_start_date'           => 'date',
            'dev_end_date'             => 'date',
            'state_implementation_date'=> 'date',
            'contract_value'           => 'decimal:2',
        ];
    }

    public function system()
    {
        return $this->belongsTo(System::class);
    }

    public function donationTypeLabel(): string
    {
        return match($this->donation_type) {
            'thesis'           => 'Tesis',
            'research_project' => 'Proyecto de investigación',
            'direct_donation'  => 'Donación directa',
            'agreement'        => 'Convenio',
            default            => '—',
        };
    }

    public function methodologyLabel(): string
    {
        return match($this->methodology) {
            'scrum'     => 'Scrum',
            'kanban'    => 'Kanban',
            'waterfall' => 'Cascada (Waterfall)',
            'rup'       => 'RUP',
            'other'     => 'Otra',
            default     => '—',
        };
    }
}
