<div class="section-header">
    <h1>Comments</h1>
    <?php if ($pending > 0): ?>
    <span class="badge badge-warning"><?= $pending ?> pending</span>
    <?php endif; ?>
</div>

<table class="admin-table">
    <thead>
        <tr>
            <th>Author</th>
            <th>Comment</th>
            <th>Article</th>
            <th>Status</th>
            <th>Date</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($comments)): ?>
        <tr><td colspan="6" class="empty-state">No comments yet.</td></tr>
        <?php else: ?>
        <?php foreach ($comments as $comment): ?>
        <tr class="<?= $comment['status'] === 'pending' ? 'row-highlight' : '' ?>">
            <td>
                <strong><?= htmlspecialchars($comment['author_name']) ?></strong><br>
                <small><?= htmlspecialchars($comment['email']) ?></small>
            </td>
            <td class="comment-preview"><?= htmlspecialchars(mb_substr($comment['content'], 0, 100)) ?>...</td>
            <td><a href="/article/<?= htmlspecialchars($comment['article_slug']) ?>" target="_blank">
                <?= htmlspecialchars($comment['article_title']) ?>
            </a></td>
            <td>
                <span class="badge badge-<?= $comment['status'] === 'approved' ? 'success' : 'warning' ?>">
                    <?= $comment['status'] ?>
                </span>
            </td>
            <td><?= date('M j, Y', strtotime($comment['created_at'])) ?></td>
            <td class="actions">
                <?php if ($comment['status'] === 'pending'): ?>
                <a href="/admin/comments/<?= $comment['id'] ?>/approve" class="btn btn-sm btn-success">Approve</a>
                <?php endif; ?>
                <a href="/admin/comments/<?= $comment['id'] ?>/delete" class="btn btn-sm btn-danger"
                   onclick="return confirm('Delete this comment?')">Delete</a>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>
