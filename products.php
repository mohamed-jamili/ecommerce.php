<?php
// products.php - Page liste des comptes
require_once 'header.php';

// Gestion des filtres
$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';
$sort = $_GET['sort'] ?? 'name';

// Filtrer et trier les comptes
$accounts = get_accounts();

// Filtre par recherche
if (!empty($search)) {
    $accounts = array_filter($accounts, function($account) use ($search) {
        return stripos($account['name'], $search) !== false || 
               stripos($account['description'], $search) !== false;
    });
}

// Filtre par catégorie
if (!empty($category)) {
    $accounts = array_filter($accounts, function($account) use ($category) {
        return $account['category'] === $category;
    });
}

// Tri
switch ($sort) {
    case 'price-asc':
        usort($accounts, function($a, $b) {
            return $a['price'] - $b['price'];
        });
        break;
    case 'price-desc':
        usort($accounts, function($a, $b) {
            return $b['price'] - $a['price'];
        });
        break;
    case 'name':
        usort($accounts, function($a, $b) {
            return strcmp($a['name'], $b['name']);
        });
        break;
}
?>

<div class="container py-5 mt-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-6">
            <h1 class="fw-bold">Nos Comptes Premium</h1>
            <p class="text-muted"><?php echo count($accounts); ?> compte(s) disponible(s)</p>
        </div>
        <div class="col-md-6">
            <div class="row g-2">
                <div class="col-md-6">
                    <select class="form-select" id="categoryFilter" onchange="filterByCategory()">
                        <option value="">Toutes catégories</option>
                        <option value="streaming" <?php echo $category === 'streaming' ? 'selected' : ''; ?>>Streaming</option>
                        <option value="music" <?php echo $category === 'music' ? 'selected' : ''; ?>>Musique</option>
                        <option value="gaming" <?php echo $category === 'gaming' ? 'selected' : ''; ?>>Gaming</option>
                        <option value="tv" <?php echo $category === 'tv' ? 'selected' : ''; ?>>TV</option>
                        <option value="video" <?php echo $category === 'video' ? 'selected' : ''; ?>>Vidéo</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <select class="form-select" id="sortFilter" onchange="sortProducts()">
                        <option value="name" <?php echo $sort === 'name' ? 'selected' : ''; ?>>Trier par</option>
                        <option value="price-asc" <?php echo $sort === 'price-asc' ? 'selected' : ''; ?>>Prix croissant</option>
                        <option value="price-desc" <?php echo $sort === 'price-desc' ? 'selected' : ''; ?>>Prix décroissant</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Products Grid -->
    <?php if (empty($accounts)): ?>
        <div class="text-center py-5">
            <i class="fas fa-search fa-3x text-muted mb-3"></i>
            <h4 class="text-muted">Aucun compte trouvé</h4>
            <p class="text-muted">Essayez de modifier vos critères de recherche</p>
            <a href="products.php" class="btn btn-danger">Voir tous les comptes</a>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($accounts as $account): ?>
            <div class="col-md-6 col-lg-4">
                <div class="card product-card h-100 animate__animated animate__fadeIn">
                    <img src="<?php echo $account['image']; ?>" class="card-img-top product-img" alt="<?php echo $account['name']; ?>">
                    <div class="card-body d-flex flex-column">
                        <span class="badge bg-danger mb-2"><?php echo $account['category']; ?></span>
                        <h5 class="card-title"><?php echo $account['name']; ?></h5>
                        <p class="card-text text-muted flex-grow-1"><?php echo $account['description']; ?></p>
                        
                        <div class="specs mb-3">
                            <div class="d-flex justify-content-between text-sm text-muted">
                                <span><i class="fas fa-clock me-1"></i><?php echo $account['duration']; ?></span>
                                <span><i class="fas fa-desktop me-1"></i><?php echo $account['screens']; ?></span>
                                <span><i class="fas fa-hd me-1"></i><?php echo $account['quality']; ?></span>
                            </div>
                        </div>
                        
                        <div class="mt-auto">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="h4 text-danger mb-0"><?php echo format_price($account['price']); ?></span>
                                <small class="text-success">
                                    <i class="fas fa-check-circle me-1"></i>En stock
                                </small>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="product.php?id=<?php echo $account['id']; ?>" class="btn btn-outline-dark flex-fill">
                                    <i class="fas fa-eye me-1"></i>Détails
                                </a>
                                <form method="POST" action="cart.php" class="flex-fill">
                                    <input type="hidden" name="action" value="add">
                                    <input type="hidden" name="account_id" value="<?php echo $account['id']; ?>">
                                    <button type="submit" class="btn btn-danger w-100">
                                        <i class="fas fa-cart-plus me-1"></i>Ajouter
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script>
function filterByCategory() {
    const category = document.getElementById('categoryFilter').value;
    const url = new URL(window.location.href);
    
    if (category) {
        url.searchParams.set('category', category);
    } else {
        url.searchParams.delete('category');
    }
    
    window.location.href = url.toString();
}

function sortProducts() {
    const sort = document.getElementById('sortFilter').value;
    const url = new URL(window.location.href);
    
    if (sort && sort !== 'name') {
        url.searchParams.set('sort', sort);
    } else {
        url.searchParams.delete('sort');
    }
    
    window.location.href = url.toString();
}
</script>

<?php require_once 'footer.php'; ?>