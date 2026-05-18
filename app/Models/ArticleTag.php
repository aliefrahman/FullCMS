<?php
namespace App\Models;

use App\Core\Database;

class ArticleTag
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    /**
     * Fetch list of tag IDs associated with an article
     * @param int $articleId
     * @return array Array of integer IDs
     */
    public function getTagIdsByArticle($articleId)
    {
        $this->db->query("SELECT tag_id FROM article_tags WHERE article_id = :article_id");
        $this->db->bind(':article_id', $articleId);
        $result = $this->db->resultSet();
        
        return array_map(function($item) {
            return intval($item->tag_id);
        }, $result);
    }

    /**
     * Clear all tag relations for an article
     * @param int $articleId
     * @return bool
     */
    public function detachAll($articleId)
    {
        $this->db->query("DELETE FROM article_tags WHERE article_id = :article_id");
        $this->db->bind(':article_id', $articleId);
        return $this->db->execute();
    }

    /**
     * Sync/Re-attach tags for an article (delete old, insert new)
     * @param int $articleId
     * @param array $tagIds Array of tag IDs to bind
     * @return bool
     */
    public function sync($articleId, $tagIds)
    {
        // 1. Detach existing tag relations first
        $this->detachAll($articleId);

        // 2. Attach new tag relations if any selected
        if (!empty($tagIds) && is_array($tagIds)) {
            foreach ($tagIds as $tagId) {
                $this->db->query("INSERT INTO article_tags (article_id, tag_id) VALUES (:article_id, :tag_id)");
                $this->db->bind(':article_id', $articleId);
                $this->db->bind(':tag_id', intval($tagId));
                $this->db->execute();
            }
        }
        return true;
    }
}
