<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Blog CMS') ?> | Blog CMS</title>
    <?php if (!empty($metaDesc)): ?>
    <meta name="description" content="<?= htmlspecialchars($metaDesc) ?>">
    <?php endif; ?>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <header class="site-header">
        <div class="container">
            <div class="header-inner">
                <a href="/" class="logo">Blog<span>CMS</span></a>
                <nav class="main-nav">
                    <a href="/">Articles</a>
                    <a href="/admin">Admin Panel</a>
                </nav>
            </div>
        </div>
    </header>

    <main class="site-main">
        <div class="container">
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
    </main>

    <footer class="site-footer">
        <div class="container">
            <p>&copy; <?= date('Y') ?> Blog CMS &mdash; Pure PHP MVC Demo Project</p>
        </div>
    </footer>
</body>
</html>
