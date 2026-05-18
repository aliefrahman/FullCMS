<?php 
/**
 * @var array $comments
 */
$title = "Moderasi Komentar";
$activePage = "comments";
require_once __DIR__ . '/../layouts/admin_header.php'; 
use App\Helpers\Session;

$error = Session::flash('error');
$success = Session::flash('success');

// Calculate statuses for summary metrics
$totalCount = count($comments);
$pendingCount = 0;
$approvedCount = 0;
$spamCount = 0;

foreach ($comments as $c) {
    if ($c->status === 'pending') $pendingCount++;
    elseif ($c->status === 'approved') $approvedCount++;
    elseif ($c->status === 'spam') $spamCount++;
}
?>

<div class="space-y-6">

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
            <h3 class="font-display font-extrabold text-xl text-slate-800 tracking-tight">Moderasi Komentar</h3>
            <p class="text-xs text-slate-500 font-medium">Tinjau, setujui, filter spam, atau hapus tanggapan pembaca di seluruh artikel</p>
        </div>
    </div>

    <!-- Summary Statistics Row -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Card 1: Total -->
        <div class="glass rounded-3xl p-5 border border-white/60 shadow-sm flex items-center gap-4 bg-linear-to-tr from-slate-50/50 to-white/50">
            <div class="h-12 w-12 rounded-2xl bg-slate-100 text-slate-650 flex items-center justify-center shadow-sm">
                <i data-lucide="messages-square" class="w-5 h-5"></i>
            </div>
            <div>
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Total Komentar</span>
                <span class="font-display font-extrabold text-2xl text-slate-850"><?php echo $totalCount; ?></span>
            </div>
        </div>

        <!-- Card 2: Pending -->
        <div class="glass rounded-3xl p-5 border border-white/60 shadow-sm flex items-center gap-4 bg-linear-to-tr from-amber-50/30 to-white/50">
            <div class="h-12 w-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center shadow-sm border border-amber-100/50">
                <i data-lucide="clock" class="w-5 h-5"></i>
            </div>
            <div>
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Menunggu Review</span>
                <span class="font-display font-extrabold text-2xl text-amber-600"><?php echo $pendingCount; ?></span>
            </div>
        </div>

        <!-- Card 3: Approved -->
        <div class="glass rounded-3xl p-5 border border-white/60 shadow-sm flex items-center gap-4 bg-linear-to-tr from-emerald-50/30 to-white/50">
            <div class="h-12 w-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center shadow-sm border border-emerald-100/50">
                <i data-lucide="check-circle-2" class="w-5 h-5"></i>
            </div>
            <div>
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Telah Disetujui</span>
                <span class="font-display font-extrabold text-2xl text-emerald-600"><?php echo $approvedCount; ?></span>
            </div>
        </div>

        <!-- Card 4: Spam -->
        <div class="glass rounded-3xl p-5 border border-white/60 shadow-sm flex items-center gap-4 bg-linear-to-tr from-rose-50/30 to-white/50">
            <div class="h-12 w-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center shadow-sm border border-rose-100/50">
                <i data-lucide="shield-alert" class="w-5 h-5"></i>
            </div>
            <div>
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider block">Terfilter Spam</span>
                <span class="font-display font-extrabold text-2xl text-rose-600"><?php echo $spamCount; ?></span>
            </div>
        </div>
    </div>

    <!-- Moderation table -->
    <div class="bg-white border border-slate-100 rounded-3xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100 text-xs font-bold text-slate-600 uppercase tracking-wider">
                        <th class="p-4 pl-6 w-1/5">Penulis / Pengirim</th>
                        <th class="p-4 w-2/5">Isi Komentar</th>
                        <th class="p-4 w-1/5">Artikel Asal</th>
                        <th class="p-4 text-center w-1/12">Status</th>
                        <th class="p-4 text-center pr-6 w-1/8">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    <?php if (empty($comments)): ?>
                    <tr>
                        <td colspan="5" class="p-12 text-center text-slate-400 font-medium text-sm">
                            <div class="space-y-2">
                                <i data-lucide="message-square-off" class="w-10 h-10 mx-auto text-slate-300"></i>
                                <p>Belum ada komentar masuk di website ini.</p>
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?>
                    
                    <?php foreach ($comments as $c): ?>
                    <?php 
                    // Initials generator
                    $initials = '';
                    $parts = explode(' ', $c->user_full_name ?? $c->author_name ?? 'Anonymous');
                    foreach (array_slice($parts, 0, 2) as $p) {
                        $initials .= strtoupper(substr($p, 0, 1));
                    }
                    ?>
                    <tr class="hover:bg-slate-50/30 transition-colors align-top">
                        <!-- Commenter Info -->
                        <td class="p-4 pl-6">
                            <div class="flex items-center gap-3">
                                <!-- Avatar representation -->
                                <?php if (!empty($c->user_avatar) && file_exists(__DIR__ . '/../../../public/uploads/avatars/' . $c->user_avatar)): ?>
                                    <img src="<?php echo PUBLIC_URL; ?>/uploads/avatars/<?php echo $c->user_avatar; ?>" 
                                         class="h-9 w-9 rounded-xl object-cover border border-slate-150 shadow-xs" />
                                <?php else: ?>
                                    <div class="h-9 w-9 rounded-xl bg-linear-to-tr from-slate-200 to-slate-300 text-slate-600 font-display font-extrabold text-xs flex items-center justify-center border border-slate-150">
                                        <?php echo $initials; ?>
                                    </div>
                                <?php endif; ?>
                                <div>
                                    <div class="font-bold text-slate-800 text-xs sm:text-sm">
                                        <?php echo htmlspecialchars($c->user_full_name ?? $c->author_name ?? 'Anonymous'); ?>
                                    </div>
                                    <div class="text-[10px] text-slate-400 font-medium truncate max-w-[150px]">
                                        <?php echo htmlspecialchars($c->user_email ?? $c->author_email ?? '-'); ?>
                                    </div>
                                    <span class="inline-block mt-1 px-1.5 py-0.5 rounded-sm bg-slate-100 text-[8px] font-bold text-slate-500 uppercase tracking-wide">
                                        Role: <?php echo htmlspecialchars($c->user_role ?? 'subscriber'); ?>
                                    </span>
                                </div>
                            </div>
                        </td>

                        <!-- Comment Content -->
                        <td class="p-4 text-xs sm:text-sm text-slate-700 leading-relaxed max-w-sm whitespace-pre-line">
                            <?php echo nl2br(htmlspecialchars($c->content)); ?>
                            <div class="text-[10px] text-slate-400 font-medium mt-1">
                                📅 Dikirim pada: <?php echo date('d M Y, H:i', strtotime($c->created_at)); ?>
                            </div>
                        </td>

                        <!-- Article Target link -->
                        <td class="p-4 text-xs font-semibold text-slate-600">
                            <a href="<?php echo PUBLIC_URL; ?>/read?slug=<?php echo htmlspecialchars($c->article_slug); ?>" target="_blank" class="hover:text-primary-600 transition-colors flex items-center gap-1">
                                <i data-lucide="external-link" class="w-3.5 h-3.5 text-slate-400"></i>
                                <span class="line-clamp-2" title="<?php echo htmlspecialchars($c->article_title); ?>">
                                    <?php echo htmlspecialchars($c->article_title); ?>
                                </span>
                            </a>
                        </td>

                        <!-- Comment Status Badge -->
                        <td class="p-4 text-center">
                            <?php if ($c->status === 'pending'): ?>
                                <span class="inline-flex px-2 py-1 rounded-full text-[10px] font-bold bg-amber-50 border border-amber-100 text-amber-600 uppercase tracking-wide shadow-2xs">
                                    Pending
                                </span>
                            <?php elseif ($c->status === 'approved'): ?>
                                <span class="inline-flex px-2 py-1 rounded-full text-[10px] font-bold bg-emerald-50 border border-emerald-100 text-emerald-600 uppercase tracking-wide shadow-2xs">
                                    Approved
                                </span>
                            <?php elseif ($c->status === 'spam'): ?>
                                <span class="inline-flex px-2 py-1 rounded-full text-[10px] font-bold bg-rose-50 border border-rose-100 text-rose-650 uppercase tracking-wide shadow-2xs">
                                    Spam
                                </span>
                            <?php endif; ?>
                        </td>

                        <!-- Action buttons -->
                        <td class="p-4 text-center pr-6">
                            <div class="inline-flex items-center gap-2">
                                <!-- Approve Action (POST Form) -->
                                <?php if ($c->status !== 'approved'): ?>
                                    <form method="POST" action="<?php echo PUBLIC_URL; ?>/admin/comments/approve">
                                        <?php echo \App\Helpers\Security::csrfField(); ?>
                                        <input type="hidden" name="id" value="<?php echo $c->id; ?>" />
                                        <button type="submit" title="Setujui Komentar" 
                                                class="p-2 rounded-xl bg-slate-50 border border-slate-100 text-slate-450 hover:text-emerald-600 hover:bg-emerald-50 hover:border-emerald-100 transition-all duration-200 cursor-pointer">
                                            <i data-lucide="check" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>

                                <!-- Spam Action (POST Form) -->
                                <?php if ($c->status !== 'spam'): ?>
                                    <form method="POST" action="<?php echo PUBLIC_URL; ?>/admin/comments/spam">
                                        <?php echo \App\Helpers\Security::csrfField(); ?>
                                        <input type="hidden" name="id" value="<?php echo $c->id; ?>" />
                                        <button type="submit" title="Tandai Sebagai Spam" 
                                                class="p-2 rounded-xl bg-slate-50 border border-slate-100 text-slate-450 hover:text-amber-600 hover:bg-amber-50 hover:border-amber-100 transition-all duration-200 cursor-pointer">
                                            <i data-lucide="shield-alert" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>

                                <!-- Delete Action (GET with Confirmation) -->
                                <a href="<?php echo PUBLIC_URL; ?>/admin/comments/delete?id=<?php echo $c->id; ?>" 
                                   onclick="return confirm('Apakah Anda yakin ingin menghapus komentar ini secara permanen?');"
                                   title="Hapus Komentar"
                                   class="p-2 rounded-xl bg-slate-50 border border-slate-100 text-slate-450 hover:text-rose-600 hover:bg-rose-50 hover:border-rose-100 transition-all duration-200 cursor-pointer">
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
