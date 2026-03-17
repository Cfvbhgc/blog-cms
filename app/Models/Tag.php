<?php

namespace App\Models;

use App\Core\Database;

class Tag
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Get all tags.
     */
    public function getAll(): array
    {
        return $this->db->query("SELECT * FROM tags ORDER BY name ASC");
    }

    /**
     * Get all tags with article counts.
     */
    public function getAllWithCounts(): array
    {
        return $this->db->query(
            "SELECT t.*, COUNT(at2.article_id) AS article_count
             FROM tags t
             LEFT JOIN article_tags at2 ON t.id = at2.tag_id
             LEFT JOIN articles a ON at2.article_id = a.id AND a.status = 'published'
             GROUP BY t.id
             ORDER BY t.name ASC"
        );
    }

    /**
     * Find a tag by slug.
     */
    public function findBySlug(string $slug): ?array
    {
        return $this->db->queryOne("SELECT * FROM tags WHERE slug = :slug", ['slug' => $slug]);
    }

    /**
     * Find a tag by ID.
     */
    public function findById(int $id): ?array
    {
        return $this->db->queryOne("SELECT * FROM tags WHERE id = :id", ['id' => $id]);
    }

    /**
     * Create a new tag.
     */
    public function create(array $data): int
    {
        $this->db->execute(
            "INSERT INTO tags (name, slug) VALUES (:name, :slug)",
            ['name' => $data['name'], 'slug' => $data['slug']]
        );
        return (int) $this->db->lastInsertId();
    }

    /**
     * Update a tag.
     */
    public function update(int $id, array $data): void
    {
        $this->db->execute(
            "UPDATE tags SET name = :name, slug = :slug WHERE id = :id",
            ['id' => $id, 'name' => $data['name'], 'slug' => $data['slug']]
        );
    }

    /**
     * Delete a tag.
     */
    public function delete(int $id): void
    {
        $this->db->execute("DELETE FROM article_tags WHERE tag_id = :id", ['id' => $id]);
        $this->db->execute("DELETE FROM tags WHERE id = :id", ['id' => $id]);
    }

    /**
     * Generate a unique slug.
     */
    public function generateSlug(string $name, ?int $excludeId = null): string
    {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name), '-'));
        $original = $slug;
        $counter = 1;

        while (true) {
            $params = ['slug' => $slug];
            $sql = "SELECT COUNT(*) FROM tags WHERE slug = :slug";
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
