<?php

namespace App\Models;

use App\Core\Database;

class Category
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Get all categories.
     */
    public function getAll(): array
    {
        return $this->db->query("SELECT * FROM categories ORDER BY name ASC");
    }

    /**
     * Get all categories with article counts.
     */
    public function getAllWithCounts(): array
    {
        return $this->db->query(
            "SELECT c.*, COUNT(a.id) AS article_count
             FROM categories c
             LEFT JOIN articles a ON c.id = a.category_id AND a.status = 'published'
             GROUP BY c.id
             ORDER BY c.name ASC"
        );
    }

    /**
     * Find a category by slug.
     */
    public function findBySlug(string $slug): ?array
    {
        return $this->db->queryOne(
            "SELECT * FROM categories WHERE slug = :slug",
            ['slug' => $slug]
        );
    }

    /**
     * Find a category by ID.
     */
    public function findById(int $id): ?array
    {
        return $this->db->queryOne(
            "SELECT * FROM categories WHERE id = :id",
            ['id' => $id]
        );
    }

    /**
     * Create a new category.
     */
    public function create(array $data): int
    {
        $this->db->execute(
            "INSERT INTO categories (name, slug, description) VALUES (:name, :slug, :description)",
            [
                'name'        => $data['name'],
                'slug'        => $data['slug'],
                'description' => $data['description'] ?? '',
            ]
        );
        return (int) $this->db->lastInsertId();
    }

    /**
     * Update a category.
     */
    public function update(int $id, array $data): void
    {
        $this->db->execute(
            "UPDATE categories SET name = :name, slug = :slug, description = :description WHERE id = :id",
            [
                'id'          => $id,
                'name'        => $data['name'],
                'slug'        => $data['slug'],
                'description' => $data['description'] ?? '',
            ]
        );
    }

    /**
     * Delete a category.
     */
    public function delete(int $id): void
    {
        $this->db->execute("DELETE FROM categories WHERE id = :id", ['id' => $id]);
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
            $sql = "SELECT COUNT(*) FROM categories WHERE slug = :slug";
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
