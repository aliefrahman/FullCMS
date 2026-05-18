<?php 
/**
 * @var array $articles
 */
$title = "Daftar Artikel";
$activePage = "articles";
require_once __DIR__ . '/../layouts/admin_header.php'; 
use App\Helpers\Session;

$error = Session::flash('error');
$success = Session::flash('success');
?>

<div class="space-y-6">

    <!-- Notification alerts -->
    <?php if ($error): ?>
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-100 text-rose-600 text-sm font-semibold flex items-center gap-2.5 shadow-sm animate-fade-in">
            <i data-lucide="alert-circle" class="w-5 h-5 text-rose-500 flex-shrink-0 animate-bounce"></i>
            <span><?php echo htmlspecialchars($error); ?></span>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-100 text-emerald-700 text-sm font-semibold flex items-center gap-2.5 shadow-sm animate-fade-in">
            <i data-lucide="check-circle" class="w-5 h-5 text-emerald-500 flex-shrink-0"></i>
            <span><?php echo htmlspecialchars($success); ?></span>
        </div>
    <?php endif; ?>

    <!-- Action Header -->
    <div class="flex items-center justify-between flex-wrap gap-4 bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
        <div>
            <h3 class="font-display font-extrabold text-xl text-slate-800 tracking-tight">Manajemen Artikel</h3>
            <p class="text-xs text-slate-500 font-medium">Tulis, publikasikan, dan kelola semua konten artikel web Anda</p>
        </div>
        <a href="<?php echo PUBLIC_URL; ?>/admin/articles/create" class="px-5 py-3 rounded-xl bg-primary-600 text-white font-bold text-sm tracking-wide shadow-md shadow-primary-500/10 hover:shadow-primary-500/20 hover:-translate-y-0.5 transition-all duration-200 flex items-center gap-2">
            <i data-lucide="pen-tool" class="w-4 h-4"></i> Tulis Artikel Baru
        </a>
    </div>

    <!-- Articles table -->
    <div class="bg-white border border-slate-100 rounded-3xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100 text-xs font-bold text-slate-600 uppercase tracking-wider">
                        <th class="p-4 pl-6">Detail Artikel</th>
                        <th class="p-4">Kategori</th>
                        <th class="p-4">Penulis</th>
                        <th class="p-4 text-center">Status</th>
                        <th class="p-4">Tanggal Diperbarui</th>
                        <th class="p-4 text-center pr-6">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    <?php if (empty($articles)): ?>
                    <tr>
                        <td colspan="6" class="p-8 text-center text-slate-400 font-medium text-sm">Belum ada artikel yang ditambahkan.</td>
                    </tr>
                    <?php endif; ?>
                    <?php foreach ($articles as $a): ?>
                    <tr class="hover:bg-slate-50/30 transition-colors">
                        <td class="p-4 pl-6">
                            <div class="flex items-center gap-3">
                                <?php if ($a->featured_image): ?>
                                    <div class="h-12 w-16 rounded-lg bg-slate-100 flex-shrink-0 overflow-hidden border border-slate-200">
                                        <img src="<?php echo PUBLIC_URL; ?>/uploads/articles/<?php echo htmlspecialchars($a->featured_image); ?>" class="w-full h-full object-cover" />
                                    </div>
                                <?php else: ?>
                                    <div class="h-12 w-16 rounded-lg bg-slate-100 flex-shrink-0 border border-slate-200 flex items-center justify-center text-slate-300">
                                        <i data-lucide="image" class="w-5 h-5"></i>
                                    </div>
                                <?php endif; ?>
                                <div>
                                    <div class="font-bold text-slate-800 max-w-[250px] truncate" title="<?php echo htmlspecialchars($a->title); ?>">
                                        <?php echo htmlspecialchars($a->title); ?>
                                    </div>
                                    <div class="text-[11px] text-slate-400 font-medium">/<?php echo htmlspecialchars($a->slug); ?></div>
                                </div>
                            </div>
                        </td>
                        <td class="p-4 text-xs font-semibold text-slate-600">
                            <?php echo htmlspecialchars($a->category_name ?? 'Tanpa Kategori'); ?>
                        </td>
                        <td class="p-4 text-xs font-semibold text-slate-600">
                            <?php echo htmlspecialchars($a->author_name ?? 'Unknown'); ?>
                        </td>
                        <td class="p-4 text-center">
                            <?php if ($a->status === 'published'): ?>
                                <span class="inline-flex px-2.5 py-1 rounded-full text-[10px] font-bold border border-emerald-100 bg-emerald-50 text-emerald-600 uppercase">
                                    Published
                                </span>
                            <?php elseif ($a->status === 'archived'): ?>
                                <span class="inline-flex px-2.5 py-1 rounded-full text-[10px] font-bold border border-rose-100 bg-rose-50 text-rose-600 uppercase">
                                    Archived
                                </span>
                            <?php else: ?>
                                <span class="inline-flex px-2.5 py-1 rounded-full text-[10px] font-bold border border-amber-100 bg-amber-50 text-amber-600 uppercase">
                                    Draft
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="p-4 text-xs font-medium text-slate-500">
                            <?php echo date('d M Y, H:i', strtotime($a->updated_at)); ?>
                        </td>
                        <td class="p-4 text-center pr-6">
                            <div class="inline-flex items-center gap-2">
                                <a href="<?php echo PUBLIC_URL; ?>/admin/articles/edit?id=<?php echo $a->id; ?>" class="p-2 rounded-xl bg-slate-50 border border-slate-100 text-slate-500 hover:text-primary-600 hover:bg-primary-50 hover:border-primary-100 transition-all duration-200 cursor-pointer">
                                    <i data-lucide="edit-3" class="w-4 h-4"></i>
                                </a>
                                <a href="<?php echo PUBLIC_URL; ?>/admin/articles/delete?id=<?php echo $a->id; ?>" 
                                   onclick="return confirm('Apakah Anda yakin ingin menghapus artikel ini? Tindakan ini tidak dapat dibatalkan.');"
                                   class="p-2 rounded-xl bg-slate-50 border border-slate-100 text-slate-500 hover:text-rose-600 hover:bg-rose-50 hover:border-rose-100 transition-all duration-200 cursor-pointer">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<?php require_once __DIR__ . '/../layouts/admin_footer.php'; ?>
