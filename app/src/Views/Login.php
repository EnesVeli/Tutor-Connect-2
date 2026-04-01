<!DOCTYPE html>
<html>
<head><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body class="bg-light d-flex align-items-center vh-100">
    <div class="container col-md-4"><div class="card shadow"><div class="card-header bg-primary text-white">Login</div><div class="card-body">
        <?php if (isset($error)): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
        <form method="POST" action="/login">
            <div class="mb-3"><label>Email</label><input type="email" name="email" class="form-control" required></div>
            <div class="mb-3"><label>Password</label><input type="password" name="password" class="form-control" required></div>
            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>
    </div><div class="card-footer text-center"><a href="/register">Create an account</a></div></div></div>
</body>
</html>