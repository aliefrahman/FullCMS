<?php 
/**
 * @var array $pages
 */
$title = "Manajemen Halaman Statis";
$activePage = "pages";
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
            <h3 class="font-display font-extrabold text-xl text-slate-800 tracking-tight">Manajemen Halaman</h3>
            <p class="text-xs text-slate-500 font-medium">Buat dan kelola halaman statis (seperti Tentang Kami, Kontak, atau Kebijakan Privasi)</p>
        </div>
        <a href="<?php echo PUBLIC_URL; ?>/admin/pages/create" class="px-5 py-3 rounded-xl bg-primary-600 text-white font-bold text-sm tracking-wide shadow-md shadow-primary-500/10 hover:shadow-primary-500/20 hover:-translate-y-0.5 transition-all duration-200 flex items-center gap-2">
            <i data-lucide="layout-grid" class="w-4 h-4"></i> Buat Halaman Baru
        </a>
    </div>

    <!-- Pages table -->
    <div class="bg-white border border-slate-100 rounded-3xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100 text-xs font-bold text-slate-600 uppercase tracking-wider">
                        <th class="p-4 pl-6">Judul Halaman</th>
                        <th class="p-4">Meta Deskripsi</th>
                        <th class="p-4 text-center">Status</th>
                        <th class="p-4">Tanggal Dibuat</th>
                        <th class="p-4 text-center pr-6">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    <?php if (empty($pages)): ?>
                    <tr>
                        <td colspan="5" class="p-8 text-center text-slate-400 font-medium text-sm">Belum ada halaman statis yang ditambahkan.</td>
                    </tr>
                    <?php endif; ?>
                    <?php foreach ($pages as $p): ?>
                    <tr class="hover:bg-slate-50/30 transition-colors">
                        <td class="p-4 pl-6">
                            <div>
                                <div class="font-bold text-slate-800" title="<?php echo htmlspecialchars($p->title); ?>">
                                    <?php echo htmlspecialchars($p->title); ?>
                                </div>
                                <a href="<?php echo PUBLIC_URL; ?>/p?slug=<?php echo htmlspecialchars($p->slug); ?>" target="_blank" class="text-[11px] text-primary-600 hover:underline font-semibold flex items-center gap-0.5 mt-0.5">
                                    <span>/p?slug=<?php echo htmlspecialchars($p->slug); ?></span>
                                    <i data-lucide="external-link" class="w-3 h-3"></i>
                                </a>
                            </div>
                        </td>
                        <td class="p-4 text-xs font-medium text-slate-500 max-w-[280px] truncate" title="<?php echo htmlspecialchars($p->meta_description ?? '-'); ?>">
                            <?php echo htmlspecialchars($p->meta_description ?: '-'); ?>
                        </td>
                        <td class="p-4 text-center">
                            <?php if ($p->status === 'published'): ?>
                                <span class="inline-flex px-2.5 py-1 rounded-full text-[10px] font-bold border border-emerald-100 bg-emerald-50 text-emerald-600 uppercase">
                                    Published
                                </span>
                            <?php else: ?>
                                <span class="inline-flex px-2.5 py-1 rounded-full text-[10px] font-bold border border-amber-100 bg-amber-50 text-amber-600 uppercase">
                                    Draft
                                </span>
                            <?php endif; ?>
                        </td>
                        <td class="p-4 text-xs font-semibold text-slate-500">
                            <?php echo date('d M Y, H:i', strtotime($p->created_at)); ?>
                        </td>
                        <td class="p-4 text-center pr-6">
                            <div class="flex items-center justify-center gap-2">
                                <a href="<?php echo PUBLIC_URL; ?>/admin/pages/edit?id=<?php echo $p->id; ?>" class="h-8 w-8 rounded-lg bg-slate-50 border border-slate-200 text-slate-600 hover:text-primary-600 hover:border-primary-200 flex items-center justify-center transition-all cursor-pointer" title="Edit Halaman">
                                    <i data-lucide="edit" class="w-4 h-4"></i>
                                </a>
                                <a href="<?php echo PUBLIC_URL; ?>/admin/pages/delete?id=<?php echo $p->id; ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus halaman statis ini?')" class="h-8 w-8 rounded-lg bg-slate-50 border border-slate-200 text-slate-600 hover:text-rose-600 hover:border-rose-200 flex items-center justify-center transition-all cursor-pointer" title="Hapus Halaman">
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
