<?php
// product.php - Page détail d'un compte
require_once 'header.php';

$account_id = $_GET['id'] ?? 0;
$account = get_account_by_id($account_id);

if (!$account) {
    header('Location: products.php');
    exit;
}

// Gestion ajout au panier
if ($_POST['action'] ?? '' === 'add') {
    add_to_cart($account_id, $_POST['quantity'] ?? 1);
    header('Location: cart.php');
    exit;
}
?>

<div class="container py-5 mt-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php" class="text-decoration-none">Accueil</a></li>
            <li class="breadcrumb-item"><a href="products.php" class="text-decoration-none">Comptes</a></li>
            <li class="breadcrumb-item active"><?php echo $account['name']; ?></li>
        </ol>
    </nav>

    <div class="row">
        <!-- Image -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <img src="<?php echo $account['image']; ?>" class="card-img-top" alt="<?php echo $account['name']; ?>">
            </div>
        </div>

        <!-- Details -->
        <div class="col-md-6">
            <span class="badge bg-danger mb-2"><?php echo $account['category']; ?></span>
            <h1 class="fw-bold mb-3"><?php echo $account['name']; ?></h1>
            <p class="lead text-muted mb-4"><?php echo $account['description']; ?></p>

            <!-- Prix -->
            <div class="price-section mb-4">
                <h2 class="text-danger fw-bold"><?php echo format_price($account['price']); ?></h2>
                <small class="text-success">
                    <i class="fas fa-check-circle me-1"></i>En stock - Livraison instantanée
                </small>
            </div>

            <!-- Spécifications -->
            <div class="specs-card card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Spécifications</h5>
                    <div class="row">
                        <div class="col-6 mb-2">
                            <strong><i class="fas fa-clock text-danger me-2"></i>Durée:</strong>
                            <span class="ms-2"><?php echo $account['duration']; ?></span>
                        </div>
                        <div class="col-6 mb-2">
                            <strong><i class="fas fa-desktop text-danger me-2"></i>Écrans:</strong>
                            <span class="ms-2"><?php echo $account['screens']; ?></span>
                        </div>
                        <div class="col-6 mb-2">
                            <strong><i class="fas fa-hd text-danger me-2"></i>Qualité:</strong>
                            <span class="ms-2"><?php echo $account['quality']; ?></span>
                        </div>
                        <div class="col-6 mb-2">
                            <strong><i class="fas fa-shield-alt text-danger me-2"></i>Garantie:</strong>
                            <span class="ms-2">7 jours</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Description complète -->
            <div class="description-card card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Description</h5>
                    <p class="card-text"><?php echo $account['full_description']; ?></p>
                </div>
            </div>

            <!-- Ajout au panier -->
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="product.php?id=<?php echo $account_id; ?>">
                        <input type="hidden" name="action" value="add">
                        <div class="row align-items-center">
                            <div class="col-md-4 mb-3 mb-md-0">
                                <label class="form-label fw-bold">Quantité:</label>
                                <select class="form-select" name="quantity">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="col-md-8">
                                <button type="submit" class="btn btn-danger btn-lg w-100">
                                    <i class="fas fa-cart-plus me-2"></i>Ajouter au Panier - <?php echo format_price($account['price']); ?>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Features -->
            <div class="mt-4">
                <div class="row text-center">
                    <div class="col-4">
                        <div class="feature-item">
                            <i class="fas fa-bolt fa-2x text-danger mb-2"></i>
                            <p class="small mb-0">Livraison<br>Instantanée</p>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="feature-item">
                            <i class="fas fa-shield-alt fa-2x text-danger mb-2"></i>
                            <p class="small mb-0">Garantie<br>7 Jours</p>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="feature-item">
                            <i class="fas fa-headset fa-2x text-danger mb-2"></i>
                            <p class="small mb-0">Support<br>24h/24</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Produits similaires -->
    <?php
    $related_accounts = array_filter(get_accounts(), function($acc) use ($account) {
        return $acc['category'] === $account['category'] && $acc['id'] != $account['id'];
    });
    $related_accounts = array_slice($related_accounts, 0, 3);
    ?>

    <?php if (!empty($related_accounts)): ?>
    <section class="mt-5">
        <h3 class="fw-bold mb-4">Comptes Similaires</h3>
        <div class="row g-4">
            <?php foreach ($related_accounts as $related): ?>
            <div class="col-md-4">
                <div class="card product-card h-100">
                    <img src="<?php echo $related['image']; ?>" class="card-img-top product-img" alt="<?php echo $related['name']; ?>">
                    <div class="card-body d-flex flex-column">
                        <span class="badge bg-danger mb-2"><?php echo $related['category']; ?></span>
                        <h6 class="card-title"><?php echo $related['name']; ?></h6>
                        <p class="card-text text-muted small flex-grow-1"><?php echo $related['description']; ?></p>
                        <div class="mt-auto">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="h6 text-danger mb-0"><?php echo format_price($related['price']); ?></span>
                                <a href="product.php?id=<?php echo $related['id']; ?>" class="btn btn-outline-danger btn-sm">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
    <?php endif; ?>
</div>

<?php require_once 'footer.php'; ?>