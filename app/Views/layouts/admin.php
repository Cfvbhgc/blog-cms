<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Admin') ?> | Blog CMS Admin</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="admin-body">
    <?php if (!empty($_SESSION['admin_logged_in'])): ?>
    <aside class="admin-sidebar">
        <div class="sidebar-logo">
            <a href="/admin">Blog<span>CMS</span></a>
        </div>
        <nav class="sidebar-nav">
            <a href="/admin" class="<?= ($_SERVER['REQUEST_URI'] === '/admin') ? 'active' : '' ?>">Dashboard</a>
            <a href="/admin/articles" class="<?= str_starts_with($_SERVER['REQUEST_URI'], '/admin/articles') ? 'active' : '' ?>">Articles</a>
            <a href="/admin/categories" class="<?= str_starts_with($_SERVER['REQUEST_URI'], '/admin/categories') ? 'active' : '' ?>">Categories</a>
            <a href="/admin/tags" class="<?= str_starts_with($_SERVER['REQUEST_URI'], '/admin/tags') ? 'active' : '' ?>">Tags</a>
            <a href="/admin/comments" class="<?= str_starts_with($_SERVER['REQUEST_URI'], '/admin/comments') ? 'active' : '' ?>">Comments</a>
        </nav>
        <div class="sidebar-footer">
            <a href="/" class="btn-link">View Site</a>
            <a href="/admin/logout" class="btn-link">Logout</a>
        </div>
    </aside>
    <?php endif; ?>

    <div class="admin-content <?= empty($_SESSION['admin_logged_in']) ? 'full-width' : '' ?>">
        <?php
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        if ($flash): ?>
        <div class="alert alert-<?= $flash['type'] ?>">
            <?= htmlspecialchars($flash['message']) ?>
        </div>
        <?php endif; ?>

        <?= $content ?>
    </div>
</body>
</html>
