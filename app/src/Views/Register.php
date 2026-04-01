<!DOCTYPE html>
<html>

<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light d-flex align-items-center vh-100">
    <div class="container col-md-6">
        <div class="card shadow">
            <div class="card-header bg-success text-white">Register</div>
            <div class="card-body">
                <?php if (isset($error)): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
                <form method="POST" action="/register">
                    <div class="row mb-3">
                        <div class="col"><label>First Name</label><input type="text" name="first_name"
                                class="form-control" required></div>
                        <div class="col"><label>Last Name</label><input type="text" name="last_name"
                                class="form-control" required></div>
                    </div>
                    <div class="mb-3"><label>Email</label><input type="email" name="email" class="form-control"
                            required></div>
                    <div class="mb-3"><label>Password</label><input type="password" name="password" class="form-control"
                            required></div>
                    <div class="mb-3"><label>Role</label><select name="role" class="form-select">
                            <option value="student">Student</option>
                            <option value="tutor">Tutor</option>
                        </select></div>
                    <button type="submit" class="btn btn-success w-100">Register</button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>