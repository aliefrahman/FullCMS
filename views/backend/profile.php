<?php
/** @var \stdClass $user */

$title = "Profil Saya";
$activePage = "profile";
require_once __DIR__ . '/layouts/admin_header.php';
use App\Helpers\Session;

$error = Session::flash('error');
$success = Session::flash('success');
?>

<div class="space-y-6">

    <!-- Notification alerts -->
    <?php if ($error): ?>
        <div
            class="p-4 rounded-2xl bg-rose-50 border border-rose-100 text-rose-600 text-sm font-semibold flex items-center gap-2.5 shadow-sm animate-fade-in">
            <i data-lucide="alert-circle" class="w-5 h-5 text-rose-500 shrink-0 animate-bounce"></i>
            <span><?php echo htmlspecialchars($error); ?></span>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div
            class="p-4 rounded-2xl bg-emerald-50 border border-emerald-100 text-emerald-700 text-sm font-semibold flex items-center gap-2.5 shadow-sm animate-fade-in">
            <i data-lucide="check-circle" class="w-5 h-5 text-emerald-500 shrink-0"></i>
            <span><?php echo htmlspecialchars($success); ?></span>
        </div>
    <?php endif; ?>

    <!-- Split profile layouts -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">

        <!-- Left Panel: User Info card summary -->
        <div
            class="bg-white border border-slate-100 rounded-3xl shadow-sm p-6 flex flex-col items-center text-center space-y-6 lg:col-span-1 h-fit self-start">
            <div class="space-y-4 w-full flex flex-col items-center">
                <!-- Gorgeous dynamic gradient avatar with upload capability -->
                <form method="POST" action="<?php echo PUBLIC_URL; ?>/admin/profile/update-avatar"
                    enctype="multipart/form-data" class="relative group cursor-pointer flex flex-col items-center">
                    <?php echo \App\Helpers\Security::csrfField(); ?>
                    <input type="file" name="avatar" id="avatar-input" accept="image/*" class="hidden"
                        onchange="this.form.submit()">
                    <label for="avatar-input"
                        class="cursor-pointer block relative h-24 w-24 rounded-3xl overflow-hidden shadow-xl shadow-primary-500/10 border-4 border-white group">
                        <?php if (!empty($user->avatar) && file_exists(__DIR__ . '/../../public/uploads/avatars/' . $user->avatar)): ?>
                            <img src="<?php echo PUBLIC_URL; ?>/uploads/avatars/<?php echo $user->avatar; ?>"
                                class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105">
                        <?php else: ?>
                            <div
                                class="h-full w-full bg-linear-to-tr from-primary-600 to-accent-500 flex items-center justify-center text-white font-display font-extrabold text-3xl transition-transform duration-300 group-hover:scale-105">
                                <?php echo strtoupper(substr($user->username, 0, 2)); ?>
                            </div>
                        <?php endif; ?>

                        <!-- Overlay on Hover -->
                        <div
                            class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition-all duration-300">
                            <i data-lucide="camera" class="w-7 h-7 text-white animate-pulse"></i>
                        </div>
                    </label>
                    <!-- Decorative online indicator dot -->
                    <span
                        class="absolute bottom-0 right-0 h-5 w-5 bg-emerald-500 border-4 border-white rounded-full z-10"></span>
                </form>

                <div>
                    <h3 class="font-display font-extrabold text-xl text-slate-800 tracking-tight leading-tight">
                        <?php echo htmlspecialchars($user->full_name ?? '-'); ?>
                    </h3>
                    <p class="text-xs text-slate-400 font-semibold">@<?php echo htmlspecialchars($user->username); ?>
                    </p>
                </div>

                <!-- Custom Role Badge -->
                <?php
                $roleColors = [
                    'admin' => 'bg-rose-50 border-rose-100 text-rose-600',
                    'editor' => 'bg-indigo-50 border-indigo-100 text-indigo-600',
                    'author' => 'bg-amber-50 border-amber-100 text-amber-600',
                    'subscriber' => 'bg-slate-100 border-slate-200 text-slate-650',
                ];
                $c = $roleColors[$user->role] ?? 'bg-slate-100 text-slate-650';
                ?>
                <span
                    class="inline-flex px-3.5 py-1.5 rounded-full text-xs font-bold border capitalize <?php echo $c; ?>">
                    <i data-lucide="shield" class="w-3.5 h-3.5 mr-1 shrink-0"></i>
                    <?php echo htmlspecialchars($user->role); ?>
                </span>
            </div>

            <!-- Profile metadata -->
            <div class="w-full border-t border-slate-100 pt-6 space-y-3.5 text-left text-xs font-medium text-slate-500">
                <div class="flex items-center justify-between">
                    <span class="text-slate-400">Status Akun</span>
                    <?php if ($user->status === 'active'): ?>
                        <span
                            class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-100">
                            Aktif
                        </span>
                    <?php else: ?>
                        <span
                            class="inline-flex items-center gap-1.5 text-xs font-semibold text-rose-600 bg-rose-50 px-2 py-0.5 rounded-full border border-rose-100">
                            Nonaktif
                        </span>
                    <?php endif; ?>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-400">Email Utama</span>
                    <span class="text-slate-700 font-semibold truncate max-w-[150px]"
                        title="<?php echo htmlspecialchars($user->email); ?>">
                        <?php echo htmlspecialchars($user->email); ?>
                    </span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-slate-400">Tanggal Gabung</span>
                    <span
                        class="text-slate-700 font-semibold"><?php echo date('d F Y', strtotime($user->created_at)); ?></span>
                </div>
            </div>
        </div>

        <!-- Right Panel: Modification Forms -->
        <div class="lg:col-span-2 space-y-8">

            <!-- Card 1: General Profile settings -->
            <div class="bg-white border border-slate-100 rounded-3xl shadow-sm p-6 md:p-8 space-y-6">
                <div>
                    <h4 class="font-display font-bold text-lg text-slate-800">Informasi Pribadi</h4>
                    <p class="text-xs text-slate-500 font-medium">Perbarui detail profil dasar dan alamat surel Anda</p>
                </div>

                <form method="POST" action="<?php echo PUBLIC_URL; ?>/admin/profile/update" class="space-y-4">
                    <?php echo \App\Helpers\Security::csrfField(); ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-600 tracking-wide uppercase">Nama Lengkap</label>
                            <div
                                class="flex items-center border border-slate-200 rounded-xl px-3.5 py-2.5 gap-2.5 bg-slate-50/50 focus-within:ring-2 focus-within:ring-primary-500/20 focus-within:border-primary-500 transition-all duration-200">
                                <i data-lucide="user" class="w-4 h-4 text-slate-400"></i>
                                <input type="text" name="full_name"
                                    value="<?php echo htmlspecialchars($user->full_name); ?>" placeholder="John Doe"
                                    class="w-full bg-transparent text-sm text-slate-800 placeholder-slate-400 focus:outline-none"
                                    required />
                            </div>
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-600 tracking-wide uppercase">Username</label>
                            <div
                                class="flex items-center border border-slate-200 rounded-xl px-3.5 py-2.5 gap-2.5 bg-slate-50/50 focus-within:ring-2 focus-within:ring-primary-500/20 focus-within:border-primary-500 transition-all duration-200">
                                <i data-lucide="at-sign" class="w-4 h-4 text-slate-400"></i>
                                <input type="text" name="username"
                                    value="<?php echo htmlspecialchars($user->username); ?>" placeholder="johndoe"
                                    class="w-full bg-transparent text-sm text-slate-800 placeholder-slate-400 focus:outline-none"
                                    required />
                            </div>
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-600 tracking-wide uppercase">Alamat Email</label>
                        <div
                            class="flex items-center border border-slate-200 rounded-xl px-3.5 py-2.5 gap-2.5 bg-slate-50/50 focus-within:ring-2 focus-within:ring-primary-500/20 focus-within:border-primary-500 transition-all duration-200">
                            <i data-lucide="mail" class="w-4 h-4 text-slate-400"></i>
                            <input type="email" name="email" value="<?php echo htmlspecialchars($user->email); ?>"
                                placeholder="john@example.com"
                                class="w-full bg-transparent text-sm text-slate-800 placeholder-slate-400 focus:outline-none"
                                required />
                        </div>
                    </div>

                    <div class="pt-2 flex justify-end">
                        <button type="submit"
                            class="px-5 py-3 rounded-xl bg-primary-600 text-white font-bold text-xs tracking-wide shadow-md shadow-primary-500/10 hover:shadow-primary-500/20 hover:-translate-y-0.5 transition-all duration-200 cursor-pointer">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>

            <!-- Card 2: Security settings (Password changing) -->
            <div class="bg-white border border-slate-100 rounded-3xl shadow-sm p-6 md:p-8 space-y-6">
                <div>
                    <h4 class="font-display font-bold text-lg text-slate-800">Ubah Kata Sandi</h4>
                    <p class="text-xs text-slate-500 font-medium">Perbarui kredensial keamanan akun Anda untuk
                        melindungi akun</p>
                </div>

                <form method="POST" action="<?php echo PUBLIC_URL; ?>/admin/profile/password" class="space-y-4">
                    <?php echo \App\Helpers\Security::csrfField(); ?>
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-650 tracking-wide uppercase">Kata Sandi Saat
                            Ini</label>
                        <div
                            class="flex items-center border border-slate-200 rounded-xl px-3.5 py-2.5 gap-2.5 bg-slate-50/50 focus-within:ring-2 focus-within:ring-primary-500/20 focus-within:border-primary-500 transition-all duration-200">
                            <i data-lucide="key" class="w-4 h-4 text-slate-400"></i>
                            <input type="password" name="current_password" placeholder="Masukkan password lama"
                                class="w-full bg-transparent text-sm text-slate-800 placeholder-slate-400 focus:outline-none"
                                required />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-650 tracking-wide uppercase">Kata Sandi
                                Baru</label>
                            <div
                                class="flex items-center border border-slate-200 rounded-xl px-3.5 py-2.5 gap-2.5 bg-slate-50/50 focus-within:ring-2 focus-within:ring-primary-500/20 focus-within:border-primary-500 transition-all duration-200">
                                <i data-lucide="lock" class="w-4 h-4 text-slate-400"></i>
                                <input type="password" name="new_password" placeholder="Min. 8 Karakter"
                                    class="w-full bg-transparent text-sm text-slate-800 placeholder-slate-400 focus:outline-none"
                                    required />
                            </div>
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-slate-650 tracking-wide uppercase">Konfirmasi Sandi
                                Baru</label>
                            <div
                                class="flex items-center border border-slate-200 rounded-xl px-3.5 py-2.5 gap-2.5 bg-slate-50/50 focus-within:ring-2 focus-within:ring-primary-500/20 focus-within:border-primary-500 transition-all duration-200">
                                <i data-lucide="shield-check" class="w-4 h-4 text-slate-400"></i>
                                <input type="password" name="confirm_password" placeholder="Ketik ulang password baru"
                                    class="w-full bg-transparent text-sm text-slate-800 placeholder-slate-400 focus:outline-none"
                                    required />
                            </div>
                        </div>
                    </div>

                    <div class="pt-2 flex justify-end">
                        <button type="submit"
                            class="px-5 py-3 rounded-xl bg-slate-900 text-white font-bold text-xs tracking-wide shadow-md hover:bg-slate-800 hover:-translate-y-0.5 transition-all duration-200 cursor-pointer">
                            Perbarui Kata Sandi
                        </button>
                    </div>
                </form>
            </div>

        </div>

    </div>

</div>

<?php require_once __DIR__ . '/layouts/admin_footer.php'; ?>