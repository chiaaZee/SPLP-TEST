<?php

namespace App\Http\Controllers;

use App\Models\Agency;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AgencyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Agency::latest()->get();
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $btn = '<div class="dropdown">
                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical"></i></button>
                        <div class="dropdown-menu">
                            <a class="dropdown-item edit-record" href="javascript:void(0)" data-id="' . $row->id . '"><i class="ti ti-pencil me-1"></i> Edit</a>
                            <a class="dropdown-item delete-record" href="javascript:void(0)" data-id="' . $row->id . '"><i class="ti ti-trash me-1"></i> Delete</a>
                        </div>
                    </div>';
                    return $btn;
                })
                ->addColumn('status_label', function ($row) {
                    if ($row->status == 'active') {
                        return '<span class="badge bg-label-success">Active</span>';
                    } else {
                        return '<span class="badge bg-label-secondary">Inactive</span>';
                    }
                })
                ->rawColumns(['action', 'status_label'])
                ->make(true);
        }

        return view('content.admin.agency.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:agencies,code',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $input = $request->all();

        // Handle File Upload
        if ($request->hasFile('logo')) {
            $image = $request->file('logo');
            $name = time() . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('/assets/img/agency');
            $image->move($destinationPath, $name);
            $input['logo'] = $name;
        }

        Agency::create($input);

        return response()->json(['success' => 'Agency saved successfully.']);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $agency = Agency::find($id);
        return response()->json($agency);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:agencies,code,' . $id,
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $agency = Agency::find($id);
        $input = $request->all();

        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($agency->logo && file_exists(public_path('/assets/img/agency/' . $agency->logo))) {
                unlink(public_path('/assets/img/agency/' . $agency->logo));
            }
            $image = $request->file('logo');
            $name = time() . '.' . $image->getClientOriginalExtension();
            $destinationPath = public_path('/assets/img/agency');
            $image->move($destinationPath, $name);
            $input['logo'] = $name;
        }

        $agency->update($input);

        return response()->json(['success' => 'Agency updated successfully.']);
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $agency = Agency::find($id);
        if ($agency->logo && file_exists(public_path('/assets/img/agency/' . $agency->logo))) {
            unlink(public_path('/assets/img/agency/' . $agency->logo));
        }
        $agency->delete();

        return response()->json(['success' => 'Agency deleted successfully.']);
    }
}
