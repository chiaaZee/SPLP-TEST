<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>SPLP | Kabupaten Lumajang</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/img/logo/favicon.png') }}" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-brand-bg text-brand-dark antialiased selection:bg-brand-primary selection:text-white relative"
      x-data="{ scrolled: false, isMenuOpen: false }"
      @scroll.window="scrolled = (window.pageYOffset > 50)">

    <div class="fixed inset-0 z-[-1] overflow-hidden pointer-events-none">
        <div class="absolute top-0 left-[-10%] w-[500px] h-[500px] bg-brand-primary/20 rounded-full mix-blend-multiply filter blur-[100px] opacity-70 animate-blob"></div>

        <div class="absolute top-0 right-[-10%] w-[500px] h-[500px] bg-sky-300/30 rounded-full mix-blend-multiply filter blur-[100px] opacity-70 animate-blob animation-delay-2000"></div>

        <div class="absolute bottom-[-10%] left-[20%] w-[600px] h-[600px] bg-purple-300/30 rounded-full mix-blend-multiply filter blur-[120px] opacity-70 animate-blob animation-delay-4000"></div>
    </div>

    @include('layouts.partials.landing.header')

    <section id="beranda" class="relative overflow-hidden pt-28 pb-12 lg:pt-52 lg:pb-32">

        <div class="max-w-[1400px] mx-auto px-6 lg:px-12 grid lg:grid-cols-2 gap-12 lg:gap-20 items-center">

            <div x-data="animateOnLoad"
                :class="visible ? 'opacity-100 translate-x-0' : 'opacity-0 -translate-x-12'"
                class="transition-all duration-1000 ease-out z-10 relative">

                <div class="flex items-center gap-4 mb-6 lg:mb-10">
                    <span class="text-xs font-bold uppercase tracking-ultra text-brand-steelBlue">Sistem Penghubung Layanan Pemerintah</span>
                    <div class="h-[1px] w-12 bg-brand-mutedBlue"></div>
                </div>

                <h1 class="text-4xl sm:text-5xl md:text-[5rem] lg:text-[6rem] font-black leading-tight lg:leading-[0.9] tracking-tighter mb-6 lg:mb-10 text-brand-dark">
                    SPLP <br> <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-primary to-brand-deepBlue">Lumajang</span>
                </h1>

                <p class="max-w-md text-sm font-bold uppercase tracking-widest leading-loose opacity-60 mb-10 lg:mb-14 text-left">
                    Integrasi layanan digital daerah yang dibangun untuk menciptakan ekosistem pemerintahan yang saling terhubung, aman, dan responsif.
                </p>

                <div class="flex flex-col sm:flex-row items-center gap-4 lg:gap-8">
                    @auth
                        <a href="{{ route('dashboard') }}"
                        class="w-full sm:w-auto group relative inline-flex items-center justify-center gap-3 px-12 py-4 rounded-full text-xs sm:text-sm font-black uppercase tracking-widest text-white transition-all duration-500
                                bg-gradient-to-r from-[#7367F0] via-[#A69EF5] to-[#7367F0] bg-[length:200%_auto]
                                hover:bg-right hover:scale-105 shadow-[0_10px_25px_rgba(115,103,240,0.4)]">
                            <span>Dashboard</span>
                            <svg class="w-5 h-5 transition-transform duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                            </svg>
                        </a>
                    @else
                        <a href="{{ route('register') }}"
                        class="w-full sm:w-auto group relative inline-flex items-center justify-center gap-3 px-12 py-4 rounded-full text-xs sm:text-sm font-black uppercase tracking-widest text-white transition-all duration-500
                                bg-gradient-to-r from-[#7367F0] via-[#A69EF5] to-[#7367F0] bg-[length:200%_auto]
                                hover:bg-right hover:scale-105 shadow-[0_10px_25px_rgba(115,103,240,0.4)]">
                            <span>Mulai berkolaborasi</span>
                            <svg class="w-5 h-5 transition-transform duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                            </svg>
                        </a>
                    @endauth
                </div>
            </div>

            <div class="relative flex items-center justify-center mt-10 lg:mt-0"
                x-data="heroSlider"
                @mouseenter="stopAutoplay()"
                @mouseleave="startAutoplay()">

                <div class="relative w-full max-w-[320px] sm:max-w-[400px] lg:max-w-[500px] aspect-square flex items-center justify-center">

                    <div class="absolute inset-0 bg-radial-gradient from-[#7367F0]/20 to-transparent blur-3xl scale-150 z-0"></div>
                    <div class="absolute inset-[-22%] animate-[spin_60s_linear_infinite] z-1">
                        <div class="w-full h-full rounded-full border border-dashed border-[#7367F0]/30 relative">
                            <div class="absolute top-0 left-1/2 -translate-x-1/2 -translate-y-1/2 w-5 h-5 rounded-full bg-[#9FB4C7] shadow-[0_0_20px_rgba(115,103,240,0.5)]"></div>
                            <div class="absolute top-1/2 right-0 translate-x-1/2 -translate-y-1/2 w-9 h-9 rounded-full bg-[#A69EF5] shadow-sm"></div>
                        </div>
                    </div>
                    <div class="absolute inset-[-8%] animate-[spin_40s_linear_infinite_reverse] z-2">
                        <div class="w-full h-full rounded-full border border-[#A69EF5]/30 relative">
                            <div class="absolute bottom-0 left-1/2 -translate-x-1/2 translate-y-1/2 w-8 h-8 rounded-full bg-[#7367F0] shadow-[0_0_15px_rgba(166,158,245,0.6)]"></div>
                        </div>
                    </div>
                    <div class="absolute inset-[6%] animate-[spin_20s_linear_infinite] z-3">
                        <div class="w-full h-full rounded-full border-2 border-dotted border-[#7367F0]/40 relative">
                            <div class="absolute top-1/2 left-0 -translate-x-1/2 -translate-y-1/2 w-6 h-6 rounded-full bg-[#255B9D] shadow-[0_0_10px_rgba(37,91,157,0.8)]"></div>
                        </div>
                    </div>

                    <div class="relative z-20 flex items-center justify-center">
                        <div class="w-64 h-80 sm:scale-100 scale-90 bg-white/70 rounded-[3rem] shadow-[0_30px_60px_-15px_rgba(115,103,240,0.25)] border border-white/60 backdrop-blur-md flex flex-col p-8 overflow-hidden relative group animate-bounce-slow">
                            <div class="absolute top-0 right-0 w-32 h-32 bg-[#7367F0]/10 rounded-full -mr-16 -mt-16 blur-2xl transition-colors group-hover:bg-[#7367F0]/20 z-0"></div>

                            <div class="flex-1 relative w-full z-10">
                                <template x-for="(slide, index) in slides" :key="index">
                                    <div x-show="active === index"
                                        x-transition:enter="transition ease-out duration-500"
                                        x-transition:enter-start="opacity-0 translate-y-4"
                                        x-transition:enter-end="opacity-100 translate-y-0"
                                        x-transition:leave="transition ease-in duration-300"
                                        x-transition:leave-start="opacity-100 translate-y-0"
                                        x-transition:leave-end="opacity-0 -translate-y-4"
                                        class="absolute inset-0 flex flex-col h-full w-full">
                                        <div class="flex-1">
                                            <div class="w-14 h-14 rounded-2xl flex items-center justify-center mb-6 text-white shadow-lg transition-colors duration-500"
                                                :class="slide.color">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" x-html="slide.icon"></svg>
                                            </div>
                                        </div>
                                        <div class="mt-auto">
                                            <span class="text-[10px] font-black uppercase tracking-widest mb-2 block text-brand-steelBlue" x-text="slide.category"></span>
                                            <h3 class="text-2xl font-black tracking-tighter text-slate-900" x-text="slide.title"></h3>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <div class="mt-6 flex gap-1 relative z-20 shrink-0">
                                <template x-for="(slide, index) in slides" :key="index">
                                    <button @click="goTo(index)" class="h-1 flex-1 rounded-full transition-all duration-500 cursor-pointer" :class="active === index ? 'bg-brand-deepBlue' : 'bg-slate-200 hover:bg-slate-300'"></button>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-12 lg:py-20 relative" x-data="statsSection({{ json_encode($stats) }})">

        <div class="max-w-[1400px] mx-auto px-4 lg:px-12">
            <div class="bg-white/40 backdrop-blur-lg rounded-3xl lg:rounded-[3rem] border border-white/50 p-8 lg:p-20 shadow-sm">

                <div class="grid grid-cols-1 md:grid-cols-3 gap-12 lg:gap-20 text-center">

                    <div class="group flex flex-col items-center">
                        <div class="text-[10px] lg:text-xs font-bold uppercase tracking-[0.2em] mb-2 lg:mb-6 text-brand-primary">Katalog</div>

                        <div class="text-5xl lg:text-7xl font-black tracking-tighter mb-3 lg:mb-4 text-brand-dark drop-shadow-sm"
                            x-text="stats.katalog">
                            -
                        </div>

                        <p class="text-xs lg:text-[10px] font-semibold lg:font-bold uppercase tracking-wider lg:tracking-widest opacity-70 lg:opacity-60 leading-relaxed max-w-[220px] lg:max-w-[250px] mx-auto">
                            Layanan data terintegrasi yang tersedia saat ini.
                        </p>
                    </div>

                    <div class="group flex flex-col items-center">
                        <div class="text-[10px] lg:text-xs font-bold uppercase tracking-[0.2em] mb-2 lg:mb-6 text-brand-primary">Endpoint</div>

                        <div class="text-5xl lg:text-7xl font-black tracking-tighter mb-3 lg:mb-4 text-brand-dark drop-shadow-sm"
                            x-text="stats.endpoint">
                            -
                        </div>

                        <p class="text-xs lg:text-[10px] font-semibold lg:font-bold uppercase tracking-wider lg:tracking-widest opacity-70 lg:opacity-60 leading-relaxed max-w-[220px] lg:max-w-[250px] mx-auto">
                            Titik akses API siap pakai untuk berbagai kebutuhan.
                        </p>
                    </div>

                    <div class="group flex flex-col items-center">
                        <div class="text-[10px] lg:text-xs font-bold uppercase tracking-[0.2em] mb-2 lg:mb-6 text-brand-primary">Total Request</div>

                        <div class="text-5xl lg:text-7xl font-black tracking-tighter mb-3 lg:mb-4 text-brand-dark drop-shadow-sm"
                            x-text="stats.total_request">
                            -
                        </div>

                        <p class="text-xs lg:text-[10px] font-semibold lg:font-bold uppercase tracking-wider lg:tracking-widest opacity-70 lg:opacity-60 leading-relaxed max-w-[220px] lg:max-w-[250px] mx-auto">
                            Transaksi data diproses secara real-time.
                        </p>
                    </div>

                </div>
            </div>
        </div>
    </section>

    <section id="tentang" class="py-16 lg:py-40 relative overflow-hidden">
        <div class="absolute inset-0 w-full h-full pointer-events-none -z-10 [mask-image:linear-gradient(to_bottom,transparent,black_15%,black_85%,transparent)]">
            <div class="absolute inset-0 bg-slate-50"></div>
            <div class="absolute inset-0 bg-[linear-gradient(to_right,#80808012_1px,transparent_1px),linear-gradient(to_bottom,#80808012_1px,transparent_1px)] bg-[size:24px_24px]"></div>
            <div class="absolute top-[20%] right-[0%] w-[20rem] h-[20rem] md:w-[40rem] md:h-[40rem] bg-brand-primary/5 rounded-full blur-3xl"></div>
        </div>

        <div class="max-w-[1400px] mx-auto px-6 lg:px-12 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-24 items-center">

                <div class="lg:col-span-6 relative order-last lg:order-first">
                    <div class="relative w-full aspect-[4/3] bg-white rounded-3xl border border-slate-200 shadow-2xl p-4 md:p-6 overflow-hidden group select-none">

                        <div class="absolute inset-0 bg-gradient-to-br from-slate-50 via-white to-indigo-50/40 z-0"></div>

                        <svg class="absolute inset-0 w-full h-full pointer-events-none z-0" viewBox="0 0 400 300" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <defs>
                                <linearGradient id="flowGradient" x1="0%" y1="0%" x2="100%" y2="0%">
                                    <stop offset="0%" stop-color="#cbd5e1" />
                                    <stop offset="50%" stop-color="#6366f1" />
                                    <stop offset="100%" stop-color="#cbd5e1" />
                                </linearGradient>
                            </defs>

                            <path d="M 60 115 C 100 115, 100 175, 200 175" stroke="#cbd5e1" stroke-width="2" />
                            <circle r="3" fill="#3b82f6">
                                <animateMotion dur="3s" repeatCount="indefinite" path="M 60 115 C 100 115, 100 175, 200 175" keyPoints="0;1" keyTimes="0;1" calcMode="linear" />
                            </circle>

                            <path d="M 60 235 C 100 235, 100 175, 200 175" stroke="#cbd5e1" stroke-width="2" />
                            <circle r="3" fill="#10b981">
                                <animateMotion dur="3s" repeatCount="indefinite" path="M 60 235 C 100 235, 100 175, 200 175" keyPoints="0;1" keyTimes="0;1" calcMode="linear" />
                            </circle>

                            <path d="M 200 175 C 300 175, 300 115, 340 115" stroke="#cbd5e1" stroke-width="2" />
                            <circle r="3" fill="#3b82f6">
                                <animateMotion dur="2.5s" begin="3s" repeatCount="indefinite" path="M 200 175 C 300 175, 300 115, 340 115" keyPoints="0;1" keyTimes="0;1" calcMode="linear" />
                            </circle>

                            <path d="M 200 175 C 300 175, 300 235, 340 235" stroke="#cbd5e1" stroke-width="2" />
                            <circle r="3" fill="#10b981">
                                <animateMotion dur="2.5s" begin="3s" repeatCount="indefinite" path="M 200 175 C 300 175, 300 235, 340 235" keyPoints="0;1" keyTimes="0;1" calcMode="linear" />
                            </circle>

                            <path d="M 200 140 L 200 100" stroke="#fbbf24" stroke-width="2" stroke-dasharray="4 4" class="opacity-60" />
                            <circle r="2.5" fill="#f59e0b">
                                <animateMotion dur="2s" repeatCount="indefinite" path="M 200 140 L 200 100" keyPoints="0;1" keyTimes="0;1" calcMode="linear" />
                            </circle>
                        </svg>


                        <div class="absolute flex flex-col items-center justify-center gap-1 md:gap-2 z-20 group/node cursor-help"
                            style="left: 50%; top: 25%; transform: translate(-50%, -50%);">

                            <!-- Tooltip -->
                            <div class="absolute bottom-full mb-2 opacity-0 group-hover/node:opacity-100 transition-opacity duration-300 bg-slate-800 text-white text-[10px] py-1 px-2 rounded shadow-lg pointer-events-none whitespace-nowrap z-50">
                                Pantauan Sistem Real-time
                                <div class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-slate-800"></div>
                            </div>

                            <span class="text-[9px] md:text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                Monitoring
                            </span>
                            <div class="w-10 h-10 md:w-14 md:h-14 rounded-xl md:rounded-2xl bg-white border-2 border-amber-100 shadow-[0_8px_20px_rgb(251,191,36,0.15)] flex items-center justify-center">
                                <svg class="w-5 h-5 md:w-6 md:h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path></svg>
                            </div>
                        </div>

                        <div class="absolute z-30 group/node cursor-help"
                            style="left: 50%; top: 58%; transform: translate(-50%, -50%);">

                            <!-- Tooltip -->
                            <div class="absolute bottom-full mb-3 opacity-0 group-hover/node:opacity-100 transition-opacity duration-300 bg-slate-800 text-white text-[10px] py-1 px-2 rounded shadow-lg pointer-events-none whitespace-nowrap z-50 left-1/2 -translate-x-1/2">
                                Pusat Integrasi & Validasi Data
                                <div class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-slate-800"></div>
                            </div>

                            <div class="relative group/hub">
                                <div class="absolute inset-0 bg-indigo-500/30 rounded-2xl md:rounded-3xl blur-xl animate-pulse"></div>
                                <div class="w-16 h-16 md:w-24 md:h-24 bg-brand-primary rounded-2xl md:rounded-3xl shadow-2xl shadow-indigo-500/40 flex flex-col items-center justify-center relative transform transition-transform duration-500 hover:scale-105">
                                    <svg class="w-6 h-6 md:w-10 md:h-10 text-white mb-0.5 md:mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"></path></svg>
                                    <span class="text-[8px] md:text-[11px] font-black text-white tracking-tighter leading-none">SPLP HUB</span>
                                </div>
                            </div>
                        </div>

                        <div class="absolute flex flex-col items-center gap-1 md:gap-3 z-20 group/node cursor-help"
                            style="left: 15%; top: 38%; transform: translate(-50%, -50%);">

                            <!-- Tooltip -->
                            <div class="absolute bottom-full mb-2 opacity-0 group-hover/node:opacity-100 transition-opacity duration-300 bg-slate-800 text-white text-[10px] py-1 px-2 rounded shadow-lg pointer-events-none whitespace-nowrap z-50">
                                Sumber Data ASN
                                <div class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-slate-800"></div>
                            </div>

                            <div class="w-12 h-12 md:w-16 md:h-16 rounded-xl md:rounded-2xl bg-white border border-blue-100 shadow-lg flex items-center justify-center relative">
                                <span class="absolute -top-1 -right-1 md:-top-2 md:-right-2 w-2 h-2 md:w-3 md:h-3 border-2 border-white"></span>
                                <svg class="w-5 h-5 md:w-7 md:h-7 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            </div>
                            <span class="text-[9px] md:text-[11px] font-bold text-slate-500 text-center leading-tight">App<br>Kepegawaian</span>
                        </div>

                        <div class="absolute flex flex-col items-center gap-1 md:gap-3 z-20 group/node cursor-help"
                            style="left: 15%; top: 78%; transform: translate(-50%, -50%);">

                            <!-- Tooltip -->
                            <div class="absolute bottom-full mb-2 opacity-0 group-hover/node:opacity-100 transition-opacity duration-300 bg-slate-800 text-white text-[10px] py-1 px-2 rounded shadow-lg pointer-events-none whitespace-nowrap z-50">
                                Sumber Data Perizinan
                                <div class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-slate-800"></div>
                            </div>

                            <div class="w-12 h-12 md:w-16 md:h-16 rounded-xl md:rounded-2xl bg-white border border-emerald-100 shadow-lg flex items-center justify-center relative">
                                <span class="absolute -top-1 -right-1 md:-top-2 md:-right-2 w-2 h-2 md:w-3 md:h-3 border-2 border-white"></span>
                                <svg class="w-5 h-5 md:w-7 md:h-7 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                            </div>
                            <span class="text-[9px] md:text-[11px] font-bold text-slate-500 text-center leading-tight">App<br>Perizinan</span>
                        </div>

                        <div class="absolute flex flex-col items-center gap-1 md:gap-3 z-20 group/node cursor-help"
                            style="left: 85%; top: 37%; transform: translate(-50%, -50%);">

                            <!-- Tooltip -->
                            <div class="absolute bottom-full mb-2 opacity-0 group-hover/node:opacity-100 transition-opacity duration-300 bg-slate-800 text-white text-[10px] py-1 px-2 rounded shadow-lg pointer-events-none whitespace-nowrap z-50">
                                Output: Tampilan Data ASN
                                <div class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-slate-800"></div>
                            </div>

                            <div class="w-12 h-12 md:w-16 md:h-16 rounded-xl md:rounded-2xl bg-white border border-indigo-200 shadow-lg flex items-center justify-center relative">
                                <svg class="w-5 h-5 md:w-7 md:h-7 text-slate-400 group-hover/item:text-indigo-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            </div>
                            <span class="text-[9px] md:text-[11px] font-bold text-slate-500 uppercase tracking-wider">Web SKPD</span>
                        </div>

                        <div class="absolute flex flex-col items-center gap-1 md:gap-3 z-20 group/node cursor-help"
                            style="left: 85%; top: 77%; transform: translate(-50%, -50%);">

                            <!-- Tooltip -->
                            <div class="absolute bottom-full mb-2 opacity-0 group-hover/node:opacity-100 transition-opacity duration-300 bg-slate-800 text-white text-[10px] py-1 px-2 rounded shadow-lg pointer-events-none whitespace-nowrap z-50">
                                Output: Tampilan Status Izin
                                <div class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-slate-800"></div>
                            </div>

                            <div class="w-12 h-12 md:w-16 md:h-16 rounded-xl md:rounded-2xl bg-white border border-indigo-200 shadow-lg flex items-center justify-center relative">
                                <svg class="w-5 h-5 md:w-7 md:h-7 text-slate-400 group-hover/item:text-indigo-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            </div>
                            <span class="text-[9px] md:text-[11px] font-bold text-slate-500 uppercase tracking-wider">Web SKPD</span>
                        </div>

                    </div>
                </div>

                <div class="lg:col-span-6 space-y-6 md:space-y-10 text-left pl-0 lg:pl-10">
                    <div>
                        <span class="inline-block py-1 px-3 rounded-full bg-brand-primary/10 text-brand-primary text-[10px] font-bold uppercase tracking-widest mb-4 md:mb-6 border border-brand-primary/20">
                            Cara Kerja Sistem
                        </span>
                        <h2 class="text-3xl md:text-5xl lg:text-6xl font-black tracking-tighter text-brand-dark leading-[1.1]">
                            Tentang <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-primary to-blue-400">SPLP</span>
                        </h2>
                    </div>

                    <p class="text-base md:text-lg text-slate-600 leading-relaxed font-medium">
                        SPLP Kabupaten Lumajang hadir sebagai infrastruktur strategis untuk mewujudkan interoperabilitas data antar-Perangkat Daerah. Sistem ini memfasilitasi pertukaran data yang aman, terpusat, dan real-time, menghilangkan hambatan perbedaan format sistem antar-Perangkat Daerah demi pelayanan publik yang lebih cepat dan transparan.
                    </p>

                    <div class="space-y-6 pt-2 md:pt-4">
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center shrink-0 text-brand-primary mt-1">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            </div>
                            <div>
                                <h4 class="text-lg md:text-xl font-bold text-brand-dark tracking-tight">Efisiensi Birokrasi</h4>
                                <p class="text-sm text-slate-500 mt-1">Memangkas waktu permohonan data antar instansi dari hari menjadi mili-detik.</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center shrink-0 text-brand-primary mt-1">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                            </div>
                            <div>
                                <h4 class="text-lg md:text-xl font-bold text-brand-dark tracking-tight">Keamanan Terpusat</h4>
                                <p class="text-sm text-slate-500 mt-1">Seluruh lalu lintas data dipantau dan dienkripsi melalui satu pintu gerbang (Gateway).</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <section id="fitur" class="py-20 relative h-[800px] lg:h-[900px] flex items-center justify-center overflow-hidden"
         x-data="featuresSection"
         @mouseenter="stopAutoplay()"
         @mouseleave="startAutoplay()">

        <div class="absolute top-10 md:top-16 left-1/2 -translate-x-1/2 text-center z-20 w-full px-6 pointer-events-none">
            <h2 class="text-3xl md:text-5xl font-black text-slate-900 tracking-tight mb-4">
                Fitur Unggulan <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-primary to-blue-400">SPLP</span>
            </h2>
            <p class="text-slate-500 text-sm md:text-base font-medium max-w-3xl mx-auto leading-relaxed">
                Teknologi di balik integrasi data Lumajang yang aman, cepat, dan <span class="text-blue-600 font-bold">terintegrasi</span>.
            </p>
        </div>

        <div class="lg:hidden absolute top-1/2 -translate-y-1/2 left-0 w-full px-4 flex justify-between z-50 pointer-events-none">
            <div>
                <button @click="prev()"
                        x-show="active > 0"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 -translate-x-2"
                        x-transition:enter-end="opacity-100 translate-x-0"
                        class="pointer-events-auto w-12 h-12 rounded-full bg-white/80 backdrop-blur border border-slate-200 shadow-lg text-slate-600 flex items-center justify-center hover:bg-brand-primary hover:text-white transition-all">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                </button>
            </div>
            <div>
                <button @click="next()"
                        class="pointer-events-auto w-12 h-12 rounded-full bg-white/80 backdrop-blur border border-slate-200 shadow-lg text-slate-600 flex items-center justify-center hover:bg-brand-primary hover:text-white transition-all">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </button>
            </div>
        </div>

        <div class="w-full max-w-[1400px] px-6 lg:px-12 relative h-full flex items-center justify-center lg:justify-between">

            <div class="hidden lg:flex w-1/3 flex-col gap-16 relative z-10 pr-10">
                <template x-for="(slide, index) in slides.slice(0, 2)" :key="slide.id">
                    <div @click="goTo(slide.id)"
                        class="group cursor-pointer text-right relative pl-8 transition-all duration-300"
                        :class="active === slide.id ? 'opacity-100 scale-100' : 'opacity-40 hover:opacity-80 scale-95'">
                        <div class="flex items-center justify-end gap-5 mb-3">
                            <div class="w-14 h-14 rounded-[1.5rem] border flex items-center justify-center transition-all duration-500 shadow-sm"
                                :class="active === slide.id ? 'bg-brand-primary text-white border-brand-primary shadow-brand-primary/30' : 'bg-white border-slate-200 text-slate-400 group-hover:border-brand-primary/50'">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-html="slide.icon"></svg>
                            </div>
                        </div>
                        <h4 class="text-2xl font-black text-slate-800 tracking-tight" x-text="slide.title"></h4>
                        <p class="text-xs font-medium text-slate-500 mt-1 max-w-[200px] ml-auto leading-relaxed" x-text="slide.desc"></p>
                    </div>
                </template>
            </div>

            <div class="relative w-full md:w-[400px] h-[650px] flex items-center justify-center z-20 pointer-events-none perspective-1000">
                <template x-for="(slide, index) in slides" :key="slide.id">
                    <div class="absolute transition-all duration-700 cubic-bezier(0.34, 1.56, 0.64, 1) transform-gpu origin-center cursor-pointer pointer-events-auto"
                        @click="active === index ? isExpanded = !isExpanded : goTo(index)"
                        :class="{
                            'z-50 w-[80vw] md:w-[850px] h-[550px] md:h-[500px] rounded-[3rem] shadow-2xl translate-y-0 scale-100': active === index && isExpanded,

                            'translate-x-0 md:translate-x-6': active === index && isExpanded && index < 2,
                            '-translate-x-0 md:-translate-x-6': active === index && isExpanded && index >= 2,

                            'z-40 w-[80vw] md:w-[340px] h-[480px] rounded-[3rem] shadow-[0_20px_60px_-15px_rgba(0,0,0,0.15)] translate-x-0 translate-y-0 scale-100': active === index && !isExpanded,
                            'z-30 w-[80vw] md:w-[340px] h-[480px] rounded-[3rem] shadow-lg translate-y-[35px] scale-95 opacity-90': (active + 1) % slides.length === index && !isExpanded,
                            'z-20 w-[80vw] md:w-[340px] h-[480px] rounded-[3rem] shadow-md translate-y-[70px] scale-90 opacity-80': (active + 2) % slides.length === index && !isExpanded,
                            'z-10 w-[80vw] md:w-[340px] h-[480px] rounded-[3rem] shadow-sm translate-y-[105px] scale-85 opacity-70': (active + 3) % slides.length === index && !isExpanded,
                            'opacity-0 scale-50 pointer-events-none': (isExpanded && active !== index)
                        }">

                        <div class="w-full h-full overflow-hidden relative transition-all duration-500 group border border-white/50 rounded-[3rem]"
                            :class="[
                                active === index ? 'bg-white' : 'bg-slate-100',
                                isExpanded ? 'ring-1 ring-slate-200' : ''
                            ]">

                            <div class="absolute inset-0 flex flex-col md:flex-row"
                                x-show="isExpanded"
                                x-transition:enter="transition ease-out duration-700 delay-300"
                                x-transition:enter-start="opacity-0 translate-y-4"
                                x-transition:enter-end="opacity-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-100"
                                x-transition:leave-start="opacity-100"
                                x-transition:leave-end="opacity-0">

                                <div class="w-full md:w-2/5 h-2/5 md:h-full relative flex items-center justify-center overflow-hidden" :class="slide.lightColor">
                                    <div class="absolute top-6 left-6 md:top-8 md:left-8 px-4 py-1.5 bg-white/80 backdrop-blur rounded-full text-[10px] font-bold tracking-widest uppercase text-slate-500 shadow-sm" x-text="slide.badge"></div>
                                    <svg class="absolute -bottom-10 -left-10 w-80 h-80 opacity-5 text-current" :class="slide.textColor" fill="currentColor" viewBox="0 0 24 24" x-html="slide.icon"></svg>

                                    <div class="w-20 h-20 md:w-28 md:h-28 rounded-[2rem] text-white shadow-xl flex items-center justify-center transform group-hover:scale-110 transition-transform duration-500"
                                        :class="slide.color">
                                        <svg class="w-10 h-10 md:w-14 md:h-14" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-html="slide.icon"></svg>
                                    </div>
                                </div>

                                <div class="w-full md:w-3/5 h-3/5 md:h-full p-8 md:p-12 flex flex-col justify-center relative bg-white">
                                    <button @click.stop="closeCard()" class="absolute top-4 right-4 md:top-8 md:right-8 p-2 rounded-full hover:bg-slate-100 text-slate-400 hover:text-slate-800 transition-colors z-50">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                    <h2 class="text-2xl md:text-4xl font-black text-slate-800 mb-3 md:mb-5 tracking-tight" x-text="slide.title"></h2>
                                    <div class="w-16 h-1.5 rounded-full mb-4 md:mb-8" :class="slide.color"></div>
                                    <p class="text-slate-600 leading-7 text-sm md:text-[15px]" x-text="slide.details"></p>
                                </div>
                            </div>

                            <div class="absolute inset-0 p-8 flex flex-col items-center justify-between"
                                x-show="!isExpanded"
                                x-transition:enter="transition ease-out duration-500 delay-300"
                                x-transition:enter-start="opacity-0 scale-90"
                                x-transition:enter-end="opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-100"
                                x-transition:leave-start="opacity-100"
                                x-transition:leave-end="opacity-0">

                                <div class="w-full flex justify-between items-center opacity-50" :class="active !== index ? 'opacity-20' : ''">
                                    <div class="flex gap-1.5">
                                        <div class="w-2 h-2 rounded-full bg-slate-300"></div>
                                        <div class="w-2 h-2 rounded-full bg-slate-300"></div>
                                    </div>
                                </div>

                                <div class="relative flex-1 w-full flex items-center justify-center">
                                    <svg class="absolute w-56 h-56 opacity-[0.03] scale-150 transition-transform duration-700"
                                        :class="active === index ? 'text-slate-900' : 'hidden'"
                                        fill="currentColor" viewBox="0 0 24 24" x-html="slide.icon"></svg>

                                    <div class="w-28 h-28 rounded-[2.5rem] shadow-xl flex items-center justify-center text-white relative z-10 transition-all duration-500"
                                        :class="active === index ? 'bg-gradient-to-br from-brand-primary to-blue-600 group-hover:-translate-y-2' : 'bg-slate-200/50 text-slate-300 shadow-none scale-90'">
                                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-html="slide.icon"></svg>
                                    </div>
                                </div>

                                <div class="text-center w-full pb-4">
                                    <h5 class="text-2xl font-black text-slate-800 tracking-tight transition-opacity duration-300"
                                        :class="active === index ? 'opacity-100' : 'opacity-0'"
                                        x-text="slide.title"></h5>
                                    <div class="text-[10px] text-brand-primary font-bold uppercase tracking-widest mt-3 transition-opacity duration-300"
                                        :class="active === index ? 'opacity-100' : 'opacity-0'">
                                        Klik untuk detail
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <div class="hidden lg:flex w-1/3 flex-col gap-16 relative z-10 pl-10">
                <template x-for="(slide, index) in slides.slice(2, 4)" :key="slide.id">
                    <div @click="goTo(slide.id)"
                        class="group cursor-pointer text-left relative pr-8 transition-all duration-300"
                        :class="active === slide.id ? 'opacity-100 scale-100' : 'opacity-40 hover:opacity-80 scale-95'">
                        <div class="flex items-center justify-start gap-5 mb-3">
                            <div class="w-14 h-14 rounded-[1.5rem] border flex items-center justify-center transition-all duration-500 shadow-sm"
                                :class="active === slide.id ? 'bg-brand-primary text-white border-brand-primary shadow-brand-primary/30' : 'bg-white border-slate-200 text-slate-400 group-hover:border-brand-primary/50'">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-html="slide.icon"></svg>
                            </div>
                        </div>
                        <h4 class="text-2xl font-black text-slate-800 tracking-tight" x-text="slide.title"></h4>
                        <p class="text-xs font-medium text-slate-500 mt-1 max-w-[200px] mr-auto leading-relaxed" x-text="slide.desc"></p>
                    </div>
                </template>
            </div>

        </div>
    </section>

    <section id="kontak" class="pb-20">
        <div class="max-w-[1200px] mx-auto px-6 lg:px-12">
            <div class="rounded-[3rem] p-10 lg:p-16 relative overflow-hidden bg-[#090E12] shadow-2xl border border-white/10">

                <div class="absolute top-0 left-0 w-full h-full opacity-30 pointer-events-none">
                    <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-[#7367F0] rounded-full blur-[120px] -mr-64 -mt-64"></div>
                    <div class="absolute bottom-0 left-0 w-[400px] h-[400px] bg-blue-600 rounded-full blur-[100px] -ml-40 -mb-40"></div>
                </div>

                <div class="relative z-10 flex flex-col items-center">

                    <div class="text-center mb-12 max-w-2xl">
                        <h2 class="text-3xl md:text-5xl font-black text-white tracking-tighter mb-4">
                            Hubungi <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#7367F0] to-blue-400">Kami</span>
                        </h2>
                        <p class="text-slate-400 text-sm md:text-base">
                            Silakan hubungi kami melalui saluran berikut untuk pertanyaan, kendala teknis, atau kunjungan langsung ke kantor kami.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 w-full">

                        <a href="mailto:{{ $footer->email ?? 'diskominfo@lumajangkab.go.id' }}" class="group relative p-6 rounded-2xl bg-white/5 border border-white/10 hover:bg-white/10 transition-all duration-300 hover:-translate-y-1">
                            <div class="w-12 h-12 rounded-full bg-[#7367F0]/20 flex items-center justify-center text-[#7367F0] mb-4 group-hover:scale-110 transition-transform">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                            </div>
                            <h3 class="text-lg font-bold text-white mb-2">Email Resmi</h3>
                            <p class="text-xs text-slate-400 leading-relaxed break-words">{{ $footer->email ?? 'diskominfo@lumajangkab.go.id' }}</p>
                            <p class="text-xs text-slate-500 mt-2">{{ $footer->response_time ?? 'Respon dalam 24 jam kerja' }}</p>
                        </a>

                        <a href="tel:{{ $footer->phone ?? '+62334881255' }}" class="group relative p-6 rounded-2xl bg-white/5 border border-white/10 hover:bg-white/10 transition-all duration-300 hover:-translate-y-1">
                            <div class="w-12 h-12 rounded-full bg-emerald-500/20 flex items-center justify-center text-emerald-400 mb-4 group-hover:scale-110 transition-transform">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                            </div>
                            <h3 class="text-lg font-bold text-white mb-2">Telepon / Fax</h3>
                            <p class="text-xs text-slate-400 leading-relaxed">{{ $footer->phone ?? '(0334) 881255' }}</p>
                            <p class="text-xs text-slate-500 mt-2">{{ $footer->work_hours ?? 'Senin - Jumat (08:00 - 15:00)' }}</p>
                        </a>

                        <a href="{{ $footer->google_map ?? 'https://maps.google.com' }}" target="_blank" class="group relative p-6 rounded-2xl bg-white/5 border border-white/10 hover:bg-white/10 transition-all duration-300 hover:-translate-y-1">
                            <div class="w-12 h-12 rounded-full bg-orange-500/20 flex items-center justify-center text-orange-400 mb-4 group-hover:scale-110 transition-transform">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                            </div>
                            <h3 class="text-lg font-bold text-white mb-2">Alamat Kantor</h3>
                            <p class="text-xs text-slate-400 leading-relaxed">{{ $footer->address ?? 'Jl. Alun-Alun Utara No. 7, Lumajang, Jawa Timur' }}</p>
                            <p class="text-xs text-slate-500 mt-2">Lihat di Google Maps &rarr;</p>
                        </a>

                    </div>

                </div>
            </div>
        </div>
    </section>
    @include('layouts.partials.landing.footer')

</body>
</html>
