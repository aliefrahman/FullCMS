<?php
namespace App\Models;

use App\Core\Database;

class Comment
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * Insert a new comment into the database
     * @param array $data
     * @return bool
     */
    public function create($data)
    {
        $this->db->query("INSERT INTO comments (article_id, user_id, parent_id, author_name, author_email, content, status) 
                          VALUES (:article_id, :user_id, :parent_id, :author_name, :author_email, :content, :status)");
        
        $this->db->bind(':article_id', $data['article_id']);
        $this->db->bind(':user_id', $data['user_id'] ?? null);
        $this->db->bind(':parent_id', $data['parent_id'] ?? null);
        $this->db->bind(':author_name', $data['author_name'] ?? null);
        $this->db->bind(':author_email', $data['author_email'] ?? null);
        $this->db->bind(':content', $data['content']);
        $this->db->bind(':status', $data['status'] ?? 'pending');

        return $this->db->execute();
    }

    /**
     * Get approved comments for a specific article
     * @param int $articleId
     * @return array
     */
    public function getApprovedByArticle($articleId)
    {
        $query = "
            SELECT c.*, u.avatar, u.username as user_username, u.full_name as user_full_name, u.role as user_role
            FROM comments c 
            LEFT JOIN users u ON c.user_id = u.id 
            WHERE c.article_id = :article_id AND c.status = 'approved' 
            ORDER BY c.created_at ASC
        ";
        $this->db->query($query);
        $this->db->bind(':article_id', $articleId);
        return $this->db->resultSet();
    }

    /**
     * Get all comments for administration dashboard (moderation list)
     * @return array
     */
    public function getAll()
    {
        $query = "
            SELECT c.*, a.title as article_title, a.slug as article_slug, 
                   u.full_name as user_full_name, u.email as user_email, u.avatar as user_avatar
            FROM comments c 
            JOIN articles a ON c.article_id = a.id 
            LEFT JOIN users u ON c.user_id = u.id 
            ORDER BY c.created_at DESC
        ";
        $this->db->query($query);
        return $this->db->resultSet();
    }

    /**
     * Get a comment by its ID
     * @param int $id
     * @return object|false
     */
    public function getById($id)
    {
        $this->db->query("SELECT * FROM comments WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    /**
     * Update comment status (approved, pending, spam)
     * @param int $id
     * @param string $status
     * @return bool
     */
    public function updateStatus($id, $status)
    {
        $this->db->query("UPDATE comments SET status = :status WHERE id = :id");
        $this->db->bind(':status', $status);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    /**
     * Delete comment from database
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        $this->db->query("DELETE FROM comments WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    /**
     * Count comments by status
     * @param string $status
     * @return int
     */
    public function countByStatus($status)
    {
        $this->db->query("SELECT COUNT(*) as total FROM comments WHERE status = :status");
        $this->db->bind(':status', $status);
        return (int)$this->db->single()->total;
    }
}
