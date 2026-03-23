<h1><?= $article ? 'Edit Article' : 'Create Article' ?></h1>

<form action="<?= $article ? '/admin/articles/' . $article['id'] . '/update' : '/admin/articles/store' ?>"
      method="POST" class="admin-form">
    <input type="hidden" name="csrf_token" value="<?= $csrf ?>">

    <div class="form-group">
        <label for="title">Title *</label>
        <input type="text" id="title" name="title" required
               value="<?= htmlspecialchars($article['title'] ?? '') ?>">
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="category_id">Category</label>
            <select id="category_id" name="category_id">
                <option value="">— Select Category —</option>
                <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>"
                    <?= ($article['category_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['name']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="status">Status</label>
            <select id="status" name="status">
                <option value="draft" <?= ($article['status'] ?? 'draft') === 'draft' ? 'selected' : '' ?>>Draft</option>
                <option value="published" <?= ($article['status'] ?? '') === 'published' ? 'selected' : '' ?>>Published</option>
            </select>
        </div>
    </div>

    <div class="form-group">
        <label for="excerpt">Excerpt</label>
        <textarea id="excerpt" name="excerpt" rows="2"><?= htmlspecialchars($article['excerpt'] ?? '') ?></textarea>
    </div>

    <div class="form-group">
        <label for="content">Content * (HTML supported)</label>
        <textarea id="content" name="content" rows="15" required><?= htmlspecialchars($article['content'] ?? '') ?></textarea>
    </div>

    <div class="form-group">
        <label>Tags</label>
        <div class="checkbox-group">
            <?php foreach ($tags as $tag): ?>
            <label class="checkbox-label">
                <input type="checkbox" name="tags[]" value="<?= $tag['id'] ?>"
                    <?= in_array($tag['id'], $selectedTags) ? 'checked' : '' ?>>
                <?= htmlspecialchars($tag['name']) ?>
            </label>
            <?php endforeach; ?>
        </div>
    </div>

    <fieldset class="form-fieldset">
        <legend>SEO Settings</legend>
        <div class="form-group">
            <label for="seo_title">SEO Title</label>
            <input type="text" id="seo_title" name="seo_title"
                   value="<?= htmlspecialchars($article['seo_title'] ?? '') ?>">
        </div>
        <div class="form-group">
            <label for="seo_description">SEO Description</label>
            <textarea id="seo_description" name="seo_description" rows="2"><?= htmlspecialchars($article['seo_description'] ?? '') ?></textarea>
        </div>
    </fieldset>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary"><?= $article ? 'Update Article' : 'Create Article' ?></button>
        <a href="/admin/articles" class="btn">Cancel</a>
    </div>
</form>
