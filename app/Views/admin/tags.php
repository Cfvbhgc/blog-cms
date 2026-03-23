<h1>Tags</h1>

<div class="admin-two-col">
    <div class="admin-form-col">
        <h2>Add Tag</h2>
        <form action="/admin/tags/store" method="POST" class="admin-form">
            <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
            <div class="form-group">
                <label for="name">Name *</label>
                <input type="text" id="name" name="name" required>
            </div>
            <button type="submit" class="btn btn-primary">Add Tag</button>
        </form>
    </div>

    <div class="admin-table-col">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Slug</th>
                    <th>Articles</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tags as $tag): ?>
                <tr>
                    <td><?= htmlspecialchars($tag['name']) ?></td>
                    <td><code><?= htmlspecialchars($tag['slug']) ?></code></td>
                    <td><?= $tag['article_count'] ?></td>
                    <td>
                        <a href="/admin/tags/<?= $tag['id'] ?>/delete" class="btn btn-sm btn-danger"
                           onclick="return confirm('Delete this tag?')">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
