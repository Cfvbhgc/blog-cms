<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Comment;

class AdminController extends Controller
{
    private Article $article;
    private Category $category;
    private Tag $tag;
    private Comment $comment;

    public function __construct()
    {
        $this->article = new Article();
        $this->category = new Category();
        $this->tag = new Tag();
        $this->comment = new Comment();
    }

    /**
     * Show login form.
     */
    public function loginForm(): void
    {
        if (!empty($_SESSION['admin_logged_in'])) {
            $this->redirect('/admin');
            return;
        }
        $this->view('admin.login', [
            'pageTitle' => 'Admin Login',
        ], 'admin');
    }

    /**
     * Process login.
     */
    public function login(): void
    {
        $username = $this->input('username', '');
        $password = $this->input('password', '');

        $adminUser = $_ENV['ADMIN_USERNAME'] ?? 'admin';
        $adminPass = $_ENV['ADMIN_PASSWORD'] ?? 'admin123';

        if ($username === $adminUser && $password === $adminPass) {
            $_SESSION['admin_logged_in'] = true;
            $this->setFlash('success', 'Welcome to the admin panel!');
            $this->redirect('/admin');
        } else {
            $this->setFlash('error', 'Invalid credentials.');
            $this->redirect('/admin/login');
        }
    }

    /**
     * Logout.
     */
    public function logout(): void
    {
        session_destroy();
        $this->redirect('/admin/login');
    }

    /**
     * Dashboard.
     */
    public function dashboard(): void
    {
        $this->requireAdmin();

        $this->view('admin.dashboard', [
            'pageTitle'       => 'Dashboard',
            'articleCount'    => $this->article->countAll(),
            'categoryCount'  => count($this->category->getAll()),
            'tagCount'       => count($this->tag->getAll()),
            'commentCount'   => $this->comment->countAll(),
            'pendingComments' => $this->comment->countPending(),
            'recentArticles' => $this->article->getAll(1, 5),
        ], 'admin');
    }

    // --- Article Management ---

    /**
     * List all articles.
     */
    public function articles(): void
    {
        $this->requireAdmin();
        $page = max(1, (int) $this->query('page', 1));
        $articles = $this->article->getAll($page, 20);

        $this->view('admin.articles', [
            'pageTitle' => 'Manage Articles',
            'articles'  => $articles,
        ], 'admin');
    }

    /**
     * Show article create form.
     */
    public function articleCreate(): void
    {
        $this->requireAdmin();
        $this->view('admin.article-form', [
            'pageTitle'  => 'Create Article',
            'article'    => null,
            'categories' => $this->category->getAll(),
            'tags'       => $this->tag->getAll(),
            'selectedTags' => [],
            'csrf'       => $this->generateCsrf(),
        ], 'admin');
    }

    /**
     * Store a new article.
     */
    public function articleStore(): void
    {
        $this->requireAdmin();
        if (!$this->validateCsrf()) {
            $this->setFlash('error', 'Invalid form submission.');
            $this->redirect('/admin/articles/create');
            return;
        }

        $data = [
            'title'           => trim($this->input('title', '')),
            'slug'            => '',
            'content'         => $this->input('content', ''),
            'excerpt'         => trim($this->input('excerpt', '')),
            'seo_title'       => trim($this->input('seo_title', '')),
            'seo_description' => trim($this->input('seo_description', '')),
            'category_id'     => $this->input('category_id', ''),
            'status'          => $this->input('status', 'draft'),
        ];

        if (empty($data['title'])) {
            $this->setFlash('error', 'Title is required.');
            $this->redirect('/admin/articles/create');
            return;
        }

        $data['slug'] = $this->article->generateSlug($data['title']);
        $articleId = $this->article->create($data);

        // Sync tags
        $tagIds = $this->input('tags', []);
        if (!empty($tagIds) && is_array($tagIds)) {
            $this->article->syncTags($articleId, $tagIds);
        }

        $this->setFlash('success', 'Article created successfully.');
        $this->redirect('/admin/articles');
    }

    /**
     * Show article edit form.
     */
    public function articleEdit(string $id): void
    {
        $this->requireAdmin();
        $article = $this->article->findById((int) $id);
        if (!$article) {
            $this->setFlash('error', 'Article not found.');
            $this->redirect('/admin/articles');
            return;
        }

        $this->view('admin.article-form', [
            'pageTitle'    => 'Edit Article',
            'article'      => $article,
            'categories'   => $this->category->getAll(),
            'tags'         => $this->tag->getAll(),
            'selectedTags' => $this->article->getTagIds($article['id']),
            'csrf'         => $this->generateCsrf(),
        ], 'admin');
    }

    /**
     * Update an article.
     */
    public function articleUpdate(string $id): void
    {
        $this->requireAdmin();
        if (!$this->validateCsrf()) {
            $this->setFlash('error', 'Invalid form submission.');
            $this->redirect('/admin/articles/' . $id . '/edit');
            return;
        }

        $articleId = (int) $id;
        $data = [
            'title'           => trim($this->input('title', '')),
            'slug'            => '',
            'content'         => $this->input('content', ''),
            'excerpt'         => trim($this->input('excerpt', '')),
            'seo_title'       => trim($this->input('seo_title', '')),
            'seo_description' => trim($this->input('seo_description', '')),
            'category_id'     => $this->input('category_id', ''),
            'status'          => $this->input('status', 'draft'),
        ];

        if (empty($data['title'])) {
            $this->setFlash('error', 'Title is required.');
            $this->redirect('/admin/articles/' . $id . '/edit');
            return;
        }

        $data['slug'] = $this->article->generateSlug($data['title'], $articleId);
        $this->article->update($articleId, $data);

        // Sync tags
        $tagIds = $this->input('tags', []);
        $this->article->syncTags($articleId, is_array($tagIds) ? $tagIds : []);

        $this->setFlash('success', 'Article updated successfully.');
        $this->redirect('/admin/articles');
    }

    /**
     * Delete an article.
     */
    public function articleDelete(string $id): void
    {
        $this->requireAdmin();
        $this->article->delete((int) $id);
        $this->setFlash('success', 'Article deleted.');
        $this->redirect('/admin/articles');
    }

    // --- Category Management ---

    public function categories(): void
    {
        $this->requireAdmin();
        $this->view('admin.categories', [
            'pageTitle'  => 'Manage Categories',
            'categories' => $this->category->getAllWithCounts(),
            'csrf'       => $this->generateCsrf(),
        ], 'admin');
    }

    public function categoryStore(): void
    {
        $this->requireAdmin();
        if (!$this->validateCsrf()) {
            $this->setFlash('error', 'Invalid form submission.');
            $this->redirect('/admin/categories');
            return;
        }

        $name = trim($this->input('name', ''));
        if (empty($name)) {
            $this->setFlash('error', 'Category name is required.');
            $this->redirect('/admin/categories');
            return;
        }

        $this->category->create([
            'name'        => $name,
            'slug'        => $this->category->generateSlug($name),
            'description' => trim($this->input('description', '')),
        ]);

        $this->setFlash('success', 'Category created.');
        $this->redirect('/admin/categories');
    }

    public function categoryDelete(string $id): void
    {
        $this->requireAdmin();
        $this->category->delete((int) $id);
        $this->setFlash('success', 'Category deleted.');
        $this->redirect('/admin/categories');
    }

    // --- Tag Management ---

    public function tags(): void
    {
        $this->requireAdmin();
        $this->view('admin.tags', [
            'pageTitle' => 'Manage Tags',
            'tags'      => $this->tag->getAllWithCounts(),
            'csrf'      => $this->generateCsrf(),
        ], 'admin');
    }

    public function tagStore(): void
    {
        $this->requireAdmin();
        if (!$this->validateCsrf()) {
            $this->setFlash('error', 'Invalid form submission.');
            $this->redirect('/admin/tags');
            return;
        }

        $name = trim($this->input('name', ''));
        if (empty($name)) {
            $this->setFlash('error', 'Tag name is required.');
            $this->redirect('/admin/tags');
            return;
        }

        $this->tag->create([
            'name' => $name,
            'slug' => $this->tag->generateSlug($name),
        ]);

        $this->setFlash('success', 'Tag created.');
        $this->redirect('/admin/tags');
    }

    public function tagDelete(string $id): void
    {
        $this->requireAdmin();
        $this->tag->delete((int) $id);
        $this->setFlash('success', 'Tag deleted.');
        $this->redirect('/admin/tags');
    }

    // --- Comment Management ---

    public function comments(): void
    {
        $this->requireAdmin();
        $this->view('admin.comments', [
            'pageTitle' => 'Manage Comments',
            'comments'  => $this->comment->getAll(1, 50),
            'pending'   => $this->comment->countPending(),
        ], 'admin');
    }

    public function commentApprove(string $id): void
    {
        $this->requireAdmin();
        $this->comment->approve((int) $id);
        $this->setFlash('success', 'Comment approved.');
        $this->redirect('/admin/comments');
    }

    public function commentDelete(string $id): void
    {
        $this->requireAdmin();
        $this->comment->delete((int) $id);
        $this->setFlash('success', 'Comment deleted.');
        $this->redirect('/admin/comments');
    }
}
