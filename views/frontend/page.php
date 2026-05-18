<?php
/**
 * @var object $page
 */
require_once __DIR__ . '/layouts/header.php';
?>

<!-- Static Page Header Section -->
<section class="relative pt-16 pb-12 overflow-hidden">
    <!-- Gradient background blurs -->
    <div class="absolute top-0 right-1/4 -z-10 h-72 w-72 rounded-full bg-accent-50/50 blur-3xl"></div>
    <div class="absolute bottom-0 left-1/4 -z-10 h-72 w-72 rounded-full bg-primary-50/50 blur-3xl"></div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-4">
        <!-- Breadcrumb / Back to Home -->
        <a href="<?php echo PUBLIC_URL; ?>/"
            class="inline-flex items-center gap-2 text-xs font-semibold text-slate-500 hover:text-primary-600 transition-colors group">
            <i data-lucide="arrow-left" class="w-4 h-4 group-hover:-translate-x-0.5 transition-transform"></i> Kembali
            ke Beranda
        </a>

        <!-- Page Title -->
        <h1
            class="font-display font-extrabold text-3xl sm:text-4xl lg:text-5xl text-slate-900 tracking-tight leading-tight">
            <?php echo htmlspecialchars($page->title); ?>
        </h1>

        <!-- Last Updated Info -->
        <p class="text-xs font-semibold text-slate-400">
            Terakhir Diperbarui: <?php echo date('d F Y', strtotime($page->updated_at)); ?>
        </p>
    </div>
</section>

<!-- Static Page Content Section -->
<section class="pb-24">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Body Card (Glassmorphic) -->
        <div class="glass rounded-3xl p-8 sm:p-8 shadow-lg relative border border-white/40 space-y-6">
            <div class="ql-container">
                <div class="ql-editor text-slate-700 leading-relaxed text-md sm:text-lg">
                    <?php echo $page->content; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>