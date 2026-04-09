<?php

namespace App\Http\Controllers;

use App\Models\System;
use App\Models\SystemService;
use App\Models\SystemServiceDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SystemServiceDocumentController extends Controller
{
    private const DOC_TYPES = 'solicitud,acta_entrega,oficio,contrato,memo,resolucion,otro';

    public function store(Request $request, System $system, SystemService $service)
    {
        $request->validate([
            'file'          => 'required|file|mimes:pdf,doc,docx,xls,xlsx,png,jpg,jpeg|max:10240',
            'document_type' => 'required|in:' . self::DOC_TYPES,
            'direction'     => 'required|in:sent,received',
            'description'   => 'nullable|string|max:255',
        ]);

        $file = $request->file('file');
        $path = $file->store("service-docs/{$service->id}", 'local');

        $service->documents()->create([
            'original_name' => $file->getClientOriginalName(),
            'file_path'     => $path,
            'document_type' => $request->document_type,
            'direction'     => $request->direction,
            'description'   => $request->input('description') ?: null,
        ]);

        return back()->with('success', 'Documento adjuntado correctamente.');
    }

    public function download(System $system, SystemService $service, SystemServiceDocument $document)
    {
        abort_unless(Storage::disk('local')->exists($document->file_path), 404, 'El archivo no existe.');
        return Storage::disk('local')->download($document->file_path, $document->original_name);
    }

    public function preview(System $system, SystemService $service, SystemServiceDocument $document)
    {
        abort_unless(Storage::disk('local')->exists($document->file_path), 404, 'El archivo no existe.');

        $ext = strtolower(pathinfo($document->original_name, PATHINFO_EXTENSION));
        $mimeMap = [
            'pdf'  => 'application/pdf',
            'png'  => 'image/png',
            'jpg'  => 'image/jpeg',
            'jpeg' => 'image/jpeg',
        ];

        $mime = $mimeMap[$ext] ?? Storage::disk('local')->mimeType($document->file_path);

        return response()->file(
            Storage::disk('local')->path($document->file_path),
            ['Content-Type' => $mime, 'Content-Disposition' => 'inline; filename="' . rawurlencode($document->original_name) . '"']
        );
    }

    public function destroy(System $system, SystemService $service, SystemServiceDocument $document)
    {
        Storage::disk('local')->delete($document->file_path);
        $document->delete();
        return back()->with('success', 'Documento eliminado.');
    }
}
