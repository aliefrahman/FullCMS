<?php
require_once __DIR__ . '/../layouts/header.php';
use App\Helpers\Session;

// Fetch any pending flash alerts from the Session helper
$error = Session::flash('error');
$success = Session::flash('success');
?>

<!-- Right Panel - Authentication Forms -->
<div class="w-full lg:w-1/2 xl:w-2/5 flex flex-col justify-center p-8 sm:p-12 md:p-16 relative bg-white min-h-screen lg:min-h-0 animate-fade-in"
    x-data="{ mode: 'login' }">

    <!-- Decorative Ambient light on Right Panel -->
    <div class="absolute top-0 right-0 w-32 h-32 bg-primary-100/50 rounded-full blur-2xl pointer-events-none"></div>
    <div class="absolute bottom-0 left-0 w-32 h-32 bg-accent-100/30 rounded-full blur-2xl pointer-events-none"></div>

    <div class="max-w-md w-full mx-auto space-y-8 relative z-10">

        <!-- Mobile Logo (Visible only on mobile devices) -->
        <div class="lg:hidden flex items-center justify-center gap-2.5 mb-8">
            <div
                class="w-10 h-10 rounded-xl bg-primary-gradient flex items-center justify-center text-white shadow-md shadow-primary-500/20">
                <i data-lucide="newspaper" class="w-5 h-5"></i>
            </div>
            <span class="font-display text-xl font-bold tracking-tight text-slate-900">Nexus<span
                    class="text-primary-500">CMS</span></span>
        </div>

        <!-- Section Title -->
        <div class="text-center lg:text-left space-y-2">
            <h2 class="font-display font-extrabold text-3xl text-slate-900 tracking-tight"
                x-text="mode === 'login' ? 'Selamat Datang Kembali' : 'Buat Akun Baru'">
                Selamat Datang Kembali
            </h2>
            <p class="text-slate-500 text-sm"
                x-text="mode === 'login' ? 'Masukkan kredensial Anda untuk mengakses panel admin.' : 'Lengkapi formulir untuk mendaftarkan akun admin baru.'">
                Masukkan kredensial Anda untuk mengakses panel admin.
            </p>
        </div>

        <!-- Notification Alerts (PHP Flash Session) -->
        <?php if ($error): ?>
            <div
                class="p-4 rounded-xl bg-rose-50 border border-rose-100 text-rose-600 text-xs font-semibold flex items-center gap-2.5 shadow-sm animate-fade-in">
                <i data-lucide="alert-circle" class="w-4 h-4 text-rose-500 shrink-0 animate-bounce"></i>
                <span><?php echo htmlspecialchars($error); ?></span>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div
                class="p-4 rounded-xl bg-emerald-50 border border-emerald-100 text-emerald-600 text-xs font-semibold flex items-center gap-2.5 shadow-sm animate-fade-in">
                <i data-lucide="check-circle" class="w-4 h-4 text-emerald-500 shrink-0"></i>
                <span><?php echo htmlspecialchars($success); ?></span>
            </div>
        <?php endif; ?>

        <!-- Mode Toggle Switch (Alpine.js) -->
        <div class="p-1 rounded-xl bg-slate-100 flex items-center gap-1">
            <button @click="mode = 'login'"
                :class="mode === 'login' ? 'bg-white text-slate-800 shadow-sm' : 'text-slate-500 hover:text-slate-800'"
                class="flex-1 py-2 rounded-lg text-xs font-bold transition-all duration-200">
                Masuk
            </button>
            <button @click="mode = 'register'"
                :class="mode === 'register' ? 'bg-white text-slate-800 shadow-sm' : 'text-slate-500 hover:text-slate-800'"
                class="flex-1 py-2 rounded-lg text-xs font-bold transition-all duration-200">
                Daftar
            </button>
        </div>

        <!-- LOGIN FORM -->
        <form x-show="mode === 'login'" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
            method="POST" action="<?php echo PUBLIC_URL; ?>/auth/login" class="space-y-5">
            <?php echo \App\Helpers\Security::csrfField(); ?>
            <!-- Email / Username Field -->
            <div class="space-y-1.5">
                <label class="text-xs font-bold text-slate-600 tracking-wide uppercase">Email atau Username</label>
                <div
                    class="flex items-center border border-slate-200 rounded-xl px-3.5 py-3 gap-2.5 input-focus-ring bg-slate-50/50 transition-all duration-200">
                    <i data-lucide="mail" class="w-4 h-4 text-slate-400"></i>
                    <input type="text" name="identity" placeholder="admin@example.com atau username"
                        class="w-full bg-transparent text-sm text-slate-800 placeholder-slate-400 focus:outline-none"
                        required />
                </div>
            </div>

            <!-- Password Field -->
            <div class="space-y-1.5" x-data="{ show: false }">
                <div class="flex items-center justify-between">
                    <label class="text-xs font-bold text-slate-600 tracking-wide uppercase">Kata Sandi</label>
                    <a href="#" class="text-xs font-bold text-primary-600 hover:text-primary-700 transition-colors">Lupa
                        Password?</a>
                </div>
                <div
                    class="flex items-center border border-slate-200 rounded-xl px-3.5 py-3 gap-2.5 input-focus-ring bg-slate-50/50 transition-all duration-200">
                    <i data-lucide="lock" class="w-4 h-4 text-slate-400"></i>
                    <input :type="show ? 'text' : 'password'" name="password" placeholder="••••••••"
                        class="w-full bg-transparent text-sm text-slate-800 placeholder-slate-400 focus:outline-none"
                        required />
                    <button type="button" @click="show = !show" class="text-slate-400 hover:text-slate-600">
                        <i x-show="!show" data-lucide="eye" class="w-4 h-4"></i>
                        <i x-show="show" data-lucide="eye-off" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>

            <!-- Remember Me checkbox -->
            <div class="flex items-center gap-2.5 py-1">
                <input type="checkbox" id="remember"
                    class="w-4.5 h-4.5 rounded border-slate-200 text-primary-600 focus:ring-primary-500/20" />
                <label for="remember"
                    class="text-xs font-semibold text-slate-600 cursor-pointer selection:bg-transparent select-none">Ingat
                    saya di perangkat ini</label>
            </div>

            <!-- Submit Button -->
            <button type="submit"
                class="w-full py-3.5 rounded-xl font-bold text-white btn-primary text-sm tracking-wide shadow-lg flex items-center justify-center gap-2 cursor-pointer">
                Masuk ke Dashboard <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </button>
        </form>

        <!-- REGISTRATION FORM -->
        <form x-show="mode === 'register'" x-cloak x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
            method="POST" action="<?php echo PUBLIC_URL; ?>/auth/register" class="space-y-5">
            <?php echo \App\Helpers\Security::csrfField(); ?>
            <!-- Full Name Field -->
            <div class="space-y-1.5">
                <label class="text-xs font-bold text-slate-600 tracking-wide uppercase">Nama Lengkap</label>
                <div
                    class="flex items-center border border-slate-200 rounded-xl px-3.5 py-3 gap-2.5 input-focus-ring bg-slate-50/50 transition-all duration-200">
                    <i data-lucide="user" class="w-4 h-4 text-slate-400"></i>
                    <input type="text" name="full_name" placeholder="John Doe"
                        class="w-full bg-transparent text-sm text-slate-800 placeholder-slate-400 focus:outline-none"
                        required />
                </div>
            </div>

            <!-- Username Field -->
            <div class="space-y-1.5">
                <label class="text-xs font-bold text-slate-600 tracking-wide uppercase">Username</label>
                <div
                    class="flex items-center border border-slate-200 rounded-xl px-3.5 py-3 gap-2.5 input-focus-ring bg-slate-50/50 transition-all duration-200">
                    <i data-lucide="at-sign" class="w-4 h-4 text-slate-400"></i>
                    <input type="text" name="username" placeholder="johndoe"
                        class="w-full bg-transparent text-sm text-slate-800 placeholder-slate-400 focus:outline-none"
                        required />
                </div>
            </div>

            <!-- Email Field -->
            <div class="space-y-1.5">
                <label class="text-xs font-bold text-slate-600 tracking-wide uppercase">Alamat Email</label>
                <div
                    class="flex items-center border border-slate-200 rounded-xl px-3.5 py-3 gap-2.5 input-focus-ring bg-slate-50/50 transition-all duration-200">
                    <i data-lucide="mail" class="w-4 h-4 text-slate-400"></i>
                    <input type="email" name="email" placeholder="admin@example.com"
                        class="w-full bg-transparent text-sm text-slate-800 placeholder-slate-400 focus:outline-none"
                        required />
                </div>
            </div>

            <!-- Password Field -->
            <div class="space-y-1.5" x-data="{ show: false }">
                <label class="text-xs font-bold text-slate-600 tracking-wide uppercase">Kata Sandi Baru</label>
                <div
                    class="flex items-center border border-slate-200 rounded-xl px-3.5 py-3 gap-2.5 input-focus-ring bg-slate-50/50 transition-all duration-200">
                    <i data-lucide="lock" class="w-4 h-4 text-slate-400"></i>
                    <input :type="show ? 'text' : 'password'" name="password"
                        placeholder="Min. 8 Karakter & Min. 1 Simbol ($, #, !,@, %)"
                        class="w-full bg-transparent text-sm text-slate-800 placeholder-slate-400 focus:outline-none"
                        required />
                    <button type="button" @click="show = !show" class="text-slate-400 hover:text-slate-600">
                        <i x-show="!show" data-lucide="eye" class="w-4 h-4"></i>
                        <i x-show="show" data-lucide="eye-off" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>

            <!-- Confirm Password Field -->
            <div class="space-y-1.5" x-data="{ show: false }">
                <label class="text-xs font-bold text-slate-600 tracking-wide uppercase">Ulangi Kata Sandi Baru</label>
                <div
                    class="flex items-center border border-slate-200 rounded-xl px-3.5 py-3 gap-2.5 input-focus-ring bg-slate-50/50 transition-all duration-200">
                    <i data-lucide="lock-keyhole" class="w-4 h-4 text-slate-400"></i>
                    <input :type="show ? 'text' : 'password'" name="confirm_password" placeholder="Ulangi Kata Sandi"
                        class="w-full bg-transparent text-sm text-slate-800 placeholder-slate-400 focus:outline-none"
                        required />
                    <button type="button" @click="show = !show" class="text-slate-400 hover:text-slate-600">
                        <i x-show="!show" data-lucide="eye" class="w-4 h-4"></i>
                        <i x-show="show" data-lucide="eye-off" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>


            <!-- Terms & Conditions checkbox -->
            <div class="flex items-start gap-2.5 py-1">
                <input type="checkbox" id="terms"
                    class="mt-0.5 w-4.5 h-4.5 rounded border-slate-200 text-primary-600 focus:ring-primary-500/20"
                    required />
                <label for="terms" class="text-xs font-semibold text-slate-600 cursor-pointer select-none">Saya
                    menyetujui semua <a href="#" class="text-primary-600 hover:underline">Syarat & Ketentuan</a> serta
                    <a href="#" class="text-primary-600 hover:underline">Kebijakan Privasi</a>.</label>
            </div>

            <!-- Submit Button -->
            <button type="submit"
                class="w-full py-3.5 rounded-xl font-bold text-white btn-primary text-sm tracking-wide shadow-lg flex items-center justify-center gap-2 cursor-pointer">
                Daftar & Masuk <i data-lucide="user-plus" class="w-4 h-4"></i>
            </button>
        </form>

        <!-- Divider -->
        <div class="relative flex items-center justify-center py-2">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-slate-100"></div>
            </div>
            <span class="relative z-10 px-4 text-xs font-bold text-slate-400 bg-white uppercase tracking-wider">Atau
                masuk dengan</span>
        </div>

        <!-- Social Sign-in Buttons -->
        <div class="flex justify-center">
            <button
                class="social-btn w-full py-3 rounded-xl flex items-center justify-center gap-2 text-xs font-bold text-slate-700 cursor-pointer">
                <svg class="w-4 h-4" viewBox="0 0 24 24">
                    <path fill="#EA4335"
                        d="M12 5.04c1.62 0 3.08.56 4.22 1.65l3.15-3.15C17.45 1.77 14.94 1 12 1 7.35 1 3.4 3.65 1.48 7.5l3.77 2.92C6.18 7.37 8.87 5.04 12 5.04z" />
                    <path fill="#4285F4"
                        d="M23.49 12.27c0-.81-.07-1.59-.2-2.36H12v4.51h6.46c-.28 1.46-1.1 2.69-2.34 3.52l3.62 2.81c2.12-1.95 3.75-4.83 3.75-8.48z" />
                    <path fill="#FBBC05"
                        d="M5.25 10.42c-.24-.72-.38-1.49-.38-2.29s.14-1.57.38-2.29L1.48 4.92C.54 6.81 0 8.94 0 11.2s.54 4.39 1.48 6.28l3.77-2.92c-.24-.72-.38-1.49-.38-2.29z" />
                    <path fill="#34A853"
                        d="M12 23c3.24 0 5.97-1.07 7.96-2.92l-3.62-2.81c-1.1.74-2.52 1.18-4.34 1.18-3.13 0-5.82-2.33-6.77-5.38L1.48 16c1.92 3.85 5.87 6.5 10.52 6.5z" />
                </svg>
                Google
            </button>
        </div>

    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>