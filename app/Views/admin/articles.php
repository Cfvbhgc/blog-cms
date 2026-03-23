<div class="section-header">
    <h1>Articles</h1>
    <a href="/admin/articles/create" class="btn btn-primary">+ New Article</a>
</div>

<table class="admin-table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Category</th>
            <th>Status</th>
            <th>Created</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($articles)): ?>
        <tr><td colspan="6" class="empty-state">No articles yet.</td></tr>
        <?php else: ?>
        <?php foreach ($articles as $article): ?>
        <tr>
            <td><?= $article['id'] ?></td>
            <td>
                <a href="/admin/articles/<?= $article['id'] ?>/edit">
                    <?= htmlspecialchars($article['title']) ?>
                </a>
            </td>
            <td><?= htmlspecialchars($article['category_name'] ?? '—') ?></td>
            <td>
                <span class="badge badge-<?= $article['status'] === 'published' ? 'success' : 'warning' ?>">
                    <?= $article['status'] ?>
                </span>
            </td>
            <td><?= date('M j, Y', strtotime($article['created_at'])) ?></td>
            <td class="actions">
                <a href="/admin/articles/<?= $article['id'] ?>/edit" class="btn btn-sm">Edit</a>
                <a href="/article/<?= htmlspecialchars($article['slug']) ?>" class="btn btn-sm" target="_blank">View</a>
                <a href="/admin/articles/<?= $article['id'] ?>/delete" class="btn btn-sm btn-danger"
                   onclick="return confirm('Delete this article?')">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>
