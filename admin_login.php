<?php
require_once 'config.php';

// Redirection si déjà connecté
if (is_admin_logged_in()) {
    header('Location: orders.php');
    exit;
}

$error = '';

if ($_POST['action'] ?? '' === 'login') {
    $email = sanitize_input($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if ($email === ADMIN_EMAIL && $password === ADMIN_PASSWORD) {
        $_SESSION['admin_logged_in'] = true;
        header('Location: orders.php');
        exit;
    } else {
        $error = "Email ou mot de passe administrateur incorrect";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .login-container {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            padding: 2rem;
            width: 100%;
            max-width: 400px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="text-center mb-4">
                <i class="fas fa-crown fa-3x text-primary mb-3"></i>
                <h2 class="fw-bold"><?php echo SITE_NAME; ?></h2>
                <p class="text-muted">Espace Administrateur</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i><?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST">
                <input type="hidden" name="action" value="login">
                
                <div class="mb-3">
                    <label class="form-label fw-bold">Email Admin</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-envelope"></i>
                        </span>
                        <input type="email" 
                               class="form-control" 
                               name="email" 
                               value="<?php echo ADMIN_EMAIL; ?>"
                               required>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label fw-bold">Mot de passe</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input type="password" 
                               class="form-control" 
                               name="password" 
                               placeholder="Mot de passe administrateur" 
                               required>
                    </div>
                </div>
                
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-sign-in-alt me-2"></i>Se connecter
                    </button>
                </div>
            </form>
            
            <div class="text-center mt-4">
                <a href="index.php" class="text-decoration-none">
                    <i class="fas fa-arrow-left me-2"></i>Retour au site
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>