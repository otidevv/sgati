<?php

namespace App\Traits;

use App\Models\SystemActivityLog;

trait LogsSystemActivity
{
    protected static function bootLogsSystemActivity(): void
    {
        static::created(fn($m) => $m->recordActivity('creado'));

        static::updated(function ($model) {
            $changes = collect($model->getChanges())
                ->except($model->ignoredForActivity())
                ->mapWithKeys(fn($new, $field) => [
                    $field => ['old' => $model->getOriginal($field), 'new' => $new],
                ])
                ->all();

            if (empty($changes)) return;

            $model->recordActivity('actualizado', $changes);
        });

        static::deleted(fn($m) => $m->recordActivity('eliminado'));
    }

    private function recordActivity(string $event, array $properties = []): void
    {
        $systemId = $this->resolveActivitySystemId();
        if (!$systemId) return;

        SystemActivityLog::create([
            'system_id'    => $systemId,
            'causer_id'    => auth()->id(),
            'subject_type' => $this->activitySubjectType(),
            'subject_id'   => $this->getKey(),
            'event'        => $event,
            'properties'   => $properties ?: null,
        ]);
    }

    protected function resolveActivitySystemId(): ?int
    {
        return $this->system_id ?? null;
    }

    protected function ignoredForActivity(): array
    {
        return ['updated_at', 'created_at', 'deleted_at'];
    }

    abstract protected function activitySubjectType(): string;
}
