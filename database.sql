-- Création de la base de données
CREATE DATABASE IF NOT EXISTS compuestore CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE compuestore;

-- Table des utilisateurs
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    role ENUM('user', 'admin') DEFAULT 'user',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table des commandes
CREATE TABLE orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id VARCHAR(50) UNIQUE NOT NULL,
    user_id INT,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    city VARCHAR(50) NOT NULL,
    address TEXT NOT NULL,
    notes TEXT,
    total DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'confirmed', 'delivered', 'cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Table des articles de commande
CREATE TABLE order_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    product_name VARCHAR(255) NOT NULL,
    product_price DECIMAL(10,2) NOT NULL,
    quantity INT NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);

-- Table des produits (optionnelle)
CREATE TABLE products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    category VARCHAR(50),
    image_url VARCHAR(500),
    description TEXT,
    duration VARCHAR(50),
    screens VARCHAR(50),
    quality VARCHAR(50),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insertion d'un administrateur par défaut
INSERT INTO users (full_name, email, password, phone, role) VALUES 
('Administrateur', 'admin@compuestore.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '0612345678', 'admin');

-- Insertion de produits d'exemple
INSERT INTO products (name, price, category, image_url, description, duration, screens, quality) VALUES 
('Netflix Premium 1 Mois', 45.00, 'streaming', 'https://images.unsplash.com/photo-1574375927938-d5a98e8ffe85?w=400&h=300&fit=crop', 'Compte Netflix Premium valable 1 mois - 4K UHD', '1 Mois', '4 Écrans', '4K UHD'),
('Spotify Premium 3 Mois', 60.00, 'music', 'https://images.unsplash.com/photo-1611339555312-e607c8352fd7?w=400&h=300&fit=crop', 'Spotify Premium 3 mois - Écoute hors ligne', '3 Mois', '1 Compte', 'Très Haute'),
('Disney+ 6 Mois', 120.00, 'streaming', 'https://images.unsplash.com/photo-1626814026160-2237a95fc5a0?w=400&h=300&fit=crop', 'Abonnement Disney+ 6 mois - Tous contenus', '6 Mois', '4 Écrans', '4K UHD');