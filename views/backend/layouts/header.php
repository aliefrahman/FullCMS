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

        < !-- Rich Content CSS Styling -->

        /* Reset some default border and padding from Quill container in read mode */
        .ql-container.ql-bubble {
            border: none !important;
            font-family: inherit !important;
            font-size: inherit !important;
        }

        .ql-editor {
            padding: 0 !important;
            overflow-y: visible !important;
            line-height: 1.650 !important;
            /* Inisialisasi counter di level root agar tidak direset tiap <ol> terpisah */
            counter-reset: list-1 list-2 list-3 !important;
        }

        .ql-editor p {
            margin-bottom: 1rem !important;
        }

        /* Restore Quill list markers — override Tailwind Preflight resets with !important */
        .ql-editor ul,
        .ql-editor ol {
            list-style-type: none !important;
            padding-left: 0 !important;
            margin-top: 0 !important;
            margin-bottom: 0 !important;
        }

        .ql-editor li {
            display: block !important;
            position: relative !important;
            padding-left: 1.5em !important;
            margin-bottom: 0.375rem !important;
        }

        .ql-editor li::before {
            display: inline-block !important;
            white-space: nowrap !important;
            text-align: right !important;
            position: absolute !important;
            left: 0 !important;
            width: 1.2em !important;
            font-weight: 600 !important;
        }

        /* Bullet list — simbol bulat */
        .ql-editor ul li::before {
            content: "\2022" !important;
            color: #4b5563 !important;
        }

        /* Ordered list — counter berurutan 1, 2, 3...
                       counter-increment di sini agar tiap li menaikkan counter,
                       meski setiap item QuillJS dibungkus tag <ol> berbeda */
        .ql-editor ol li {
            counter-increment: list-1 !important;
        }

        .ql-editor ol li::before {
            content: counter(list-1, decimal) ". " !important;
        }

        .ql-editor ol li.ql-indent-1 {
            counter-increment: list-2 !important;
        }

        .ql-editor ol li.ql-indent-1::before {
            content: counter(list-2, lower-alpha) ". " !important;
        }

        .ql-editor ol li.ql-indent-2 {
            counter-increment: list-3 !important;
        }

        .ql-editor ol li.ql-indent-2::before {
            content: counter(list-3, lower-roman) ". " !important;
        }

        /* Padding dan margin untuk ul/ol agar rapi */
        .ql-editor ul {
            padding-left: 1.75rem !important;
            margin-top: 0.75rem !important;
            margin-bottom: 0.75rem !important;
        }

        .ql-editor ol {
            padding-left: 1.25rem !important;
            margin-top: 0.75rem !important;
            margin-bottom: 0.75rem !important;
        }

        /* Baris terakhir dipakai sebagai spacer agar tidak terlalu rapat */
        .ql-editor li {
            display: block !important;
            position: relative !important;
            padding-left: 1.5em !important;
            margin-bottom: 0.375rem !important;
        }

        /* Custom bullets & numbers padding alignment for perfect indentation */
        .ql-editor ul {
            padding-left: 1.75rem !important;
            /* Diperkecil agar rapat dan pas */
            margin-top: 0.75rem !important;
            margin-bottom: 0.75rem !important;
        }

        .ql-editor ol {
            padding-left: 1.25rem !important;
            /* Diperkecil agar rapat dan pas */
            margin-top: 0.75rem !important;
            margin-bottom: 0.75rem !important;
        }

        /* Enhance custom blockquote from Quill with premium glassmorphism */
        .ql-editor blockquote {
            border-left: 4px solid #3b82f6 !important;
            padding-left: 1.25rem !important;
            font-style: italic !important;
            color: #475569 !important;
            margin: 1.5rem 0 !important;
            background-color: rgb(248 250 252 / 0.5) !important;
            border-radius: 0 0.75rem 0.75rem 0 !important;
            padding-top: 0.5rem !important;
            padding-bottom: 0.5rem !important;
        }

        /* Headings style mapping */
        .ql-editor h1,
        .ql-editor h2,
        .ql-editor h3,
        .ql-editor h4 {
            color: #0f172a !important;
            font-family: inherit !important;
            font-weight: 800 !important;
            line-height: 1.25 !important;
            display: block !important;
        }

        .ql-editor h1 {
            font-size: 1.75rem !important;
            margin-top: 2rem !important;
            margin-bottom: 1rem !important;
        }

        .ql-editor h2 {
            font-size: 1.4rem !important;
            margin-top: 1.75rem !important;
            margin-bottom: 0.75rem !important;
        }

        .ql-editor h3 {
            font-size: 1.2rem !important;
            margin-top: 1.5rem !important;
            margin-bottom: 0.5rem !important;
        }

        /* Inline links */
        .ql-editor a {
            color: #2563eb !important;
            text-decoration: underline !important;
            font-weight: 600 !important;
        }

        /* Code and pre tags styling match */
        .ql-editor code {
            background-color: #f1f5f9 !important;
            color: #0f172a !important;
            padding: 0.2rem 0.4rem !important;
            border-radius: 0.375rem !important;
            font-family: monospace !important;
            font-size: 0.85em !important;
        }

        .ql-editor pre {
            background-color: #0f172a !important;
            color: #f8fafc !important;
            padding: 1.25rem !important;
            border-radius: 1rem !important;
            overflow-x: auto !important;
            margin: 1.5rem 0 !important;
            font-family: monospace !important;
            font-size: 0.875rem !important;
        }

        /* Premium image styling */
        .ql-editor img {
            border-radius: 1rem !important;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1) !important;
            margin: 1.5rem auto !important;
            max-width: 100% !important;
            height: auto !important;
            display: block !important;
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