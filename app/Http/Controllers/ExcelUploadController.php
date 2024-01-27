<?php

namespace App\Http\Controllers;

use App\Helper\FileHandler;
use App\Jobs\FileUploadJob;
use Illuminate\Http\Request;

class ExcelUploadController extends Controller
{
    public function upload(Request $request)
    {
        // Validating the Request
        $request->validate([
            'file' => 'required|mimes:csv,xlsx,xls|max:20000', //20MB
        ]);

        $file = $request->file('file');
        $uploadedPath = FileHandler::upload($file, 'excel_uploads');
        $uploadedPath = public_path('storage/' . $uploadedPath);

        // Dispatch the job to process the Excel file in the background processing
        FileUploadJob::dispatch($uploadedPath);

        return response()->json([
            'status' => 'success',
            'msg' => "File Saved! We'll be update soon after completion",
        ]);

    }
}
