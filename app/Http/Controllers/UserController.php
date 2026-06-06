<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Agency;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index(Request $request)
    {
        return view('content.admin.users.index');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,dinas,user',
            'agency_id' => 'nullable|exists:agencies,id',
            'status' => 'required|in:active,pending,inactive,rejected',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $input = $request->all();
        $input['password'] = Hash::make($input['password']);

        User::create($input);

        return response()->json(['success' => 'User created successfully.']);
    }

    public function edit($id)
    {
        $user = User::with('agency')->find($id);
        return response()->json($user);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8',
            'role' => 'required|in:admin,dinas,user',
            'agency_id' => 'nullable|exists:agencies,id',
            'status' => 'required|in:active,pending,inactive,rejected',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::find($id);
        $input = $request->except('password');

        if ($request->filled('password')) {
            $input['password'] = Hash::make($request->password);
        }

        $user->update($input);

        return response()->json(['success' => 'User updated successfully.']);
    }

    public function destroy($id)
    {
        User::find($id)->delete();
        return response()->json(['success' => 'User deleted successfully.']);
    }
}
