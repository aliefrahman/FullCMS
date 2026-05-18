<?php 
/**
 * @var object $page
 */
$title = "Edit Halaman: " . $page->title;
$activePage = "pages";
require_once __DIR__ . '/../layouts/admin_header.php'; 
use App\Helpers\Session;

$error = Session::flash('error');
?>

<div class="space-y-6 max-w-5xl mx-auto">

    <!-- Notification alerts -->
    <?php if ($error): ?>
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-100 text-rose-600 text-sm font-semibold flex items-center gap-2.5 shadow-sm animate-fade-in">
            <i data-lucide="alert-circle" class="w-5 h-5 text-rose-500 shrink-0 animate-bounce"></i>
            <span><?php echo htmlspecialchars($error); ?></span>
        </div>
    <?php endif; ?>

    <div class="flex items-center justify-between">
        <a href="<?php echo PUBLIC_URL; ?>/admin/pages" class="flex items-center gap-2 text-sm font-semibold text-slate-500 hover:text-primary-600 transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke Daftar Halaman
        </a>
    </div>

    <form id="page-form" method="POST" action="<?php echo PUBLIC_URL; ?>/admin/pages/update" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <?php echo \App\Helpers\Security::csrfField(); ?>
        
        <input type="hidden" name="id" value="<?php echo $page->id; ?>">

        <!-- Left Panel: Main Editor -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white border border-slate-100 rounded-3xl shadow-sm p-6 md:p-8 space-y-6">
                <div>
                    <h3 class="font-display font-extrabold text-lg text-slate-800">Edit Konten Halaman</h3>
                    <p class="text-xs text-slate-400 font-medium mt-0.5">Ubah judul, slug, atau isi teks lengkap halaman statis Anda</p>
                </div>

                <div class="space-y-4">
                    <!-- Title Input -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-650 tracking-wide uppercase">Judul Halaman <span class="text-rose-500">*</span></label>
                        <input type="text" name="title" value="<?php echo htmlspecialchars($page->title); ?>" placeholder="Masukkan judul halaman..." class="w-full border border-slate-200 rounded-xl px-4 py-3 text-lg font-bold text-slate-800 placeholder-slate-300 focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all duration-200" required />
                    </div>

                    <!-- Slug Input -->
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between">
                            <label class="text-xs font-bold text-slate-650 tracking-wide uppercase">Slug URL</label>
                        </div>
                        <div class="flex items-center border border-slate-200 rounded-xl px-3 py-2 bg-slate-50 text-sm focus-within:bg-white focus-within:ring-2 focus-within:ring-primary-500/20 focus-within:border-primary-500 transition-all duration-200">
                            <span class="text-slate-400 font-medium whitespace-nowrap">/p?slug=</span>
                            <input type="text" name="slug" value="<?php echo htmlspecialchars($page->slug); ?>" placeholder="judul-halaman-anda" class="w-full bg-transparent border-none text-slate-700 placeholder-slate-400 focus:outline-none ml-1" />
                        </div>
                    </div>

                    <!-- Quill Content Editor -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-650 tracking-wide uppercase">Isi Konten <span class="text-rose-500">*</span></label>
                        <!-- Quill CSS -->
                        <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet" />
                        
                        <!-- Hidden textarea to bind form submission -->
                        <textarea name="content" id="page-content" class="hidden" required><?php echo htmlspecialchars($page->content); ?></textarea>
                        
                        <!-- Quill Editor Container -->
                        <div class="border border-slate-200 rounded-xl overflow-hidden shadow-sm bg-white">
                            <div id="editor" class="h-96 text-slate-850 text-sm">
                                <?php echo $page->content; ?>
                            </div>
                        </div>
                        
                        <!-- Quill Script & Init -->
                        <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
                        <script>
                            document.addEventListener("DOMContentLoaded", function() {
                                const quill = new Quill('#editor', {
                                    theme: 'snow',
                                    placeholder: 'Tuliskan seluruh isi konten halaman statis Anda di sini secara menarik...',
                                    modules: {
                                        toolbar: [
                                            [{ 'header': [1, 2, 3, false] }],
                                            ['bold', 'italic', 'underline', 'strike'],
                                            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                                            ['blockquote', 'code-block'],
                                            ['link', 'clean']
                                        ]
                                    }
                                });

                                // Sync Quill editor HTML with the hidden textarea
                                quill.on('text-change', function() {
                                    document.getElementById('page-content').value = quill.getSemanticHTML();
                                });

                                // Ensure latest content is captured on form submission
                                document.getElementById('page-form').addEventListener('submit', function() {
                                    document.getElementById('page-content').value = quill.getSemanticHTML();
                                });
                            });
                        </script>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Panel: Meta Settings -->
        <div class="space-y-6">
            <div class="bg-white border border-slate-100 rounded-3xl shadow-sm p-6 md:p-8 space-y-6">
                <div>
                    <h3 class="font-display font-extrabold text-lg text-slate-800">Pengaturan Halaman</h3>
                </div>

                <div class="space-y-4">
                    <!-- Status Visibilitas -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-650 tracking-wide uppercase">Status Halaman</label>
                        <div class="relative">
                            <select name="status" class="w-full appearance-none border border-slate-200 rounded-xl px-4 py-3 text-sm font-bold text-slate-800 bg-white focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all duration-200 cursor-pointer">
                                <option value="published" <?php echo $page->status === 'published' ? 'selected' : ''; ?>>Publikasi (Tayang)</option>
                                <option value="draft" <?php echo $page->status === 'draft' ? 'selected' : ''; ?>>Draf (Disembunyikan)</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-slate-400">
                                <i data-lucide="chevron-down" class="w-4 h-4"></i>
                            </div>
                        </div>
                    </div>

                    <!-- SEO / Meta Description -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-650 tracking-wide uppercase">Meta Deskripsi (SEO)</label>
                        <textarea name="meta_description" rows="5" placeholder="Tulis deskripsi singkat halaman..." class="w-full border border-slate-200 rounded-xl px-4 py-3 text-xs text-slate-800 placeholder-slate-350 focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all duration-200"><?php echo htmlspecialchars($page->meta_description ?? ''); ?></textarea>
                        <p class="text-[10px] text-slate-400 leading-tight font-medium">Tip: Ringkasan 1-2 kalimat (maksimal 160 karakter) agar terindeks dengan baik di Google.</p>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-6 border-t border-slate-100 space-y-3">
                    <p class="text-[11px] text-slate-500 font-medium">Terakhir diperbarui: <?php echo date('d M Y, H:i', strtotime($page->updated_at)); ?></p>
                    <button type="submit" class="w-full px-5 py-3.5 rounded-xl bg-slate-900 text-white font-bold text-sm tracking-wide shadow-md hover:bg-slate-800 hover:-translate-y-0.5 transition-all duration-200 cursor-pointer flex items-center justify-center gap-2">
                        <i data-lucide="save" class="w-4 h-4"></i> Perbarui Halaman
                    </button>
                </div>
            </div>
        </div>

    </form>

</div>

<?php require_once __DIR__ . '/../layouts/admin_footer.php'; ?>
