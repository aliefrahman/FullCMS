<?php
/**
 * @var array $articles
 */
require_once __DIR__ . '/layouts/header.php'; ?>

<!-- Latest Articles Section -->
<section id="articles" class="py-20 bg-slate-50/50 border-t border-slate-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Section Header -->
        <div class="flex items-end justify-between mb-12">
            <div>
                <span
                    class="text-xs font-bold text-primary-600 uppercase tracking-widest bg-primary-50 px-3 py-1 rounded-full">Kabar
                    Terbaru</span>
                <h2 class="font-display font-extrabold text-3xl text-slate-900 tracking-tight mt-3">Artikel Terpublikasi
                </h2>
                <p class="text-xs text-slate-500 font-medium mt-1">Temukan berbagai tutorial, wawasan teknologi, dan
                    panduan praktis terbaru</p>
            </div>
        </div>

        <!-- Articles Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php if (empty($articles)): ?>
                <div class="col-span-full py-16 text-center bg-white border border-dashed border-slate-200 rounded-3xl p-8">
                    <div
                        class="h-14 w-14 rounded-full bg-slate-50 flex items-center justify-center mx-auto mb-4 text-slate-400">
                        <i data-lucide="book-open" class="w-6 h-6"></i>
                    </div>
                    <h3 class="font-display font-bold text-slate-700 text-lg">Belum Ada Artikel</h3>
                    <p class="text-xs text-slate-400 mt-1">Silakan tulis dan publikasikan artikel pertama Anda melalui panel
                        admin.</p>
                </div>
            <?php else: ?>
                <?php foreach ($articles as $art): ?>
                    <article
                        class="group bg-white rounded-3xl border border-slate-100 hover:border-slate-200/60 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 overflow-hidden flex flex-col relative">
                        <!-- Thumbnail Wrapper -->
                        <div class="aspect-video w-full bg-slate-100 overflow-hidden relative border-b border-slate-100">
                            <?php if ($art->featured_image): ?>
                                <img src="<?php echo PUBLIC_URL; ?>/uploads/articles/<?php echo htmlspecialchars($art->featured_image); ?>"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                    alt="<?php echo htmlspecialchars($art->title); ?>" />
                            <?php else: ?>
                                <div class="w-full h-full flex items-center justify-center text-slate-300">
                                    <i data-lucide="image" class="w-10 h-10"></i>
                                </div>
                            <?php endif; ?>

                            <!-- Category Badge on thumbnail -->
                            <div class="absolute top-4 left-4 z-10">
                                <span
                                    class="px-3 py-1 rounded-full bg-slate-900/80 backdrop-blur-sm text-[10px] font-bold text-white uppercase tracking-wider">
                                    <?php echo htmlspecialchars($art->category_name ?? 'Umum'); ?>
                                </span>
                            </div>
                        </div>

                        <!-- Card Content -->
                        <div class="p-6 grow flex flex-col justify-between space-y-4">
                            <div class="space-y-2">
                                <!-- Published Date -->
                                <div
                                    class="flex items-center gap-1.5 text-[11px] text-slate-400 font-semibold uppercase tracking-wider">
                                    <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
                                    <span><?php echo date('d M Y', strtotime($art->published_at ?? $art->created_at)); ?></span>
                                </div>

                                <!-- Article Title -->
                                <h3 class="font-display font-extrabold text-2xl text-slate-800 group-hover:text-primary-600 transition-colors line-clamp-2 leading-snug"
                                    title="<?php echo htmlspecialchars($art->title); ?>">
                                    <a href="<?php echo PUBLIC_URL; ?>/read?slug=<?php echo htmlspecialchars($art->slug); ?>">
                                        <?php echo htmlspecialchars($art->title); ?>
                                    </a>
                                </h3>

                                <!-- Content snippet -->
                                <p class="text-md text-slate-500 leading-relaxed line-clamp-3">
                                    <?php echo strip_tags($art->content); ?>
                                </p>
                            </div>

                            <!-- Card Footer: Author Info & Read button -->
                            <div class="pt-4 border-t border-slate-50 flex items-center justify-between text-xs font-semibold">
                                <div class="flex items-center gap-2">
                                    <div
                                        class="h-7 w-7 rounded-full bg-primary-100 text-primary-700 flex items-center justify-center font-display font-bold text-[10px]">
                                        <?php
                                        $initials = '';
                                        $parts = explode(' ', $art->author_name ?? 'Unknown');
                                        foreach (array_slice($parts, 0, 2) as $p) {
                                            $initials .= strtoupper(substr($p, 0, 1));
                                        }
                                        echo $initials;
                                        ?>
                                    </div>
                                    <span
                                        class="text-slate-600"><?php echo htmlspecialchars($art->author_name ?? 'Author'); ?></span>
                                </div>

                                <a href="<?php echo PUBLIC_URL; ?>/read?slug=<?php echo htmlspecialchars($art->slug); ?>"
                                    class="inline-flex items-center gap-1 text-primary-600 hover:text-primary-700 transition-colors group/btn">
                                    Baca Selengkapnya
                                    <i data-lucide="arrow-right"
                                        class="w-3.5 h-3.5 group-hover/btn:translate-x-0.5 transition-transform"></i>
                                </a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

    </div>
</section>

<!-- Active Tech Stack Showcase -->
<section class="py-24 bg-[#fafafa] overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center space-y-4 mb-16">
            <h2 class="font-display font-extrabold text-2xl sm:text-3xl text-slate-900 tracking-tight">
                Modern Technology Stack
            </h2>
            <p class="text-slate-500 text-sm">
                Built on top of stable, trusted, and cutting-edge software tools.
            </p>
        </div>

        <!-- Tech Badges Grid -->
        <div class="flex flex-wrap items-center justify-center gap-6 max-w-4xl mx-auto">
            <div class="px-6 py-4 rounded-xl bg-white border border-slate-100 shadow-sm flex items-center gap-3">
                <i data-lucide="code" class="w-6 h-6 text-indigo-600"></i>
                <span class="font-semibold text-slate-700 text-sm">PHP Native MVC</span>
            </div>
            <div class="px-6 py-4 rounded-xl bg-white border border-slate-100 shadow-sm flex items-center gap-3">
                <i data-lucide="database" class="w-6 h-6 text-sky-600"></i>
                <span class="font-semibold text-slate-700 text-sm">MySQL Database</span>
            </div>
            <div class="px-6 py-4 rounded-xl bg-white border border-slate-100 shadow-sm flex items-center gap-3">
                <i data-lucide="layout" class="w-6 h-6 text-blue-500"></i>
                <span class="font-semibold text-slate-700 text-sm">Tailwind CSS v4</span>
            </div>
            <div class="px-6 py-4 rounded-xl bg-white border border-slate-100 shadow-sm flex items-center gap-3">
                <i data-lucide="bolt" class="w-6 h-6 text-amber-500"></i>
                <span class="font-semibold text-slate-700 text-sm">Alpine.js Reactive</span>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action Banner -->
<section class="py-20 bg-white">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div
            class="rounded-3xl bg-linear-to-r from-primary-900 to-slate-900 p-8 sm:p-12 lg:p-16 text-center space-y-6 relative overflow-hidden shadow-2xl">
            <!-- Background blurs inside banner -->
            <div class="absolute -top-12 -left-12 h-32 w-32 rounded-full bg-primary-600/20 blur-2xl"></div>
            <div class="absolute -bottom-16 -right-16 h-48 w-48 rounded-full bg-accent-500/20 blur-2xl"></div>

            <h2
                class="font-display font-extrabold text-3xl sm:text-4xl text-white tracking-tight relative z-10 leading-tight">
                Ready to Experience Ultimate Performance?
            </h2>
            <p class="text-slate-300 text-base max-w-xl mx-auto relative z-10 leading-relaxed">
                Start crafting high-performance, aesthetically pleasing modern web interfaces with FullCMS today. It's
                fast, flexible, and completely free.
            </p>
            <div class="pt-4 relative z-10 flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="#"
                    class="w-full sm:w-auto px-8 py-4 rounded-xl font-bold bg-white text-slate-900 hover:bg-slate-50 shadow-md transition-all duration-200">
                    Get Started Now
                </a>
                <a href="#"
                    class="w-full sm:w-auto px-8 py-4 rounded-xl font-bold bg-slate-800 text-white hover:bg-slate-75 border border-slate-700 transition-all duration-200">
                    Read the Docs
                </a>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>