<?php 
$title = "Manajemen User";
$activePage = "users";
require_once __DIR__ . '/../layouts/admin_header.php'; 
use App\Helpers\Session;

$error = Session::flash('error');
$success = Session::flash('success');
?>

<div x-data="{ 
    createModalOpen: false, 
    editModalOpen: false,
    currentUser: { id: '', full_name: '', username: '', email: '', role: 'subscriber', status: 'active' }
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
            <h3 class="font-display font-extrabold text-xl text-slate-800 tracking-tight">Daftar Pengguna Sistem</h3>
            <p class="text-xs text-slate-500 font-medium">Buat, edit, perbarui status, atau hapus user terdaftar dari database cms_db</p>
        </div>
        <button @click="createModalOpen = true" class="px-5 py-3 rounded-xl bg-primary-600 text-white font-bold text-sm tracking-wide shadow-md shadow-primary-500/10 hover:shadow-primary-500/20 hover:-translate-y-0.5 transition-all duration-200 flex items-center gap-2 cursor-pointer">
            <i data-lucide="user-plus" class="w-4 h-4"></i> Tambah User Baru
        </button>
    </div>

    <!-- Users table grid -->
    <div class="bg-white border border-slate-100 rounded-3xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100 text-xs font-bold text-slate-600 uppercase tracking-wider">
                        <th class="p-4 pl-6">Profil Pengguna</th>
                        <th class="p-4">Username</th>
                        <th class="p-4">Alamat Email</th>
                        <th class="p-4">Hak Akses (Role)</th>
                        <th class="p-4">Status</th>
                        <th class="p-4 text-center pr-6">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    <?php foreach ($users as $u): ?>
                    <tr class="hover:bg-slate-50/30 transition-colors">
                        <!-- Profile/Name -->
                        <td class="p-4 pl-6 flex items-center gap-3">
                            <div class="h-10 w-10 rounded-xl bg-linear-to-tr from-slate-100 to-slate-200 text-slate-700 font-display font-bold flex items-center justify-center shadow-inner">
                                <?php echo strtoupper(substr($u->username, 0, 2)); ?>
                            </div>
                            <div>
                                <div class="font-bold text-slate-800 leading-tight"><?php echo htmlspecialchars($u->full_name ?? '-'); ?></div>
                                <div class="text-[11px] text-slate-400 font-medium">Terdaftar: <?php echo date('d M Y', strtotime($u->created_at)); ?></div>
                            </div>
                        </td>
                        <!-- Username -->
                        <td class="p-4 font-semibold text-slate-700">
                            @<?php echo htmlspecialchars($u->username); ?>
                        </td>
                        <!-- Email -->
                        <td class="p-4 text-slate-650 font-medium">
                            <?php echo htmlspecialchars($u->email); ?>
                        </td>
                        <!-- Role -->
                        <td class="p-4">
                            <?php 
                            $roleColors = [
                                'admin' => 'bg-rose-50 border-rose-100 text-rose-600',
                                'editor' => 'bg-indigo-50 border-indigo-100 text-indigo-600',
                                'author' => 'bg-amber-50 border-amber-100 text-amber-600',
                                'subscriber' => 'bg-slate-100 border-slate-200 text-slate-600',
                            ];
                            $c = $roleColors[$u->role] ?? 'bg-slate-100 text-slate-605';
                            ?>
                            <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-bold border capitalize <?php echo $c; ?>">
                                <?php echo htmlspecialchars($u->role); ?>
                            </span>
                        </td>
                        <!-- Status -->
                        <td class="p-4">
                            <?php if ($u->status === 'active'): ?>
                                <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-100">
                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> Aktif
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-rose-600 bg-rose-50 px-2.5 py-1 rounded-full border border-rose-100">
                                    <span class="h-1.5 w-1.5 rounded-full bg-rose-500"></span> Nonaktif
                                </span>
                            <?php endif; ?>
                        </td>
                        <!-- Actions -->
                        <td class="p-4 text-center pr-6">
                            <div class="inline-flex items-center gap-2">
                                <button @click="
                                    currentUser = { 
                                        id: '<?php echo $u->id; ?>', 
                                        full_name: '<?php echo addslashes($u->full_name); ?>', 
                                        username: '<?php echo addslashes($u->username); ?>', 
                                        email: '<?php echo addslashes($u->email); ?>', 
                                        role: '<?php echo $u->role; ?>', 
                                        status: '<?php echo $u->status; ?>' 
                                    }; 
                                    editModalOpen = true;
                                " class="p-2 rounded-xl bg-slate-50 border border-slate-100 text-slate-500 hover:text-primary-600 hover:bg-primary-50 hover:border-primary-100 transition-all duration-200 cursor-pointer">
                                    <i data-lucide="edit-3" class="w-4 h-4"></i>
                                </button>
                                <?php if ($u->id !== intval(Session::get('user_id'))): ?>
                                    <a href="<?php echo PUBLIC_URL; ?>/admin/users/delete?id=<?php echo $u->id; ?>" 
                                       onclick="return confirm('Apakah Anda yakin ingin menghapus user ini dari sistem database secara permanen?');"
                                       class="p-2 rounded-xl bg-slate-50 border border-slate-100 text-slate-500 hover:text-rose-600 hover:bg-rose-50 hover:border-rose-100 transition-all duration-200 cursor-pointer">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </a>
                                <?php else: ?>
                                    <button class="p-2 rounded-xl bg-slate-50 border border-slate-100 text-slate-300 cursor-not-allowed" title="Anda tidak dapat menghapus akun sendiri.">
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

    <!-- CREATE USER MODAL -->
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" x-show="createModalOpen" x-transition x-cloak>
        <div class="bg-white rounded-3xl w-full max-w-lg shadow-2xl overflow-hidden border border-slate-100" @click.away="createModalOpen = false">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="font-display font-extrabold text-lg text-slate-800">Tambah Akun Baru</h3>
                    <p class="text-xs text-slate-500 font-medium">Buat profil user baru dan tentukan hak aksesnya.</p>
                </div>
                <button @click="createModalOpen = false" class="p-2 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-400">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
            
            <form method="POST" action="<?php echo PUBLIC_URL; ?>/admin/users/create" class="p-6 space-y-4">
                <?php echo \App\Helpers\Security::csrfField(); ?>
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-650 tracking-wide uppercase">Nama Lengkap</label>
                        <input type="text" name="full_name" placeholder="John Doe" class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-primary-500/20" required />
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-655 tracking-wide uppercase">Username</label>
                        <input type="text" name="username" placeholder="johndoe" class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-primary-500/20" required />
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-650 tracking-wide uppercase">Alamat Email</label>
                    <input type="email" name="email" placeholder="john@example.com" class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-primary-500/20" required />
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-650 tracking-wide uppercase">Kata Sandi</label>
                    <input type="password" name="password" placeholder="••••••••" class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-primary-500/20" required />
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-650 tracking-wide uppercase">Pilih Hak Akses (Role)</label>
                        <select name="role" class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm text-slate-800 focus:outline-none cursor-pointer">
                            <option value="subscriber">Subscriber</option>
                            <option value="author">Author</option>
                            <option value="editor">Editor</option>
                            <option value="admin">Administrator</option>
                        </select>
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-650 tracking-wide uppercase">Status Akun</label>
                        <select name="status" class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm text-slate-800 focus:outline-none cursor-pointer">
                            <option value="active">Aktif</option>
                            <option value="inactive">Nonaktif</option>
                        </select>
                    </div>
                </div>

                <div class="pt-4 flex items-center justify-end gap-3">
                    <button type="button" @click="createModalOpen = false" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-semibold text-xs hover:bg-slate-50">Batalkan</button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-primary-600 text-white font-bold text-xs shadow-md shadow-primary-500/10 hover:shadow-primary-500/20">Simpan User</button>
                </div>
            </form>
        </div>
    </div>

    <!-- EDIT USER MODAL -->
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm" x-show="editModalOpen" x-transition x-cloak>
        <div class="bg-white rounded-3xl w-full max-w-lg shadow-2xl overflow-hidden border border-slate-100" @click.away="editModalOpen = false">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="font-display font-extrabold text-lg text-slate-800">Edit Data Pengguna</h3>
                    <p class="text-xs text-slate-500 font-medium">Ubah profil atau perbarui kata sandi user terpilih.</p>
                </div>
                <button @click="editModalOpen = false" class="p-2 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-400">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
            
            <form method="POST" action="<?php echo PUBLIC_URL; ?>/admin/users/update" class="p-6 space-y-4">
                <?php echo \App\Helpers\Security::csrfField(); ?>
                <!-- Hidden inputs -->
                <input type="hidden" name="id" :value="currentUser.id" />

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-650 tracking-wide uppercase">Nama Lengkap</label>
                        <input type="text" name="full_name" :value="currentUser.full_name" @input="currentUser.full_name = $event.target.value" placeholder="John Doe" class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-primary-500/20" required />
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-650 tracking-wide uppercase">Username</label>
                        <input type="text" name="username" :value="currentUser.username" @input="currentUser.username = $event.target.value" placeholder="johndoe" class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-primary-500/20" required />
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-650 tracking-wide uppercase">Alamat Email</label>
                    <input type="email" name="email" :value="currentUser.email" @input="currentUser.email = $event.target.value" placeholder="john@example.com" class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-primary-500/20" required />
                </div>

                <div class="space-y-1.5">
                    <div class="flex items-center justify-between">
                        <label class="text-xs font-bold text-slate-650 tracking-wide uppercase">Ubah Kata Sandi</label>
                        <span class="text-[10px] text-slate-400 font-semibold uppercase">*Kosongkan jika tidak ingin diubah</span>
                    </div>
                    <input type="password" name="password" placeholder="••••••••" class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-primary-500/20" />
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-650 tracking-wide uppercase">Pilih Hak Akses (Role)</label>
                        <select name="role" x-model="currentUser.role" class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm text-slate-800 focus:outline-none cursor-pointer">
                            <option value="subscriber">Subscriber</option>
                            <option value="author">Author</option>
                            <option value="editor">Editor</option>
                            <option value="admin">Administrator</option>
                        </select>
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-650 tracking-wide uppercase">Status Akun</label>
                        <select name="status" x-model="currentUser.status" class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm text-slate-800 focus:outline-none cursor-pointer">
                            <option value="active">Aktif</option>
                            <option value="inactive">Nonaktif</option>
                        </select>
                    </div>
                </div>

                <div class="pt-4 flex items-center justify-end gap-3">
                    <button type="button" @click="editModalOpen = false" class="px-5 py-2.5 rounded-xl border border-slate-200 text-slate-600 font-semibold text-xs hover:bg-slate-50">Batalkan</button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-primary-600 text-white font-bold text-xs shadow-md shadow-primary-500/10 hover:shadow-primary-500/20">Perbarui User</button>
                </div>
            </form>
        </div>
    </div>

</div>

<?php require_once __DIR__ . '/../layouts/admin_footer.php'; ?>
