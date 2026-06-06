@if(isset($announcements) && $announcements->count() > 0)
    @php
        $banners = $announcements->where('placement', 'banner');
        $modals = $announcements->where('placement', 'modal');
    @endphp

    <!-- Banners -->
    @foreach($banners as $banner)
        <div class="card mb-4 border-0 shadow-sm animate__animated animate__fadeIn position-relative overflow-hidden">

            <!-- Decorative Background Icon (Right) -->
            <!-- Modified: Lower opacity, positioned slightly lower -->
            <div class="position-absolute end-0 p-3 pe-4" style="bottom: -20px; opacity: 0.15;">
                 <i class="ti ti-{{ $banner->type == 'info' ? 'info-circle' : ($banner->type == 'warning' ? 'alert-triangle' : ($banner->type == 'danger' ? 'alert-circle' : 'check')) }}" style="font-size: 8rem;"></i>
            </div>

            <!-- Left Colored Border Strip -->
            <div class="position-absolute start-0 top-0 bottom-0"
                 style="width: 5px; background: {{ $banner->type == 'info' ? '#2196f3' : ($banner->type == 'warning' ? '#ffc107' : ($banner->type == 'danger' ? '#f44336' : '#4caf50')) }};">
            </div>

            <div class="card-body position-relative z-1 d-flex align-items-center justify-content-between p-4 ps-5">
                <div class="d-flex align-items-center">
                    <!-- Modified: Colored Circle with White Icon -->
                    <div class="avatar avatar-md me-4 rounded-circle d-flex align-items-center justify-content-center shadow-sm flex-shrink-0
                         bg-{{ $banner->type == 'info' ? 'info' : ($banner->type == 'warning' ? 'warning' : ($banner->type == 'danger' ? 'danger' : 'success')) }}
                         text-white">
                        <i class="ti ti-{{ $banner->type == 'info' ? 'info-circle' : ($banner->type == 'warning' ? 'alert-triangle' : ($banner->type == 'danger' ? 'alert-circle' : 'check')) }} fs-3"></i>
                    </div>
                    <div>
                        <h5 class="card-title mb-1 fw-bold text-heading">{{ $banner->title }}</h5>
                         <p class="card-text mb-0 text-body opacity-75" style="line-height: 1.5;">{{ $banner->content }}</p>
                    </div>
                </div>
                <button type="button" class="btn-close ms-3" onclick="this.closest('.card').remove()" aria-label="Close"></button>
            </div>
        </div>
    @endforeach

    <!-- Modals -->
    @if($modals->count() > 0)
        @php $modal = $modals->first(); @endphp
        <div class="modal fade" id="announcementModal-{{ $modal->id }}" tabindex="-1">
            <!-- Modal Content (Unchanged Styling) -->
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content overflow-hidden border-0 shadow-lg" style="background: var(--bs-body-bg);">
                    <!-- Header with Gradient -->
                    <div class="modal-header border-0 pb-0 pt-4 d-flex justify-content-center position-relative"
                         style="background: {{ $modal->type == 'info' ? 'linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%)' :
                                   ($modal->type == 'warning' ? 'linear-gradient(135deg, #fff8e1 0%, #ffecb3 100%)' :
                                   ($modal->type == 'danger' ? 'linear-gradient(135deg, #ffebee 0%, #ffcdd2 100%)' :
                                   'linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%)')) }};
                                min-height: 140px;">

                         <div class="avatar avatar-xl rounded-circle shadow-sm d-flex align-items-center justify-content-center position-absolute"
                              style="bottom: -35px; width: 90px; height: 90px; border: 4px solid var(--bs-body-bg); background: var(--bs-body-bg);">
                            <i class="ti ti-{{ $modal->type == 'info' ? 'speakerphone text-info' : ($modal->type == 'warning' ? 'bell-ringing text-warning' : ($modal->type == 'danger' ? 'alert-octagon text-danger' : 'award text-success')) }}" style="font-size: 2.5rem;"></i>
                         </div>

                         <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close" style="z-index: 10;"></button>
                    </div>

                    <div class="modal-body px-4 pb-4 pt-5 text-center mt-3">
                        <span class="badge bg-label-{{ $modal->type == 'info' ? 'info' : ($modal->type == 'warning' ? 'warning' : ($modal->type == 'danger' ? 'danger' : 'success')) }} mb-3 rounded-pill px-3">
                             {{ $modal->start_date ? \Carbon\Carbon::parse($modal->start_date)->isoFormat('D MMMM Y') : 'Pengumuman' }}
                        </span>

                        <h4 class="mb-2 fw-bold text-heading">{{ $modal->title }}</h4>

                        <div class="text-body mb-4 px-2" style="line-height: 1.6; font-size: 1.05rem;">
                            {{ $modal->content }}
                        </div>

                        <div class="d-grid gap-2">
                             <button type="button" class="btn btn-{{ $modal->type == 'info' ? 'info' : ($modal->type == 'warning' ? 'warning' : ($modal->type == 'danger' ? 'danger' : 'success')) }} rounded-pill py-2 fw-bold" data-bs-dismiss="modal">
                                <i class="ti ti-check me-1"></i> Saya Mengerti
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var modalElement = document.getElementById('announcementModal-{{ $modal->id }}');
                if (modalElement) {
                    var myModal = new bootstrap.Modal(modalElement, {
                        backdrop: 'static',
                        keyboard: false
                    });

                    // Logic: Show once per Browser Session (Tiap kali login/buka tab baru)
                    // Added Auth ID to key so different users on same machine see it independently
                    var storageKey = 'announcement-seen-{{ $modal->id }}-{{ Auth::id() }}';

                    if (!sessionStorage.getItem(storageKey)) {
                        myModal.show();

                        modalElement.addEventListener('hidden.bs.modal', function () {
                             sessionStorage.setItem(storageKey, 'true');
                        });
                    }
                }
            });
        </script>
    @endif
@endif
