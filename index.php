<?php
// index.php - Page d'accueil
require_once 'header.php';
?>

<!-- Hero Section -->
<section class="hero-section bg-dark text-white py-5">
    <div class="container py-5">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-4 animate__animated animate__fadeInUp">
                    Comptes Premium <span class="text-danger">Pas Chers</span>
                </h1>
                <p class="lead mb-4 animate__animated animate__fadeInUp animate__delay-1s">
                    Netflix, Spotify, Disney+, YouTube Premium et plus encore. 
                    <strong>Livraison instantanée</strong>, qualité garantie, support 24h/24.
                </p>
                <div class="animate__animated animate__fadeInUp animate__delay-2s">
                    <a href="products.php" class="btn btn-danger btn-lg px-4 me-3">
                        <i class="fas fa-shopping-bag me-2"></i>Acheter Maintenant
                    </a>
                    <a href="#features" class="btn btn-outline-light btn-lg px-4">
                        <i class="fas fa-info-circle me-2"></i>En savoir plus
                    </a>
                </div>
            </div>
            <div class="col-lg-6 text-center">
                <img src="https://images.unsplash.com/photo-1574375927938-d5a98e8ffe85?w=600&h=400&fit=crop" 
                     alt="Comptes Premium" class="img-fluid rounded shadow animate__animated animate__zoomIn">
            </div>
        </div>
    </div>
</section>

<!-- Featured Products -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Comptes <span class="text-danger">Populaires</span></h2>
            <p class="text-muted">Les comptes les plus demandés par nos clients</p>
        </div>
        
        <div class="row g-4">
            <?php
            $featured_accounts = array_slice(get_accounts(), 0, 4);
            foreach ($featured_accounts as $account):
            ?>
            <div class="col-md-6 col-lg-3">
                <div class="card product-card h-100 animate__animated animate__fadeInUp">
                    <img src="<?php echo $account['image']; ?>" class="card-img-top product-img" alt="<?php echo $account['name']; ?>">
                    <div class="card-body d-flex flex-column">
                        <span class="badge bg-danger mb-2"><?php echo $account['category']; ?></span>
                        <h5 class="card-title"><?php echo $account['name']; ?></h5>
                        <p class="card-text text-muted flex-grow-1"><?php echo $account['description']; ?></p>
                        <div class="mt-auto">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="h5 text-danger mb-0"><?php echo format_price($account['price']); ?></span>
                                <small class="text-muted"><?php echo $account['duration']; ?></small>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="product.php?id=<?php echo $account['id']; ?>" class="btn btn-outline-dark flex-fill">
                                    <i class="fas fa-eye me-1"></i>Voir
                                </a>
                                <form method="POST" action="cart.php" class="flex-fill">
                                    <input type="hidden" name="action" value="add">
                                    <input type="hidden" name="account_id" value="<?php echo $account['id']; ?>">
                                    <button type="submit" class="btn btn-danger w-100">
                                        <i class="fas fa-cart-plus me-1"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center mt-4">
            <a href="products.php" class="btn btn-outline-dark btn-lg">
                Voir tous les comptes <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>
    </div>
</section>

<!-- Features Section -->
<section id="features" class="bg-light py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Pourquoi Nous <span class="text-danger">Choisir</span> ?</h2>
        </div>
        
        <div class="row g-4 text-center">
            <div class="col-md-4">
                <div class="feature-card p-4 h-100">
                    <i class="fas fa-bolt fa-3x text-danger mb-3"></i>
                    <h4>Livraison Instantanée</h4>
                    <p class="text-muted">Réception immédiate des identifiants après paiement</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card p-4 h-100">
                    <i class="fas fa-shield-alt fa-3x text-danger mb-3"></i>
                    <h4>Garantie 7 Jours</h4>
                    <p class="text-muted">Remboursement si le compte ne fonctionne pas</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-card p-4 h-100">
                    <i class="fas fa-headset fa-3x text-danger mb-3"></i>
                    <h4>Support 24h/24</h4>
                    <p class="text-muted">Équipe disponible pour vous aider à tout moment</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Avis <span class="text-danger">Clients</span></h2>
        </div>
        
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card testimonial-card h-100">
                    <div class="card-body text-center">
                        <div class="stars mb-3">
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                        </div>
                        <p class="card-text">"Compte Netflix reçu instantanément, fonctionne parfaitement. Je recommande !"</p>
                        <h6 class="card-title text-danger">- Mohamed</h6>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card testimonial-card h-100">
                    <div class="card-body text-center">
                        <div class="stars mb-3">
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                        </div>
                        <p class="card-text">"Spotify Premium à un prix imbattable. Service client réactif."</p>
                        <h6 class="card-title text-danger">- Fatima</h6>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card testimonial-card h-100">
                    <div class="card-body text-center">
                        <div class="stars mb-3">
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                        </div>
                        <p class="card-text">"Disney+ pour 6 mois, excellent rapport qualité-prix. Merci !"</p>
                        <h6 class="card-title text-danger">- Karim</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'footer.php'; ?>