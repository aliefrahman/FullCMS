<?php
/**
 * @var object $article
 * @var array $categories
 * @var array $tags
 * @var array $selectedTags
 */
$title = "Edit Artikel";
$activePage = "articles";
require_once __DIR__ . '/../layouts/admin_header.php';
use App\Helpers\Session;
use App\Helpers\Auth;

$error = Session::flash('error');
?>

<div class="space-y-6 max-w-5xl mx-auto">

    <!-- Notification alerts -->
    <?php if ($error): ?>
        <div
            class="p-4 rounded-2xl bg-rose-50 border border-rose-100 text-rose-600 text-sm font-semibold flex items-center gap-2.5 shadow-sm animate-fade-in">
            <i data-lucide="alert-circle" class="w-5 h-5 text-rose-500 shrink-0 animate-bounce"></i>
            <span><?php echo htmlspecialchars($error); ?></span>
        </div>
    <?php endif; ?>

    <div class="flex items-center justify-between">
        <a href="<?php echo PUBLIC_URL; ?>/admin/articles"
            class="flex items-center gap-2 text-sm font-semibold text-slate-500 hover:text-primary-600 transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke Daftar
        </a>
    </div>

    <form id="article-form" method="POST" action="<?php echo PUBLIC_URL; ?>/admin/articles/update"
        enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <?php echo \App\Helpers\Security::csrfField(); ?>

        <input type="hidden" name="id" value="<?php echo $article->id; ?>">

        <!-- Left Panel: Main Editor -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white border border-slate-100 rounded-3xl shadow-sm p-6 md:p-8 space-y-6">
                <div>
                    <h3 class="font-display font-extrabold text-xl text-slate-800 tracking-tight">Editor Konten</h3>
                    <p class="text-xs text-slate-500 font-medium">Perbarui informasi dan isi konten artikel ini.</p>
                </div>

                <div class="space-y-4">
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-650 tracking-wide uppercase">Judul Artikel <span
                                class="text-rose-500">*</span></label>
                        <input type="text" name="title" value="<?php echo htmlspecialchars($article->title); ?>"
                            class="w-full border border-slate-200 rounded-xl px-4 py-3 text-lg font-bold text-slate-800 placeholder-slate-300 focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all duration-200"
                            required />
                    </div>

                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between">
                            <label class="text-xs font-bold text-slate-650 tracking-wide uppercase">Slug URL</label>
                            <span class="text-[10px] text-slate-400 uppercase font-semibold">Bisa Diubah</span>
                        </div>
                        <div
                            class="flex items-center border border-slate-200 rounded-xl px-3 py-2 bg-slate-50 text-sm focus-within:bg-white focus-within:ring-2 focus-within:ring-primary-500/20 focus-within:border-primary-500 transition-all duration-200">
                            <span class="text-slate-400 font-medium whitespace-nowrap">/read/</span>
                            <input type="text" name="slug" value="<?php echo htmlspecialchars($article->slug); ?>"
                                class="w-full bg-transparent border-none text-slate-700 placeholder-slate-400 focus:outline-none ml-1" />
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-650 tracking-wide uppercase">Konten Lengkap <span
                                class="text-rose-500">*</span></label>

                        <!-- Hidden textarea to bind form submission -->
                        <textarea name="content" id="article-content" class="hidden"
                            required><?php echo htmlspecialchars($article->content); ?></textarea>

                        <!-- Quill Editor Container -->
                        <div class="border border-slate-200 rounded-xl overflow-hidden shadow-sm bg-white">
                            <div id="editor" class="h-96 text-slate-800 text-sm">
                                <?php echo $article->content; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Panel: Meta Settings -->
        <div class="space-y-6">
            <div class="bg-white border border-slate-100 rounded-3xl shadow-sm p-6 md:p-8 space-y-6">
                <div>
                    <h3 class="font-display font-extrabold text-lg text-slate-800">Pengaturan Publikasi</h3>
                </div>

                <div class="space-y-4">
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-650 tracking-wide uppercase">Kategori</label>
                        <div class="relative">
                            <select name="category_id"
                                class="w-full appearance-none border border-slate-200 rounded-xl px-4 py-3 text-sm font-semibold text-slate-700 bg-white focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all duration-200 cursor-pointer">
                                <option value="">-- Pilih Kategori --</option>
                                <?php foreach ($categories as $c): ?>
                                    <option value="<?php echo $c->id; ?>" <?php echo ($c->id == $article->category_id) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($c->name); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div
                                class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-slate-400">
                                <i data-lucide="chevron-down" class="w-4 h-4"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Tags Selector (Bisa Tambah Baru Inline) -->
                    <div class="space-y-1.5 pt-2 border-t border-slate-100" x-data="{
                        newTagName: '',
                        isSubmitting: false,
                        errorMessage: '',
                        successMessage: '',
                        addTag() {
                            if (!this.newTagName.trim()) return;
                            this.isSubmitting = true;
                            this.errorMessage = '';
                            this.successMessage = '';
                            
                            const formData = new FormData();
                            formData.append('name', this.newTagName);
                            
                            fetch('<?php echo PUBLIC_URL; ?>/admin/tags/ajax-create', {
                                method: 'POST',
                                body: formData
                            })
                            .then(res => res.json())
                            .then(data => {
                                this.isSubmitting = false;
                                if (data.success) {
                                    this.successMessage = data.message;
                                    
                                    // Dynamically append new tag checkbox to the list
                                    const container = document.getElementById('tags-checkbox-container');
                                    
                                    // Remove empty message if it exists
                                    const emptyMsg = document.getElementById('no-tags-message');
                                    if (emptyMsg) emptyMsg.remove();
                                    
                                    const label = document.createElement('label');
                                    label.className = 'inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-primary-200 bg-primary-50 hover:bg-primary-100 cursor-pointer text-xs font-bold text-primary-800 select-none transition-all';
                                    
                                    const input = document.createElement('input');
                                    input.type = 'checkbox';
                                    input.name = 'tags[]';
                                    input.value = data.tag.id;
                                    input.checked = true;
                                    input.className = 'rounded text-primary-600 focus:ring-primary-500/20';
                                    
                                    const span = document.createElement('span');
                                    span.textContent = '#' + data.tag.name;
                                    
                                    label.appendChild(input);
                                    label.appendChild(span);
                                    container.appendChild(label);
                                    
                                    this.newTagName = '';
                                } else {
                                    this.errorMessage = data.message;
                                }
                            })
                            .catch(err => {
                                this.isSubmitting = false;
                                this.errorMessage = 'Terjadi kesalahan sistem.';
                            });
                        }
                    }">
                        <div class="flex items-center justify-between">
                            <label class="text-xs font-bold text-slate-650 tracking-wide uppercase">Tag Artikel</label>
                            <span class="text-[10px] text-slate-400 font-semibold">Bisa Buat Baru</span>
                        </div>
                        <p class="text-[10px] text-slate-400 font-semibold mb-2">Pilih kata kunci yang relevan:</p>

                        <div id="tags-checkbox-container"
                            class="flex flex-wrap gap-2 max-h-40 overflow-y-auto p-2 bg-slate-50 border border-slate-200 rounded-xl mb-3">
                            <?php if (empty($tags)): ?>
                                <span id="no-tags-message" class="text-xs text-slate-450 font-medium p-1">Belum ada tag
                                    tersedia.</span>
                            <?php endif; ?>
                            <?php foreach ($tags as $t): ?>
                                <?php $checked = in_array($t->id, $selectedTags) ? 'checked' : ''; ?>
                                <label
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-100 cursor-pointer text-xs font-bold text-slate-700 select-none transition-all">
                                    <input type="checkbox" name="tags[]" value="<?php echo $t->id; ?>" <?php echo $checked; ?> class="rounded text-primary-600 focus:ring-primary-500/20" />
                                    <span>#<?php echo htmlspecialchars($t->name); ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>

                        <!-- Inline Creator Input -->
                        <div class="flex gap-2">
                            <input type="text" x-model="newTagName" placeholder="Tulis tag baru..."
                                @keydown.enter.prevent="addTag()"
                                class="grow text-xs border border-slate-200 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500" />
                            <button type="button" @click="addTag()" :disabled="isSubmitting"
                                class="px-3 py-2 rounded-xl bg-slate-900 text-white font-bold text-xs hover:bg-slate-800 disabled:opacity-50 flex items-center gap-1 cursor-pointer">
                                <i data-lucide="plus" class="w-3.5 h-3.5"></i> Tambah
                            </button>
                        </div>
                        <p x-show="errorMessage" class="text-[10px] text-rose-500 font-semibold mt-1"
                            x-text="errorMessage" x-cloak></p>
                        <p x-show="successMessage" class="text-[10px] text-emerald-600 font-semibold mt-1"
                            x-text="successMessage" x-cloak></p>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-650 tracking-wide uppercase">Status
                            Visibilitas</label>
                        <?php if (Auth::hasPermission('publish_articles')): ?>
                            <div class="relative">
                                <select name="status"
                                    class="w-full appearance-none border border-slate-200 rounded-xl px-4 py-3 text-sm font-bold text-slate-800 bg-white focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all duration-200 cursor-pointer">
                                    <option value="draft" <?php echo ($article->status === 'draft') ? 'selected' : ''; ?>>Draf
                                        (Disembunyikan)</option>
                                    <option value="published" <?php echo ($article->status === 'published') ? 'selected' : ''; ?>>Publikasi (Tayang)</option>
                                    <option value="archived" <?php echo ($article->status === 'archived') ? 'selected' : ''; ?>>Arsip (Disembunyikan)</option>
                                </select>
                                <div
                                    class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-slate-400">
                                    <i data-lucide="chevron-down" class="w-4 h-4"></i>
                                </div>
                            </div>
                        <?php else: ?>
                            <?php if ($article->status === 'published'): ?>
                                <div
                                    class="w-full border border-emerald-200 bg-emerald-50 rounded-xl px-4 py-3 text-sm font-bold text-emerald-600 flex items-center justify-between cursor-not-allowed">
                                    <span>Telah Dipublikasikan</span>
                                    <i data-lucide="check-circle" class="w-4 h-4 text-emerald-500"></i>
                                </div>
                            <?php else: ?>
                                <div
                                    class="w-full border border-slate-200 bg-slate-50 rounded-xl px-4 py-3 text-sm font-bold text-amber-600 flex items-center justify-between cursor-not-allowed">
                                    <span>Status: <?php echo ucfirst($article->status); ?></span>
                                    <i data-lucide="lock" class="w-4 h-4 text-amber-400"></i>
                                </div>
                            <?php endif; ?>
                            <input type="hidden" name="status" value="<?php echo htmlspecialchars($article->status); ?>">
                        <?php endif; ?>
                    </div>

                    <div class="space-y-1.5 pt-2 border-t border-slate-100">
                        <label class="text-xs font-bold text-slate-650 tracking-wide uppercase">Gambar Utama
                            (Thumbnail)</label>

                        <?php if ($article->featured_image): ?>
                            <div class="mb-3 rounded-xl overflow-hidden border border-slate-200 bg-slate-50">
                                <img src="<?php echo PUBLIC_URL; ?>/uploads/articles/<?php echo htmlspecialchars($article->featured_image); ?>"
                                    class="w-full h-32 object-cover" />
                            </div>
                        <?php endif; ?>

                        <div class="mt-2 flex justify-center rounded-xl border border-dashed border-slate-300 px-6 py-6 hover:bg-slate-50 hover:border-primary-300 transition-all duration-200 group cursor-pointer relative overflow-hidden"
                            x-data="{ fileName: '' }">
                            <div class="text-center" x-show="!fileName">
                                <div
                                    class="mx-auto flex h-10 w-10 items-center justify-center rounded-full bg-slate-100 group-hover:bg-primary-50 transition-colors">
                                    <i data-lucide="image-plus"
                                        class="h-4 w-4 text-slate-400 group-hover:text-primary-500"></i>
                                </div>
                                <div class="mt-3 flex justify-center text-sm leading-6 text-slate-600">
                                    <span
                                        class="relative cursor-pointer rounded-md font-semibold text-primary-600 focus-within:outline-none hover:text-primary-500">
                                        <?php echo $article->featured_image ? 'Ganti Gambar' : 'Pilih File Gambar'; ?>
                                    </span>
                                </div>
                                <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mt-1">
                                    Biarkan kosong jika tidak diganti</p>
                            </div>
                            <div class="text-center" x-show="fileName" x-cloak>
                                <div
                                    class="mx-auto flex h-10 w-10 items-center justify-center rounded-full bg-emerald-50 text-emerald-500">
                                    <i data-lucide="check" class="h-5 w-5"></i>
                                </div>
                                <p class="mt-2 text-sm font-bold text-slate-800 truncate px-4" x-text="fileName"></p>
                            </div>
                            <input id="file-upload" name="featured_image" type="file"
                                accept="image/png, image/jpeg, image/webp"
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                @change="fileName = $event.target.files[0].name" />
                        </div>

                        <!-- Caption Input -->
                        <div class="mt-3 space-y-1">
                            <label
                                class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Keterangan
                                Gambar (Caption)</label>
                            <input type="text" name="featured_image_caption"
                                value="<?php echo htmlspecialchars($article->featured_image_caption ?? ''); ?>"
                                placeholder="Tulis takarir gambar (misal: Ilustrasi AI)..."
                                class="w-full text-xs border border-slate-200 rounded-xl px-3 py-2.5 bg-white focus:outline-none focus:ring-2 focus:ring-primary-500/20 focus:border-primary-500 transition-all font-medium text-slate-700" />
                        </div>
                    </div>
                </div>

                <div class="pt-6 border-t border-slate-100 space-y-3">
                    <p class="text-[11px] text-slate-500 font-medium">Terakhir diperbarui:
                        <?php echo date('d M Y, H:i', strtotime($article->updated_at)); ?>
                    </p>
                    <button type="submit"
                        class="w-full px-5 py-3.5 rounded-xl bg-slate-900 text-white font-bold text-sm tracking-wide shadow-md hover:bg-slate-800 hover:-translate-y-0.5 transition-all duration-200 cursor-pointer flex items-center justify-center gap-2">
                        <i data-lucide="save" class="w-4 h-4"></i> Perbarui Artikel
                    </button>
                </div>
            </div>
        </div>

    </form>

</div>

<?php require_once __DIR__ . '/../layouts/admin_footer.php'; ?>