<?php

namespace App\Http\Controllers;

use App\Exports\ActivityExport;
use Maatwebsite\Excel\Facades\Excel;

class ExportActivityController extends Controller
{
    public function exportExcel()
    {
        return Excel::download(new ActivityExport, 'report_activity.xlsx');
    }
}
