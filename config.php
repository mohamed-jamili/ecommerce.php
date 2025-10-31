<?php
// config.php - Configuration pour E-commerce des comptes
session_start();

// Configuration de base
define('SITE_NAME', 'CompteStore');
define('ADMIN_PASSWORD', 'admin123');
define('ADMIN_EMAIL', 'admin@compuestore.com');
define('WHATSAPP_NUMBER', '212733597191');
define('ORDERS_FILE', __DIR__ . '/data/orders.json');
define('USERS_FILE', __DIR__ . '/data/users.json');

// Initialiser les sessions
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
if (!isset($_SESSION['user'])) {
    $_SESSION['user'] = null;
}
if (!isset($_SESSION['admin_logged_in'])) {
    $_SESSION['admin_logged_in'] = false;
}

// Fonctions de sécurité
function sanitize_input($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

function validate_phone($phone) {
    return preg_match('/^[0-9]{10}$/', $phone);
}

function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function generate_order_id() {
    return 'CMD-' . date('Ymd-His') . '-' . rand(1000, 9999);
}

// Fonctions de gestion des utilisateurs
function get_users() {
    if (!file_exists(USERS_FILE)) {
        return [];
    }
    $users = json_decode(file_get_contents(USERS_FILE), true);
    return is_array($users) ? $users : [];
}

function save_user($user_data) {
    $users = get_users();
    
    // Vérifier si l'email existe déjà
    foreach ($users as $user) {
        if ($user['email'] === $user_data['email']) {
            return false;
        }
    }
    
    $users[] = $user_data;
    return file_put_contents(USERS_FILE, json_encode($users, JSON_PRETTY_PRINT));
}

function verify_user($email, $password) {
    $users = get_users();
    foreach ($users as $user) {
        if ($user['email'] === $email && password_verify($password, $user['password'])) {
            return $user;
        }
    }
    return false;
}

function user_exists($email) {
    $users = get_users();
    foreach ($users as $user) {
        if ($user['email'] === $email) {
            return true;
        }
    }
    return false;
}

// Fonctions de gestion des comptes
function get_accounts() {
    return [
        [
            'id' => 1,
            'name' => 'Netflix Premium 1 Mois',
            'price' => 45,
            'category' => 'streaming',
            'image' => 'https://images.unsplash.com/photo-1574375927938-d5a98e8ffe85?w=400&h=300&fit=crop',
            'description' => 'Compte Netflix Premium valable 1 mois - 4K UHD',
            'full_description' => 'Compte Netflix Premium partagé valable 1 mois. Qualité 4K UHD, 4 écrans simultanés. Garantie 7 jours.',
            'duration' => '1 Mois',
            'screens' => '4 Écrans',
            'quality' => '4K UHD'
        ],
        [
            'id' => 2,
            'name' => 'Spotify Premium 3 Mois',
            'price' => 60,
            'category' => 'music',
            'image' => 'https://images.unsplash.com/photo-1611339555312-e607c8352fd7?w=400&h=300&fit=crop',
            'description' => 'Spotify Premium 3 mois - Écoute hors ligne',
            'full_description' => 'Compte Spotify Premium valable 3 mois. Écoute sans publicité, téléchargement pour hors ligne, qualité très haute.',
            'duration' => '3 Mois',
            'screens' => '1 Compte',
            'quality' => 'Très Haute'
        ],
        [
            'id' => 3,
            'name' => 'Disney+ 6 Mois',
            'price' => 120,
            'category' => 'streaming',
            'image' => 'https://images.unsplash.com/photo-1626814026160-2237a95fc5a0?w=400&h=300&fit=crop',
            'description' => 'Abonnement Disney+ 6 mois - Tous contenus',
            'full_description' => 'Compte Disney+ complet valable 6 mois. Accès à Marvel, Star Wars, Pixar, National Geographic. 4 écrans simultanés.',
            'duration' => '6 Mois',
            'screens' => '4 Écrans',
            'quality' => '4K UHD'
        ],
        [
            'id' => 4,
            'name' => 'YouTube Premium 1 An',
            'price' => 180,
            'category' => 'video',
            'image' => 'https://images.unsplash.com/photo-1611162617474-5b21e879e113?w=400&h=300&fit=crop',
            'description' => 'YouTube Premium 12 mois - YouTube Music inclus',
            'full_description' => 'YouTube Premium valable 12 mois. Pas de publicités, téléchargement des vidéos, YouTube Music inclus.',
            'duration' => '12 Mois',
            'screens' => '1 Compte',
            'quality' => '4K'
        ],
        [
            'id' => 5,
            'name' => 'CANAL+ Premium 3 Mois',
            'price' => 150,
            'category' => 'tv',
            'image' => 'https://images.unsplash.com/photo-1586902197503-e71026292412?w=400&h=300&fit=crop',
            'description' => 'CANAL+ Premium 3 mois - Chaînes complètes',
            'full_description' => 'Compte CANAL+ Premium avec toutes les chaînes. Sports, films, séries en exclusivité. Valable 3 mois.',
            'duration' => '3 Mois',
            'screens' => '2 Écrans',
            'quality' => 'Full HD'
        ],
        [
            'id' => 6,
            'name' => 'PlayStation Plus 12 Mois',
            'price' => 350,
            'category' => 'gaming',
            'image' => 'https://images.unsplash.com/photo-1606144042614-b2417e99c4e3?w=400&h=300&fit=crop',
            'description' => 'PS Plus 12 mois - Jeux gratuits et online',
            'full_description' => 'Abonnement PlayStation Plus Essential 12 mois. Jeux mensuels gratuits, multijoueur en ligne, stockage cloud.',
            'duration' => '12 Mois',
            'screens' => '1 Console',
            'quality' => 'Standard'
        ]
    ];
}

function get_account_by_id($id) {
    $accounts = get_accounts();
    foreach ($accounts as $account) {
        if ($account['id'] == $id) {
            return $account;
        }
    }
    return null;
}

// Fonctions panier
function add_to_cart($account_id, $quantity = 1) {
    $account = get_account_by_id($account_id);
    if (!$account) return false;

    if (isset($_SESSION['cart'][$account_id])) {
        $_SESSION['cart'][$account_id]['quantity'] += $quantity;
    } else {
        $_SESSION['cart'][$account_id] = [
            'id' => $account['id'],
            'name' => $account['name'],
            'price' => $account['price'],
            'image' => $account['image'],
            'quantity' => $quantity
        ];
    }
    return true;
}

function remove_from_cart($account_id) {
    if (isset($_SESSION['cart'][$account_id])) {
        unset($_SESSION['cart'][$account_id]);
        return true;
    }
    return false;
}

function update_cart_quantity($account_id, $quantity) {
    if ($quantity <= 0) {
        return remove_from_cart($account_id);
    }
    
    if (isset($_SESSION['cart'][$account_id])) {
        $_SESSION['cart'][$account_id]['quantity'] = $quantity;
        return true;
    }
    return false;
}

function get_cart_total() {
    $total = 0;
    foreach ($_SESSION['cart'] as $item) {
        $total += $item['price'] * $item['quantity'];
    }
    return $total;
}

function get_cart_count() {
    $count = 0;
    foreach ($_SESSION['cart'] as $item) {
        $count += $item['quantity'];
    }
    return $count;
}

// Fonctions commandes
function save_order($order_data) {
    $orders = [];
    if (file_exists(ORDERS_FILE)) {
        $orders = json_decode(file_get_contents(ORDERS_FILE), true) ?: [];
    }
    
    $orders[] = $order_data;
    file_put_contents(ORDERS_FILE, json_encode($orders, JSON_PRETTY_PRINT));
}

function get_orders() {
    if (!file_exists(ORDERS_FILE)) {
        return [];
    }
    return json_decode(file_get_contents(ORDERS_FILE), true) ?: [];
}

// Formatage prix
function format_price($price) {
    return number_format($price, 2, ',', ' ') . ' DH';
}

// Fonctions de vérification d'accès
function require_user_login() {
    if (!isset($_SESSION['user']) || $_SESSION['user'] === null) {
        header('Location: login.php');
        exit;
    }
}

function require_admin_login() {
    if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
        header('Location: admin_login.php');
        exit;
    }
}

function is_user_logged_in() {
    return isset($_SESSION['user']) && $_SESSION['user'] !== null;
}

function is_admin_logged_in() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'];
}
?>