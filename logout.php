<?php
require_once 'config.php';

// Déconnecter l'utilisateur et l'admin
session_destroy();

// Rediriger vers la page d'accueil
header('Location: index.php');
exit;
?>