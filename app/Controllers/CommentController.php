<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Comment;
use App\Models\Article;
use App\Helpers\Session;
use App\Helpers\Security;

class CommentController extends Controller
{
    private $commentModel;
    private $articleModel;

    public function __construct()
    {
        $this->commentModel = new Comment();
        $this->articleModel = new Article();
    }

    /**
     * Store comment submitted by subscriber/reader
     */
    public function store()
    {
        // Enforce POST method
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . PUBLIC_URL . '/');
            exit;
        }

        // 1. Enforce Authentication: Guests must register & log in to comment
        if (!Session::has('user_id')) {
            Session::flash('error', 'Anda harus masuk atau mendaftar terlebih dahulu untuk mengirim komentar.');
            header('Location: ' . PUBLIC_URL . '/auth');
            exit;
        }

        // Get POST values
        $articleId = intval($_POST['article_id'] ?? 0);
        $content = trim($_POST['content'] ?? '');

        // Fetch corresponding article to redirect back properly
        $article = $this->articleModel->getById($articleId);
        if (!$article) {
            Session::flash('error', 'Artikel tidak ditemukan.');
            header('Location: ' . PUBLIC_URL . '/');
            exit;
        }

        // Validate content is not empty
        if (empty($content)) {
            Session::flash('error', 'Komentar tidak boleh kosong.');
            header('Location: ' . PUBLIC_URL . '/read?slug=' . urlencode($article->slug));
            exit;
        }

        // Sanitize comment content to prevent XSS
        $sanitizedContent = Security::sanitize($content);

        // Prepare comment data
        $commentData = [
            'article_id' => $articleId,
            'user_id' => Session::get('user_id'),
            'parent_id' => null, // flat comments structure by default
            'author_name' => Session::get('full_name') ?? Session::get('username'),
            'author_email' => Session::get('email'),
            'content' => $sanitizedContent,
            'status' => 'pending' // Moderated by default
        ];

        // Save comment
        if ($this->commentModel->create($commentData)) {
            // Log audit for comment action
            Security::logAudit('COMMENT_SUBMITTED', 'User ' . Session::get('username') . ' submitted comment on article ID: ' . $articleId);
            
            Session::flash('success', 'Komentar Anda berhasil dikirim dan sedang menunggu moderasi oleh Administrator.');
        } else {
            Session::flash('error', 'Terjadi kesalahan sistem saat mengirim komentar. Silakan coba lagi.');
        }

        // Redirect back to the article view
        header('Location: ' . PUBLIC_URL . '/read?slug=' . urlencode($article->slug));
        exit;
    }
}
