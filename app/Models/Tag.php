<?php
namespace App\Models;

use App\Core\Database;

class Tag
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * Get all tags alphabetically
     * @return array
     */
    public function getAll()
    {
        $this->db->query("SELECT * FROM tags ORDER BY name ASC");
        return $this->db->resultSet();
    }

    /**
     * Get all tags along with total articles count using pivot table
     * @return array
     */
    public function getAllWithCount()
    {
        $query = "
            SELECT t.*, COUNT(at.article_id) as articles_count 
            FROM tags t 
            LEFT JOIN article_tags at ON t.id = at.tag_id 
            GROUP BY t.id 
            ORDER BY t.created_at DESC
        ";
        $this->db->query($query);
        return $this->db->resultSet();
    }

    /**
     * Get tag by ID
     * @param int $id
     * @return object|false
     */
    public function getById($id)
    {
        $this->db->query("SELECT * FROM tags WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    /**
     * Get tag by Slug
     * @param string $slug
     * @return object|false
     */
    public function getBySlug($slug)
    {
        $this->db->query("SELECT * FROM tags WHERE slug = :slug");
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
            $this->db->query("SELECT id FROM tags WHERE slug = :slug AND id != :id");
            $this->db->bind(':slug', $slug);
            $this->db->bind(':id', $excludeId);
        } else {
            $this->db->query("SELECT id FROM tags WHERE slug = :slug");
            $this->db->bind(':slug', $slug);
        }
        return $this->db->single();
    }

    /**
     * Insert tag
     * @param array $data
     * @return bool
     */
    public function create($data)
    {
        $this->db->query("INSERT INTO tags (name, slug) VALUES (:name, :slug)");
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':slug', $data['slug']);
        return $this->db->execute();
    }

    /**
     * Update tag
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data)
    {
        $this->db->query("UPDATE tags SET name = :name, slug = :slug WHERE id = :id");
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':slug', $data['slug']);
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    /**
     * Delete tag and clear its pivot table relationships
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        // First delete relationships
        $this->db->query("DELETE FROM article_tags WHERE tag_id = :id");
        $this->db->bind(':id', $id);
        $this->db->execute();

        // Delete tag itself
        $this->db->query("DELETE FROM tags WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }
}
