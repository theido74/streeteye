<?php
require_once('includes/dataAccess.php');
session_start();

$error = '';  //


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Récupération et validation
    $username = trim(!empty($_POST['username']) ? $_POST['username'] : '');
    $password = !empty($_POST['password']) ? $_POST['password'] : '';

    if (empty($username) || empty($password)) {
        $error = "Tous les champs sont requis.";
    } else {

        $result = getMdpByUsername($username);


        if ($result && password_verify($password, $result['hashmdp'])) {
            $_SESSION['username'] = $username;
            header('Location: index.php');
            exit;
        } else {
            $error = "Identifiants invalides.";
            echo $error;
        }
    }

}
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>🛰️ StreetEye ESIG / Alertes</title>
    <link rel="stylesheet" href="style.css"/>
</head>

<body>

<!-- ============================================================
     FORMULAIRE DE CONNEXION – STYLE EXISTANT UNIQUEMENT
     ============================================================ -->
<div class="card" style="max-width:420px; margin:40px auto;">
    <!-- Titre avec badge -->
    <div class="card-title">
        <span class="icon">🔐</span>
        Connexion
        <span class="badge">SECURE</span>
    </div>

    <!-- Formulaire -->
    <form method="post" action="">
        <!-- Grille 2 colonnes pour les champs -->
        <div class="stat-grid">
            <!-- Champ identifiant -->
            <div class="stat-item">
                <label for="username" class="stat-label">👤 Identifiant</label>
                <input type="text" id="username" name="username"
                       placeholder="nom d'utilisateur"
                       style="width:100%; background:transparent; border:none; color:#b0e0ff; font-family:inherit; font-size:1rem; outline:none;">
            </div>
            <!-- Champ mot de passe -->
            <div class="stat-item">
                <label for="password" class="stat-label">🔒 Mot de passe</label>
                <input type="password" id="password" name="password"
                       placeholder="••••••••"
                       style="width:100%; background:transparent; border:none; color:#b0e0ff; font-family:inherit; font-size:1rem; outline:none;">
            </div>
        </div>

        <!-- Bouton de soumission -->
        <button type="submit" class="stat-item"
                style="width:100%; margin-top:12px; text-align:center; cursor:pointer; border:none; font-family:inherit; font-size:1rem; color:#b0e0ff; background:rgba(0,150,255,0.1); transition:background 0.3s;">
            🔑 Se connecter
        </button>
    </form>

</div>
</body>

</html>
