<?php 
/**
 * @var array $categories
 */
$title = "Kategori Artikel";
$activePage = "categories";
require_once __DIR__ . '/../layouts/admin_header.php'; 
use App\Helpers\Session;

$error = Session::flash('error');
$success = Session::flash('success');
?>

<div x-data="{ 
    createModalOpen: false, 
    editModalOpen: false,
    currentCategory: { id: '', name: '', slug: '', description: '' }
}" class="space-y-6">

    <!-- Notification alerts -->
    <?php if ($error): ?>
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-100 text-rose-600 text-sm font-semibold flex items-center gap-2.5 shadow-sm animate-fade-in">
            <i data-lucide="alert-circle" class="w-5 h-5 text-rose-500 shrink-0 animate-bounce"></i>
            <span><?php echo htmlspecialchars($error); ?></span>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-100 text-emerald-700 text-sm font-semibold flex items-center gap-2.5 shadow-sm animate-fade-in">
            <i data-lucide="check-circle" class="w-5 h-5 text-emerald-500 shrink-0"></i>
            <span><?php echo htmlspecialchars($success); ?></span>
        </div>
    <?php endif; ?>

    <!-- Action Header -->
    <div class="flex items-center justify-between flex-wrap gap-4 bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
        <div>
            <h3 class="font-display font-extrabold text-xl text-slate-800 tracking-tight">Manajemen Kategori</h3>
            <p class="text-xs text-slate-500 font-medium">Buat pengelompokkan untuk mengatur dan menyusun rapi artikel web Anda</p>
        </div>
        <button @click="createModalOpen = true" class="px-5 py-3 rounded-xl bg-primary-600 text-white font-bold text-sm tracking-wide shadow-md shadow-primary-500/10 hover:shadow-primary-500/20 hover:-translate-y-0.5 transition-all duration-200 flex items-center gap-2 cursor-pointer">
            <i data-lucide="folder-plus" class="w-4 h-4"></i> Tambah Kategori
        </button>
    </div>

    <!-- Category table grid -->
    <div class="bg-white border border-slate-100 rounded-3xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100 text-xs font-bold text-slate-600 uppercase tracking-wider">
                        <th class="p-4 pl-6">Nama Kategori</th>
                        <th class="p-4">Deskripsi</th>
                        <th class="p-4 text-center">Jumlah Artikel</th>
                        <th class="p-4">Ditambahkan</th>
                        <th class="p-4 text-center pr-6">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    <?php if (empty($categories)): ?>
                    <tr>
                        <td colspan="5" class="p-8 text-center text-slate-400 font-medium text-sm">Belum ada kategori yang ditambahkan.</td>
                    </tr>
                    <?php endif; ?>
                    <?php foreach ($categories as $c): ?>
                    <tr class="hover:bg-slate-50/30 transition-colors">
                        <td class="p-4 pl-6">
                            <div class="font-bold text-slate-800"><?php echo htmlspecialchars($c->name); ?></div>
                            <div class="text-[11px] text-slate-400 font-medium">/<?php echo htmlspecialchars($c->slug); ?></div>
                        </td>
                        <td class="p-4 text-slate-500 text-xs max-w-xs truncate" title="<?php echo htmlspecialchars($c->description); ?>">
                            <?php echo htmlspecialchars($c->description ?: '-'); ?>
                        </td>
                        <td class="p-4 text-center">
                            <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold border border-primary-100 bg-primary-50 text-primary-600">
                                <?php echo $c->articles_count; ?> Artikel
                            </span>
                        </td>
                        <td class="p-4 text-xs font-medium text-slate-500">
                            <?php echo date('d M Y', strtotime($c->created_at)); ?>
                        </td>
                        <td class="p-4 text-center pr-6">
                            <div class="inline-flex items-center gap-2">
                                <button @click="
                                    currentCategory = { 
                                        id: '<?php echo $c->id; ?>', 
                                        name: '<?php echo addslashes($c->name); ?>', 
                                        slug: '<?php echo addslashes($c->slug); ?>', 
                                        description: '<?php echo addslashes($c->description); ?>' 
                                    }; 
                                    editModalOpen = true;
                                " class="p-2 rounded-xl bg-slate-50 border border-slate-100 text-slate-500 hover:text-primary-600 hover:bg-primary-50 hover:border-primary-100 transition-all duration-200 cursor-pointer">
                                    <i data-lucide="edit-3" class="w-4 h-4"></i>
                                </button>
                                <?php if ($c->articles_count == 0): ?>
                                    <a href="<?php echo PUBLIC_URL; ?>/admin/categories/delete?id=<?php echo $c->id; ?>" 
                                       onclick="return confirm('Apakah Anda yakin ingin menghapus kategori ini?');"
                                       class="p-2 rounded-xl bg-slate-50 border border-slate-100 text-slate-500 hover:text-rose-600 hover:bg-rose-50 hover:border-rose-100 transition-all duration-200 cursor-pointer">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </a>
                                <?php else: ?>
                                    <button class="p-2 rounded-xl bg-slate-50 border border-slate-100 text-slate-300 cursor-not-allowed" title="Tidak dapat menghapus kategori yang memiliki artikel.">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- CREATE MODAL -->
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" x-show="createModalOpen" x-transition x-cloak>
        <div class="bg-white rounded-3xl w-full max-w-lg shadow-2xl overflow-hidden border border-slate-100" @click.away="createModalOpen = false">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="font-display font-extrabold text-lg text-slate-800">Tambah Kategori</h3>
                    <p class="text-xs text-slate-500 font-medium">Buat label klasifikasi konten baru.</p>
                </div>
                <button @click="createModalOpen = false" class="p-2 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-400">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
            
            <form method="POST" action="<?php echo PUBLIC_URL; ?>/admin/categories/create" class="p-6 space-y-4">
                <?php echo \App\Helpers\Security::csrfField(); ?>
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-650 tracking-wide uppercase">Nama Kategori <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" placeholder="Misal: Teknologi" class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-primary-500/20" required />
                </div>
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between">
                        <label class="text-xs font-bold text-slate-650 tracking-wide uppercase">Slug URL</label>
                        <span class="text-[10px] text-slate-400 uppercase font-semibold">Kosongkan agar terisi otomatis</span>
                    </div>
                    <input type="text" name="slug" placeholder="teknologi" class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-primary-500/20" />
                </div>
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-650 tracking-wide uppercase">Deskripsi</label>
                    <textarea name="description" rows="3" placeholder="Deskripsi pendek tentang kategori ini" class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-primary-500/20"></textarea>
                </div>
                <div class="pt-4 flex items-center justify-end gap-3">
                    <button type="button" @click="createModalOpen = false" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-semibold text-xs hover:bg-slate-50">Batalkan</button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-primary-600 text-white font-bold text-xs shadow-md shadow-primary-500/10 hover:shadow-primary-500/20">Simpan Kategori</button>
                </div>
            </form>
        </div>
    </div>

    <!-- EDIT MODAL -->
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" x-show="editModalOpen" x-transition x-cloak>
        <div class="bg-white rounded-3xl w-full max-w-lg shadow-2xl overflow-hidden border border-slate-100" @click.away="editModalOpen = false">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="font-display font-extrabold text-lg text-slate-800">Edit Kategori</h3>
                    <p class="text-xs text-slate-500 font-medium">Perbarui rincian atau alamat slug kategori.</p>
                </div>
                <button @click="editModalOpen = false" class="p-2 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-400">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
            
            <form method="POST" action="<?php echo PUBLIC_URL; ?>/admin/categories/update" class="p-6 space-y-4">
                <?php echo \App\Helpers\Security::csrfField(); ?>
                <input type="hidden" name="id" :value="currentCategory.id" />
                
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-650 tracking-wide uppercase">Nama Kategori <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" x-model="currentCategory.name" class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-primary-500/20" required />
                </div>
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-650 tracking-wide uppercase">Slug URL</label>
                    <input type="text" name="slug" x-model="currentCategory.slug" class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-primary-500/20" />
                </div>
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-650 tracking-wide uppercase">Deskripsi</label>
                    <textarea name="description" x-model="currentCategory.description" rows="3" class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-primary-500/20"></textarea>
                </div>
                <div class="pt-4 flex items-center justify-end gap-3">
                    <button type="button" @click="editModalOpen = false" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-semibold text-xs hover:bg-slate-50">Batalkan</button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-primary-600 text-white font-bold text-xs shadow-md shadow-primary-500/10 hover:shadow-primary-500/20">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

</div>

<?php require_once __DIR__ . '/../layouts/admin_footer.php'; ?>
