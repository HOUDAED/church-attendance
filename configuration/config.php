<?php
session_start();
require_once __DIR__ . '/database.php';

// Fonctions utiles
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function redirect($page) {
    header("Location: $page");
    exit;
}

// Ajout d'une fonction de sécurité pour les sorties HTML
function h($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}