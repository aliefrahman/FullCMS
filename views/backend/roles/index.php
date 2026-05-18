<?php 
$title = "Hak Akses (Roles)";
$activePage = "roles";
require_once __DIR__ . '/../layouts/admin_header.php'; 
use App\Helpers\Session;

$success = Session::flash('success');
?>

<!-- Alert Success notification -->
<?php if ($success): ?>
    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-100 text-emerald-700 text-sm font-semibold flex items-center gap-2.5 shadow-sm animate-fade-in">
        <i data-lucide="check-circle" class="w-5 h-5 text-emerald-500 shrink-0 animate-bounce"></i>
        <span><?php echo htmlspecialchars($success); ?></span>
    </div>
<?php endif; ?>

<!-- Section Introduction -->
<div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm flex items-center justify-between flex-wrap gap-4">
    <div>
        <h3 class="font-display font-extrabold text-xl text-slate-800 tracking-tight">Matriks Hak Akses & Kewenangan</h3>
        <p class="text-xs text-slate-500 font-medium">Konfigurasikan kewenangan dan akses fitur masing-masing tingkat pengguna (Roles) pada NexusCMS</p>
    </div>
</div>

<!-- Role Cards Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
    <?php foreach ($rolesData as $roleKey => $r): ?>
    <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm hover:shadow-md transition-all duration-300 space-y-4">
        <div class="flex items-center justify-between">
            <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-bold border capitalize <?php echo $r['color']; ?>">
                <?php echo htmlspecialchars($r['name']); ?>
            </span>
            <div class="h-9 w-9 rounded-xl bg-slate-50 text-slate-600 font-display font-bold flex items-center justify-center text-xs">
                <?php echo $r['count']; ?> User
            </div>
        </div>
        <p class="text-xs text-slate-500 leading-relaxed min-h-[50px]"><?php echo htmlspecialchars($r['description']); ?></p>
    </div>
    <?php endforeach; ?>
</div>

<!-- Role Permission Matrix Form -->
<form method="POST" action="<?php echo PUBLIC_URL; ?>/admin/roles/update" class="bg-white border border-slate-100 rounded-3xl shadow-sm overflow-hidden p-6 md:p-8 space-y-8">
    <?php echo \App\Helpers\Security::csrfField(); ?>
    <div class="flex items-center justify-between flex-wrap gap-4">
        <div>
            <h4 class="font-display font-bold text-lg text-slate-800">Matriks Izin Modul (Permission Mapping)</h4>
            <p class="text-xs text-slate-500 font-medium">Beri centang pada kolom role untuk mengaktifkan izin modul terkait</p>
        </div>
        <button type="submit" class="px-5 py-3 rounded-xl bg-primary-600 text-white font-bold text-sm tracking-wide shadow-md shadow-primary-500/10 hover:shadow-primary-500/20 hover:-translate-y-0.5 transition-all duration-200 flex items-center gap-2 cursor-pointer">
            <i data-lucide="save" class="w-4 h-4"></i> Simpan Pengaturan Hak Akses
        </button>
    </div>

    <!-- Matrix Table -->
    <div class="overflow-x-auto rounded-2xl border border-slate-100">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50/50 border-b border-slate-100 text-xs font-bold text-slate-600 uppercase tracking-wider">
                    <th class="p-4 pl-6 w-2/5">Kewenangan / Izin Fitur</th>
                    <th class="p-4 text-center">Subscriber</th>
                    <th class="p-4 text-center">Author</th>
                    <th class="p-4 text-center">Editor</th>
                    <th class="p-4 text-center">Administrator</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-sm">
                <?php 
                $allPermissions = [
                    'all_access' => [
                        'title' => 'Kontrol Sistem Penuh (All Access)',
                        'desc' => 'Akses mutlak bypass semua gerbang keamanan.'
                    ],
                    'manage_users' => [
                        'title' => 'Manajemen Pengguna (User CRUD)',
                        'desc' => 'Kemampuan membuat, mengedit, dan menghapus akun user.'
                    ],
                    'manage_roles' => [
                        'title' => 'Manajemen Hak Akses & Matriks Izin',
                        'desc' => 'Kemampuan mengatur dan menyimpan matriks kewenangan ini.'
                    ],
                    'publish_articles' => [
                        'title' => 'Publikasi Artikel (Publish Content)',
                        'desc' => 'Kemampuan mengubah status draf artikel menjadi terbit di web frontend.'
                    ],
                    'edit_articles' => [
                        'title' => 'Ubah Artikel Pengguna Lain',
                        'desc' => 'Kemampuan mengoreksi, mengedit, atau menghapus artikel author lain.'
                    ],
                    'create_articles' => [
                        'title' => 'Buat & Edit Artikel Sendiri',
                        'desc' => 'Menulis kontribusi konten baru dan menyunting draf tulisan buatan sendiri.'
                    ],
                    'read_articles' => [
                        'title' => 'Baca Artikel Terpublikasi',
                        'desc' => 'Mengakses, melihat, dan membaca semua artikel yang terbit di situs utama.'
                    ]
                ];
                $rolesKeys = ['subscriber', 'author', 'editor', 'admin'];
                ?>
                
                <?php foreach ($allPermissions as $permKey => $permInfo): ?>
                <tr class="hover:bg-slate-50/20 transition-colors">
                    <td class="p-4 pl-6">
                        <div class="font-bold text-slate-800"><?php echo $permInfo['title']; ?></div>
                        <div class="text-[11px] text-slate-400 font-medium"><?php echo $permInfo['desc']; ?></div>
                    </td>
                    <?php foreach ($rolesKeys as $rKey): ?>
                    <td class="p-4 text-center">
                        <?php 
                        $isChecked = in_array($permKey, $rolesData[$rKey]['permissions']);
                        // Disable admin checkboxes to prevent lockout, and disable all_access for non-admins
                        $isDisabled = ($rKey === 'admin') || ($permKey === 'all_access' && $rKey !== 'admin');
                        
                        $inputClass = $isDisabled 
                            ? ($isChecked ? 'h-4.5 w-4.5 rounded border-slate-300 text-primary-600 focus:ring-0 cursor-not-allowed' : 'h-4.5 w-4.5 rounded border-slate-200 text-primary-500/50 focus:ring-0 cursor-not-allowed bg-slate-100')
                            : 'h-4.5 w-4.5 rounded border-slate-200 text-primary-600 focus:ring-primary-500/20';
                        ?>
                        <input type="checkbox" 
                            name="permissions[<?php echo $rKey; ?>][]" 
                            value="<?php echo $permKey; ?>" 
                            class="<?php echo $inputClass; ?>"
                            <?php echo $isChecked ? 'checked' : ''; ?>
                            <?php echo $isDisabled ? 'disabled' : ''; ?>
                        />
                    </td>
                    <?php endforeach; ?>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Security advisory -->
    <div class="p-4 rounded-2xl bg-amber-50 border border-amber-100 text-amber-800 text-xs font-semibold flex items-center gap-2.5">
        <i data-lucide="shield-alert" class="w-4 h-4 text-amber-500 shrink-0"></i>
        <span>Kebijakan keamanan sistem: Izin untuk akun tingkat **Administrator** bersifat paten dan tidak dapat dinonaktifkan demi mencegah penguncian sistem secara tidak disengaja.</span>
    </div>
</form>

<?php require_once __DIR__ . '/../layouts/admin_footer.php'; ?>
