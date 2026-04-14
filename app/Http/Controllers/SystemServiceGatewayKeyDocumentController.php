<?php

namespace App\Http\Controllers;

use App\Models\ServiceGatewayKey;
use App\Models\ServiceGatewayKeyDocument;
use App\Models\System;
use App\Models\SystemService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SystemServiceGatewayKeyDocumentController extends Controller
{
    private const DOC_TYPES = 'solicitud,resolucion_directoral,resolucion_jefatural,memorando,oficio,contrato,acta,convenio,otro';

    public function store(Request $request, System $system, SystemService $service, ServiceGatewayKey $key)
    {
        abort_if($key->system_service_id !== $service->id, 403);

        $data = $request->validate([
            'file'            => 'required|file|mimes:pdf,doc,docx,xls,xlsx,png,jpg,jpeg|max:10240',
            'description'     => 'nullable|string|max:255',
            'document_type'   => 'nullable|in:' . self::DOC_TYPES,
            'document_number' => 'nullable|string|max:100',
            'document_date'   => 'nullable|date',
            'document_notes'  => 'nullable|string|max:500',
        ]);

        $file = $request->file('file');
        $path = $file->store("gw-key-docs/{$key->id}", 'local');

        $key->documents()->create([
            'original_name'   => $file->getClientOriginalName(),
            'file_path'       => $path,
            'description'     => $data['description']     ?: null,
            'document_type'   => $data['document_type']   ?: null,
            'document_number' => $data['document_number'] ?: null,
            'document_date'   => $data['document_date']   ?: null,
            'document_notes'  => $data['document_notes']  ?: null,
        ]);

        return back()->with('success', 'Documento adjuntado al solicitante correctamente.');
    }

    public function download(System $system, SystemService $service, ServiceGatewayKey $key, ServiceGatewayKeyDocument $document)
    {
        abort_if($document->gateway_key_id !== $key->id, 403);

        $disk = Storage::disk('local');
        if (! $disk->exists($document->file_path)) {
            abort(404, 'El archivo no existe en el servidor.');
        }

        return $disk->download($document->file_path, $document->original_name);
    }

    public function preview(System $system, SystemService $service, ServiceGatewayKey $key, ServiceGatewayKeyDocument $document)
    {
        abort_if($document->gateway_key_id !== $key->id, 403);

        $disk = Storage::disk('local');
        if (! $disk->exists($document->file_path)) {
            abort(404, 'El archivo no existe en el servidor.');
        }

        $ext     = strtolower(pathinfo($document->original_name, PATHINFO_EXTENSION));
        $mimeMap = [
            'pdf'  => 'application/pdf',
            'png'  => 'image/png',
            'jpg'  => 'image/jpeg',
            'jpeg' => 'image/jpeg',
        ];
        $mime = $mimeMap[$ext] ?? $disk->mimeType($document->file_path);

        return response()->file(
            $disk->path($document->file_path),
            ['Content-Type' => $mime, 'Content-Disposition' => 'inline; filename="' . rawurlencode($document->original_name) . '"']
        );
    }

    public function destroy(System $system, SystemService $service, ServiceGatewayKey $key, ServiceGatewayKeyDocument $document)
    {
        abort_if($document->gateway_key_id !== $key->id, 403);

        Storage::disk('local')->delete($document->file_path);
        $document->delete();

        return back()->with('success', 'Documento eliminado.');
    }
}
