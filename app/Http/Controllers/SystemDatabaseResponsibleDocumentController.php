<?php

namespace App\Http\Controllers;

use App\Models\System;
use App\Models\SystemDatabase;
use App\Models\SystemDatabaseResponsible;
use App\Models\SystemDatabaseResponsibleDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SystemDatabaseResponsibleDocumentController extends Controller
{
    private const DOC_TYPES = 'resolucion_directoral,resolucion_jefatural,memorando,oficio,contrato,acta,otro';

    public function store(Request $request, System $system, SystemDatabase $database, SystemDatabaseResponsible $responsible)
    {
        $data = $request->validate([
            'file'            => 'required|file|mimes:pdf,doc,docx,xls,xlsx,png,jpg,jpeg|max:10240',
            'description'     => 'nullable|string|max:255',
            'document_type'   => 'nullable|in:' . self::DOC_TYPES,
            'document_number' => 'nullable|string|max:100',
            'document_date'   => 'nullable|date',
            'document_notes'  => 'nullable|string|max:500',
        ]);

        $file = $request->file('file');
        $path = $file->store("sysdb-responsible-docs/{$responsible->id}", 'local');

        $responsible->documents()->create([
            'original_name'   => $file->getClientOriginalName(),
            'file_path'       => $path,
            'description'     => $data['description']     ?: null,
            'document_type'   => $data['document_type']   ?: null,
            'document_number' => $data['document_number'] ?: null,
            'document_date'   => $data['document_date']   ?: null,
            'document_notes'  => $data['document_notes']  ?: null,
        ]);

        return redirect(route('systems.show', $system) . '#databases')->with('success', 'Documento adjuntado correctamente.');
    }

    public function download(System $system, SystemDatabase $database, SystemDatabaseResponsible $responsible, SystemDatabaseResponsibleDocument $document)
    {
        $disk = $this->disk();

        if (! $disk->exists($document->file_path)) {
            abort(404, 'El archivo no existe en el servidor.');
        }

        return $disk->download($document->file_path, $document->original_name);
    }

    public function preview(System $system, SystemDatabase $database, SystemDatabaseResponsible $responsible, SystemDatabaseResponsibleDocument $document)
    {
        $disk = $this->disk();

        if (! $disk->exists($document->file_path)) {
            abort(404, 'El archivo no existe en el servidor.');
        }

        $ext = strtolower(pathinfo($document->original_name, PATHINFO_EXTENSION));
        $mimeMap = [
            'pdf'  => 'application/pdf',
            'png'  => 'image/png',
            'jpg'  => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif'  => 'image/gif',
            'webp' => 'image/webp',
            'svg'  => 'image/svg+xml',
        ];

        $mime = $mimeMap[$ext] ?? $disk->mimeType($document->file_path);

        return response()->file(
            $disk->path($document->file_path),
            ['Content-Type' => $mime, 'Content-Disposition' => 'inline; filename="' . rawurlencode($document->original_name) . '"']
        );
    }

    public function destroy(System $system, SystemDatabase $database, SystemDatabaseResponsible $responsible, SystemDatabaseResponsibleDocument $document)
    {
        $this->disk()->delete($document->file_path);
        $document->delete();

        return redirect(route('systems.show', $system) . '#databases')->with('success', 'Documento eliminado.');
    }

    /** @return \Illuminate\Filesystem\FilesystemAdapter */
    private function disk(): \Illuminate\Filesystem\FilesystemAdapter
    {
        return Storage::disk('local');
    }
}
