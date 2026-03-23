<h1>Categories</h1>

<div class="admin-two-col">
    <div class="admin-form-col">
        <h2>Add Category</h2>
        <form action="/admin/categories/store" method="POST" class="admin-form">
            <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
            <div class="form-group">
                <label for="name">Name *</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="3"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Add Category</button>
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
                <?php foreach ($categories as $cat): ?>
                <tr>
                    <td><?= htmlspecialchars($cat['name']) ?></td>
                    <td><code><?= htmlspecialchars($cat['slug']) ?></code></td>
                    <td><?= $cat['article_count'] ?></td>
                    <td>
                        <a href="/admin/categories/<?= $cat['id'] ?>/delete" class="btn btn-sm btn-danger"
                           onclick="return confirm('Delete this category?')">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
