<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Comment;

class ArticleController extends Controller
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
     * Display published articles list (public).
     */
    public function index(): void
    {
        $page = max(1, (int) $this->query('page', 1));
        $perPage = 6;
        $articles = $this->article->getPublished($page, $perPage);
        $total = $this->article->countPublished();
        $totalPages = (int) ceil($total / $perPage);
        $categories = $this->category->getAllWithCounts();
        $tags = $this->tag->getAllWithCounts();

        $this->view('articles.index', [
            'pageTitle'  => 'Blog',
            'articles'   => $articles,
            'categories' => $categories,
            'tags'       => $tags,
            'page'       => $page,
            'totalPages' => $totalPages,
        ]);
    }

    /**
     * Display a single article (public).
     */
    public function show(string $slug): void
    {
        $article = $this->article->findBySlug($slug);
        if (!$article || $article['status'] !== 'published') {
            http_response_code(404);
            $this->view('errors.404', ['pageTitle' => 'Not Found']);
            return;
        }

        $tags = $this->article->getTags($article['id']);
        $comments = $this->comment->getByArticle($article['id']);

        $this->view('articles.show', [
            'pageTitle' => $article['seo_title'] ?: $article['title'],
            'metaDesc'  => $article['seo_description'] ?: $article['excerpt'],
            'article'   => $article,
            'tags'      => $tags,
            'comments'  => $comments,
        ]);
    }

    /**
     * Display articles by category (public).
     */
    public function byCategory(string $slug): void
    {
        $category = $this->category->findBySlug($slug);
        if (!$category) {
            http_response_code(404);
            $this->view('errors.404', ['pageTitle' => 'Not Found']);
            return;
        }

        $page = max(1, (int) $this->query('page', 1));
        $articles = $this->article->getByCategory($slug, $page, 6);
        $categories = $this->category->getAllWithCounts();

        $this->view('articles.index', [
            'pageTitle'       => 'Category: ' . $category['name'],
            'articles'        => $articles,
            'categories'      => $categories,
            'tags'            => $this->tag->getAllWithCounts(),
            'page'            => $page,
            'totalPages'      => 1,
            'currentCategory' => $category,
        ]);
    }

    /**
     * Display articles by tag (public).
     */
    public function byTag(string $slug): void
    {
        $tag = $this->tag->findBySlug($slug);
        if (!$tag) {
            http_response_code(404);
            $this->view('errors.404', ['pageTitle' => 'Not Found']);
            return;
        }

        $page = max(1, (int) $this->query('page', 1));
        $articles = $this->article->getByTag($slug, $page, 6);
        $categories = $this->category->getAllWithCounts();

        $this->view('articles.index', [
            'pageTitle'  => 'Tag: ' . $tag['name'],
            'articles'   => $articles,
            'categories' => $categories,
            'tags'       => $this->tag->getAllWithCounts(),
            'page'       => $page,
            'totalPages' => 1,
            'currentTag' => $tag,
        ]);
    }

    /**
     * Submit a comment on an article (public POST).
     */
    public function comment(string $slug): void
    {
        $article = $this->article->findBySlug($slug);
        if (!$article) {
            $this->redirect('/');
            return;
        }

        if (!$this->validateCsrf()) {
            $this->setFlash('error', 'Invalid form submission.');
            $this->redirect('/article/' . $slug);
            return;
        }

        $authorName = trim($this->input('author_name', ''));
        $email = trim($this->input('email', ''));
        $content = trim($this->input('content', ''));

        if (empty($authorName) || empty($email) || empty($content)) {
            $this->setFlash('error', 'All fields are required.');
            $this->redirect('/article/' . $slug);
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->setFlash('error', 'Invalid email address.');
            $this->redirect('/article/' . $slug);
            return;
        }

        $this->comment->create([
            'article_id'  => $article['id'],
            'author_name' => $authorName,
            'email'       => $email,
            'content'     => $content,
        ]);

        $this->setFlash('success', 'Comment submitted and awaiting moderation.');
        $this->redirect('/article/' . $slug);
    }
}
