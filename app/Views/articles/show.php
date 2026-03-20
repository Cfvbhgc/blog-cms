<article class="article-detail">
    <header class="article-header">
        <?php if ($article['category_name']): ?>
        <a href="/category/<?= htmlspecialchars($article['category_slug']) ?>" class="article-category">
            <?= htmlspecialchars($article['category_name']) ?>
        </a>
        <?php endif; ?>
        <h1><?= htmlspecialchars($article['title']) ?></h1>
        <div class="article-meta">
            <time datetime="<?= $article['created_at'] ?>">
                Published on <?= date('F j, Y', strtotime($article['created_at'])) ?>
            </time>
            <?php if ($article['updated_at'] !== $article['created_at']): ?>
            <span>&bull; Updated <?= date('F j, Y', strtotime($article['updated_at'])) ?></span>
            <?php endif; ?>
        </div>
        <?php if (!empty($tags)): ?>
        <div class="article-tags">
            <?php foreach ($tags as $tag): ?>
            <a href="/tag/<?= htmlspecialchars($tag['slug']) ?>" class="tag"><?= htmlspecialchars($tag['name']) ?></a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </header>

    <div class="article-content prose">
        <?= $article['content'] ?>
    </div>
</article>

<section class="comments-section">
    <h2>Comments (<?= count($comments) ?>)</h2>

    <?php if (empty($comments)): ?>
        <p class="empty-state">No comments yet. Be the first to comment!</p>
    <?php else: ?>
        <div class="comments-list">
            <?php foreach ($comments as $comment): ?>
            <div class="comment">
                <div class="comment-header">
                    <strong class="comment-author"><?= htmlspecialchars($comment['author_name']) ?></strong>
                    <time class="comment-date"><?= date('M j, Y \a\t g:i A', strtotime($comment['created_at'])) ?></time>
                </div>
                <div class="comment-body">
                    <?= nl2br(htmlspecialchars($comment['content'])) ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <div class="comment-form-wrapper">
        <h3>Leave a Comment</h3>
        <form action="/article/<?= htmlspecialchars($article['slug']) ?>/comment" method="POST" class="comment-form">
            <?php if (empty($_SESSION['csrf_token'])) { $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); } ?>
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <div class="form-row">
                <div class="form-group">
                    <label for="author_name">Name *</label>
                    <input type="text" id="author_name" name="author_name" required>
                </div>
                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" id="email" name="email" required>
                </div>
            </div>
            <div class="form-group">
                <label for="content">Comment *</label>
                <textarea id="content" name="content" rows="4" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Submit Comment</button>
        </form>
    </div>
</section>

<a href="/" class="btn btn-back">&larr; Back to Articles</a>
