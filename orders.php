<?php
// orders.php - Page gestion commandes admin
require_once 'config.php';
require_admin_login(); // Protection de la page - seulement pour admin

// Gestion actions
if ($_POST['action'] ?? '') {
    $order_id = $_POST['order_id'] ?? '';
    
    switch ($_POST['action']) {
        case 'update_status':
            $status = $_POST['status'] ?? '';
            update_order_status($order_id, $status);
            break;
            
        case 'delete':
            delete_order($order_id);
            break;
    }
    
    header('Location: orders.php');
    exit;
}

// Fonctions gestion commandes
function update_order_status($order_id, $status) {
    $orders = get_orders();
    foreach ($orders as &$order) {
        if ($order['order_id'] === $order_id) {
            $order['status'] = $status;
            $order['updated_at'] = date('Y-m-d H:i:s');
            break;
        }
    }
    file_put_contents(ORDERS_FILE, json_encode($orders, JSON_PRETTY_PRINT));
}

function delete_order($order_id) {
    $orders = get_orders();
    $orders = array_filter($orders, function($order) use ($order_id) {
        return $order['order_id'] !== $order_id;
    });
    file_put_contents(ORDERS_FILE, json_encode(array_values($orders), JSON_PRETTY_PRINT));
}

function get_status_badge($status) {
    $badges = [
        'pending' => 'bg-warning',
        'confirmed' => 'bg-info',
        'delivered' => 'bg-success',
        'cancelled' => 'bg-danger'
    ];
    return $badges[$status] ?? 'bg-secondary';
}

function get_status_text($status) {
    $texts = [
        'pending' => 'En attente',
        'confirmed' => 'Confirmée',
        'delivered' => 'Livrée',
        'cancelled' => 'Annulée'
    ];
    return $texts[$status] ?? $status;
}

// Récupérer commandes
$orders = get_orders();
$total_orders = count($orders);
$pending_orders = count(array_filter($orders, function($order) {
    return $order['status'] === 'pending';
}));
$total_revenue = array_sum(array_column($orders, 'total'));
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Commandes - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .stats-card {
            border-left: 4px solid #0d6efd;
        }
        .order-card {
            transition: transform 0.2s;
        }
        .order-card:hover {
            transform: translateY(-2px);
        }
    </style>
</head>
<body class="bg-light">
    <!-- Navigation Admin -->
    <nav class="navbar navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand fw-bold" href="orders.php">
                <i class="fas fa-crown me-2"></i><?php echo SITE_NAME; ?> - Admin
            </a>
            <div class="d-flex">
                <a href="index.php" class="btn btn-outline-light me-2">
                    <i class="fas fa-store me-1"></i>Site
                </a>
                <a href="logout.php" class="btn btn-outline-warning">
                    <i class="fas fa-sign-out-alt me-1"></i>Déconnexion
                </a>
            </div>
        </div>
    </nav>

    <div class="container py-4">
        <!-- Statistiques -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card stats-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title text-muted">Total Commandes</h6>
                                <h3 class="text-primary"><?php echo $total_orders; ?></h3>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-shopping-cart fa-2x text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card stats-card" style="border-left-color: #fd7e14;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title text-muted">En Attente</h6>
                                <h3 class="text-warning"><?php echo $pending_orders; ?></h3>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-clock fa-2x text-warning"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card stats-card" style="border-left-color: #198754;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title text-muted">Chiffre d'Affaires</h6>
                                <h3 class="text-success"><?php echo format_price($total_revenue); ?></h3>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-chart-line fa-2x text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card stats-card" style="border-left-color: #6f42c1;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="card-title text-muted">Taux Livraison</h6>
                                <h3 class="text-info">
                                    <?php 
                                    $delivered = count(array_filter($orders, function($order) {
                                        return $order['status'] === 'delivered';
                                    }));
                                    echo $total_orders > 0 ? round(($delivered / $total_orders) * 100) : 0;
                                    ?>%
                                </h3>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-truck fa-2x text-info"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Liste des commandes -->
        <div class="card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Gestion des Commandes</h4>
                <span class="badge bg-primary"><?php echo $total_orders; ?> commande(s)</span>
            </div>
            <div class="card-body">
                <?php if (empty($orders)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Aucune commande pour le moment</h5>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID Commande</th>
                                    <th>Client</th>
                                    <th>Téléphone</th>
                                    <th>Email</th>
                                    <th>Articles</th>
                                    <th>Total</th>
                                    <th>Date</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (array_reverse($orders) as $order): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo $order['order_id']; ?></strong>
                                    </td>
                                    <td>
                                        <div>
                                            <strong><?php echo $order['full_name']; ?></strong>
                                            <br>
                                            <small class="text-muted"><?php echo $order['city']; ?></small>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="tel:<?php echo $order['phone']; ?>" class="text-decoration-none">
                                            <?php echo $order['phone']; ?>
                                        </a>
                                    </td>
                                    <td>
                                        <small><?php echo $order['user_email'] ?? 'N/A'; ?></small>
                                    </td>
                                    <td>
                                        <small>
                                            <?php 
                                            $items_count = count($order['items']);
                                            $first_item = $order['items'][0]['name'] ?? '';
                                            echo $first_item;
                                            if ($items_count > 1) {
                                                echo " + " . ($items_count - 1) . " autre(s)";
                                            }
                                            ?>
                                        </small>
                                    </td>
                                    <td>
                                        <strong class="text-success"><?php echo format_price($order['total']); ?></strong>
                                    </td>
                                    <td>
                                        <small><?php echo date('d/m/Y H:i', strtotime($order['date'])); ?></small>
                                    </td>
                                    <td>
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="action" value="update_status">
                                            <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                                <option value="pending" <?php echo $order['status'] === 'pending' ? 'selected' : ''; ?>>En attente</option>
                                                <option value="confirmed" <?php echo $order['status'] === 'confirmed' ? 'selected' : ''; ?>>Confirmée</option>
                                                <option value="delivered" <?php echo $order['status'] === 'delivered' ? 'selected' : ''; ?>>Livrée</option>
                                                <option value="cancelled" <?php echo $order['status'] === 'cancelled' ? 'selected' : ''; ?>>Annulée</option>
                                            </select>
                                        </form>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <!-- Voir détails -->
                                            <button type="button" class="btn btn-outline-primary" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#orderModal<?php echo $order['order_id']; ?>">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            
                                            <!-- WhatsApp -->
                                            <a href="https://wa.me/<?php echo $order['phone']; ?>" 
                                               target="_blank" 
                                               class="btn btn-outline-success">
                                                <i class="fab fa-whatsapp"></i>
                                            </a>
                                            
                                            <!-- Supprimer -->
                                            <form method="POST" class="d-inline">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                                                <button type="submit" class="btn btn-outline-danger" 
                                                        onclick="return confirm('Supprimer cette commande ?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Modal Détails Commande -->
                                <div class="modal fade" id="orderModal<?php echo $order['order_id']; ?>" tabindex="-1">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Détails Commande <?php echo $order['order_id']; ?></h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <!-- Info client -->
                                                <div class="row mb-4">
                                                    <div class="col-md-6">
                                                        <h6>Informations Client</h6>
                                                        <p class="mb-1"><strong>Nom:</strong> <?php echo $order['full_name']; ?></p>
                                                        <p class="mb-1"><strong>Email:</strong> <?php echo $order['user_email'] ?? 'N/A'; ?></p>
                                                        <p class="mb-1"><strong>Téléphone:</strong> <?php echo $order['phone']; ?></p>
                                                        <p class="mb-1"><strong>Ville:</strong> <?php echo $order['city']; ?></p>
                                                        <p class="mb-0"><strong>Adresse:</strong> <?php echo $order['address']; ?></p>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <h6>Détails Commande</h6>
                                                        <p class="mb-1"><strong>Date:</strong> <?php echo date('d/m/Y H:i', strtotime($order['date'])); ?></p>
                                                        <p class="mb-1"><strong>Statut:</strong> 
                                                            <span class="badge <?php echo get_status_badge($order['status']); ?>">
                                                                <?php echo get_status_text($order['status']); ?>
                                                            </span>
                                                        </p>
                                                        <p class="mb-0"><strong>Total:</strong> <?php echo format_price($order['total']); ?></p>
                                                    </div>
                                                </div>

                                                <!-- Articles -->
                                                <h6>Articles Commandés</h6>
                                                <div class="table-responsive">
                                                    <table class="table table-sm">
                                                        <thead>
                                                            <tr>
                                                                <th>Produit</th>
                                                                <th>Prix Unitaire</th>
                                                                <th>Quantité</th>
                                                                <th>Total</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php foreach ($order['items'] as $item): ?>
                                                            <tr>
                                                                <td><?php echo $item['name']; ?></td>
                                                                <td><?php echo format_price($item['price']); ?></td>
                                                                <td><?php echo $item['quantity']; ?></td>
                                                                <td><?php echo format_price($item['price'] * $item['quantity']); ?></td>
                                                            </tr>
                                                            <?php endforeach; ?>
                                                        </tbody>
                                                        <tfoot>
                                                            <tr class="table-dark">
                                                                <td colspan="3" class="text-end"><strong>Total:</strong></td>
                                                                <td><strong><?php echo format_price($order['total']); ?></strong></td>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                </div>

                                                <!-- Notes -->
                                                <?php if (!empty($order['notes'])): ?>
                                                <div class="mt-3">
                                                    <h6>Notes du client:</h6>
                                                    <p class="text-muted"><?php echo $order['notes']; ?></p>
                                                </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                                                <a href="https://wa.me/<?php echo $order['phone']; ?>" 
                                                   target="_blank" 
                                                   class="btn btn-success">
                                                    <i class="fab fa-whatsapp me-2"></i>Contacter
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>