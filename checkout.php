<?php
// checkout.php - Page validation commande avec MySQL
require_once 'config.php';
require_user_login(); // Protection de la page

// Redirection si panier vide
if (empty($_SESSION['cart'])) {
    header('Location: cart.php');
    exit;
}

// Traitement du formulaire
$errors = [];
$success = false;
$order_id = '';
$whatsapp_url = '';

if ($_POST['action'] ?? '' === 'checkout') {
    // Validation des données
    $full_name = sanitize_input($_POST['full_name'] ?? '');
    $phone = sanitize_input($_POST['phone'] ?? '');
    $city = sanitize_input($_POST['city'] ?? '');
    $address = sanitize_input($_POST['address'] ?? '');
    $notes = sanitize_input($_POST['notes'] ?? '');
    
    // Validation
    if (empty($full_name)) {
        $errors[] = "Le nom complet est obligatoire";
    }
    
    if (empty($phone) || !validate_phone($phone)) {
        $errors[] = "Le numéro de téléphone doit contenir 10 chiffres";
    }
    
    if (empty($city)) {
        $errors[] = "La ville est obligatoire";
    }
    
    if (empty($address)) {
        $errors[] = "L'adresse est obligatoire";
    }
    
    // Si pas d'erreurs, traitement de la commande
    if (empty($errors)) {
        $order_id = generate_order_id();
        $total = get_cart_total();
        
        // Préparer les données de la commande
        $order_data = [
            'order_id' => $order_id,
            'user_id' => $_SESSION['user']['id'],
            'full_name' => $full_name,
            'email' => $_SESSION['user']['email'],
            'phone' => $phone,
            'city' => $city,
            'address' => $address,
            'notes' => $notes,
            'items' => $_SESSION['cart'],
            'total' => $total
        ];
        
        // Sauvegarder la commande dans MySQL
        if (save_order($order_data)) {
            // Préparer le message WhatsApp
            $whatsapp_message = format_whatsapp_message($order_data);
            $encoded_message = urlencode($whatsapp_message);
            $whatsapp_url = "https://wa.me/" . WHATSAPP_NUMBER . "?text=" . $encoded_message;
            
            // Vider le panier
            $_SESSION['cart'] = [];
            
            $success = true;
        } else {
            $errors[] = "Erreur lors de l'enregistrement de la commande";
        }
    }
}

function format_whatsapp_message($order_data) {
    $message = "🛒 NOUVELLE COMMANDE - " . SITE_NAME . "\n\n";
    $message .= "📋 Numéro de commande: " . $order_data['order_id'] . "\n";
    $message .= "👤 Client: " . $order_data['full_name'] . "\n";
    $message .= "📞 Téléphone: " . $order_data['phone'] . "\n";
    $message .= "📧 Email: " . $order_data['email'] . "\n";
    $message .= "🏙️ Ville: " . $order_data['city'] . "\n";
    $message .= "📍 Adresse: " . $order_data['address'] . "\n\n";
    
    $message .= "📦 ARTICLES COMMANDÉS:\n";
    foreach ($order_data['items'] as $item) {
        $message .= "• " . $item['name'] . " x" . $item['quantity'] . " = " . format_price($item['price'] * $item['quantity']) . "\n";
    }
    
    $message .= "\n💰 TOTAL: " . format_price($order_data['total']) . "\n";
    
    if (!empty($order_data['notes'])) {
        $message .= "\n📝 NOTES: " . $order_data['notes'] . "\n";
    }
    
    $message .= "\n⏰ Date: " . date('d/m/Y H:i') . "\n";
    $message .= "\n✅ Merci de confirmer cette commande rapidement!";
    
    return $message;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finaliser la Commande - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php require_once 'header.php'; ?>

    <div class="container py-5 mt-4">
        <h1 class="fw-bold mb-4">Finaliser la Commande</h1>
        
        <?php if ($success): ?>
            <!-- Succès commande -->
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card border-success">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-check-circle fa-4x text-success mb-4"></i>
                            <h2 class="text-success mb-3">Commande Confirmée !</h2>
                            <p class="lead mb-4">Votre commande <strong><?php echo $order_id; ?></strong> a été enregistrée avec succès.</p>
                            
                            <div class="alert alert-info mb-4">
                                <h5 class="alert-heading">Prochaines étapes:</h5>
                                <p class="mb-2">1. Cliquez sur le bouton WhatsApp ci-dessous</p>
                                <p class="mb-2">2. Envoyez le message pré-rempli à notre service client</p>
                                <p class="mb-0">3. Vous recevrez vos comptes dans les plus brefs délais</p>
                            </div>
                            
                            <div class="d-grid gap-3">
                                <a href="<?php echo $whatsapp_url; ?>" 
                                   target="_blank" 
                                   class="btn btn-success btn-lg">
                                    <i class="fab fa-whatsapp me-2"></i>Envoyer sur WhatsApp
                                </a>
                                <a href="products.php" class="btn btn-outline-dark">
                                    <i class="fas fa-store me-2"></i>Retour à la boutique
                                </a>
                            </div>

                            <div class="mt-4 pt-4 border-top">
                                <h6>Détails de votre commande</h6>
                                <div class="row text-start">
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Numéro de commande:</strong> <?php echo $order_id; ?></p>
                                        <p class="mb-1"><strong>Client:</strong> <?php echo $_SESSION['user']['full_name']; ?></p>
                                        <p class="mb-1"><strong>Email:</strong> <?php echo $_SESSION['user']['email']; ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Date:</strong> <?php echo date('d/m/Y à H:i'); ?></p>
                                        <p class="mb-1"><strong>Total:</strong> <?php echo format_price(get_cart_total()); ?></p>
                                        <p class="mb-0"><strong>Statut:</strong> <span class="badge bg-warning">En attente</span></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <!-- Formulaire commande -->
            <div class="row">
                <!-- Formulaire -->
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header bg-dark text-white">
                            <h5 class="mb-0"><i class="fas fa-truck me-2"></i>Informations de livraison</h5>
                        </div>
                        <div class="card-body">
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
                            
                            <form method="POST" class="needs-validation" novalidate>
                                <input type="hidden" name="action" value="checkout">
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Nom complet *</label>
                                        <input type="text" 
                                               class="form-control" 
                                               name="full_name" 
                                               value="<?php echo $_POST['full_name'] ?? $_SESSION['user']['full_name']; ?>" 
                                               required
                                               placeholder="Votre nom complet">
                                        <div class="invalid-feedback">
                                            Veuillez entrer votre nom complet.
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Téléphone *</label>
                                        <input type="tel" 
                                               class="form-control" 
                                               name="phone" 
                                               value="<?php echo $_POST['phone'] ?? $_SESSION['user']['phone']; ?>" 
                                               pattern="[0-9]{10}"
                                               placeholder="0612345678"
                                               required>
                                        <small class="text-muted">10 chiffres sans espaces</small>
                                        <div class="invalid-feedback">
                                            Veuillez entrer un numéro de téléphone valide.
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Ville *</label>
                                        <input type="text" 
                                               class="form-control" 
                                               name="city" 
                                               value="<?php echo $_POST['city'] ?? ''; ?>" 
                                               required
                                               placeholder="Ex: Casablanca">
                                        <div class="invalid-feedback">
                                            Veuillez entrer votre ville.
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">Adresse complète *</label>
                                        <input type="text" 
                                               class="form-control" 
                                               name="address" 
                                               value="<?php echo $_POST['address'] ?? ''; ?>" 
                                               required
                                               placeholder="Votre adresse complète">
                                        <div class="invalid-feedback">
                                            Veuillez entrer votre adresse.
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <label class="form-label fw-bold">Notes (optionnel)</label>
                                    <textarea class="form-control" 
                                              name="notes" 
                                              rows="3"
                                              placeholder="Informations supplémentaires, instructions de livraison..."><?php echo $_POST['notes'] ?? ''; ?></textarea>
                                </div>
                                
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-danger btn-lg py-3">
                                        <i class="fas fa-check-circle me-2"></i>Confirmer la commande
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Informations de sécurité -->
                    <div class="card mt-4 border-warning">
                        <div class="card-body">
                            <h6 class="card-title text-warning">
                                <i class="fas fa-shield-alt me-2"></i>Paiement Sécurisé
                            </h6>
                            <p class="card-text small mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                Aucun paiement en ligne requis
                            </p>
                            <p class="card-text small mb-2">
                                <i class="fas fa-check text-success me-2"></i>
                                Vous payez après réception des comptes
                            </p>
                            <p class="card-text small mb-0">
                                <i class="fas fa-check text-success me-2"></i>
                                Transaction 100% sécurisée via WhatsApp
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Récapitulatif -->
                <div class="col-lg-4">
                    <div class="card sticky-top" style="top: 100px;">
                        <div class="card-header bg-light">
                            <h5 class="mb-0"><i class="fas fa-receipt me-2"></i>Votre commande</h5>
                        </div>
                        <div class="card-body">
                            <!-- Articles -->
                            <h6 class="fw-bold mb-3">Articles commandés</h6>
                            <?php foreach ($_SESSION['cart'] as $item): ?>
                            <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                                <div class="d-flex align-items-center">
                                    <img src="<?php echo $item['image']; ?>" 
                                         alt="<?php echo $item['name']; ?>" 
                                         class="rounded me-3"
                                         style="width: 50px; height: 50px; object-fit: cover;">
                                    <div>
                                        <h6 class="mb-0 small"><?php echo $item['name']; ?></h6>
                                        <small class="text-muted">x<?php echo $item['quantity']; ?></small>
                                    </div>
                                </div>
                                <span class="fw-bold text-danger"><?php echo format_price($item['price'] * $item['quantity']); ?></span>
                            </div>
                            <?php endforeach; ?>
                            
                            <hr>
                            
                            <!-- Calcul des frais -->
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Sous-total:</span>
                                    <span class="fw-bold"><?php echo format_price(get_cart_total()); ?></span>
                                </div>
                                
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Livraison:</span>
                                    <span class="text-success fw-bold">GRATUITE</span>
                                </div>
                                
                                <div class="d-flex justify-content-between mb-3">
                                    <span>Garantie:</span>
                                    <span class="text-success fw-bold">INCLUSE</span>
                                </div>
                                
                                <hr>
                                
                                <!-- Total -->
                                <div class="d-flex justify-content-between mb-4">
                                    <strong class="h5">Total:</strong>
                                    <strong class="text-danger h4"><?php echo format_price(get_cart_total()); ?></strong>
                                </div>
                            </div>
                            
                            <!-- Garanties -->
                            <div class="alert alert-success">
                                <h6 class="alert-heading mb-2">✅ Inclus avec votre commande:</h6>
                                <ul class="mb-0 small">
                                    <li>Livraison instantanée par email</li>
                                    <li>Garantie 7 jours satisfait ou remboursé</li>
                                    <li>Support client 24h/24</li>
                                    <li>Instructions d'utilisation détaillées</li>
                                </ul>
                            </div>

                            <!-- Processus de livraison -->
                            <div class="mt-3">
                                <h6 class="fw-bold mb-2">📦 Processus de livraison</h6>
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-credit-card text-success me-2"></i>
                                    <small>1. Confirmation de commande</small>
                                </div>
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-whatsapp text-success me-2"></i>
                                    <small>2. Contact WhatsApp</small>
                                </div>
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-envelope text-success me-2"></i>
                                    <small>3. Réception des comptes par email</small>
                                </div>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    <small>4. Activation et utilisation</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Assistance -->
                    <div class="card mt-3 border-info">
                        <div class="card-body text-center">
                            <h6 class="card-title text-info">
                                <i class="fas fa-headset me-2"></i>Besoin d'aide ?
                            </h6>
                            <p class="card-text small mb-3">
                                Notre équipe est disponible pour vous accompagner
                            </p>
                            <a href="https://wa.me/<?php echo WHATSAPP_NUMBER; ?>" 
                               target="_blank" 
                               class="btn btn-outline-info btn-sm w-100">
                                <i class="fab fa-whatsapp me-2"></i>Contactez-nous
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Validation Bootstrap
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

        // Animation du bouton de confirmation
        document.addEventListener('DOMContentLoaded', function() {
            const confirmBtn = document.querySelector('button[type="submit"]');
            if (confirmBtn) {
                confirmBtn.addEventListener('click', function() {
                    if (this.form.checkValidity()) {
                        this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Traitement...';
                        this.disabled = true;
                    }
                });
            }
        });

        // Auto-format du téléphone
        const phoneInput = document.querySelector('input[name="phone"]');
        if (phoneInput) {
            phoneInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length > 10) {
                    value = value.substring(0, 10);
                }
                e.target.value = value;
            });
        }
    </script>
</body>
</html>