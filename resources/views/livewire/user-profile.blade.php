@section('title', 'Profil Saya')

@section('page-style')
@vite(['resources/assets/vendor/scss/pages/page-profile.scss'])
@endsection

<div>
    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="user-profile-header-banner" style="background: linear-gradient(120deg, #7367f0 0%, #9e95f5 100%); height: 250px; border-radius: 0.375rem 0.375rem 0 0; position: relative; overflow: hidden;">
                    <div class="d-flex justify-content-end align-items-center h-100 px-4 px-md-5">
                         <div class="text-white text-end z-1 col-md-6">
                            <h2 class="text-white fw-bold mb-1">Halo, {{ $name }}!</h2>
                            <p class="mb-0 fs-5 opacity-75">Senang melihat Anda kembali. Kelola profil dan keamanan akun Anda di sini.</p>
                         </div>
                         <!-- Decorative Icon BG -->
                         <i class="ti ti-user-circle position-absolute start-0 bottom-0 text-white" style="font-size: 15rem; transform: translate(10%, 30%); opacity: 0.08;"></i>
                    </div>
                </div>
                <!-- User Profile Header -->
                <div class="user-profile-header d-flex flex-column flex-sm-row text-sm-start text-center mb-4 position-relative z-1">
                    <div class="flex-shrink-0 mt-n2 mx-sm-0 mx-auto">
                        @if ($photo)
                            <img src="{{ $photo->temporaryUrl() }}" alt="user image" class="d-block h-auto ms-0 ms-sm-4 rounded user-profile-img" style="width: 100px; height: 100px; object-fit: cover; border: 3px solid #fff;">
                        @elseif($existingPhoto)
                            <img src="{{ Storage::url($existingPhoto) }}" alt="user image" class="d-block h-auto ms-0 ms-sm-4 rounded user-profile-img" style="width: 100px; height: 100px; object-fit: cover; border: 3px solid #fff;">
                        @else
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($name) }}&background=7367f0&color=ffffff" alt="user image" class="d-block h-auto ms-0 ms-sm-4 rounded user-profile-img" style="width: 100px; height: 100px; object-fit: cover; border: 3px solid #fff;">
                        @endif
                    </div>
                    <div class="flex-grow-1 mt-3 mt-sm-5">
                        <div class="d-flex align-items-md-end align-items-sm-start align-items-center justify-content-md-between justify-content-start mx-4 flex-md-row flex-column gap-4">
                            <div class="user-profile-info">
                                <h4>{{ $name }}</h4>
                                <ul class="list-inline mb-0 d-flex align-items-center flex-wrap justify-content-sm-start justify-content-center gap-2">
                                    <li class="list-inline-item">
                                        <i class='ti ti-briefcase'></i> {{ $jabatan ?: 'Jabatan Belum Diisi' }}
                                    </li>
                                    <li class="list-inline-item">
                                        <i class='ti ti-building'></i> {{ Auth::user()->agency->name ?? 'Perangkat Daerah' }}
                                    </li>
                                    <li class="list-inline-item">
                                        <i class='ti ti-calendar'></i> Joined {{ Auth::user()->created_at->format('F Y') }}
                                    </li>
                                </ul>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <button class="btn btn-primary" onclick="document.getElementById('uploadPhoto').click()">
                                    <i class='ti ti-camera me-1'></i> Ganti Foto
                                </button>
                                <input type="file" id="uploadPhoto" wire:model="photo" class="d-none" accept="image/*">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--/ Header -->

    <!-- Navbar pills -->
    <div class="row">
        <div class="col-md-12">
            <ul class="nav nav-pills flex-column flex-sm-row mb-4" id="profileTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile-content" type="button" role="tab" aria-controls="profile" aria-selected="true"><i class='ti ti-user-check me-1'></i> Profil</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="security-tab" data-bs-toggle="tab" data-bs-target="#security-content" type="button" role="tab" aria-controls="security" aria-selected="false"><i class='ti ti-lock me-1'></i> Keamanan</button>
                </li>
            </ul>
        </div>
    </div>
    <!--/ Navbar pills -->

    <div class="tab-content p-0">
        <!-- Tab 1: Profil & Widgets -->
        <div class="tab-pane fade show active" id="profile-content" role="tabpanel" aria-labelledby="profile-tab">
            <div class="row">
                <!-- Data Diri Form -->
                <div class="col-xl-6 col-lg-6 col-md-6">
                    <div class="card mb-4">
                        <h5 class="card-header">Detail Profil</h5>
                        <div class="card-body">
                            <form wire:submit.prevent="updateProfile">
                                <div class="row">
                                    <div class="mb-3 col-12">
                                        <label class="form-label">Nama Lengkap</label>
                                        <input type="text" class="form-control" wire:model="name" placeholder="John Doe" />
                                        @error('name') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="mb-3 col-12">
                                        <label class="form-label">NIP</label>
                                        <input type="text" class="form-control" wire:model="nip" placeholder="1990..." />
                                        @error('nip') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="mb-3 col-12">
                                        <label class="form-label">Jabatan</label>
                                        <input type="text" class="form-control" wire:model="jabatan" placeholder="Kepala Bidang..." />
                                        @error('jabatan') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="mb-3 col-12">
                                        <label class="form-label">Email</label>
                                        <input type="email" class="form-control" wire:model="email" />
                                        @error('email') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </div>
                                     <div class="mb-3 col-12">
                                        <label class="form-label">Nomor WhatsApp</label>
                                        <input type="text" class="form-control" wire:model="phone" placeholder="08..." />
                                        <div class="form-text">Nomor ini digunakan untuk notifikasi penting.</div>
                                        @error('phone') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="mb-3 col-12">
                                        <label class="form-label">Perangkat Daerah</label>
                                        <input type="text" class="form-control" value="{{ Auth::user()->agency->name ?? '-' }}" readonly disabled />
                                    </div>

                                    <div class="col-12 mt-2">
                                        <button type="submit" class="btn btn-primary me-2">Simpan Perubahan</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Widgets (Right Side) -->
                <div class="col-xl-6 col-lg-6 col-md-6">
                    <!-- User Activity Timeline Widget -->
                    <div class="card card-action mb-4">
                        <div class="card-header align-items-center">
                            <h5 class="card-action-title mb-0">Timeline Aktivitas User</h5>
                        </div>
                        <div class="card-body pb-3">
                             <ul class="timeline ms-1 mb-0">
                                @forelse($this->userActivity as $activity)
                                <li class="timeline-item timeline-item-transparent">
                                    <span class="timeline-point timeline-point-{{ $activity['color'] }}"></span>
                                    <div class="timeline-event">
                                        <div class="timeline-header mb-1">
                                            <h6 class="mb-0">{{ $activity['title'] }}</h6>
                                            <small class="text-muted">{{ $activity['time']->diffForHumans() }}</small>
                                        </div>
                                        <p class="mb-0 text-muted">{{ $activity['description'] }}</p>
                                    </div>
                                </li>
                                @empty
                                <li class="timeline-item timeline-item-transparent">
                                    <span class="timeline-point timeline-point-secondary"></span>
                                    <div class="timeline-event">
                                        <p class="mb-0">Belum ada aktivitas tercatat.</p>
                                    </div>
                                </li>
                                @endforelse

                                <!-- Static Login Item as fallback/example -->
                                <li class="timeline-item timeline-item-transparent">
                                    <span class="timeline-point timeline-point-success"></span>
                                    <div class="timeline-event">
                                        <div class="timeline-header mb-1">
                                            <h6 class="mb-0">Sesi Aktif</h6>
                                            <small class="text-muted">Sekarang</small>
                                        </div>
                                        <p class="mb-0 text-muted">Sedang login di perangkat ini.</p>
                                    </div>
                                </li>
                            </ul>
                            <div class="border-top pt-3 mt-4">
                                <a href="{{ route('pages-profile-activity') }}" class="btn btn-label-primary w-100">Lihat Semua Aktivitas</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab 2: Keamanan -->
        <div class="tab-pane fade" id="security-content" role="tabpanel" aria-labelledby="security-tab">
            <div class="row">
                 <div class="col-12 col-lg-7">
                    <div class="card mb-4" x-data="{
                        currentPass: '',
                        newPass: '',
                        confirmPass: '',
                        showCurrent: false,
                        showNew: false,
                        showConfirm: false,

                        get isStrong() {
                            const p = this.newPass;
                            const hasUpper = /[A-Z]/.test(p);
                            const hasLower = /[a-z]/.test(p);
                            const hasNum = /[0-9]/.test(p);
                            const hasSym = /[!@#$%^&*(),.?:{}|<>]/.test(p);
                            const isLen = p.length >= 8;
                            return hasUpper && hasLower && hasNum && hasSym && isLen;
                        },
                        get isMatch() {
                            return this.newPass === this.confirmPass && this.newPass !== '';
                        }
                    }">
                        <h5 class="card-header">Ganti Password</h5>
                        <div class="card-body">
                            <form wire:submit.prevent="updatePassword">
                                <div class="row">
                                    <div class="mb-3 col-12 col-md-6">
                                        <label class="form-label">Password Saat Ini</label>
                                        <div class="input-group input-group-merge">
                                            <input :type="showCurrent ? 'text' : 'password'" class="form-control" wire:model="current_password" x-model="currentPass" />
                                            <span class="input-group-text cursor-pointer" @click="showCurrent = !showCurrent"><i :class="showCurrent ? 'ti ti-eye' : 'ti ti-eye-off'"></i></span>
                                        </div>
                                        @error('current_password') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="mb-3 col-12 col-md-6">
                                        <label class="form-label">Password Baru</label>
                                        <div class="input-group input-group-merge">
                                            <input :type="showNew ? 'text' : 'password'" class="form-control" wire:model="new_password" x-model="newPass" />
                                            <span class="input-group-text cursor-pointer" @click="showNew = !showNew"><i :class="showNew ? 'ti ti-eye' : 'ti ti-eye-off'"></i></span>
                                        </div>
                                        @error('new_password')
                                            <span class="text-danger small">{{ $message }}</span>
                                        @enderror

                                        <!-- Realtime Validation Message -->
                                        <div class="mt-2" x-show="newPass.length > 0">
                                             <small :class="isStrong ? 'text-success' : 'text-danger'">
                                                <i :class="isStrong ? 'ti ti-check' : 'ti ti-x'"></i>
                                                Harus 8 karakter, huruf besar, huruf kecil, angka, dan simbol.
                                            </small>
                                        </div>
                                    </div>

                                    <div class="mb-3 col-12 col-md-6">
                                        <label class="form-label">Konfirmasi Password Baru</label>
                                        <div class="input-group input-group-merge">
                                            <input :type="showConfirm ? 'text' : 'password'" class="form-control" wire:model="new_password_confirmation" x-model="confirmPass" />
                                            <span class="input-group-text cursor-pointer" @click="showConfirm = !showConfirm"><i :class="showConfirm ? 'ti ti-eye' : 'ti ti-eye-off'"></i></span>
                                        </div>
                                         <!-- Realtime Match Message -->
                                        <div class="mt-2" x-show="confirmPass.length > 0">
                                             <small :class="isMatch ? 'text-success' : 'text-danger'">
                                                <i :class="isMatch ? 'ti ti-check' : 'ti ti-x'"></i>
                                                <span x-text="isMatch ? 'Password cocok.' : 'Password tidak cocok.'"></span>
                                            </small>
                                        </div>
                                    </div>

                                    <div class="col-12 mt-2">
                                        <button type="submit" class="btn btn-warning me-2" :disabled="!isStrong || !isMatch">Ganti Password</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Security Tips Widget -->
                <div class="col-12 col-lg-5">
                    <div class="card mb-4 bg-label-secondary">
                         <div class="card-header pb-2">
                            <h5 class="card-title mb-0"><i class="ti ti-shield-lock me-2"></i>Tips Keamanan</h5>
                         </div>
                         <div class="card-body">
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2">
                                    <i class="ti ti-check text-success me-2"></i> Gunakan minimal 8 karakter.
                                </li>
                                <li class="mb-2">
                                    <i class="ti ti-check text-success me-2"></i> Kombinasikan Huruf Besar (A-Z) dan Kecil (a-z).
                                </li>
                                <li class="mb-2">
                                    <i class="ti ti-check text-success me-2"></i> Gunakan Angka (0-9).
                                </li>
                                <li class="mb-2">
                                    <i class="ti ti-check text-success me-2"></i> Gunakan Simbol (!@#$%).
                                </li>
                                <li class="mb-2">
                                   <i class="ti ti-alert-triangle text-warning me-2"></i> Jangan gunakan password yang sudah pernah dipakai.
                                </li>
                            </ul>
                         </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@script
<script>
    // Toggle Password Visibility Logic could go here or be handled by main.js if generic
</script>
@endscript
