<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="/">Tutor Connect</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item">
                        <span class="navbar-text text-white me-3">
                            Welcome, <strong><?= htmlspecialchars($_SESSION['user_name'] ?? 'User') ?></strong>
                            <span class="badge bg-light text-primary rounded-pill ms-1">
                                <?= ucfirst($_SESSION['user_role'] ?? 'Guest') ?>
                            </span>
                        </span>
                    </li>
                    <li class="nav-item">
                        <a href="/" class="nav-link text-white">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a href="/logout" class="btn btn-light btn-sm ms-2 text-primary fw-bold">Logout</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a href="/login" class="nav-link text-white">Login</a>
                    </li>
                    <li class="nav-item">
                        <a href="/register" class="btn btn-light btn-sm ms-2 text-primary fw-bold">Register</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>