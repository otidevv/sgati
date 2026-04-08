<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Server;
use App\Models\ServerResponsible;
use App\Models\ServerResponsibleDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ServerResponsibleDocumentController extends Controller
{
    public function store(Request $request, Server $server, ServerResponsible $responsible)
    {
        $request->validate([
            'file'        => 'required|file|mimes:pdf,doc,docx,xls,xlsx,png,jpg,jpeg|max:10240',
            'description' => 'nullable|string|max:255',
        ]);

        $file = $request->file('file');
        $path = $file->store("server-responsible-docs/{$responsible->id}", 'local');

        $responsible->documents()->create([
            'original_name' => $file->getClientOriginalName(),
            'file_path'     => $path,
            'description'   => $request->input('description') ?: null,
        ]);

        return back()->with('success', 'Documento adjuntado correctamente.');
    }

    public function download(Server $server, ServerResponsible $responsible, ServerResponsibleDocument $document)
    {
        if (! Storage::disk('local')->exists($document->file_path)) {
            abort(404, 'El archivo no existe en el servidor.');
        }

        return Storage::disk('local')->download($document->file_path, $document->original_name);
    }

    public function destroy(Server $server, ServerResponsible $responsible, ServerResponsibleDocument $document)
    {
        Storage::disk('local')->delete($document->file_path);
        $document->delete();

        return back()->with('success', 'Documento eliminado.');
    }
}
