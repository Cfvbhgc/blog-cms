<div class="blog-layout">
    <div class="blog-main">
        <h1 class="page-title"><?= htmlspecialchars($pageTitle) ?></h1>

        <?php if (empty($articles)): ?>
            <p class="empty-state">No articles found.</p>
        <?php else: ?>
            <div class="article-grid">
                <?php foreach ($articles as $article): ?>
                <article class="article-card">
                    <div class="article-card-body">
                        <?php if ($article['category_name']): ?>
                        <a href="/category/<?= htmlspecialchars($article['category_slug']) ?>" class="article-category">
                            <?= htmlspecialchars($article['category_name']) ?>
                        </a>
                        <?php endif; ?>
                        <h2 class="article-card-title">
                            <a href="/article/<?= htmlspecialchars($article['slug']) ?>">
                                <?= htmlspecialchars($article['title']) ?>
                            </a>
                        </h2>
                        <p class="article-excerpt"><?= htmlspecialchars($article['excerpt']) ?></p>
                        <div class="article-meta">
                            <time datetime="<?= $article['created_at'] ?>">
                                <?= date('M j, Y', strtotime($article['created_at'])) ?>
                            </time>
                        </div>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>

            <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?page=<?= $page - 1 ?>" class="btn btn-sm">&laquo; Previous</a>
                <?php endif; ?>
                <span class="pagination-info">Page <?= $page ?> of <?= $totalPages ?></span>
                <?php if ($page < $totalPages): ?>
                    <a href="?page=<?= $page + 1 ?>" class="btn btn-sm">Next &raquo;</a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <aside class="blog-sidebar">
        <div class="sidebar-widget">
            <h3>Categories</h3>
            <ul class="sidebar-list">
                <?php foreach ($categories as $cat): ?>
                <li>
                    <a href="/category/<?= htmlspecialchars($cat['slug']) ?>">
                        <?= htmlspecialchars($cat['name']) ?>
                        <span class="count">(<?= $cat['article_count'] ?>)</span>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div class="sidebar-widget">
            <h3>Tags</h3>
            <div class="tag-cloud">
                <?php foreach ($tags as $tag): ?>
                <a href="/tag/<?= htmlspecialchars($tag['slug']) ?>" class="tag">
                    <?= htmlspecialchars($tag['name']) ?>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </aside>
</div>
