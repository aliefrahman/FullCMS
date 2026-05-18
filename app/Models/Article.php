<?php
namespace App\Models;

use App\Core\Database;

class Article
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * Get all articles, optionally isolated to a specific author
     * @param bool $canViewAll If false, restricts to current author
     * @param int|null $authorId The isolated author's user ID
     * @return array
     */
    public function getAll($canViewAll = true, $authorId = null)
    {
        if ($canViewAll) {
            $query = "
                SELECT a.*, c.name as category_name, u.full_name as author_name 
                FROM articles a 
                LEFT JOIN categories c ON a.category_id = c.id 
                LEFT JOIN users u ON a.author_id = u.id 
                ORDER BY a.created_at DESC
            ";
            $this->db->query($query);
        } else {
            $query = "
                SELECT a.*, c.name as category_name, u.full_name as author_name 
                FROM articles a 
                LEFT JOIN categories c ON a.category_id = c.id 
                LEFT JOIN users u ON a.author_id = u.id 
                WHERE a.author_id = :author_id
                ORDER BY a.created_at DESC
            ";
            $this->db->query($query);
            $this->db->bind(':author_id', $authorId);
        }
        return $this->db->resultSet();
    }

    /**
     * Get all published articles with category and author info
     * @return array
     */
    public function getPublished()
    {
        $query = "
            SELECT a.*, c.name as category_name, u.full_name as author_name 
            FROM articles a 
            LEFT JOIN categories c ON a.category_id = c.id 
            LEFT JOIN users u ON a.author_id = u.id 
            WHERE a.status = 'published'
            ORDER BY a.published_at DESC, a.created_at DESC
        ";
        $this->db->query($query);
        return $this->db->resultSet();
    }

    /**
     * Find article by ID
     * @param int $id
     * @return object|false
     */
    public function getById($id)
    {
        $this->db->query("SELECT * FROM articles WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    /**
     * Find article by Slug
     * @param string $slug
     * @return object|false
     */
    public function getBySlug($slug)
    {
        $this->db->query("SELECT * FROM articles WHERE slug = :slug");
        $this->db->bind(':slug', $slug);
        return $this->db->single();
    }

    /**
     * Check if slug exists excluding current ID
     * @param string $slug
     * @param int|null $excludeId
     * @return object|false
     */
    public function checkSlugExists($slug, $excludeId = null)
    {
        if ($excludeId) {
            $this->db->query("SELECT id FROM articles WHERE slug = :slug AND id != :id");
            $this->db->bind(':slug', $slug);
            $this->db->bind(':id', $excludeId);
        } else {
            $this->db->query("SELECT id FROM articles WHERE slug = :slug");
            $this->db->bind(':slug', $slug);
        }
        return $this->db->single();
    }

    /**
     * Insert a new article and return the insert ID
     * @param array $data
     * @return int|false Returns last insert ID on success
     */
    public function create($data)
    {
        $query = "
            INSERT INTO articles (title, slug, content, category_id, featured_image, featured_image_caption, author_id, status, published_at) 
            VALUES (:title, :slug, :content, :category_id, :featured_image, :featured_image_caption, :author_id, :status, :published_at)
        ";
        $this->db->query($query);
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':slug', $data['slug']);
        $this->db->bind(':content', $data['content']);
        $this->db->bind(':category_id', $data['category_id'] > 0 ? $data['category_id'] : null);
        $this->db->bind(':featured_image', $data['featured_image']);
        $this->db->bind(':featured_image_caption', $data['featured_image_caption'] ?? null);
        $this->db->bind(':author_id', $data['author_id']);
        $this->db->bind(':status', $data['status'] ?? 'draft');
        $this->db->bind(':published_at', $data['published_at']);

        if ($this->db->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    /**
     * Update an existing article
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data)
    {
        $query = "
            UPDATE articles 
            SET title = :title, slug = :slug, content = :content, category_id = :category_id, 
                featured_image = :featured_image, featured_image_caption = :featured_image_caption, status = :status, published_at = :published_at 
            WHERE id = :id
        ";
        $this->db->query($query);
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':slug', $data['slug']);
        $this->db->bind(':content', $data['content']);
        $this->db->bind(':category_id', $data['category_id'] > 0 ? $data['category_id'] : null);
        $this->db->bind(':featured_image', $data['featured_image']);
        $this->db->bind(':featured_image_caption', $data['featured_image_caption'] ?? null);
        $this->db->bind(':status', $data['status']);
        $this->db->bind(':published_at', $data['published_at']);
        $this->db->bind(':id', $id);

        return $this->db->execute();
    }

    /**
     * Delete article
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        $this->db->query("DELETE FROM articles WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}
