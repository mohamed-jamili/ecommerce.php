<?php
require_once 'config.php';
require_user_login(); // Protection de la page

$user = $_SESSION['user'];
$success = '';
$error = '';

if ($_POST['action'] ?? '' === 'update_profile') {
    $full_name = sanitize_input($_POST['full_name'] ?? '');
    $phone = sanitize_input($_POST['phone'] ?? '');
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    
    // Validation
    if (empty($full_name)) {
        $error = "Le nom complet est obligatoire";
    }
    
    if (empty($phone) || !validate_phone($phone)) {
        $error = "Le numéro de téléphone doit contenir 10 chiffres";
    }
    
    // Si un nouveau mot de passe est fourni
    if (!empty($new_password)) {
        if (empty($current_password)) {
            $error = "Le mot de passe actuel est requis pour changer le mot de passe";
        } elseif (strlen($new_password) < 6) {
            $error = "Le nouveau mot de passe doit contenir au moins 6 caractères";
        } else {
            // Vérifier le mot de passe actuel
            $users = get_users();
            $current_user = null;
            foreach ($users as $u) {
                if ($u['email'] === $user['email']) {
                    $current_user = $u;
                    break;
                }
            }
            
            if (!$current_user || !password_verify($current_password, $current_user['password'])) {
                $error = "Le mot de passe actuel est incorrect";
            }
        }
    }
    
    if (empty($error)) {
        // Mettre à jour l'utilisateur
        $users = get_users();
        foreach ($users as &$u) {
            if ($u['email'] === $user['email']) {
                $u['full_name'] = $full_name;
                $u['phone'] = $phone;
                if (!empty($new_password)) {
                    $u['password'] = password_hash($new_password, PASSWORD_DEFAULT);
                }
                break;
            }
        }
        
        if (file_put_contents(USERS_FILE, json_encode($users, JSON_PRETTY_PRINT))) {
            // Mettre à jour la session
            $_SESSION['user']['full_name'] = $full_name;
            $_SESSION['user']['phone'] = $phone;
            $user = $_SESSION['user'];
            $success = "Profil mis à jour avec succès";
        } else {
            $error = "Erreur lors de la mise à jour du profil";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Profil - <?php echo SITE_NAME; ?></title>
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
            <div class="navbar-nav ms-auto">
                <a href="logout.php" class="btn btn-outline-light btn-sm">
                    <i class="fas fa-sign-out-alt me-1"></i>Déconnexion
                </a>
            </div>
        </div>
    </nav>

    <div class="container py-5 mt-5">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card shadow">
                    <div class="card-header bg-dark text-white">
                        <h4 class="mb-0"><i class="fas fa-user me-2"></i>Mon Profil</h4>
                    </div>
                    <div class="card-body p-4">
                        
                        <?php if ($success): ?>
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle me-2"></i><?php echo $success; ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($error): ?>
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle me-2"></i><?php echo $error; ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST">
                            <input type="hidden" name="action" value="update_profile">
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Nom complet</label>
                                    <input type="text" 
                                           class="form-control" 
                                           name="full_name" 
                                           value="<?php echo $user['full_name']; ?>" 
                                           required>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">Email</label>
                                    <input type="email" 
                                           class="form-control" 
                                           value="<?php echo $user['email']; ?>" 
                                           disabled
                                           style="background-color: #f8f9fa;">
                                    <small class="text-muted">L'email ne peut pas être modifié</small>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">Téléphone</label>
                                <input type="tel" 
                                       class="form-control" 
                                       name="phone" 
                                       value="<?php echo $user['phone']; ?>" 
                                       required
                                       pattern="[0-9]{10}">
                            </div>
                            
                            <hr class="my-4">
                            
                            <h5 class="mb-3">Changer le mot de passe</h5>
                            
                            <div class="mb-3">
                                <label class="form-label">Mot de passe actuel</label>
                                <input type="password" 
                                       class="form-control" 
                                       name="current_password" 
                                       placeholder="Laisser vide pour ne pas changer">
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label">Nouveau mot de passe</label>
                                <input type="password" 
                                       class="form-control" 
                                       name="new_password" 
                                       placeholder="Au moins 6 caractères"
                                       minlength="6">
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-danger btn-lg">
                                    <i class="fas fa-save me-2"></i>Mettre à jour le profil
                                </button>
                            </div>
                        </form>
                        
                        <div class="mt-4 pt-4 border-top">
                            <h6>Informations du compte</h6>
                            <div class="row">
                                <div class="col-6">
                                    <small class="text-muted">Membre depuis:</small><br>
                                    <strong><?php echo date('d/m/Y', strtotime($user['created_at'])); ?></strong>
                                </div>
                                <div class="col-6 text-end">
                                    <a href="logout.php" class="btn btn-outline-danger btn-sm">
                                        <i class="fas fa-sign-out-alt me-1"></i>Déconnexion
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="text-center mt-3">
                    <a href="index.php" class="text-decoration-none">
                        <i class="fas fa-arrow-left me-2"></i>Retour à l'accueil
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>