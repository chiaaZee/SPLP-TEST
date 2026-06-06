@extends('layouts/layoutMaster')

@section('title', 'Hak Akses & Role')

@section('content')
    <!-- Hero Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card text-white h-100 overflow-hidden"
                style="background: linear-gradient(120deg, #7367f0 0%, #9e95f5 100%); border: none; box-shadow: 0 4px 15px rgba(115, 103, 240, 0.4);">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h3 class="text-white fw-bold mb-1">Hak Akses & Role</h3>
                            <p class="text-white opacity-75 mb-0">Atur perizinan user berdasarkan role.</p>
                        </div>
                        <i class="ti ti-lock text-white opacity-25" style="font-size: 5rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>


<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Matrix Hak Akses</h5>
        <small class="text-muted">Centang kotak untuk memberikan izin akses ke Role tertentu.</small>
    </div>
    <div class="card-body">
        @if(session('success'))
        <div class="alert alert-success alert-dismissible" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <form action="{{ route('roles-permissions.update') }}" method="POST">
            @csrf

            <div class="table-responsive text-nowrap">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Permission / Fitur</th>
                            @foreach($roles as $role)
                            <th class="text-center text-uppercase">{{ $role->name }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($groupedPermissions as $group => $permissions)
                        <tr class="table-secondary">
                            <td colspan="{{ $roles->count() + 1 }}" class="fw-bold">{{ $group }}</td>
                        </tr>
                        @foreach($permissions as $perm)
                        <tr>
                            <td>
                                {{ $perm->name }}
                            </td>
                            @foreach($roles as $role)
                            <td class="text-center">
                                <div class="form-check d-flex justify-content-center">
                                    <input class="form-check-input" type="checkbox"
                                           name="permissions[{{ $role->name }}][]"
                                           value="{{ $perm->name }}"
                                           {{ $role->hasPermissionTo($perm->name) ? 'checked' : '' }}>
                                </div>
                            </td>
                            @endforeach
                        </tr>
                        @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4 text-end">
                <button type="submit" class="btn btn-primary">
                    <i class="ti ti-device-floppy me-1"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
