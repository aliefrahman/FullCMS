<?php
namespace App\Models;

use App\Core\Database;

class Page
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * Get all pages sorted by creation date
     * @return array
     */
    public function getAll()
    {
        $this->db->query("SELECT * FROM pages ORDER BY created_at DESC");
        return $this->db->resultSet();
    }

    /**
     * Find page by ID
     * @param int $id
     * @return object|false
     */
    public function getById($id)
    {
        $this->db->query("SELECT * FROM pages WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    /**
     * Find page by Slug
     * @param string $slug
     * @return object|false
     */
    public function getBySlug($slug)
    {
        $this->db->query("SELECT * FROM pages WHERE slug = :slug");
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
            $this->db->query("SELECT id FROM pages WHERE slug = :slug AND id != :id");
            $this->db->bind(':slug', $slug);
            $this->db->bind(':id', $excludeId);
        } else {
            $this->db->query("SELECT id FROM pages WHERE slug = :slug");
            $this->db->bind(':slug', $slug);
        }
        return $this->db->single();
    }

    /**
     * Insert a new page
     * @param array $data
     * @return bool
     */
    public function create($data)
    {
        $this->db->query("
            INSERT INTO pages (title, slug, content, meta_description, status) 
            VALUES (:title, :slug, :content, :meta_description, :status)
        ");
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':slug', $data['slug']);
        $this->db->bind(':content', $data['content']);
        $this->db->bind(':meta_description', $data['meta_description'] ?? null);
        $this->db->bind(':status', $data['status'] ?? 'published');
        return $this->db->execute();
    }

    /**
     * Update an existing page
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data)
    {
        $this->db->query("
            UPDATE pages 
            SET title = :title, slug = :slug, content = :content, meta_description = :meta_description, status = :status 
            WHERE id = :id
        ");
        $this->db->bind(':title', $data['title']);
        $this->db->bind(':slug', $data['slug']);
        $this->db->bind(':content', $data['content']);
        $this->db->bind(':meta_description', $data['meta_description'] ?? null);
        $this->db->bind(':status', $data['status']);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    /**
     * Delete page
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        $this->db->query("DELETE FROM pages WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}
