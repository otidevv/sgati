<?php

namespace App\Http\Controllers;

use App\Models\System;
use App\Models\SystemDocument;
use App\Enums\DocType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SystemDocumentController extends Controller
{
    public function store(Request $request, System $system)
    {
        $request->validate([
            'title'      => 'required|string|max:255',
            'doc_type'   => 'required|in:manual_user,manual_technical,oficio,resolution,acta,contract,diagram,other',
            'file'       => 'required|file|max:51200|mimes:pdf,doc,docx,xls,xlsx,png,jpg,jpeg',
            'doc_number' => 'nullable|string|max:100',
            'issuer'     => 'nullable|string|max:150',
            'issue_date' => 'nullable|date',
            'notes'      => 'nullable|string',
        ]);

        $file  = $request->file('file');
        $year  = now()->format('Y');
        $month = now()->format('m');
        $path  = $file->store("documents/{$year}/{$month}", 'local');

        $system->documents()->create([
            'title'       => $request->title,
            'doc_type'    => $request->doc_type,
            'doc_number'  => $request->doc_number,
            'issuer'      => $request->issuer,
            'issue_date'  => $request->issue_date,
            'file_path'   => $path,
            'file_name'   => $file->getClientOriginalName(),
            'file_size'   => $file->getSize(),
            'mime_type'   => $file->getMimeType(),
            'uploaded_by' => auth()->id(),
            'notes'       => $request->notes,
        ]);

        return redirect(route('systems.show', $system) . '#documents')->with('success', 'Documento cargado correctamente.');
    }

    public function destroy(System $system, SystemDocument $document)
    {
        abort_if($document->system_id !== $system->id, 404);
        Storage::disk('local')->delete($document->file_path);
        $document->delete();

        return redirect(route('systems.show', $system) . '#documents')->with('success', 'Documento eliminado.');
    }

    public function download(System $system, SystemDocument $document)
    {
        abort_if($document->system_id !== $system->id, 404);

        return Storage::disk('local')->download($document->file_path, $document->file_name);
    }

    public function preview(System $system, SystemDocument $document)
    {
        abort_if($document->system_id !== $system->id, 404);

        if (! Storage::disk('local')->exists($document->file_path)) {
            abort(404, 'El archivo no existe en el servidor.');
        }

        $mime = $document->mime_type ?: Storage::disk('local')->mimeType($document->file_path);

        return response()->file(
            Storage::disk('local')->path($document->file_path),
            ['Content-Type' => $mime, 'Content-Disposition' => 'inline; filename="' . rawurlencode($document->file_name) . '"']
        );
    }

    public function repository(Request $request)
    {
        $documents = SystemDocument::with('system')
            ->when($request->doc_type, fn($q) => $q->where('doc_type', $request->doc_type))
            ->when($request->search, fn($q) => $q->where('title', 'ilike', "%{$request->search}%"))
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        $docTypes = DocType::cases();

        return view('documents.index', compact('documents', 'docTypes'));
    }
}
