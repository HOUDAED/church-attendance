<?php
require_once '../configuration/config.php';

// Détruire la session
session_destroy();

// Rediriger vers la page de connexion
redirect('../login.php');