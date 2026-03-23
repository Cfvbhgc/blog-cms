<div class="login-page">
    <div class="login-card">
        <h1>Blog<span>CMS</span> Admin</h1>
        <form action="/admin/login" method="POST" class="login-form">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required autofocus>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Login</button>
        </form>
        <p class="login-hint">Default: admin / admin123</p>
    </div>
</div>
