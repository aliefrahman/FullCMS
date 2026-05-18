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
        <div class="p-6 md:p-8 space-y-8 max-w-7xl w-full mx-auto">