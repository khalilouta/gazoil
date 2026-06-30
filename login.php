<?php
require_once __DIR__ . '/auth.php';

$error = '';

if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (attemptLogin($username, $password)) {
        header('Location: index.php');
        exit;
    }

    $error = 'Nom d\'utilisateur ou mot de passe invalide.';
}
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Connexion - Tableau de Suivi du Carburant</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body class="login-page">
<div class="login-shell">
    <div class="login-card card shadow-lg border-0">
        <div class="card-body p-4 p-md-5">
            <div class="text-center mb-4">
                <div class="login-badge mb-3"><i class="fa-solid fa-gas-pump"></i></div>
                <h1 class="h3 mb-2">Tableau de Suivi du Carburant</h1>
                <p class="text-muted mb-0">Connectez-vous pour gérer les bons, budgets et allocations.</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="post" class="d-grid gap-3">
                <div>
                    <label class="form-label">Nom d'utilisateur</label>
                    <input type="text" name="username" class="form-control form-control-lg" value="cmnaour" required autofocus>
                </div>
                <div>
                    <label class="form-label">Mot de passe</label>
                    <input type="password" name="password" class="form-control form-control-lg" required>
                </div>
                <button type="submit" class="btn btn-primary btn-lg">Se connecter</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>