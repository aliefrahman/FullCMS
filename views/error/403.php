<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 Forbidden - FullCMS</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Compiled Tailwind CSS v4 -->
    <link rel="stylesheet" href="<?php echo PUBLIC_URL; ?>/assets/css/style.css">
</head>
<body class="bg-[#fafafa] text-slate-900 min-h-screen flex items-center justify-center p-6 relative overflow-hidden font-sans">
    <!-- Gradient blurs -->
    <div class="absolute top-1/4 left-1/4 -z-10 h-72 w-72 rounded-full bg-amber-200/30 blur-3xl"></div>
    <div class="absolute bottom-1/4 right-1/4 -z-10 h-72 w-72 rounded-full bg-indigo-200/30 blur-3xl"></div>

    <!-- 403 Glass Card -->
    <div class="max-w-md w-full bg-white/70 backdrop-blur-md border border-white/40 rounded-3xl p-8 sm:p-10 shadow-2xl text-center space-y-6">
        <!-- Icon -->
        <div class="h-20 w-20 rounded-2xl bg-gradient-to-tr from-amber-500 to-indigo-600 text-white flex items-center justify-center shadow-lg shadow-amber-500/20 mx-auto animate-bounce">
            <i data-lucide="shield-alert" class="w-10 h-10"></i>
        </div>

        <div class="space-y-2">
            <h1 class="font-display font-extrabold text-7xl bg-gradient-to-r from-amber-600 to-indigo-600 bg-clip-text text-transparent tracking-tighter">
                403
            </h1>
            <h2 class="font-display font-bold text-xl text-slate-800 tracking-tight">
                Access Forbidden
            </h2>
            <p class="text-sm text-slate-500 leading-relaxed">
                You don't have the necessary administrative privileges to view or modify this directory resource.
            </p>
        </div>

        <!-- Back Button -->
        <div class="pt-2">
            <a href="<?php echo PUBLIC_URL; ?>/" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl font-bold bg-gradient-to-r from-amber-600 to-indigo-600 text-white hover:opacity-95 shadow-md shadow-amber-500/10 hover:shadow-amber-500/20 hover:-translate-y-0.5 transition-all duration-200 w-full justify-center">
                <i data-lucide="home" class="w-4 h-4"></i> Back to Home
            </a>
        </div>
    </div>

    <!-- Lucide Icon Renderer -->
    <script src="<?php echo PUBLIC_URL; ?>/assets/js/lucide.min.js"></script>
    <script>
        lucide.createIcons();
    </script>
</body>
</html>