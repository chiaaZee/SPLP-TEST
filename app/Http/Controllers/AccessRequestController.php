<?php

namespace App\Http\Controllers;

use App\Models\ServiceAccessRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AccessRequestController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        return view('content.admin.access-requests.index');
    }

    public function download($id)
    {
        $request = ServiceAccessRequest::findOrFail($id);

        if (!$request->attachment) {
            return back()->with('error', 'Tidak ada lampiran.');
        }

        $path = public_path('uploads/access_requests/' . $request->attachment);

        if (!file_exists($path)) {
            return back()->with('error', 'File tidak ditemukan di server.');
        }

        return response()->download($path);
    }
}
