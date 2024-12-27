<?php

namespace App\Http\Controllers;

use App\Exports\LabelExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class FileUploadController extends Controller
{
    public function uploadForm()
    {
        return view('upload');
    }

    public function processUpload(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv|max:2048',
        ]);

        // Process file
        $data = Excel::toArray([], $request->file('file')->getRealPath());

        // Pass data to label generation logic
        return Excel::download(new LabelExport($data), 'labels.xlsx');
    }
}
