<?php
namespace App\Models;

use App\Core\Database;

class Category
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * Get all categories sorted alphabetically
     * @return array
     */
    public function getAll()
    {
        $this->db->query("SELECT * FROM categories ORDER BY name ASC");
        return $this->db->resultSet();
    }

    /**
     * Get all categories along with articles count
     * @return array
     */
    public function getAllWithCount()
    {
        $query = "
            SELECT c.*, COUNT(a.id) as articles_count 
            FROM categories c 
            LEFT JOIN articles a ON c.id = a.category_id 
            GROUP BY c.id 
            ORDER BY c.created_at DESC
        ";
        $this->db->query($query);
        return $this->db->resultSet();
    }

    /**
     * Find category by ID
     * @param int $id
     * @return object|false
     */
    public function getById($id)
    {
        $this->db->query("SELECT * FROM categories WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    /**
     * Find category by Slug
     * @param string $slug
     * @return object|false
     */
    public function getBySlug($slug)
    {
        $this->db->query("SELECT * FROM categories WHERE slug = :slug");
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
            $this->db->query("SELECT id FROM categories WHERE slug = :slug AND id != :id");
            $this->db->bind(':slug', $slug);
            $this->db->bind(':id', $excludeId);
        } else {
            $this->db->query("SELECT id FROM categories WHERE slug = :slug");
            $this->db->bind(':slug', $slug);
        }
        return $this->db->single();
    }

    /**
     * Insert a new category
     * @param array $data
     * @return bool
     */
    public function create($data)
    {
        $this->db->query("INSERT INTO categories (name, slug, description) VALUES (:name, :slug, :description)");
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':slug', $data['slug']);
        $this->db->bind(':description', $data['description'] ?? '');
        return $this->db->execute();
    }

    /**
     * Update an existing category
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data)
    {
        $this->db->query("UPDATE categories SET name = :name, slug = :slug, description = :description WHERE id = :id");
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':slug', $data['slug']);
        $this->db->bind(':description', $data['description'] ?? '');
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    /**
     * Delete category
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        $this->db->query("DELETE FROM categories WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    /**
     * Check if category has associated articles
     * @param int $id
     * @return bool
     */
    public function hasArticles($id)
    {
        $this->db->query("SELECT COUNT(*) as total FROM articles WHERE category_id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single()->total > 0;
    }
}
