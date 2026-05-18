<?php
/**
 * @var object $article
 * @var array $tags
 * @var array $recentArticles
 */
require_once __DIR__ . '/layouts/header.php';

// Generate author initials dynamically
$initials = '';
$parts = explode(' ', $article->author_name ?? 'Unknown');
foreach (array_slice($parts, 0, 2) as $p) {
    $initials .= strtoupper(substr($p, 0, 1));
}
?>

<!-- Article Banner/Header Section -->
<section class="relative pt-12 pb-16 overflow-hidden">
    <!-- Gradient background blurs -->
    <div class="absolute top-0 right-1/4 -z-10 h-72 w-72 rounded-full bg-accent-50/50 blur-3xl"></div>
    <div class="absolute bottom-0 left-1/4 -z-10 h-72 w-72 rounded-full bg-primary-50/50 blur-3xl"></div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-6">
        <!-- Back Link -->
        <a href="<?php echo PUBLIC_URL; ?>/"
            class="inline-flex items-center gap-2 text-xs font-semibold text-slate-500 hover:text-primary-600 transition-colors group">
            <i data-lucide="arrow-left" class="w-4 h-4 group-hover:-translate-x-0.5 transition-transform"></i> Kembali
            ke Beranda
        </a>

        <!-- Article Title -->
        <h1
            class="font-display font-extrabold text-3xl sm:text-4xl lg:text-5xl text-slate-900 tracking-tight leading-tight">
            <?php echo htmlspecialchars($article->title); ?>
        </h1>

        <!-- Author / Date Metadata bar -->
        <div class="flex items-center justify-center gap-6 text-sm text-slate-500 pt-2">
            <div class="flex items-center gap-2">
                <div
                    class="h-8 w-8 rounded-full bg-linear-to-tr from-primary-600 to-accent-500 flex items-center justify-center text-white font-display font-bold text-xs">
                    <?php echo $initials; ?>
                </div>
                <span
                    class="font-semibold text-slate-700"><?php echo htmlspecialchars($article->author_name ?? 'Unknown'); ?></span>
            </div>
            <div class="h-4 w-px bg-slate-200"></div>
            <div class="flex items-center gap-1.5">
                <i data-lucide="calendar" class="w-4 h-4 text-slate-400"></i>
                <span><?php echo date('d M Y, H:i', strtotime($article->published_at ?? $article->created_at)); ?></span>
            </div>
        </div>
        <!-- Category Badge -->
        <div
            class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-primary-50 border border-primary-100 text-xs font-bold text-primary-600 shadow-sm mx-auto uppercase tracking-wide">
            <?php echo htmlspecialchars($article->category_name ?? 'Umum'); ?>
        </div>
    </div>
</section>

<!-- Article Content Section -->
<section class="pb-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">

            <!-- Main Content (Left) -->
            <div class="lg:col-span-8 space-y-8">

                <!-- Featured Image -->
                <div
                    class="rounded-3xl overflow-hidden shadow-xl aspect-video bg-slate-900 flex items-center justify-center text-center relative border border-slate-100">
                    <?php if ($article->featured_image): ?>
                        <img src="<?php echo PUBLIC_URL; ?>/uploads/articles/<?php echo htmlspecialchars($article->featured_image); ?>"
                            class="w-full h-full object-cover" alt="<?php echo htmlspecialchars($article->title); ?>" />
                    <?php else: ?>
                        <div class="space-y-4 text-slate-400 p-8">
                            <div
                                class="h-16 w-16 rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 flex items-center justify-center mx-auto shadow-lg shadow-black/10">
                                <i data-lucide="code" class="w-8 h-8 text-white"></i>
                            </div>
                            <h3 class="font-display font-extrabold text-2xl text-white max-w-lg mx-auto leading-tight">
                                <?php echo htmlspecialchars($article->title); ?>
                            </h3>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if ($article->featured_image && !empty($article->featured_image_caption)): ?>
                    <p class="text-xs text-center text-slate-500 font-semibold italic mt-2.5 px-4 tracking-wide">
                        📷 <?php echo htmlspecialchars($article->featured_image_caption); ?>
                    </p>
                <?php endif; ?>

                <!-- Article Body Card (Glassmorphic) -->
                <div class="glass rounded-3xl p-6 sm:p-6 shadow-lg relative border border-white/40 space-y-6">
                    <div class="ql-container">
                        <div class="ql-editor text-slate-700 leading-relaxed text-md sm:text-lg">
                            <?php echo $article->content; ?>
                        </div>
                    </div>

                    <!-- Tags list under content -->
                    <?php if (!empty($tags)): ?>
                        <div class="pt-6 border-t border-slate-150/50">
                            <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">Tag Terkait:</h4>
                            <div class="flex flex-wrap gap-2">
                                <?php foreach ($tags as $t): ?>
                                    <span
                                        class="px-3 py-1.5 rounded-xl bg-white border border-slate-200 text-xs font-bold text-slate-600 hover:text-primary-600 hover:border-primary-200 transition-colors select-none">
                                        #<?php echo htmlspecialchars($t->name); ?>
                                    </span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Comments Section -->
                <div class="space-y-6 pt-6">
                    <h3 class="font-display font-extrabold text-xl text-slate-900 flex items-center gap-2">
                        <i data-lucide="message-square" class="w-5 h-5 text-primary-500"></i>
                        Diskusi & Komentar (<?php echo count($comments ?? []); ?>)
                    </h3>

                    <!-- Comments Alerts (Flash Session) -->
                    <?php 
                    $commentSuccess = \App\Helpers\Session::flash('success');
                    $commentError = \App\Helpers\Session::flash('error');
                    ?>
                    <?php if ($commentSuccess): ?>
                        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-100 text-emerald-600 text-xs font-semibold flex items-center gap-2.5 shadow-sm animate-fade-in">
                            <i data-lucide="check-circle" class="w-4 h-4 text-emerald-500 shrink-0"></i>
                            <span><?php echo htmlspecialchars($commentSuccess); ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if ($commentError): ?>
                        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-100 text-rose-650 text-xs font-semibold flex items-center gap-2.5 shadow-sm animate-fade-in">
                            <i data-lucide="alert-circle" class="w-4 h-4 text-rose-500 shrink-0 animate-bounce"></i>
                            <span><?php echo htmlspecialchars($commentError); ?></span>
                        </div>
                    <?php endif; ?>

                    <!-- Comments List -->
                    <div class="space-y-4">
                        <?php if (empty($comments)): ?>
                            <div class="glass rounded-3xl p-8 text-center border border-white/40 space-y-3 bg-linear-to-tr from-slate-50/50 to-white/50">
                                <div class="w-12 h-12 bg-slate-100 text-slate-400 rounded-2xl flex items-center justify-center mx-auto shadow-inner">
                                    <i data-lucide="message-circle" class="w-6 h-6"></i>
                                </div>
                                <div class="space-y-1">
                                    <h4 class="font-bold text-slate-700 text-sm">Belum Ada Komentar</h4>
                                    <p class="text-xs text-slate-400 max-w-xs mx-auto">Jadilah yang pertama untuk membagikan pendapat Anda mengenai artikel ini!</p>
                                </div>
                            </div>
                        <?php else: ?>
                            <?php foreach ($comments as $c): ?>
                                <?php
                                // Generate initials dynamically
                                $cInitials = '';
                                $cParts = explode(' ', $c->user_full_name ?? $c->author_name ?? 'Anonymous');
                                foreach (array_slice($cParts, 0, 2) as $p) {
                                    $cInitials .= strtoupper(substr($p, 0, 1));
                                }
                                
                                // Determine role badge styling
                                $roleBadge = '';
                                if ($c->user_role === 'admin') {
                                    $roleBadge = '<span class="px-2 py-0.5 rounded-md bg-rose-50 border border-rose-100 text-[10px] font-bold text-rose-650 uppercase tracking-wide">Administrator</span>';
                                } elseif ($c->user_role === 'editor') {
                                    $roleBadge = '<span class="px-2 py-0.5 rounded-md bg-indigo-50 border border-indigo-100 text-[10px] font-bold text-indigo-650 uppercase tracking-wide">Editor</span>';
                                } elseif ($c->user_role === 'author') {
                                    $roleBadge = '<span class="px-2 py-0.5 rounded-md bg-amber-50 border border-amber-100 text-[10px] font-bold text-amber-650 uppercase tracking-wide">Author</span>';
                                } elseif ($c->user_role === 'subscriber') {
                                    $roleBadge = '<span class="px-2 py-0.5 rounded-md bg-slate-100 border border-slate-200 text-[10px] font-bold text-slate-500 uppercase tracking-wide">Pembaca</span>';
                                }
                                ?>
                                <div class="glass rounded-3xl p-5 border border-white/50 shadow-xs flex gap-4 items-start relative hover:border-slate-200/60 transition-colors">
                                    <!-- Avatar -->
                                    <div class="shrink-0">
                                        <?php if (!empty($c->avatar) && file_exists(__DIR__ . '/../../public/uploads/avatars/' . $c->avatar)): ?>
                                            <img src="<?php echo PUBLIC_URL; ?>/uploads/avatars/<?php echo $c->avatar; ?>"
                                                class="h-10 w-10 rounded-2xl object-cover border border-slate-100 shadow-sm" alt="Avatar">
                                        <?php else: ?>
                                            <div class="h-10 w-10 rounded-2xl bg-linear-to-tr from-slate-200 to-slate-300 border border-slate-100 flex items-center justify-center text-slate-600 font-display font-bold text-xs shadow-inner">
                                                <?php echo $cInitials; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <!-- Comment content -->
                                    <div class="space-y-2 flex-1">
                                        <div class="flex flex-wrap items-center gap-2 justify-between">
                                            <div class="flex items-center gap-2">
                                                <h4 class="font-bold text-slate-800 text-sm">
                                                    <?php echo htmlspecialchars($c->user_full_name ?? $c->author_name ?? 'Anonymous'); ?>
                                                </h4>
                                                <?php echo $roleBadge; ?>
                                            </div>
                                            <span class="text-[10px] font-medium text-slate-400">
                                                <?php echo date('d M Y, H:i', strtotime($c->created_at)); ?>
                                            </span>
                                        </div>
                                        <p class="text-slate-650 text-xs sm:text-sm leading-relaxed whitespace-pre-line">
                                            <?php echo nl2br(htmlspecialchars($c->content)); ?>
                                        </p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <!-- Comment Input Form (Requires Login/Subscriber role) -->
                    <div class="pt-4">
                        <?php if (\App\Helpers\Session::has('user_id')): ?>
                            <!-- Logged In Subscriber/User Form -->
                            <div class="glass rounded-3xl p-6 border border-white/50 shadow-md space-y-4">
                                <div class="flex items-center gap-3">
                                    <?php if (!empty($_SESSION['avatar']) && file_exists(__DIR__ . '/../../public/uploads/avatars/' . $_SESSION['avatar'])): ?>
                                        <img src="<?php echo PUBLIC_URL; ?>/uploads/avatars/<?php echo $_SESSION['avatar']; ?>"
                                            class="h-8 w-8 rounded-xl object-cover shadow-xs border border-slate-100">
                                    <?php else: ?>
                                        <div class="h-8 w-8 rounded-xl bg-linear-to-tr from-primary-600 to-accent-500 flex items-center justify-center text-white font-display font-bold text-xs shadow-sm">
                                            <?php echo strtoupper(substr($_SESSION['username'] ?? 'U', 0, 2)); ?>
                                        </div>
                                    <?php endif; ?>
                                    <div>
                                        <h4 class="font-bold text-slate-800 text-xs sm:text-sm">
                                            <?php echo htmlspecialchars($_SESSION['full_name'] ?? $_SESSION['username'] ?? 'User'); ?>
                                        </h4>
                                        <span class="text-[10px] font-bold text-primary-500 uppercase tracking-wide">
                                            Role: <?php echo htmlspecialchars($_SESSION['role'] ?? 'subscriber'); ?>
                                        </span>
                                    </div>
                                </div>

                                <form action="<?php echo PUBLIC_URL; ?>/comment/store" method="POST" class="space-y-4">
                                    <?php echo \App\Helpers\Security::csrfField(); ?>
                                    <input type="hidden" name="article_id" value="<?php echo intval($article->id); ?>">
                                    
                                    <div class="space-y-1.5">
                                        <textarea name="content" rows="4" placeholder="Tulis komentar Anda secara bijak..." class="w-full rounded-2xl border border-slate-200 p-4 focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 focus:outline-none bg-slate-50/50 text-slate-800 text-xs sm:text-sm transition-all" required></textarea>
                                    </div>
                                    
                                    <div class="flex justify-between items-center">
                                        <span class="text-[10px] text-slate-400 font-medium">
                                            💡 Komentar Anda akan ditinjau dan dimoderasi terlebih dahulu.
                                        </span>
                                        <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold text-white btn-primary shadow-md shadow-primary-500/10 hover:shadow-lg transition-all cursor-pointer">
                                            Kirim Komentar <i data-lucide="send" class="w-3.5 h-3.5"></i>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        <?php else: ?>
                            <!-- Guest Prompt (CTA to Login/Register) -->
                            <div class="glass rounded-3xl p-8 text-center border border-white/60 space-y-4 shadow-sm bg-linear-to-tr from-slate-50/80 to-white/80">
                                <div class="w-12 h-12 bg-primary-50 text-primary-600 rounded-2xl flex items-center justify-center mx-auto shadow-sm">
                                    <i data-lucide="message-square-plus" class="w-6 h-6"></i>
                                </div>
                                <div class="space-y-1">
                                    <h4 class="font-display font-bold text-slate-800 text-base">Ingin Ikut Berdiskusi?</h4>
                                    <p class="text-xs text-slate-500 max-w-sm mx-auto leading-relaxed">
                                        Tamu dan pembaca wajib terdaftar dan masuk menggunakan akun <strong>Subscriber</strong> untuk dapat mengirimkan komentar di artikel ini.
                                    </p>
                                </div>
                                <div class="pt-2">
                                    <a href="<?php echo PUBLIC_URL; ?>/auth" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold text-white btn-primary shadow-md shadow-primary-500/10 hover:shadow-lg transition-all cursor-pointer">
                                        Masuk / Daftar Akun <i data-lucide="arrow-right" class="w-4 h-4"></i>
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

            </div>

            <!-- Sidebar (Right) -->
            <div class="lg:col-span-4 space-y-8">

                <!-- Sidebar Widget 1: About Author -->
                <div class="glass rounded-3xl p-6 shadow-md border border-white/40 space-y-4">
                    <h3 class="font-display font-extrabold text-lg text-slate-900 border-b border-slate-100/50 pb-2">
                        Tentang Penulis
                    </h3>
                    <div class="flex items-center gap-3">
                        <div
                            class="h-10 w-10 rounded-full bg-linear-to-tr from-primary-600 to-accent-500 flex items-center justify-center text-white font-display font-bold text-sm">
                            <?php echo $initials; ?>
                        </div>
                        <div>
                            <div class="font-bold text-slate-800 text-sm">


                                <?php echo e($article->author_name ?? 'Author'); ?>
                            </div>
                            <div class="text-xs text-slate-500 font-medium">Kontributor Konten</div>
                        </div>
                    </div>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Merupakan penulis resmi terdaftar pada NexusCMS Portal yang berkomitmen untuk membagikan
                        gagasan, wawasan, serta panduan tutorial teknis berkualitas bagi komunitas.
                    </p>
                </div>

                <!-- Sidebar Widget 2: Recent Articles -->
                <div class="glass rounded-3xl p-6 shadow-md border border-white/40 space-y-4">
                    <h3 class="font-display font-extrabold text-lg text-slate-900 border-b border-slate-100/50 pb-2">
                        Artikel Terbaru Lainnya
                    </h3>
                    <div class="space-y-4">
                        <?php if (empty($recentArticles)): ?>
                            <p class="text-xs text-slate-400 font-medium py-2">Tidak ada artikel terbaru lainnya.</p>
                        <?php else: ?>
                            <?php foreach ($recentArticles as $recent): ?>
                                <a href="<?php echo PUBLIC_URL; ?>/read?slug=<?php echo htmlspecialchars($recent->slug); ?>"
                                    class="block group space-y-1">
                                    <h4 class="font-semibold text-slate-800 text-sm leading-snug group-hover:text-primary-600 transition-colors line-clamp-2"
                                        title="<?php echo htmlspecialchars($recent->title); ?>">
                                        <?php echo htmlspecialchars($recent->title); ?>
                                    </h4>
                                    <span
                                        class="text-[10px] font-bold text-slate-400"><?php echo date('d M Y', strtotime($recent->published_at ?? $recent->created_at)); ?></span>
                                </a>
                                <div class="h-px bg-slate-100/50"></div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

            </div>

        </div>
    </div>
</section>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>