<?php
/**
 * @var object $category
 * @var array $articles
 */
require_once __DIR__ . '/layouts/header.php'; ?>

<!-- Category Articles Section -->
<section id="category-articles" class="py-20 bg-slate-50/50 border-t border-slate-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Section Header -->
        <div class="flex items-end justify-between mb-12">
            <div>
                <span class="text-xs font-bold text-primary-600 uppercase tracking-widest bg-primary-50 px-3 py-1 rounded-full">Kategori</span>
                <h2 class="font-display font-extrabold text-3xl text-slate-900 tracking-tight mt-3">
                    Arsip: <?php echo e($category->name); ?>
                </h2>
                <?php if (!empty($category->description)): ?>
                    <p class="text-sm text-slate-500 font-medium mt-2 max-w-2xl leading-relaxed">
                        <?php echo e($category->description); ?>
                    </p>
                <?php else: ?>
                    <p class="text-xs text-slate-400 font-medium mt-1">
                        Menampilkan semua artikel yang diterbitkan dalam kategori <?php echo e($category->name); ?>.
                    </p>
                <?php endif; ?>
            </div>
            
            <a href="<?php echo PUBLIC_URL; ?>/" class="inline-flex items-center gap-2 text-xs font-bold text-slate-500 hover:text-primary-600 transition-colors uppercase tracking-wider">
                <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke Beranda
            </a>
        </div>

        <!-- Articles Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php if (empty($articles)): ?>
                <div class="col-span-full py-20 text-center bg-white border border-dashed border-slate-200 rounded-3xl p-8 shadow-xs">
                    <div class="h-14 w-14 rounded-full bg-slate-50 flex items-center justify-center mx-auto mb-4 text-slate-400">
                        <i data-lucide="book-open" class="w-6 h-6"></i>
                    </div>
                    <h3 class="font-display font-bold text-slate-700 text-lg">Tidak Ada Artikel</h3>
                    <p class="text-xs text-slate-400 mt-1">Belum ada artikel yang diterbitkan untuk kategori ini.</p>
                </div>
            <?php else: ?>
                <?php foreach ($articles as $art): ?>
                    <article class="group bg-white rounded-3xl border border-slate-100 hover:border-slate-200/60 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 overflow-hidden flex flex-col relative">
                        <!-- Thumbnail Wrapper -->
                        <div class="aspect-video w-full bg-slate-100 overflow-hidden relative border-b border-slate-100">
                            <?php if ($art->featured_image): ?>
                                <img src="<?php echo PUBLIC_URL; ?>/uploads/articles/<?php echo e($art->featured_image); ?>"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                    alt="<?php echo e($art->title); ?>" />
                            <?php else: ?>
                                <div class="w-full h-full flex items-center justify-center text-slate-300">
                                    <i data-lucide="image" class="w-10 h-10"></i>
                                </div>
                            <?php endif; ?>

                            <!-- Category Badge on thumbnail -->
                            <div class="absolute top-4 left-4 z-10">
                                <span class="px-3 py-1 rounded-full bg-slate-900/80 backdrop-blur-sm text-[10px] font-bold text-white uppercase tracking-wider">
                                    <?php echo e($art->category_name ?? $category->name); ?>
                                </span>
                            </div>
                        </div>

                        <!-- Card Content -->
                        <div class="p-6 grow flex flex-col justify-between space-y-4">
                            <div class="space-y-2">
                                <!-- Published Date -->
                                <div class="flex items-center gap-1.5 text-[11px] text-slate-400 font-semibold uppercase tracking-wider">
                                    <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
                                    <span><?php echo date('d M Y', strtotime($art->published_at ?? $art->created_at)); ?></span>
                                </div>

                                <!-- Article Title -->
                                <h3 class="font-display font-extrabold text-2xl text-slate-800 group-hover:text-primary-600 transition-colors line-clamp-2 leading-snug"
                                    title="<?php echo e($art->title); ?>">
                                    <a href="<?php echo PUBLIC_URL; ?>/read?slug=<?php echo e($art->slug); ?>">
                                        <?php echo e($art->title); ?>
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
                                    <div class="h-7 w-7 rounded-full bg-primary-100 text-primary-700 flex items-center justify-center font-display font-bold text-[10px]">
                                        <?php
                                        $initials = '';
                                        $parts = explode(' ', $art->author_name ?? 'Unknown');
                                        foreach (array_slice($parts, 0, 2) as $p) {
                                            $initials .= strtoupper(substr($p, 0, 1));
                                        }
                                        echo $initials;
                                        ?>
                                    </div>
                                    <span class="text-slate-600"><?php echo e($art->author_name ?? 'Author'); ?></span>
                                </div>

                                <a href="<?php echo PUBLIC_URL; ?>/read?slug=<?php echo e($art->slug); ?>"
                                    class="inline-flex items-center gap-1 text-primary-600 hover:text-primary-700 transition-colors group/btn">
                                    Baca Selengkapnya
                                    <i data-lucide="arrow-right" class="w-3.5 h-3.5 group-hover/btn:translate-x-0.5 transition-transform"></i>
                                </a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

    </div>
</section>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>
