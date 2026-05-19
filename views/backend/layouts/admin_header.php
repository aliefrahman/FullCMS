<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? 'NexusCMS Admin Panel'; ?></title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Compiled Tailwind CSS v4 -->
    <link rel="stylesheet" href="<?php echo PUBLIC_URL; ?>/assets/css/style.css">

    <!-- Quill CSS -->
    <link href="<?php echo PUBLIC_URL; ?>/assets/quill/dist/quill.snow.css" rel="stylesheet" />

    <!-- Alpine.js -->
    <script defer src="<?php echo PUBLIC_URL; ?>/assets/alpinejs/dist/cdn.min.js"></script>

    <link rel="stylesheet" href="<?php echo PUBLIC_URL; ?>/assets/highlight/styles/atom-one-dark.min.css">

    <style>
        * {
            font-family: var(--font-sans);
        }

        .font-display {
            font-family: var(--font-display);
        }

        .glass {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
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

<body class="bg-[#f8fafc] text-slate-900 min-h-screen flex flex-col md:flex-row relative overflow-x-hidden"
    x-data="{ sidebarOpen: false }">

    <!-- Sidebar navigation -->
    <aside
        class="fixed inset-y-0 left-0 z-50 w-64 bg-slate-900 text-slate-400 transform -translate-x-full md:translate-x-0 transition-transform duration-300 md:static md:shrink-0 flex flex-col justify-between"
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
        <div class="p-6 space-y-8">
            <!-- Brand Logo -->
            <div class="flex items-center gap-3">
                <div
                    class="w-10 h-10 rounded-xl bg-linear-to-tr from-primary-600 to-accent-500 flex items-center justify-center text-white font-display font-extrabold text-lg shadow-md shadow-primary-500/20">
                    N
                </div>
                <span class="text-white font-display text-xl font-bold tracking-tight">Nexus<span
                        class="text-primary-500">CMS</span></span>
            </div>

            <!-- Nav Links -->
            <nav class="space-y-1.5">
                <a href="<?php echo PUBLIC_URL; ?>/admin/dashboard"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 <?php echo ($activePage === 'dashboard') ? 'bg-primary-600 text-white font-semibold shadow-md shadow-primary-500/10' : 'hover:bg-slate-800 hover:text-white'; ?>">
                    <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                    Dashboard
                </a>

                <?php if (\App\Helpers\Auth::hasPermission('create_articles')): ?>
                    <a href="<?php echo PUBLIC_URL; ?>/admin/articles"
                        class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 <?php echo ($activePage === 'articles') ? 'bg-primary-600 text-white font-semibold shadow-md shadow-primary-500/10' : 'hover:bg-slate-800 hover:text-white'; ?>">
                        <i data-lucide="file-text" class="w-5 h-5"></i>
                        Semua Artikel
                    </a>
                <?php endif; ?>

                <?php if (\App\Helpers\Auth::hasPermission('publish_articles')): ?>
                    <a href="<?php echo PUBLIC_URL; ?>/admin/categories"
                        class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 <?php echo ($activePage === 'categories') ? 'bg-primary-600 text-white font-semibold shadow-md shadow-primary-500/10' : 'hover:bg-slate-800 hover:text-white'; ?>">
                        <i data-lucide="folder-tree" class="w-5 h-5"></i>
                        Kategori Artikel
                    </a>
                <?php endif; ?>

                <?php if (\App\Helpers\Auth::hasPermission('publish_articles')): ?>
                    <a href="<?php echo PUBLIC_URL; ?>/admin/tags"
                        class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 <?php echo ($activePage === 'tags') ? 'bg-primary-600 text-white font-semibold shadow-md shadow-primary-500/10' : 'hover:bg-slate-800 hover:text-white'; ?>">
                        <i data-lucide="tag" class="w-5 h-5"></i>
                        Tag Artikel
                    </a>
                <?php endif; ?>

                <?php if (\App\Helpers\Auth::hasPermission('publish_articles')): ?>
                    <a href="<?php echo PUBLIC_URL; ?>/admin/pages"
                        class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 <?php echo ($activePage === 'pages') ? 'bg-primary-600 text-white font-semibold shadow-md shadow-primary-500/10' : 'hover:bg-slate-800 hover:text-white'; ?>">
                        <i data-lucide="layout-grid" class="w-5 h-5"></i>
                        Halaman Statis
                    </a>
                <?php endif; ?>

                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <a href="<?php echo PUBLIC_URL; ?>/admin/comments"
                        class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 <?php echo ($activePage === 'comments') ? 'bg-primary-600 text-white font-semibold shadow-md shadow-primary-500/10' : 'hover:bg-slate-800 hover:text-white'; ?>">
                        <i data-lucide="message-square" class="w-5 h-5"></i>
                        Moderasi Komentar
                    </a>
                <?php endif; ?>

                <a href="<?php echo PUBLIC_URL; ?>/" target="_blank"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-slate-800 hover:text-white transition-all duration-200">
                    <i data-lucide="globe" class="w-5 h-5"></i>
                    Lihat Situs
                </a>
                <div class="h-px bg-slate-800 my-4"></div>
                <?php if (\App\Helpers\Auth::hasPermission('manage_users') || \App\Helpers\Auth::hasPermission('manage_roles')): ?>
                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider px-4 block mb-2">Sistem
                        Informasi</span>
                <?php endif; ?>

                <?php if (\App\Helpers\Auth::hasPermission('manage_users')): ?>
                    <a href="<?php echo PUBLIC_URL; ?>/admin/users"
                        class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 <?php echo ($activePage === 'users') ? 'bg-primary-600 text-white font-semibold shadow-md shadow-primary-500/10' : 'hover:bg-slate-800 hover:text-white'; ?>">
                        <i data-lucide="users" class="w-5 h-5"></i>
                        Manajemen User
                    </a>
                <?php endif; ?>

                <?php if (\App\Helpers\Auth::hasPermission('manage_roles')): ?>
                    <a href="<?php echo PUBLIC_URL; ?>/admin/roles"
                        class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 <?php echo ($activePage === 'roles') ? 'bg-primary-600 text-white font-semibold shadow-md shadow-primary-500/10' : 'hover:bg-slate-800 hover:text-white'; ?>">
                        <i data-lucide="shield" class="w-5 h-5"></i>
                        Hak Akses (Roles)
                    </a>
                <?php endif; ?>
                <a href="<?php echo PUBLIC_URL; ?>/admin/profile"
                    class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 <?php echo ($activePage === 'profile') ? 'bg-primary-600 text-white font-semibold shadow-md shadow-primary-500/10' : 'hover:bg-slate-800 hover:text-white'; ?>">
                    <i data-lucide="user-cog" class="w-5 h-5"></i>
                    Profil Saya
                </a>
            </nav>
        </div>

        <!-- Footer block in sidebar (User Profile) -->
        <div class="p-4 border-t border-slate-800 bg-slate-950/50">
            <div class="flex items-center gap-3 mb-4">
                <?php if (!empty($_SESSION['avatar']) && file_exists(__DIR__ . '/../../../public/uploads/avatars/' . $_SESSION['avatar'])): ?>
                    <img src="<?php echo PUBLIC_URL; ?>/uploads/avatars/<?php echo $_SESSION['avatar']; ?>"
                        class="h-9 w-9 rounded-lg object-cover shadow-inner border border-slate-800">
                <?php else: ?>
                    <div
                        class="h-9 w-9 rounded-lg bg-linear-to-tr from-primary-600 to-accent-500 flex items-center justify-center text-white font-display font-bold text-sm">
                        <?php echo strtoupper(substr($_SESSION['username'] ?? 'AD', 0, 2)); ?>
                    </div>
                <?php endif; ?>
                <div class="overflow-hidden">
                    <h4 class="font-bold text-white text-xs truncate">
                        <?php echo htmlspecialchars($_SESSION['full_name'] ?? $_SESSION['username'] ?? 'Admin'); ?>
                    </h4>
                    <span
                        class="text-[10px] font-bold text-primary-500 uppercase tracking-wider"><?php echo htmlspecialchars($_SESSION['role'] ?? 'admin'); ?></span>
                </div>
            </div>
            <a href="<?php echo PUBLIC_URL; ?>/auth/logout"
                class="w-full py-2.5 rounded-lg border border-slate-800 text-slate-400 hover:bg-rose-500 hover:text-white hover:border-rose-500 text-xs font-bold transition-all duration-200 flex items-center justify-center gap-2 cursor-pointer">
                <i data-lucide="log-out" class="w-4 h-4"></i> Keluar
            </a>
        </div>
    </aside>

    <!-- Overlay sidebar on mobile -->
    <div class="fixed inset-0 z-40 bg-black/40 backdrop-blur-sm md:hidden" x-show="sidebarOpen"
        @click="sidebarOpen = false" x-cloak></div>

    <!-- Main Content Area -->
    <main class="flex-1 flex flex-col overflow-y-auto">
        <!-- Top bar Header -->
        <header
            class="bg-white border-b border-slate-100 px-6 py-4 flex items-center justify-between shadow-sm sticky top-0 z-30">
            <div class="flex items-center gap-4">
                <!-- Burger menu on mobile -->
                <button @click="sidebarOpen = !sidebarOpen"
                    class="p-2 rounded-lg bg-slate-50 border border-slate-100 hover:bg-slate-100 md:hidden">
                    <i data-lucide="menu" class="w-5 h-5 text-slate-600"></i>
                </button>
                <div>
                    <h2 class="font-display font-bold text-xl text-slate-800"><?php echo $title ?? 'Dashboard'; ?></h2>
                    <p class="text-xs text-slate-500 font-medium">NexusCMS Portal Manajemen v1.0.0</p>
                </div>
            </div>

            <!-- Profile status -->
            <div class="flex items-center gap-3">
                <div class="hidden sm:block text-right">
                    <div class="text-xs font-bold text-slate-700">Aktif sebagai


                        <?php echo htmlspecialchars($_SESSION['role'] ?? 'admin'); ?>
                    </div>
                    <div class="text-[10px] font-medium text-emerald-600 flex items-center justify-end gap-1.5">
                        <span class="h-2 w-2 rounded-full bg-emerald-500 animate-pulse"></span> Server Online
                    </div>
                </div>
            </div>
        </header>

        <!-- Page body wrapper -->
        <div class="p-6 md:p-8 space-y-8 max-w-8xl w-full mx-auto">