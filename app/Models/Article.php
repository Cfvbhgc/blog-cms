<?php

namespace App\Models;

use App\Core\Database;

class Article
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Get published articles with pagination.
     */
    public function getPublished(int $page = 1, int $perPage = 10): array
    {
        $offset = ($page - 1) * $perPage;
        return $this->db->query(
            "SELECT a.*, c.name AS category_name, c.slug AS category_slug
             FROM articles a
             LEFT JOIN categories c ON a.category_id = c.id
             WHERE a.status = 'published'
             ORDER BY a.created_at DESC
             LIMIT :limit OFFSET :offset",
            ['limit' => $perPage, 'offset' => $offset]
        );
    }

    /**
     * Count published articles.
     */
    public function countPublished(): int
    {
        return $this->db->count("SELECT COUNT(*) FROM articles WHERE status = 'published'");
    }

    /**
     * Get all articles (admin view).
     */
    public function getAll(int $page = 1, int $perPage = 20): array
    {
        $offset = ($page - 1) * $perPage;
        return $this->db->query(
            "SELECT a.*, c.name AS category_name
             FROM articles a
             LEFT JOIN categories c ON a.category_id = c.id
             ORDER BY a.created_at DESC
             LIMIT :limit OFFSET :offset",
            ['limit' => $perPage, 'offset' => $offset]
        );
    }

    /**
     * Count all articles.
     */
    public function countAll(): int
    {
        return $this->db->count("SELECT COUNT(*) FROM articles");
    }

    /**
     * Find an article by slug.
     */
    public function findBySlug(string $slug): ?array
    {
        return $this->db->queryOne(
            "SELECT a.*, c.name AS category_name, c.slug AS category_slug
             FROM articles a
             LEFT JOIN categories c ON a.category_id = c.id
             WHERE a.slug = :slug",
            ['slug' => $slug]
        );
    }

    /**
     * Find an article by ID.
     */
    public function findById(int $id): ?array
    {
        return $this->db->queryOne(
            "SELECT a.*, c.name AS category_name
             FROM articles a
             LEFT JOIN categories c ON a.category_id = c.id
             WHERE a.id = :id",
            ['id' => $id]
        );
    }

    /**
     * Get articles by category slug.
     */
    public function getByCategory(string $categorySlug, int $page = 1, int $perPage = 10): array
    {
        $offset = ($page - 1) * $perPage;
        return $this->db->query(
            "SELECT a.*, c.name AS category_name, c.slug AS category_slug
             FROM articles a
             JOIN categories c ON a.category_id = c.id
             WHERE c.slug = :slug AND a.status = 'published'
             ORDER BY a.created_at DESC
             LIMIT :limit OFFSET :offset",
            ['slug' => $categorySlug, 'limit' => $perPage, 'offset' => $offset]
        );
    }

    /**
     * Get articles by tag slug.
     */
    public function getByTag(string $tagSlug, int $page = 1, int $perPage = 10): array
    {
        $offset = ($page - 1) * $perPage;
        return $this->db->query(
            "SELECT a.*, c.name AS category_name, c.slug AS category_slug
             FROM articles a
             LEFT JOIN categories c ON a.category_id = c.id
             JOIN article_tags at2 ON a.id = at2.article_id
             JOIN tags t ON at2.tag_id = t.id
             WHERE t.slug = :slug AND a.status = 'published'
             ORDER BY a.created_at DESC
             LIMIT :limit OFFSET :offset",
            ['slug' => $tagSlug, 'limit' => $perPage, 'offset' => $offset]
        );
    }

    /**
     * Create a new article.
     */
    public function create(array $data): int
    {
        $this->db->execute(
            "INSERT INTO articles (title, slug, content, excerpt, seo_title, seo_description, category_id, status)
             VALUES (:title, :slug, :content, :excerpt, :seo_title, :seo_description, :category_id, :status)",
            [
                'title'           => $data['title'],
                'slug'            => $data['slug'],
                'content'         => $data['content'],
                'excerpt'         => $data['excerpt'] ?? '',
                'seo_title'       => $data['seo_title'] ?? '',
                'seo_description' => $data['seo_description'] ?? '',
                'category_id'     => $data['category_id'] ?: null,
                'status'          => $data['status'] ?? 'draft',
            ]
        );
        return (int) $this->db->lastInsertId();
    }

    /**
     * Update an existing article.
     */
    public function update(int $id, array $data): void
    {
        $this->db->execute(
            "UPDATE articles SET title = :title, slug = :slug, content = :content,
             excerpt = :excerpt, seo_title = :seo_title, seo_description = :seo_description,
             category_id = :category_id, status = :status, updated_at = NOW()
             WHERE id = :id",
            [
                'id'              => $id,
                'title'           => $data['title'],
                'slug'            => $data['slug'],
                'content'         => $data['content'],
                'excerpt'         => $data['excerpt'] ?? '',
                'seo_title'       => $data['seo_title'] ?? '',
                'seo_description' => $data['seo_description'] ?? '',
                'category_id'     => $data['category_id'] ?: null,
                'status'          => $data['status'] ?? 'draft',
            ]
        );
    }

    /**
     * Delete an article by ID.
     */
    public function delete(int $id): void
    {
        $this->db->execute("DELETE FROM articles WHERE id = :id", ['id' => $id]);
    }

    /**
     * Get tag IDs for an article.
     */
    public function getTagIds(int $articleId): array
    {
        $rows = $this->db->query(
            "SELECT tag_id FROM article_tags WHERE article_id = :id",
            ['id' => $articleId]
        );
        return array_column($rows, 'tag_id');
    }

    /**
     * Get tags for an article.
     */
    public function getTags(int $articleId): array
    {
        return $this->db->query(
            "SELECT t.* FROM tags t
             JOIN article_tags at2 ON t.id = at2.tag_id
             WHERE at2.article_id = :id
             ORDER BY t.name",
            ['id' => $articleId]
        );
    }

    /**
     * Sync article tags (delete existing, insert new).
     */
    public function syncTags(int $articleId, array $tagIds): void
    {
        $this->db->execute("DELETE FROM article_tags WHERE article_id = :id", ['id' => $articleId]);
        foreach ($tagIds as $tagId) {
            $this->db->execute(
                "INSERT INTO article_tags (article_id, tag_id) VALUES (:article_id, :tag_id)",
                ['article_id' => $articleId, 'tag_id' => (int) $tagId]
            );
        }
    }

    /**
     * Generate a unique slug from title.
     */
    public function generateSlug(string $title, ?int $excludeId = null): string
    {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title), '-'));
        $original = $slug;
        $counter = 1;

        while (true) {
            $params = ['slug' => $slug];
            $sql = "SELECT COUNT(*) FROM articles WHERE slug = :slug";
            if ($excludeId) {
                $sql .= " AND id != :id";
                $params['id'] = $excludeId;
            }
            if ($this->db->count($sql, $params) === 0) {
                break;
            }
            $slug = $original . '-' . $counter++;
        }

        return $slug;
    }
}
