<?php

namespace App\Http\Controllers;

use App\Models\System;
use App\Exports\SystemsExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index()
    {
        $systems = System::with(['area', 'responsible', 'infrastructure'])
            ->orderBy('name')
            ->get();

        return view('reports.index', compact('systems'));
    }

    public function exportExcel()
    {
        return Excel::download(new SystemsExport, 'sistemas_sgati_' . now()->format('Ymd') . '.xlsx');
    }

    public function exportPdf()
    {
        $systems = System::with(['area', 'responsible', 'infrastructure'])
            ->orderBy('name')
            ->get();

        $pdf = Pdf::loadView('pdf.systems-report', compact('systems'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('sistemas_sgati_' . now()->format('Ymd') . '.pdf');
    }
}
