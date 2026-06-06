@section('title', 'Hak Akses & Role')

<div>
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
        <div class="card-header border-bottom">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title mb-0">Matrix Hak Akses</h5>
                    <small class="text-muted">Centang kotak untuk memberikan izin akses ke Role tertentu.</small>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive text-nowrap">
                <table class="table table-hover table-bordered mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">Permission / Fitur</th>
                            @foreach($roles as $role)
                            <th class="text-center text-uppercase">{{ $role->name }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($groupedPermissions as $group => $permissions)
                        <tr class="table-secondary">
                            <td colspan="{{ count($roles) + 1 }}" class="fw-bold ps-4 text-primary">
                                <i class="ti ti-folder me-1"></i> {{ $group }}
                            </td>
                        </tr>
                        @foreach($permissions as $perm)
                        <tr wire:key="perm-{{ $perm->id }}">
                            <td class="ps-4">
                                {{ $perm->name }}
                            </td>
                            @foreach($roles as $role)
                            <td class="text-center">
                                <div class="form-check d-flex justify-content-center">
                                    <input class="form-check-input" type="checkbox"
                                           wire:model="selectedPermissions.{{ $role->name }}"
                                           value="{{ $perm->name }}"
                                           id="chk-{{ $role->name }}-{{ $perm->id }}">
                                </div>
                            </td>
                            @endforeach
                        </tr>
                        @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer text-end border-top py-4">
            <button wire:click="save" wire:loading.attr="disabled" class="btn btn-primary">
                <span wire:loading.remove><i class="ti ti-device-floppy me-1"></i> Simpan Perubahan</span>
                <span wire:loading><i class="ti ti-loader-2 ti-spin me-1"></i> Menyimpan...</span>
            </button>
        </div>
    </div>
</div>
