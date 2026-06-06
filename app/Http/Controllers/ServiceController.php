<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Agency;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Service::with('agency')->latest()->get();
            return Datatables::of($data)
                ->addIndexColumn() // Enables DT_RowIndex
                ->addColumn('action', function ($row) {
                    $btn = '<div class="dropdown">
                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical"></i></button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item test-api" href="javascript:void(0)" data-id="' . $row->id . '"><i class="ti ti-plug-connected me-1"></i> Test API</a>
                            <a class="dropdown-item edit-record" href="javascript:void(0)" data-id="' . $row->id . '"><i class="ti ti-pencil me-1"></i> Edit</a>
                            <a class="dropdown-item delete-record" href="javascript:void(0)" data-id="' . $row->id . '"><i class="ti ti-trash me-1"></i> Delete</a>
                        </div>
                    </div>';
                    return $btn;
                })
                ->addColumn('agency_name', function ($row) {
                    return $row->agency ? $row->agency->name : '-';
                })
                ->addColumn('method_label', function ($row) {
                    $color = 'primary';
                    if ($row->method == 'GET')
                        $color = 'success';
                    if ($row->method == 'POST')
                        $color = 'warning';
                    if ($row->method == 'DELETE')
                        $color = 'danger';
                    return '<span class="badge bg-label-' . $color . '">' . $row->method . '</span>';
                })
                ->addColumn('status_label', function ($row) {
                    if ($row->status == 'active') {
                        return '<span class="badge bg-label-success">Active</span>';
                    } else {
                        return '<span class="badge bg-label-secondary">Inactive</span>';
                    }
                })
                ->rawColumns(['action', 'status_label', 'method_label'])
                ->make(true);
        }

        $agencies = Agency::where('status', 'active')->get();
        return view('content.admin.services.index', compact('agencies'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'agency_id' => 'required|exists:agencies,id',
            'name' => 'required|string|max:255',
            'endpoint_url' => 'required|url',
            'method' => 'required|in:GET,POST,PUT,DELETE',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $input = $request->all();
        $input['slug'] = Str::slug($input['name']);

        // Check uniqueness of slug per agency
        if (Service::where('agency_id', $input['agency_id'])->where('slug', $input['slug'])->exists()) {
            return response()->json(['errors' => ['name' => ['Service name already exists for this agency.']]], 422);
        }

        Service::create($input);

        return response()->json(['success' => 'Service created successfully.']);
    }

    public function edit($id)
    {
        $service = Service::with('agency')->find($id);
        return response()->json($service);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'agency_id' => 'required|exists:agencies,id',
            'name' => 'required|string|max:255',
            'endpoint_url' => 'required|url',
            'method' => 'required|in:GET,POST,PUT,DELETE',
            'status' => 'required|in:active,inactive',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $service = Service::find($id);
        $input = $request->all();
        $input['slug'] = Str::slug($input['name']);

        // Check uniqueness of slug per agency (excluding self)
        if (Service::where('agency_id', $input['agency_id'])->where('slug', $input['slug'])->where('id', '!=', $id)->exists()) {
            return response()->json(['errors' => ['name' => ['Service name already exists for this agency.']]], 422);
        }

        $service->update($input);

        return response()->json(['success' => 'Service updated successfully.']);
    }

    public function destroy($id)
    {
        Service::find($id)->delete();
        return response()->json(['success' => 'Service deleted successfully.']);
    }

    public function testApi($id)
    {
        $service = Service::find($id);
        if (!$service)
            return response()->json(['error' => 'Service not found'], 404);

        try {
            // Log the attempt
            // In a real gateway, we would validate headers/auth here.

            $startTime = microtime(true);

            // Execute Request
            $response = Http::withOptions([
                'verify' => false, // Initial dev mode
                'timeout' => 10,
            ])
                ->send($service->method, $service->endpoint_url);

            $duration = round((microtime(true) - $startTime) * 1000, 2); // ms

            return response()->json([
                'status' => $response->status(),
                'success' => $response->successful(),
                'headers' => $response->headers(),
                'body' => $response->json() ?? $response->body(),
                'duration' => $duration . ' ms'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'status' => 500,
                'body' => $e->getMessage(),
                'duration' => '0 ms'
            ]);
        }
    }
}
