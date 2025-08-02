<?php
require_once 'configuration/config.php';

$username = 'admin';
$password = password_hash('admin123', PASSWORD_DEFAULT);

try {
    $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    $stmt->execute([$username, $password]);
    echo "Utilisateur créé avec succès!";
} catch(PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}