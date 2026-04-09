<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Server;
use App\Models\DatabaseServer;
use App\Models\DatabaseServerResponsible;
use App\Models\DatabaseServerResponsibleDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DatabaseServerResponsibleDocumentController extends Controller
{
    public function store(Request $request, Server $server, DatabaseServer $databaseServer, DatabaseServerResponsible $responsible)
    {
        $request->validate([
            'file'        => 'required|file|mimes:pdf,doc,docx,xls,xlsx,png,jpg,jpeg|max:10240',
            'description' => 'nullable|string|max:255',
        ]);

        $file = $request->file('file');
        $path = $file->store("db-responsible-docs/{$responsible->id}", 'local');

        $responsible->documents()->create([
            'original_name' => $file->getClientOriginalName(),
            'file_path'     => $path,
            'description'   => $request->input('description') ?: null,
        ]);

        return back()->with('success', 'Documento adjuntado correctamente.');
    }

    public function download(Server $server, DatabaseServer $databaseServer, DatabaseServerResponsible $responsible, DatabaseServerResponsibleDocument $document)
    {
        if (! Storage::disk('local')->exists($document->file_path)) {
            abort(404, 'El archivo no existe en el servidor.');
        }

        return Storage::disk('local')->download($document->file_path, $document->original_name);
    }

    public function preview(Server $server, DatabaseServer $databaseServer, DatabaseServerResponsible $responsible, DatabaseServerResponsibleDocument $document)
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

    public function destroy(Server $server, DatabaseServer $databaseServer, DatabaseServerResponsible $responsible, DatabaseServerResponsibleDocument $document)
    {
        Storage::disk('local')->delete($document->file_path);
        $document->delete();

        return back()->with('success', 'Documento eliminado.');
    }
}
