<?php
require_once 'config.php';

// Redirection si déjà connecté
if (is_user_logged_in()) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_POST['action'] ?? '' === 'login') {
    $email = sanitize_input($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = "Veuillez remplir tous les champs";
    } else {
        $user = verify_user($email, $password);
        if ($user) {
            $_SESSION['user'] = $user;
            
            // Redirection selon le rôle
            if ($user['role'] === 'admin') {
                $_SESSION['admin_logged_in'] = true;
                header('Location: orders.php');
            } else {
                header('Location: index.php');
            }
            exit;
        } else {
            $error = "Email ou mot de passe incorrect";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .login-container {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .login-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            padding: 2rem;
            width: 100%;
            max-width: 450px;
        }
        .login-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .login-header i {
            background: linear-gradient(45deg, #dc3545, #e74c3c);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .form-control {
            padding: 12px 15px;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            transition: all 0.3s ease;
        }
        .form-control:focus {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }
        .btn-login {
            background: linear-gradient(45deg, #dc3545, #e74c3c);
            border: none;
            padding: 12px;
            font-weight: 600;
            border-radius: 10px;
            transition: all 0.3s ease;
            color: white;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(220, 53, 69, 0.4);
        }
        .login-links {
            text-align: center;
            margin-top: 1.5rem;
        }
        .login-links a {
            color: #dc3545;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        .login-links a:hover {
            color: #c82333;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold text-danger" href="index.php">
                <i class="fas fa-crown me-2"></i><?php echo SITE_NAME; ?>
            </a>
            <div class="navbar-nav ms-auto">
                <a href="index.php" class="btn btn-outline-light btn-sm">
                    <i class="fas fa-arrow-left me-1"></i>Retour au site
                </a>
            </div>
        </div>
    </nav>

    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <i class="fas fa-sign-in-alt fa-3x mb-3"></i>
                <h2 class="fw-bold text-dark">Connexion</h2>
                <p class="text-muted">Accédez à votre compte <?php echo SITE_NAME; ?></p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <?php echo $error; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <form method="POST" class="needs-validation" novalidate>
                <input type="hidden" name="action" value="login">
                
                <div class="mb-3">
                    <label class="form-label fw-bold">Adresse Email</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light">
                            <i class="fas fa-envelope text-muted"></i>
                        </span>
                        <input type="email" 
                               class="form-control" 
                               name="email" 
                               value="<?php echo $_POST['email'] ?? ''; ?>" 
                               required
                               placeholder="votre@email.com"
                               autocomplete="email">
                        <div class="invalid-feedback">
                            Veuillez entrer une adresse email valide.
                        </div>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="form-label fw-bold">Mot de passe</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light">
                            <i class="fas fa-lock text-muted"></i>
                        </span>
                        <input type="password" 
                               class="form-control" 
                               name="password" 
                               required
                               placeholder="Votre mot de passe"
                               autocomplete="current-password"
                               minlength="6">
                        <div class="invalid-feedback">
                            Le mot de passe doit contenir au moins 6 caractères.
                        </div>
                    </div>
                </div>
                
                <div class="d-grid mb-3">
                    <button type="submit" class="btn btn-login btn-lg">
                        <i class="fas fa-sign-in-alt me-2"></i>Se connecter
                    </button>
                </div>
                
                <div class="login-links">
                    <p class="mb-2">Pas encore de compte ? 
                        <a href="register.php" class="fw-bold">Créer un compte</a>
                    </p>
                    <p class="mb-0">
                        <a href="forgot_password.php">Mot de passe oublié ?</a>
                    </p>
                </div>

                <!-- Lien admin -->
                <div class="text-center mt-4 pt-3 border-top">
                    <small class="text-muted">
                        Accès administrateur ? 
                        <a href="admin_login.php" class="text-decoration-none">Cliquez ici</a>
                    </small>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Validation côté client
        (function () {
            'use strict'
            var forms = document.querySelectorAll('.needs-validation')
            Array.prototype.slice.call(forms)
                .forEach(function (form) {
                    form.addEventListener('submit', function (event) {
                        if (!form.checkValidity()) {
                            event.preventDefault()
                            event.stopPropagation()
                        }
                        form.classList.add('was-validated')
                    }, false)
                })
        })()

        // Afficher/masquer le mot de passe
        document.addEventListener('DOMContentLoaded', function() {
            const passwordInput = document.querySelector('input[name="password"]');
            const toggleButton = document.createElement('button');
            toggleButton.type = 'button';
            toggleButton.className = 'btn btn-outline-secondary';
            toggleButton.innerHTML = '<i class="fas fa-eye"></i>';
            toggleButton.style.marginLeft = '5px';
            
            toggleButton.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                this.innerHTML = type === 'password' ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
            });
            
            passwordInput.parentNode.appendChild(toggleButton);
        });
    </script>
</body>
</html>