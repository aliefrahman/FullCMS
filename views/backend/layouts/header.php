<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="FullCMS - A premium custom-built Content Management System for modern high-performance web experiences.">
    <title>CMS Admin - Authentication</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap"
        rel="stylesheet">

    <!-- Compiled Tailwind CSS v4 -->
    <link rel="stylesheet" href="<?php echo PUBLIC_URL; ?>/assets/css/style.css">

    <!-- Quill CSS -->
    <link href="<?php echo PUBLIC_URL; ?>/assets/quill/dist/quill.snow.css" rel="stylesheet" />

    <!-- Alpine.js -->
    <script defer src="<?php echo PUBLIC_URL; ?>/assets/alpinejs/dist/cdn.min.js"></script>

    <style>
        * {
            font-family: var(--font-sans);
        }

        .font-display {
            font-family: var(--font-display);
        }

        .bg-primary-gradient {
            background: linear-gradient(135deg, #0c4a6e 0%, #0369a1 25%, #0ea5e9 50%, #4f46e5 100%);
        }

        .bg-primary-gradient-alt {
            background: linear-gradient(135deg, #0369a1 0%, #0ea5e9 50%, #6366f1 100%);
        }

        .text-primary-gradient {
            background: linear-gradient(135deg, #0ea5e9 0%, #4f46e5 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--color-primary-600) 0%, var(--color-accent-600) 100%);
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--color-primary-700) 0%, var(--color-accent-600) 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px -5px rgba(14, 165, 233, 0.4), 0 4px 10px -3px rgba(79, 70, 229, 0.3);
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
        }

        .floating-shape {
            position: absolute;
            border-radius: 50%;
            opacity: 0.1;
            animation: float 20s ease-in-out infinite;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px) rotate(0deg);
            }

            25% {
                transform: translateY(-20px) rotate(5deg);
            }

            50% {
                transform: translateY(0px) rotate(0deg);
            }

            75% {
                transform: translateY(20px) rotate(-5deg);
            }
        }

        @keyframes pulse-slow {

            0%,
            100% {
                opacity: 0.4;
                transform: scale(1);
            }

            50% {
                opacity: 0.6;
                transform: scale(1.05);
            }
        }

        .animate-pulse-slow {
            animation: pulse-slow 4s ease-in-out infinite;
        }

        @keyframes slide-up {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-slide-up {
            animation: slide-up 0.6s ease-out forwards;
        }

        @keyframes fade-in {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .animate-fade-in {
            animation: fade-in 0.4s ease-out forwards;
        }

        .input-focus-ring:focus-within {
            box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.15), 0 0 0 1px rgba(14, 165, 233, 0.3);
            border-color: var(--color-primary-500);
        }

        .social-btn {
            transition: all 0.2s ease;
            border: 1.5px solid #e5e7eb;
        }

        .social-btn:hover {
            border-color: var(--color-primary-500);
            background: var(--color-primary-50);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .checklist-item {
            animation: slide-up 0.5s ease-out forwards;
            opacity: 0;
        }

        .checklist-item:nth-child(1) {
            animation-delay: 0.1s;
        }

        .checklist-item:nth-child(2) {
            animation-delay: 0.2s;
        }

        .checklist-item:nth-child(3) {
            animation-delay: 0.3s;
        }

        .checklist-item:nth-child(4) {
            animation-delay: 0.4s;
        }

        .grid-pattern {
            background-image: radial-gradient(rgba(255, 255, 255, 0.15) 1px, transparent 1px);
            background-size: 24px 24px;
        }

        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="min-h-screen bg-gray-50 flex flex-col lg:flex-row">
    <!-- Left Panel - Branding (Hidden on mobile) -->
    <div class="hidden lg:flex lg:w-1/2 xl:w-3/5 relative overflow-hidden bg-primary-gradient">
        <!-- Grid Pattern Overlay -->
        <div class="absolute inset-0 grid-pattern"></div>

        <!-- Floating Shapes -->
        <div class="floating-shape w-96 h-96 bg-primary-500 -top-20 -left-20" style="animation-delay: -5s;"></div>
        <div class="floating-shape w-64 h-64 bg-accent-500 top-1/3 right-10" style="animation-delay: -10s;"></div>
        <div class="floating-shape w-48 h-48 bg-white bottom-20 left-1/4" style="animation-delay: -15s;"></div>
        <div class="floating-shape w-80 h-80 bg-primary-100 bottom-10 right-1/3" style="animation-delay: -8s;"></div>
        <div class="floating-shape w-32 h-32 bg-accent-500 top-10 right-1/4 animate-pulse-slow"></div>

        <!-- Content -->
        <div class="relative z-10 flex flex-col justify-between p-12 xl:p-16 w-full">
            <!-- Logo -->
            <div class="animate-slide-up">
                <div class="flex items-center gap-3 mb-8">
                    <div class="w-12 h-12 rounded-xl bg-white/20 backdrop-blur-sm flex items-center justify-center">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                        </svg>
                    </div>
                    <span class="text-white font-display text-2xl font-bold tracking-tight">NexusCMS</span>
                </div>
            </div>

            <!-- Main Content -->
            <div class="flex-1 flex flex-col justify-center max-w-xl animate-slide-up" style="animation-delay: 0.2s;">
                <h1 class="font-display text-4xl xl:text-5xl font-bold text-white leading-tight mb-6">
                    Kelola Konten<br />
                    <span class="text-primary-100">Dengan Mudah</span>
                </h1>
                <p class="text-lg text-white/80 mb-10 leading-relaxed">
                    Platform manajemen konten modern yang membantu Anda membuat, mengelola, dan mempublikasikan konten
                    dengan efisiensi maksimal.
                </p>

                <!-- Feature List -->
                <div class="space-y-4 mb-10">
                    <div class="checklist-item flex items-center gap-3">
                        <div
                            class="w-8 h-8 rounded-lg bg-white/20 backdrop-blur-sm flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <span class="text-white/90 font-medium">Editor konten visual dengan drag & drop</span>
                    </div>
                    <div class="checklist-item flex items-center gap-3">
                        <div
                            class="w-8 h-8 rounded-lg bg-white/20 backdrop-blur-sm flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <span class="text-white/90 font-medium">Manajemen media terintegrasi</span>
                    </div>
                    <div class="checklist-item flex items-center gap-3">
                        <div
                            class="w-8 h-8 rounded-lg bg-white/20 backdrop-blur-sm flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <span class="text-white/90 font-medium">Sistem role & permission yang fleksibel</span>
                    </div>
                    <div class="checklist-item flex items-center gap-3">
                        <div
                            class="w-8 h-8 rounded-lg bg-white/20 backdrop-blur-sm flex items-center justify-center shrink-0">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <span class="text-white/90 font-medium">Analitik real-time & laporan</span>
                    </div>
                </div>
            </div>

            <!-- Stats -->
            <div class="flex gap-8 animate-slide-up" style="animation-delay: 0.4s;">
                <div>
                    <p class="font-display text-3xl font-bold text-white">10K+</p>
                    <p class="text-sm text-white/70 mt-1">Pengguna Aktif</p>
                </div>
                <div class="w-px bg-white/20"></div>
                <div>
                    <p class="font-display text-3xl font-bold text-white">50K+</p>
                    <p class="text-sm text-white/70 mt-1">Konten Dibuat</p>
                </div>
                <div class="w-px bg-white/20"></div>
                <div>
                    <p class="font-display text-3xl font-bold text-white">99.9%</p>
                    <p class="text-sm text-white/70 mt-1">Uptime</p>
                </div>
            </div>
        </div>

        <!-- Decorative Elements -->
        <div class="absolute bottom-0 right-0 w-64 h-64 bg-accent-500/20 rounded-full blur-3xl"></div>
        <div class="absolute top-0 left-1/3 w-48 h-48 bg-primary-500/20 rounded-full blur-3xl"></div>
    </div>