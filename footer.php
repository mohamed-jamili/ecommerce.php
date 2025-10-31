<?php
// footer.php - Footer commun
?>
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-white py-5 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <h5 class="text-danger fw-bold">
                        <i class="fas fa-crown me-2"></i><?php echo SITE_NAME; ?>
                    </h5>
                    <p class="text-light">Votre boutique de confiance pour les comptes premium. Qualité garantie, livraison instantanée.</p>
                    <div class="social-links">
                        <a href="#" class="text-light me-3"><i class="fab fa-facebook fa-lg"></i></a>
                        <a href="#" class="text-light me-3"><i class="fab fa-instagram fa-lg"></i></a>
                        <a href="#" class="text-light me-3"><i class="fab fa-whatsapp fa-lg"></i></a>
                    </div>
                </div>
                
                <div class="col-lg-2 mb-4">
                    <h6>Navigation</h6>
                    <ul class="list-unstyled">
                        <li><a href="index.php" class="text-light text-decoration-none">Accueil</a></li>
                        <li><a href="products.php" class="text-light text-decoration-none">Comptes</a></li>
                        <li><a href="cart.php" class="text-light text-decoration-none">Panier</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-3 mb-4">
                    <h6>Catégories</h6>
                    <ul class="list-unstyled">
                        <li><a href="products.php?category=streaming" class="text-light text-decoration-none">Streaming</a></li>
                        <li><a href="products.php?category=music" class="text-light text-decoration-none">Musique</a></li>
                        <li><a href="products.php?category=gaming" class="text-light text-decoration-none">Gaming</a></li>
                        <li><a href="products.php?category=tv" class="text-light text-decoration-none">TV</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-3 mb-4">
                    <h6>Contact</h6>
                    <ul class="list-unstyled">
                        <li class="text-light">
                            <i class="fas fa-phone me-2"></i>+212 7 33 59 71 91
                        </li>
                        <li class="text-light">
                            <i class="fas fa-envelope me-2"></i>support@compuestore.com
                        </li>
                        <li class="text-light">
                            <i class="fas fa-clock me-2"></i>24h/24 - 7j/7
                        </li>
                    </ul>
                </div>
            </div>
            
            <hr class="bg-light">
            
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-0">&copy; 2024 <?php echo SITE_NAME; ?>. Tous droits réservés.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="#" class="text-light text-decoration-none me-3">Mentions légales</a>
                    <a href="#" class="text-light text-decoration-none">Politique de confidentialité</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/script.js"></script>
</body>
</html>