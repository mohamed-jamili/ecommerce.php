<?php
// cart.php - Page panier
require_once 'config.php';
require_user_login(); // Protection de la page - seulement pour utilisateurs connectés

// Gestion des actions panier
if ($_POST['action'] ?? '') {
    $account_id = $_POST['account_id'] ?? 0;
    
    switch ($_POST['action']) {
        case 'add':
            $quantity = $_POST['quantity'] ?? 1;
            add_to_cart($account_id, $quantity);
            break;
            
        case 'update':
            $quantity = $_POST['quantity'] ?? 1;
            update_cart_quantity($account_id, $quantity);
            break;
            
        case 'remove':
            remove_from_cart($account_id);
            break;
            
        case 'clear':
            $_SESSION['cart'] = [];
            break;
    }
    
    header('Location: cart.php');
    exit;
}
?>

<div class="container py-5 mt-4">
    <h1 class="fw-bold mb-4">Votre Panier</h1>
    
    <?php if (empty($_SESSION['cart'])): ?>
        <!-- Panier vide -->
        <div class="text-center py-5">
            <i class="fas fa-shopping-cart fa-4x text-muted mb-4"></i>
            <h3 class="text-muted mb-3">Votre panier est vide</h3>
            <p class="text-muted mb-4">Découvrez nos comptes premium et ajoutez-les à votre panier</p>
            <a href="products.php" class="btn btn-danger btn-lg">
                <i class="fas fa-store me-2"></i>Voir les comptes
            </a>
        </div>
    <?php else: ?>
        <div class="row">
            <!-- Liste des articles -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header bg-light">
                        <div class="row fw-bold">
                            <div class="col-5">Produit</div>
                            <div class="col-2 text-center">Prix</div>
                            <div class="col-3 text-center">Quantité</div>
                            <div class="col-2 text-center">Total</div>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php foreach ($_SESSION['cart'] as $item): ?>
                        <div class="row align-items-center py-3 border-bottom">
                            <!-- Image et nom -->
                            <div class="col-5">
                                <div class="d-flex align-items-center">
                                    <img src="<?php echo $item['image']; ?>" 
                                         alt="<?php echo $item['name']; ?>" 
                                         class="cart-item-img me-3">
                                    <div>
                                        <h6 class="mb-1"><?php echo $item['name']; ?></h6>
                                        <small class="text-muted">Livraison instantanée</small>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Prix unitaire -->
                            <div class="col-2 text-center">
                                <span class="fw-bold"><?php echo format_price($item['price']); ?></span>
                            </div>
                            
                            <!-- Quantité -->
                            <div class="col-3">
                                <div class="d-flex align-items-center justify-content-center">
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="action" value="update">
                                        <input type="hidden" name="account_id" value="<?php echo $item['id']; ?>">
                                        <input type="hidden" name="quantity" value="<?php echo $item['quantity'] - 1; ?>">
                                        <button type="submit" class="btn btn-outline-secondary btn-sm" 
                                                <?php echo $item['quantity'] <= 1 ? 'disabled' : ''; ?>>
                                            <i class="fas fa-minus"></i>
                                        </button>
                                    </form>
                                    
                                    <span class="mx-3 fw-bold"><?php echo $item['quantity']; ?></span>
                                    
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="action" value="update">
                                        <input type="hidden" name="account_id" value="<?php echo $item['id']; ?>">
                                        <input type="hidden" name="quantity" value="<?php echo $item['quantity'] + 1; ?>">
                                        <button type="submit" class="btn btn-outline-secondary btn-sm">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            
                            <!-- Total -->
                            <div class="col-2 text-center">
                                <span class="fw-bold text-danger"><?php echo format_price($item['price'] * $item['quantity']); ?></span>
                            </div>
                            
                            <!-- Supprimer -->
                            <div class="col-12 col-md-12 mt-2 text-end">
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="action" value="remove">
                                    <input type="hidden" name="account_id" value="<?php echo $item['id']; ?>">
                                    <button type="submit" class="btn btn-outline-danger btn-sm">
                                        <i class="fas fa-trash me-1"></i>Supprimer
                                    </button>
                                </form>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        
                        <!-- Vider panier -->
                        <div class="text-end mt-3">
                            <form method="POST">
                                <input type="hidden" name="action" value="clear">
                                <button type="submit" class="btn btn-outline-dark">
                                    <i class="fas fa-broom me-1"></i>Vider le panier
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Résumé commande -->
            <div class="col-lg-4">
                <div class="card sticky-top" style="top: 100px;">
                    <div class="card-header bg-dark text-white">
                        <h5 class="mb-0">Résumé de la commande</h5>
                    </div>
                    <div class="card-body">
                        <!-- Sous-total -->
                        <div class="d-flex justify-content-between mb-2">
                            <span>Sous-total:</span>
                            <span class="fw-bold"><?php echo format_price(get_cart_total()); ?></span>
                        </div>
                        
                        <!-- Livraison -->
                        <div class="d-flex justify-content-between mb-2">
                            <span>Livraison:</span>
                            <span class="text-success fw-bold">GRATUITE</span>
                        </div>
                        
                        <!-- Garantie -->
                        <div class="d-flex justify-content-between mb-3">
                            <span>Garantie:</span>
                            <span class="text-success fw-bold">INCLUSE</span>
                        </div>
                        
                        <hr>
                        
                        <!-- Total -->
                        <div class="d-flex justify-content-between mb-4">
                            <strong>Total:</strong>
                            <strong class="text-danger h5"><?php echo format_price(get_cart_total()); ?></strong>
                        </div>
                        
                        <!-- Boutons action -->
                        <div class="d-grid gap-2">
                            <a href="checkout.php" class="btn btn-danger btn-lg">
                                <i class="fas fa-credit-card me-2"></i>Passer la commande
                            </a>
                            <a href="products.php" class="btn btn-outline-dark">
                                <i class="fas fa-arrow-left me-2"></i>Continuer mes achats
                            </a>
                        </div>
                        
                        <!-- Garanties -->
                        <div class="mt-4">
                            <div class="d-flex align-items-center text-success mb-2">
                                <i class="fas fa-check-circle me-2"></i>
                                <small>Livraison instantanée</small>
                            </div>
                            <div class="d-flex align-items-center text-success mb-2">
                                <i class="fas fa-check-circle me-2"></i>
                                <small>Garantie 7 jours</small>
                            </div>
                            <div class="d-flex align-items-center text-success">
                                <i class="fas fa-check-circle me-2"></i>
                                <small>Support 24h/24</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'footer.php'; ?>