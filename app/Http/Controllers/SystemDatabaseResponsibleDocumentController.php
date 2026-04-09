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
    public function store(Request $request, System $system, SystemDatabase $database, SystemDatabaseResponsible $responsible)
    {
        $request->validate([
            'file'        => 'required|file|mimes:pdf,doc,docx,xls,xlsx,png,jpg,jpeg|max:10240',
            'description' => 'nullable|string|max:255',
        ]);

        $file = $request->file('file');
        $path = $file->store("sysdb-responsible-docs/{$responsible->id}", 'local');

        $responsible->documents()->create([
            'original_name' => $file->getClientOriginalName(),
            'file_path'     => $path,
            'description'   => $request->input('description') ?: null,
        ]);

        return back()->with('success', 'Documento adjuntado correctamente.');
    }

    public function download(System $system, SystemDatabase $database, SystemDatabaseResponsible $responsible, SystemDatabaseResponsibleDocument $document)
    {
        if (! Storage::disk('local')->exists($document->file_path)) {
            abort(404, 'El archivo no existe en el servidor.');
        }

        return Storage::disk('local')->download($document->file_path, $document->original_name);
    }

    public function preview(System $system, SystemDatabase $database, SystemDatabaseResponsible $responsible, SystemDatabaseResponsibleDocument $document)
    {
        if (! Storage::disk('local')->exists($document->file_path)) {
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

        $mime = $mimeMap[$ext] ?? Storage::disk('local')->mimeType($document->file_path);

        return response()->file(
            Storage::disk('local')->path($document->file_path),
            ['Content-Type' => $mime, 'Content-Disposition' => 'inline; filename="' . rawurlencode($document->original_name) . '"']
        );
    }

    public function destroy(System $system, SystemDatabase $database, SystemDatabaseResponsible $responsible, SystemDatabaseResponsibleDocument $document)
    {
        Storage::disk('local')->delete($document->file_path);
        $document->delete();

        return back()->with('success', 'Documento eliminado.');
    }
}
