@extends('layouts/layoutMaster')


@section('title', 'API Control Center')

@section('page-style')
<style>
    /* Animated Gradient Background for Hero */
    .hero-animated {
        background: linear-gradient(-45deg, #7367f0, #ce9ffc, #7367f0, #9e95f5);
        background-size: 400% 400%;
        animation: gradientMove 12s ease infinite;
        border: none;
        box-shadow: 0 8px 32px rgba(115, 103, 240, 0.25);
    }
    @keyframes gradientMove {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    /* Modern Glassmorphism Card */
    .glass-card {
        background: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.6);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        transition: transform 0.3s cubic-bezier(0.25, 0.8, 0.25, 1), box-shadow 0.3s ease;
        border-radius: 12px;
    }
    .dark-style .glass-card {
        background: rgba(47, 51, 73, 0.6);
        border: 1px solid rgba(255, 255, 255, 0.05);
    }

    /* Hover Lift Effect */
    .glass-card:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        z-index: 10;
        border-color: #7367f0;
    }

    /* Staggered Entrance Animation */
    .stagger-enter {
        opacity: 0;
        animation: fadeInUp 0.5s ease-out forwards;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
@endsection

@section('page-script')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('rollingCounter', (targetValue, duration = 1500) => ({
            current: 0,
            target: targetValue,
            time: duration,
            init() {
                // Should animate even if 0 to ensure consistency,
                // but efficiently handling no-change scenario
                if (this.target === 0) {
                    this.current = 0;
                    return;
                }

                let start = 0;
                let end = this.target;
                let startTime = null;

                const animate = (currentTime) => {
                    if (!startTime) startTime = currentTime;
                    const progress = Math.min((currentTime - startTime) / this.time, 1);
                    // EaseOutExpo effect
                    const ease = progress === 1 ? 1 : 1 - Math.pow(2, -10 * progress);

                    this.current = Math.floor(ease * (end - start) + start);

                    if (progress < 1) {
                        requestAnimationFrame(animate);
                    } else {
                        this.current = this.target;
                    }
                };
                requestAnimationFrame(animate);
            }
        }));
    });
</script>
@endsection

@section('content')
<!-- Hero Section -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card text-white h-100 overflow-hidden"
            style="background: linear-gradient(120deg, #7367f0 0%, #9e95f5 100%); border: none; box-shadow: 0 4px 15px rgba(115, 103, 240, 0.4);">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h3 class="text-white fw-bold mb-1">API Control Center</h3>
                        <p class="text-white opacity-75 mb-0">
                            @if(auth()->user()->hasRole('admin'))
                            Monitoring performa global dan kesehatan layanan sistem.
                            @else
                            Monitoring penggunaan dan performa layanan API Anda.
                            @endif
                        </p>
                    </div>
                    <i class="ti ti-server-cog text-white opacity-25" style="font-size: 5rem;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Service Grid -->
@livewire('user.api-log-list')
@endsection
