<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\Agency;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserTable extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // Filters & Sorting
    public $search = '';
    public $perPage = 10;
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $statusFilter = 'all'; // Default to show all

    // Form Properties
    public $userId = null;
    public $name, $email, $role = 'user', $agency_id, $status = 'pending';
    public $password, $phone;
    public $jabatan, $nip; // New profile fields

    // Modal State
    public $isModalOpen = false;
    public $isEditMode = false;

    // Listeners
    protected $listeners = [
        'refreshTable' => '$refresh',
        'deleteConfirmed' => 'deleteUser',
        'suspendConfirmed' => 'suspendUser'
    ];

    public function mount()
    {
        if (!auth()->check() || !auth()->user()->hasRole('admin')) {
            abort(403, 'Unauthorized.');
        }
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        $this->sortField = $field;
    }

    public function openModal()
    {
        $this->resetValidation();
        $this->resetForm();
        $this->isModalOpen = true;
        $this->isEditMode = false;
        $this->role = 'user';
        $this->status = 'active'; // Default for new users created by admin
        $this->dispatch('open-user-modal');
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetForm();
        $this->dispatch('close-user-modal');
    }

    public function resetForm()
    {
        $this->userId = null;
        $this->name = '';
        $this->email = '';
        $this->role = 'user';
        $this->agency_id = null;
        $this->status = 'active';
        $this->password = '';
        $this->phone = '';
        $this->jabatan = '';
        $this->nip = '';
    }

    public function store()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,dinas,user',
            'agency_id' => 'nullable|exists:agencies,id',
            'status' => 'required|in:active,inactive,suspended,pending',
            'phone' => 'nullable|string|max:20',
        ]);

        User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'role' => $this->role,
            'agency_id' => $this->agency_id ?: null, // Handle empty string as null
            'status' => $this->status,
            'phone' => $this->phone,
            'jabatan' => $this->jabatan,
            'nip' => $this->nip,
        ]);

        $this->closeModal();
        $this->dispatch('swal:toast', type: 'success', message: 'User berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $this->resetValidation();
        $this->resetForm();

        $user = User::find($id);
        $this->userId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->role;
        $this->agency_id = $user->agency_id;
        $this->status = $user->status;
        $this->phone = $user->phone;
        $this->jabatan = $user->jabatan;
        $this->nip = $user->nip;

        $this->isEditMode = true;
        $this->isModalOpen = true;
        $this->dispatch('open-user-modal');
    }

    public function update()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $this->userId,
            'password' => 'nullable|string|min:8',
            'role' => 'required|in:admin,dinas,user',
            'agency_id' => 'nullable|exists:agencies,id',
            'status' => 'required|in:active,inactive,suspended,pending,rejected',
            'phone' => 'nullable|string|max:20',
        ]);

        $user = User::find($this->userId);
        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'agency_id' => $this->agency_id ?: null,
            'status' => $this->status,
            'phone' => $this->phone,
            'jabatan' => $this->jabatan,
            'nip' => $this->nip,
        ];

        if (!empty($this->password)) {
            $data['password'] = Hash::make($this->password);
        }

        $user->update($data);

        $this->closeModal();
        $this->dispatch('swal:toast', type: 'success', message: 'Data User berhasil diperbarui!');
    }

    public function confirmDelete($id)
    {
        $this->dispatch('swal:confirm',
            type: 'warning',
            title: 'Hapus User?',
            text: 'Data yang dihapus tidak dapat dikembalikan.',
            id: $id,
            method: 'deleteConfirmed'
        );
    }

    public function deleteUser($id)
    {
        // Handle array payload from SweetAlert
        if (is_array($id)) $id = $id['id'];

        $user = User::find($id);
        if ($user) {
            $user->delete();
            $this->dispatch('swal:toast', type: 'success', message: 'User berhasil dihapus.');
        }
    }

    // Quick Action: Suspend
    public function toggleSuspend($id)
    {
        $user = User::find($id);
        if (!$user) return;

        if ($user->status === 'suspended') {
            // Unsuspend -> active
            $user->update(['status' => 'active']);
            $this->dispatch('swal:toast', type: 'success', message: 'User telah diaktifkan kembali.');
        } else {
            // Suspend
            $this->dispatch('swal:confirm',
                type: 'warning',
                title: 'Suspend User?',
                text: 'User ini tidak akan bisa mengakses API.',
                id: $id,
                method: 'suspendConfirmed',
                confirmText: 'Ya, Suspend!',
                cancelText: 'Batal'
            );
        }
    }

    public function suspendUser($id)
    {
         // Handle array payload from SweetAlert
         if (is_array($id)) $id = $id['id'];

         $user = User::find($id);
         if ($user) {
             $user->update(['status' => 'suspended']);
             $this->dispatch('swal:toast', type: 'warning', message: 'User telah disuspend.');
         }
    }

    public function getUsersQuery()
    {
        $query = User::with('agency')
            ->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            });

        // Base Check: Only Active & Suspended users allowed in this view
        // Pending/Rejected are handled in Registration Confirmation module.
        $query->whereIn('status', ['active', 'suspended']);

        // Specific Filter
        if ($this->statusFilter === 'active') {
            $query->where('status', 'active');
        } elseif ($this->statusFilter === 'suspended') {
            $query->where('status', 'suspended');
        }
        // 'all' (default) -> shows both (already covered by whereIn above)

        return $query->orderBy($this->sortField, $this->sortDirection);
    }

    public function render()
    {
        $users = $this->getUsersQuery()->paginate($this->perPage);
        $agencies = Agency::where('status', 'active')->orderBy('name')->get();

        return view('livewire.admin.user-table', [
            'users' => $users,
            'agencies' => $agencies
        ]);
    }
}
