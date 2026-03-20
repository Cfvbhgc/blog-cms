<div class="error-page">
    <h1><?= $code ?? 404 ?></h1>
    <p><?= htmlspecialchars($message ?? 'The page you are looking for could not be found.') ?></p>
    <a href="/" class="btn btn-primary">Go Home</a>
</div>
