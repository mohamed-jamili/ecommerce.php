<?php
// header.php - Header commun à toutes les pages
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?> - Votre boutique de comptes premium</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    
    <!-- Meta Tags SEO -->
    <meta name="description" content="Achetez des comptes premium Netflix, Spotify, Disney+, YouTube Premium et plus. Meilleurs prix, livraison instantanée.">
    <meta name="keywords" content="comptes premium, netflix, spotify, disney+, youtube premium, comptes partagés">
    <meta name="author" content="<?php echo SITE_NAME; ?>">
    
    <!-- Open Graph Tags -->
    <meta property="og:title" content="<?php echo SITE_NAME; ?> - Comptes Premium">
    <meta property="og:description" content="Comptes premium aux meilleurs prix">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo $_SERVER['REQUEST_URI'] ?? ''; ?>">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top shadow">
        <div class="container">
            <a class="navbar-brand fw-bold text-danger" href="index.php">
                <i class="fas fa-crown me-2"></i><?php echo SITE_NAME; ?>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>" href="index.php">
                            <i class="fas fa-home me-1"></i>Accueil
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'products.php' ? 'active' : ''; ?>" href="products.php">
                            <i class="fas fa-store me-1"></i>Comptes
                        </a>
                    </li>
                </ul>
                
                <div class="d-flex align-items-center">
                    <!-- Search Form -->
                    <form class="d-flex me-3" method="GET" action="products.php">
                        <div class="input-group">
                            <input type="text" class="form-control" name="search" placeholder="Rechercher..." 
                                   value="<?php echo $_GET['search'] ?? ''; ?>">
                            <button class="btn btn-outline-light" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                    
                    <!-- User Menu -->
                    <?php if (is_user_logged_in()): ?>
                        <div class="dropdown me-2">
                            <button class="btn btn-outline-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user me-1"></i><?php echo $_SESSION['user']['full_name']; ?>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user me-2"></i>Mon Profil</a></li>
                                <li><a class="dropdown-item" href="cart.php"><i class="fas fa-shopping-cart me-2"></i>Panier</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Déconnexion</a></li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <div class="me-2">
                            <a href="login.php" class="btn btn-outline-light me-1">
                                <i class="fas fa-sign-in-alt me-1"></i>Connexion
                            </a>
                            <a href="register.php" class="btn btn-danger">
                                <i class="fas fa-user-plus me-1"></i>Inscription
                            </a>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Cart -->
                    <a href="cart.php" class="btn btn-outline-light position-relative me-2">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            <?php echo get_cart_count(); ?>
                        </span>
                    </a>
                    
                    <!-- Admin Link (visible seulement si connecté) -->
                    <?php if (is_admin_logged_in()): ?>
                        <a href="orders.php" class="btn btn-outline-warning ms-2">
                            <i class="fas fa-cog"></i> Admin
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">