<?php
// public/index.php

// Demarrage de la session pour toutes les pages
session_start();

// Inclusion des dépendances
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/Models/User.php';
require_once __DIR__ . '/../src/Controllers/AuthController.php';

// Initialisation des objets MVC
$userModel = new User($pdo);
$authController = new AuthController($userModel);

// Recuperation de l'URL demandee
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Systeme de routage basique
switch ($uri) {
    case '/':
    case '/login':
        $authController->login();
        break;
        
    case '/register':
        $authController->register();
        break;
        
    case '/logout':
        $authController->logout();
        break;
        
    case '/dashboard':
        // Verification de securite : l'utilisateur doit etre connecte
        if (!isset($_SESSION['utilisateur_id'])) {
            header("Location: /login");
            exit;
        }
        // Chargement de la vue du tableau de bord
        require __DIR__ . '/../src/Views/dashboard/index.php';
        break;
        
    default:
        http_response_code(404);
        echo "Page introuvable (404)";
        break;
}
?>
