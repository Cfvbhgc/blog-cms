<?php

/**
 * Blog CMS - Entry Point
 *
 * All requests are routed through this file via Nginx rewrite rules.
 */

require_once __DIR__ . '/../vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->safeLoad();

// Start session
session_start();

// Initialize router
$router = new App\Core\Router();

// --- Public Routes ---
$router->get('/', 'ArticleController', 'index');
$router->get('/article/{slug}', 'ArticleController', 'show');
$router->post('/article/{slug}/comment', 'ArticleController', 'comment');
$router->get('/category/{slug}', 'ArticleController', 'byCategory');
$router->get('/tag/{slug}', 'ArticleController', 'byTag');

// --- Admin Routes ---
$router->get('/admin/login', 'AdminController', 'loginForm');
$router->post('/admin/login', 'AdminController', 'login');
$router->get('/admin/logout', 'AdminController', 'logout');
$router->get('/admin', 'AdminController', 'dashboard');

// Admin: Articles
$router->get('/admin/articles', 'AdminController', 'articles');
$router->get('/admin/articles/create', 'AdminController', 'articleCreate');
$router->post('/admin/articles/store', 'AdminController', 'articleStore');
$router->get('/admin/articles/{id}/edit', 'AdminController', 'articleEdit');
$router->post('/admin/articles/{id}/update', 'AdminController', 'articleUpdate');
$router->get('/admin/articles/{id}/delete', 'AdminController', 'articleDelete');

// Admin: Categories
$router->get('/admin/categories', 'AdminController', 'categories');
$router->post('/admin/categories/store', 'AdminController', 'categoryStore');
$router->get('/admin/categories/{id}/delete', 'AdminController', 'categoryDelete');

// Admin: Tags
$router->get('/admin/tags', 'AdminController', 'tags');
$router->post('/admin/tags/store', 'AdminController', 'tagStore');
$router->get('/admin/tags/{id}/delete', 'AdminController', 'tagDelete');

// Admin: Comments
$router->get('/admin/comments', 'AdminController', 'comments');
$router->get('/admin/comments/{id}/approve', 'AdminController', 'commentApprove');
$router->get('/admin/comments/{id}/delete', 'AdminController', 'commentDelete');

// Dispatch the request
$router->dispatch();
