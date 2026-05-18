<?php
/**
 * @var int $totalUsers
 * @var int $totalAdmins
 * @var int $totalSubscribers
 * @var int $activeUsers
 * @var array $recentUsers
 * @var int $myArticlesCount
 * @var int $myCommentsCount
 * @var array $myArticles
 * @var array $myComments
 * @var string $username
 * @var string $role
 * @var string|null $fullName
 */
$title = "Panel Kontrol";
$activePage = "dashboard";
require_once __DIR__ . '/layouts/admin_header.php';
use App\Helpers\Session;

$success = Session::flash('success');
?>

<!-- Notification banner from session flash success -->
<?php if ($success): ?>
    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-100 text-emerald-700 text-sm font-semibold flex items-center gap-3 shadow-sm"
        x-data="{ show: true }" x-show="show" x-transition>
        <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600 shrink-0 animate-bounce"></i>
        <span class="flex-1"><?php echo htmlspecialchars($success); ?></span>
        <button @click="show = false" class="text-emerald-500 hover:text-emerald-700">
            <i data-lucide="x" class="w-4 h-4"></i>
        </button>
    </div>
<?php endif; ?>

<?php if ($role === 'subscriber'): ?>
    <!-- ========================================== -->
    <!--          SUBSCRIBER DASHBOARD VIEW         -->
    <!-- ========================================== -->

    <!-- Welcome Board -->
    <div
        class="rounded-3xl bg-slate-900 text-white p-6 sm:p-8 relative overflow-hidden shadow-xl border border-slate-800 animate-fade-in">
        <div class="absolute -top-12 -left-12 h-32 w-32 rounded-full bg-primary-600/30 blur-2xl"></div>
        <div class="absolute -bottom-16 -right-16 h-48 w-48 rounded-full bg-accent-500/20 blur-2xl"></div>

        <div class="relative z-10 space-y-4 max-w-xl">
            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-white/10 text-xs font-bold text-white/95">
                <i data-lucide="sparkles" class="w-3.5 h-3.5 text-amber-400"></i> Ruang Baca & Kontribusi Subscriber
            </span>
            <h3 class="font-display font-extrabold text-2xl sm:text-3xl tracking-tight leading-tight">
                Halo, <?php echo e($fullName ?? $username); ?>!
            </h3>
        </div>
    </div>

    <!-- Subscriber Metrics -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <div
            class="p-6 rounded-2xl bg-white border border-slate-100 shadow-sm flex items-center justify-between hover:shadow-md transition-all duration-300">
            <div class="space-y-1">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Artikel yang Saya Tulis</span>
                <div class="font-display font-extrabold text-2xl text-slate-800"><?php echo $myArticlesCount; ?></div>
            </div>
            <div class="h-12 w-12 rounded-xl bg-primary-50 text-primary-600 flex items-center justify-center">
                <i data-lucide="pen-tool" class="w-6 h-6"></i>
            </div>
        </div>

        <div
            class="p-6 rounded-2xl bg-white border border-slate-100 shadow-sm flex items-center justify-between hover:shadow-md transition-all duration-300">
            <div class="space-y-1">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Komentar Dikirim</span>
                <div class="font-display font-extrabold text-2xl text-slate-800"><?php echo $myCommentsCount; ?></div>
            </div>
            <div class="h-12 w-12 rounded-xl bg-accent-50 text-accent-600 flex items-center justify-center">
                <i data-lucide="message-square" class="w-6 h-6"></i>
            </div>
        </div>

        <div
            class="p-6 rounded-2xl bg-white border border-slate-100 shadow-sm flex items-center justify-between hover:shadow-md transition-all duration-300">
            <div class="space-y-1">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Status Anggota</span>
                <div class="font-display font-bold text-sm text-emerald-600 flex items-center gap-1.5 pt-1">
                    <span class="h-2.5 w-2.5 rounded-full bg-emerald-500 animate-pulse"></span> Pembaca Aktif
                </div>
            </div>
            <div class="h-12 w-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <i data-lucide="user-check" class="w-6 h-6"></i>
            </div>
        </div>
    </div>

    <!-- Subscriber Content Area -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        <!-- Left Panel: My Written Articles -->
        <div class="lg:col-span-2 space-y-6">
            <div class="p-6 bg-white border border-slate-100 rounded-3xl shadow-sm space-y-6">
                <div class="flex items-center justify-between flex-wrap gap-4">
                    <div>
                        <h3 class="font-display font-bold text-lg text-slate-800">Riwayat Draf Artikel Saya</h3>
                        <p class="text-xs text-slate-500 font-medium font-sans">Artikel yang Anda ajukan untuk publikasi di
                            portal</p>
                    </div>
                    <?php if (\App\Helpers\Auth::hasPermission('create_articles')): ?>
                        <a href="<?php echo PUBLIC_URL; ?>/admin/articles/create"
                            class="px-4 py-2 rounded-xl bg-primary-600 text-white font-bold text-xs shadow-md hover:bg-primary-500 transition-all flex items-center gap-1.5">
                            <i data-lucide="plus" class="w-3.5 h-3.5"></i> Buat Draf Baru
                        </a>
                    <?php endif; ?>
                </div>

                <div class="overflow-x-auto rounded-2xl border border-slate-100">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr
                                class="bg-slate-50/50 border-b border-slate-100 text-xs font-bold text-slate-600 uppercase tracking-wider">
                                <th class="p-4">Judul Artikel</th>
                                <th class="p-4">Kategori</th>
                                <th class="p-4 text-center">Status</th>
                                <th class="p-4">Dibuat Pada</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            <?php if (empty($myArticles)): ?>
                                <tr>
                                    <td colspan="4" class="p-8 text-center text-slate-400 font-medium text-xs">Anda belum
                                        mengirimkan tulisan draf apa pun.</td>
                                </tr>
                            <?php endif; ?>
                            <?php foreach ($myArticles as $ma): ?>
                                <tr class="hover:bg-slate-50/30 transition-colors">
                                    <td class="p-4">
                                        <div class="font-bold text-slate-800 max-w-[200px] truncate"
                                            title="<?php echo htmlspecialchars($ma->title); ?>">
                                            <?php echo htmlspecialchars($ma->title); ?>
                                        </div>
                                    </td>
                                    <td class="p-4 text-xs font-medium text-slate-600">
                                        <?php echo htmlspecialchars($ma->category_name ?? 'Umum'); ?>
                                    </td>
                                    <td class="p-4 text-center">
                                        <?php if ($ma->status === 'published'): ?>
                                            <span
                                                class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-100 uppercase">Tayang</span>
                                        <?php else: ?>
                                            <span
                                                class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-600 border border-amber-100 uppercase">Draf</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="p-4 text-xs text-slate-500 font-medium">
                                        <?php echo date('d M Y', strtotime($ma->created_at)); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right Panel: My Recent Comments & Actions -->
        <div class="space-y-6">
            <!-- Quick Actions -->
            <div class="p-6 bg-white border border-slate-100 rounded-3xl shadow-sm space-y-4">
                <h3 class="font-display font-bold text-sm text-slate-800 uppercase tracking-wider">Navigasi Cepat</h3>
                <div class="grid grid-cols-1 gap-2.5">
                    <a href="<?php echo PUBLIC_URL; ?>/" target="_blank"
                        class="w-full px-4 py-3 rounded-xl border border-slate-200 hover:border-primary-500 hover:bg-primary-50/20 text-slate-700 hover:text-primary-700 font-bold text-xs transition-all flex items-center gap-2 cursor-pointer">
                        <i data-lucide="globe" class="w-4 h-4 text-primary-500"></i> Kunjungi Beranda Web
                    </a>
                    <a href="<?php echo PUBLIC_URL; ?>/admin/profile"
                        class="w-full px-4 py-3 rounded-xl border border-slate-200 hover:border-indigo-500 hover:bg-indigo-50/20 text-slate-700 hover:text-indigo-700 font-bold text-xs transition-all flex items-center gap-2 cursor-pointer">
                        <i data-lucide="user-cog" class="w-4 h-4 text-indigo-500"></i> Lengkapi Profil Saya
                    </a>
                </div>
            </div>

            <!-- Recent Comments -->
            <div class="p-6 bg-white border border-slate-100 rounded-3xl shadow-sm space-y-4">
                <h3 class="font-display font-bold text-sm text-slate-800 uppercase tracking-wider">Komentar Terbaru Anda
                </h3>
                <div class="space-y-3">
                    <?php if (empty($myComments)): ?>
                        <p class="text-xs text-slate-400 font-medium py-2">Belum ada komentar yang Anda bagikan.</p>
                    <?php endif; ?>
                    <?php foreach ($myComments as $mc): ?>
                        <div
                            class="p-3 rounded-xl bg-slate-50 border border-slate-100 space-y-1.5 hover:shadow-xs transition-all">
                            <div class="flex items-center justify-between">
                                <a href="<?php echo PUBLIC_URL; ?>/read?slug=<?php echo $mc->article_slug; ?>" target="_blank"
                                    class="text-[10px] font-bold text-primary-600 hover:underline truncate max-w-[120px]">
                                    <?php echo htmlspecialchars($mc->article_title); ?>
                                </a>
                                <span
                                    class="text-[9px] text-slate-400 font-semibold"><?php echo date('d M', strtotime($mc->created_at)); ?></span>
                            </div>
                            <p class="text-xs text-slate-650 leading-relaxed italic truncate">
                                "<?php echo htmlspecialchars($mc->content); ?>"</p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

    </div>

<?php else: ?>
    <!-- ========================================== -->
    <!--            STAFF/ADMIN DASHBOARD           -->
    <!-- ========================================== -->

    <!-- Welcome Board -->
    <div
        class="rounded-3xl bg-slate-900 text-white p-6 sm:p-8 relative overflow-hidden shadow-xl border border-slate-800 animate-fade-in">
        <div class="absolute -top-12 -left-12 h-32 w-32 rounded-full bg-primary-600/30 blur-2xl"></div>
        <div class="absolute -bottom-16 -right-16 h-48 w-48 rounded-full bg-accent-500/20 blur-2xl"></div>

        <div class="relative z-10 space-y-4 max-w-xl">
            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-white/10 text-xs font-bold text-white/95">
                <i data-lucide="activity" class="w-3.5 h-3.5"></i> Aktivitas Sistem Berjalan Lancar
            </span>
            <h3 class="font-display font-extrabold text-2xl sm:text-3xl tracking-tight leading-tight">
                Halo, <?php echo e($fullName ?? $username); ?>!
            </h3>
            <p class="text-sm text-slate-300 leading-relaxed">
                Anda masuk dengan hak akses <strong class="text-primary-400 capitalize"><?php echo e($role); ?></strong>.
            </p>
        </div>
    </div>

    <!-- Stats Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <div
            class="p-6 rounded-2xl bg-white border border-slate-100 shadow-sm flex items-center justify-between hover:shadow-md transition-all duration-300">
            <div class="space-y-1">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total User</span>
                <div class="font-display font-extrabold text-2xl text-slate-800"><?php echo $totalUsers; ?></div>
            </div>
            <div class="h-12 w-12 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center">
                <i data-lucide="users" class="w-6 h-6"></i>
            </div>
        </div>

        <div
            class="p-6 rounded-2xl bg-white border border-slate-100 shadow-sm flex items-center justify-between hover:shadow-md transition-all duration-300">
            <div class="space-y-1">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Administrator</span>
                <div class="font-display font-extrabold text-2xl text-slate-800"><?php echo $totalAdmins; ?></div>
            </div>
            <div class="h-12 w-12 rounded-xl bg-primary-50 text-primary-600 flex items-center justify-center">
                <i data-lucide="shield" class="w-6 h-6"></i>
            </div>
        </div>

        <div
            class="p-6 rounded-2xl bg-white border border-slate-100 shadow-sm flex items-center justify-between hover:shadow-md transition-all duration-300">
            <div class="space-y-1">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Subscriber</span>
                <div class="font-display font-extrabold text-2xl text-slate-800"><?php echo $totalSubscribers; ?></div>
            </div>
            <div class="h-12 w-12 rounded-xl bg-accent-50 text-accent-600 flex items-center justify-center">
                <i data-lucide="user-check" class="w-6 h-6"></i>
            </div>
        </div>

        <div
            class="p-6 rounded-2xl bg-white border border-slate-100 shadow-sm flex items-center justify-between hover:shadow-md transition-all duration-300">
            <div class="space-y-1">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">User Aktif</span>
                <div class="font-display font-extrabold text-2xl text-slate-800"><?php echo $activeUsers; ?></div>
            </div>
            <div class="h-12 w-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <i data-lucide="activity" class="w-6 h-6"></i>
            </div>
        </div>
    </div>

    <?php if (\App\Helpers\Auth::hasPermission('manage_users')): ?>
        <!-- Database Content Area (Table listing) -->
        <div class="p-6 md:p-8 bg-white border border-slate-100 rounded-3xl shadow-sm space-y-6">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div>
                    <h3 class="font-display font-bold text-lg text-slate-800">User Terbaru Terdaftar (cms_db.users)</h3>
                    <p class="text-xs text-slate-500 font-medium">Memantau pendaftaran user terbaru dan status aksesnya</p>
                </div>
            </div>

            <!-- Table Content Wrapper -->
            <div class="overflow-x-auto rounded-2xl border border-slate-100">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr
                            class="bg-slate-50 border-b border-slate-100 text-xs font-bold text-slate-600 uppercase tracking-wider">
                            <th class="p-4">Nama & Username</th>
                            <th class="p-4">Alamat Email</th>
                            <th class="p-4">Hak Akses (Role)</th>
                            <th class="p-4">Status</th>
                            <th class="p-4">Tanggal Gabung</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        <?php foreach ($recentUsers as $u): ?>
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="p-4 flex items-center gap-3">
                                    <div
                                        class="h-8 w-8 rounded-lg bg-slate-100 text-slate-600 font-bold flex items-center justify-center text-xs">
                                        <?php echo strtoupper(substr($u->username, 0, 2)); ?>
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-800"><?php echo htmlspecialchars($u->full_name ?? '-'); ?>
                                        </div>
                                        <div class="text-xs text-slate-500">@<?php echo htmlspecialchars($u->username); ?></div>
                                    </div>
                                </td>
                                <td class="p-4 text-slate-650 font-medium">
                                    <?php echo htmlspecialchars($u->email); ?>
                                </td>
                                <td class="p-4">
                                    <?php
                                    $roleColors = [
                                        'admin' => 'bg-rose-50 border-rose-100 text-rose-600',
                                        'editor' => 'bg-indigo-50 border-indigo-100 text-indigo-600',
                                        'author' => 'bg-amber-50 border-amber-100 text-amber-600',
                                        'subscriber' => 'bg-slate-100 border-slate-200 text-slate-600',
                                    ];
                                    $c = $roleColors[$u->role] ?? 'bg-slate-100 text-slate-600';
                                    ?>
                                    <span
                                        class="inline-flex px-2.5 py-1 rounded-full text-xs font-bold border capitalize <?php echo $c; ?>">
                                        <?php echo htmlspecialchars($u->role); ?>
                                    </span>
                                </td>
                                <td class="p-4">
                                    <?php if ($u->status === 'active'): ?>
                                        <span
                                            class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-100">
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> Aktif
                                        </span>
                                    <?php else: ?>
                                        <span
                                            class="inline-flex items-center gap-1.5 text-xs font-semibold text-rose-600 bg-rose-50 px-2.5 py-1 rounded-full border border-rose-100">
                                            <span class="h-1.5 w-1.5 rounded-full bg-rose-500"></span> Nonaktif
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="p-4 text-xs font-medium text-slate-500">
                                    <?php echo date('d M Y, H:i', strtotime($u->created_at)); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>

<?php endif; ?>

<?php require_once __DIR__ . '/layouts/admin_footer.php'; ?>