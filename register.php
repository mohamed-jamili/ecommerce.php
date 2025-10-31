<?php
require_once 'config.php';

// Redirection si déjà connecté
if (is_user_logged_in()) {
    header('Location: index.php');
    exit;
}

$errors = [];
$success = false;

if ($_POST['action'] ?? '' === 'register') {
    $full_name = sanitize_input($_POST['full_name'] ?? '');
    $email = sanitize_input($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $phone = sanitize_input($_POST['phone'] ?? '');
    
    // Validation
    if (empty($full_name)) {
        $errors[] = "Le nom complet est obligatoire";
    }
    
    if (empty($email) || !validate_email($email)) {
        $errors[] = "L'adresse email est invalide";
    }
    
    if (empty($password) || strlen($password) < 6) {
        $errors[] = "Le mot de passe doit contenir au moins 6 caractères";
    }
    
    if ($password !== $confirm_password) {
        $errors[] = "Les mots de passe ne correspondent pas";
    }
    
    if (empty($phone) || !validate_phone($phone)) {
        $errors[] = "Le numéro de téléphone doit contenir 10 chiffres";
    }
    
    // Vérifier si l'email existe déjà
    if (user_exists($email)) {
        $errors[] = "Cette adresse email est déjà utilisée";
    }
    
    // Si pas d'erreurs, créer l'utilisateur
    if (empty($errors)) {
        $user_data = [
            'id' => uniqid(),
            'full_name' => $full_name,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'phone' => $phone,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        if (save_user($user_data)) {
            $success = true;
        } else {
            $errors[] = "Une erreur est survenue lors de l'inscription";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold text-danger" href="index.php">
                <i class="fas fa-crown me-2"></i><?php echo SITE_NAME; ?>
            </a>
        </div>
    </nav>

    <div class="container py-5 mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-dark text-white text-center">
                        <h4 class="mb-0"><i class="fas fa-user-plus me-2"></i>Créer un compte</h4>
                    </div>
                    <div class="card-body p-4">
                        
                        <?php if ($success): ?>
                            <div class="alert alert-success text-center">
                                <i class="fas fa-check-circle fa-2x mb-3"></i>
                                <h5>Inscription réussie !</h5>
                                <p class="mb-3">Votre compte a été créé avec succès.</p>
                                <a href="login.php" class="btn btn-success">Se connecter</a>
                            </div>
                        <?php else: ?>
                            
                            <?php if (!empty($errors)): ?>
                                <div class="alert alert-danger">
                                    <h6 class="alert-heading">Veuillez corriger les erreurs suivantes:</h6>
                                    <ul class="mb-0">
                                        <?php foreach ($errors as $error): ?>
                                            <li><?php echo $error; ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>

                            <form method="POST">
                                <input type="hidden" name="action" value="register">
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Nom complet *</label>
                                    <input type="text" 
                                           class="form-control" 
                                           name="full_name" 
                                           value="<?php echo $_POST['full_name'] ?? ''; ?>" 
                                           required
                                           placeholder="Votre nom complet">
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Email *</label>
                                    <input type="email" 
                                           class="form-control" 
                                           name="email" 
                                           value="<?php echo $_POST['email'] ?? ''; ?>" 
                                           required
                                           placeholder="votre@email.com">
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Téléphone *</label>
                                    <input type="tel" 
                                           class="form-control" 
                                           name="phone" 
                                           value="<?php echo $_POST['phone'] ?? ''; ?>" 
                                           required
                                           pattern="[0-9]{10}"
                                           placeholder="0612345678">
                                    <small class="text-muted">10 chiffres sans espaces</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Mot de passe *</label>
                                    <input type="password" 
                                           class="form-control" 
                                           name="password" 
                                           required
                                           placeholder="Au moins 6 caractères"
                                           minlength="6">
                                </div>
                                
                                <div class="mb-4">
                                    <label class="form-label fw-bold">Confirmer le mot de passe *</label>
                                    <input type="password" 
                                           class="form-control" 
                                           name="confirm_password" 
                                           required
                                           placeholder="Retapez votre mot de passe">
                                </div>
                                
                                <div class="d-grid mb-3">
                                    <button type="submit" class="btn btn-danger btn-lg">
                                        <i class="fas fa-user-plus me-2"></i>S'inscrire
                                    </button>
                                </div>
                                
                                <div class="text-center">
                                    <p class="mb-0">Déjà un compte ? 
                                        <a href="login.php" class="text-decoration-none">Se connecter</a>
                                    </p>
                                </div>
                            </form>
                            
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>