<?php

namespace App\Models;

use App\Core\Database;

class Comment
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Get approved comments for an article.
     */
    public function getByArticle(int $articleId): array
    {
        return $this->db->query(
            "SELECT * FROM comments WHERE article_id = :id AND status = 'approved' ORDER BY created_at ASC",
            ['id' => $articleId]
        );
    }

    /**
     * Get all comments (admin view) with article titles.
     */
    public function getAll(int $page = 1, int $perPage = 20): array
    {
        $offset = ($page - 1) * $perPage;
        return $this->db->query(
            "SELECT cm.*, a.title AS article_title, a.slug AS article_slug
             FROM comments cm
             JOIN articles a ON cm.article_id = a.id
             ORDER BY cm.created_at DESC
             LIMIT :limit OFFSET :offset",
            ['limit' => $perPage, 'offset' => $offset]
        );
    }

    /**
     * Get pending comments.
     */
    public function getPending(): array
    {
        return $this->db->query(
            "SELECT cm.*, a.title AS article_title, a.slug AS article_slug
             FROM comments cm
             JOIN articles a ON cm.article_id = a.id
             WHERE cm.status = 'pending'
             ORDER BY cm.created_at DESC"
        );
    }

    /**
     * Count all comments.
     */
    public function countAll(): int
    {
        return $this->db->count("SELECT COUNT(*) FROM comments");
    }

    /**
     * Count pending comments.
     */
    public function countPending(): int
    {
        return $this->db->count("SELECT COUNT(*) FROM comments WHERE status = 'pending'");
    }

    /**
     * Find a comment by ID.
     */
    public function findById(int $id): ?array
    {
        return $this->db->queryOne("SELECT * FROM comments WHERE id = :id", ['id' => $id]);
    }

    /**
     * Create a new comment.
     */
    public function create(array $data): int
    {
        $this->db->execute(
            "INSERT INTO comments (article_id, author_name, email, content, status)
             VALUES (:article_id, :author_name, :email, :content, 'pending')",
            [
                'article_id'  => $data['article_id'],
                'author_name' => $data['author_name'],
                'email'       => $data['email'],
                'content'     => $data['content'],
            ]
        );
        return (int) $this->db->lastInsertId();
    }

    /**
     * Approve a comment.
     */
    public function approve(int $id): void
    {
        $this->db->execute(
            "UPDATE comments SET status = 'approved' WHERE id = :id",
            ['id' => $id]
        );
    }

    /**
     * Delete a comment.
     */
    public function delete(int $id): void
    {
        $this->db->execute("DELETE FROM comments WHERE id = :id", ['id' => $id]);
    }
}
