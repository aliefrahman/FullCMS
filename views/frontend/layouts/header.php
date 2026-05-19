<?php
use App\Helpers\Session;
use App\Models\Category;

$isLoggedIn = Session::has('user_id');
$role = Session::get('role');
$isStaff = $isLoggedIn && in_array($role, ['admin', 'editor', 'author']);

// Load only categories with published articles dynamically
$navCategoryModel = new Category();
$navCategories = $navCategoryModel->getNotEmptyPublished();
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description"
        content="FullCMS - A premium custom-built Content Management System for modern high-performance web experiences.">
    <title><?php echo isset($title) ? $title . ' - FullCMS' : 'FullCMS - Premium Content Management System'; ?></title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap"
        rel="stylesheet">

    <!-- Lucide Icons loaded in footer -->

    <!-- Compiled Tailwind CSS v4 -->
    <link rel="stylesheet" href="<?php echo PUBLIC_URL; ?>/assets/css/style.css">

    <!-- Alpine.js -->
    <script defer src="<?php echo PUBLIC_URL; ?>/assets/alpinejs/dist/cdn.min.js"></script>

    <link rel="stylesheet" href="<?php echo PUBLIC_URL; ?>/assets/quill/dist/quill.bubble.css">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .font-display {
            font-family: 'Outfit', sans-serif;
        }

        /* Glassmorphism custom styling */
        .glass {
            background: rgba(255, 255, 255, 0.45);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.25);
        }

        .dark-glass {
            background: rgba(15, 23, 42, 0.65);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
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

<body
    class="bg-[#fafafa] text-slate-900 antialiased selection:bg-primary-500 selection:text-white flex flex-col min-h-screen">
    <!-- Sticky Glassmorphic Navbar -->
    <header x-data="{ mobileMenuOpen: false, isScrolled: false }"
        @scroll.window="isScrolled = (window.pageYOffset > 10) ? true : false"
        :class="isScrolled ? 'bg-white/80 backdrop-blur-md shadow-sm border-slate-100/50' : 'bg-transparent border-transparent'"
        class="sticky top-0 z-50 transition-all duration-300 border-b">
        <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <!-- Logo -->
            <a href="<?php echo PUBLIC_URL; ?>/" class="flex items-center gap-2 group">
                <div
                    class="h-10 w-10 rounded-xl bg-linear-to-tr from-primary-600 to-accent-500 flex items-center justify-center text-white font-display font-extrabold text-xl shadow-md shadow-primary-500/20 group-hover:scale-105 transition-transform duration-300">
                    H
                </div>
                <span
                    class="font-display font-extrabold text-2xl tracking-tight bg-linear-to-r from-slate-900 via-primary-600 to-accent-600 bg-clip-text text-transparent">
                    Home<span class="text-slate-900">LabX</span>
                </span>
            </a>

            <!-- Desktop Menu -->
            <div class="hidden md:flex items-center gap-8">
                <!-- Dynamic Horizontal Categories Menu -->
                <?php foreach ($navCategories as $cat): ?>
                    <a href="<?php echo PUBLIC_URL; ?>/category?slug=<?php echo e($cat->slug); ?>"
                        class="font-medium text-slate-650 hover:text-primary-600 transition-colors duration-200 relative group py-2">
                        <?php echo e($cat->name); ?>
                        <span
                            class="absolute bottom-0 left-0 w-0 h-0.5 bg-primary-600 transition-all duration-300 group-hover:w-full"></span>
                    </a>
                <?php endforeach; ?>


                <a href="#"
                    class="font-medium text-slate-600 hover:text-primary-600 transition-colors duration-200 relative group py-2">
                    Contact
                    <span
                        class="absolute bottom-0 left-0 w-0 h-0.5 bg-primary-600 transition-all duration-300 group-hover:w-full"></span>
                </a>
            </div>

            <!-- Header Action Button -->
            <div class="hidden md:flex items-center gap-4">
                <?php if ($isLoggedIn): ?>
                    <?php if ($isStaff): ?>
                        <a href="<?php echo PUBLIC_URL; ?>/admin/dashboard"
                            class="px-5 py-2.5 rounded-xl text-sm font-semibold bg-linear-to-r from-primary-600 to-accent-600 text-white hover:opacity-95 shadow-md shadow-primary-500/10 hover:shadow-primary-500/20 hover:-translate-y-0.5 transition-all duration-200">
                            Dashboard Admin
                        </a>
                    <?php else: ?>
                        <span class="text-sm font-semibold text-slate-700">Halo,
                            <?php echo e(Session::get('username')); ?>!</span>
                    <?php endif; ?>
                    <a href="<?php echo PUBLIC_URL; ?>/auth/logout"
                        class="bg-rose-50 px-5 py-2.5 rounded-xl text-sm font-semibold text-rose-500 hover:bg-rose-100 transition-all duration-200">
                        Log Out
                    </a>
                <?php else: ?>
                    <a href="<?php echo PUBLIC_URL; ?>/auth"
                        class="bg-primary-500 px-5 py-2.5 rounded-xl text-sm font-semibold text-white hover:text-slate-100 hover:bg-linear-to-tr from-primary-600 to-accent-500 transition-all duration-200">
                        Sign In
                    </a>

                <?php endif; ?>
            </div>

            <!-- Mobile Menu Toggle -->
            <button @click="mobileMenuOpen = !mobileMenuOpen"
                class="md:hidden p-2 rounded-lg text-slate-600 hover:bg-slate-100 transition-colors flex items-center justify-center">
                <i x-show="mobileMenuOpen" data-lucide="x" class="w-6 h-6"></i>
                <i x-show="!mobileMenuOpen" data-lucide="menu" class="w-6 h-6"></i>
            </button>
        </nav>

        <!-- Mobile Menu -->
        <div x-show="mobileMenuOpen" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-4"
            class="md:hidden absolute top-20 left-0 w-full bg-white border-b border-slate-100 px-4 py-6 space-y-4 shadow-lg overflow-y-auto max-h-[85vh]">

            <!-- Dynamic Mobile Categories Menu -->
            <?php foreach ($navCategories as $cat): ?>
                <a href="<?php echo PUBLIC_URL; ?>/category?slug=<?php echo e($cat->slug); ?>"
                    class="block font-semibold text-slate-750 hover:text-primary-600 transition-colors"><?php echo e($cat->name); ?></a>
            <?php endforeach; ?>

            <a href="#" class="block font-semibold text-slate-700 hover:text-primary-600 transition-colors">Contact</a>
            <div class="h-px bg-slate-100 w-full my-4"></div>
            <div class="flex flex-col gap-3">
                <?php if ($isLoggedIn): ?>
                    <?php if ($isStaff): ?>
                        <a href="<?php echo PUBLIC_URL; ?>/admin/dashboard"
                            class="w-full text-center py-3 rounded-xl text-sm font-semibold bg-linear-to-r from-primary-600 to-accent-600 text-white shadow-md">Dashboard
                            Admin</a>
                    <?php else: ?>
                        <div class="text-center text-sm font-semibold text-slate-700">Halo,
                            <?php echo e(Session::get('username')); ?>!
                        </div>
                    <?php endif; ?>
                    <a href="<?php echo PUBLIC_URL; ?>/auth/logout"
                        class="w-full text-center py-3 rounded-xl text-sm font-semibold text-rose-600 hover:bg-rose-50 transition-colors">Log
                        Out</a>
                <?php else: ?>
                    <a href="<?php echo PUBLIC_URL; ?>/auth"
                        class="w-full text-center py-3 rounded-xl text-sm font-semibold text-slate-700 hover:bg-slate-100 transition-colors">Sign
                        In</a>
                <?php endif; ?>
            </div>
        </div>
    </header>
    <main class="grow">