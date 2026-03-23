<h1>Dashboard</h1>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-number"><?= $articleCount ?></div>
        <div class="stat-label">Articles</div>
    </div>
    <div class="stat-card">
        <div class="stat-number"><?= $categoryCount ?></div>
        <div class="stat-label">Categories</div>
    </div>
    <div class="stat-card">
        <div class="stat-number"><?= $tagCount ?></div>
        <div class="stat-label">Tags</div>
    </div>
    <div class="stat-card">
        <div class="stat-number"><?= $commentCount ?></div>
        <div class="stat-label">Comments</div>
    </div>
    <?php if ($pendingComments > 0): ?>
    <div class="stat-card stat-warning">
        <div class="stat-number"><?= $pendingComments ?></div>
        <div class="stat-label">Pending Comments</div>
    </div>
    <?php endif; ?>
</div>

<div class="admin-section">
    <div class="section-header">
        <h2>Recent Articles</h2>
        <a href="/admin/articles/create" class="btn btn-primary">+ New Article</a>
    </div>
    <table class="admin-table">
        <thead>
            <tr>
                <th>Title</th>
                <th>Category</th>
                <th>Status</th>
                <th>Created</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($recentArticles as $article): ?>
            <tr>
                <td><a href="/admin/articles/<?= $article['id'] ?>/edit"><?= htmlspecialchars($article['title']) ?></a></td>
                <td><?= htmlspecialchars($article['category_name'] ?? '—') ?></td>
                <td>
                    <span class="badge badge-<?= $article['status'] === 'published' ? 'success' : 'warning' ?>">
                        <?= $article['status'] ?>
                    </span>
                </td>
                <td><?= date('M j, Y', strtotime($article['created_at'])) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
