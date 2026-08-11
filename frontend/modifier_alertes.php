<?php
session_start();
require_once("includes/dataAccess.php");

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $detection = null;
    if (!empty($_GET["vehicule_id"]) && !empty($_GET["photo_id"])) {
        $detection = getDetectionByVehiculeAndPhoto($_GET["vehicule_id"], $_GET["photo_id"]);
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>🛰️ StreetEye ESIG / Édition</title>
    <link rel="stylesheet" href="style.css"/>
</head>
<body>

<?php
$clockStatus = (isset($_SESSION['mode']) && $_SESSION['mode'] === 'demo') ? 'mode demo' : 'SYSTÈME OPÉRATIONNEL';
$clockDetails = ['🔒 CHIFFREMENT AES-256'];
include 'includes/timebar.php';
?>

<div class="card" style="max-width:540px; margin:40px auto;">

    <div class="card-title">
        <span class="icon">✏️</span>
        Modifier l’entrée
        <span class="badge">ÉDITION</span>
    </div>

    <p style="color:rgba(160,200,230,0.7); font-size:0.9rem; margin-bottom:18px;">
        Mettez à jour les champs ci‑dessous et cliquez sur <strong>Enregistrer</strong>.
    </p>

    <form method="post" action="includes/traitement.php">

        <div class="stat-grid">

            <div class="stat-item" style="grid-column: span 2; text-align:left; padding:12px 16px;">
                <label for="id"
                       style="display:block; font-size:0.8rem; color:rgba(160,200,230,0.6); margin-bottom:4px;">
                    🆔 Identifiant
                </label>
                <div id="id"
                     readonly
                     style="width:100%; background:transparent; border:none; color:#b0e0ff; font-family:inherit; font-size:1rem; outline:none; opacity:0.7; cursor:not-allowed;">
                    <?= htmlspecialchars($detection['camera_id'] . ' / ' . $detection['vehicule_id'] . ' / ' . $detection['photo_id']) ?>
                </div>
            </div>

            <input type="hidden" name="camera_id" value="<?= htmlspecialchars($detection['camera_id']) ?>">
            <input type="hidden" name="photo_id" value="<?= htmlspecialchars($detection['photo_id']) ?>">

            <div class="stat-item" style="text-align:left; padding:12px 16px;">
                <label for="vehicule_id"
                       style="display:block; font-size:0.8rem; color:rgba(160,200,230,0.6); margin-bottom:4px;">
                    📛 Vehicule
                </label>
                <input type="text" id="vehicule_id" name="vehicule_id"
                       value="<?= $detection['vehicule_id'] ?>"
                       readonly
                       style="width:100%; background:transparent; border:none; color:#b0e0ff; font-family:inherit; font-size:1rem; outline:none; opacity:0.7; cursor:not-allowed;">
            </div>

            <div class="stat-item" style="text-align:left; padding:12px 16px;">
                <label for="dateheure"
                       style="display:block; font-size:0.8rem; color:rgba(160,200,230,0.6); margin-bottom:4px;">
                    🔄 Date et Heure
                </label>
                <input type="text" id="dateheure" name="dateheure"
                       value="<?= $detection['dateheure'] ?>"
                       readonly
                       style="width:100%; background:transparent; border:none; color:#b0e0ff; font-family:inherit; font-size:1rem; outline:none; opacity:0.7; cursor:not-allowed;">
            </div>
            <div class="stat-item" style="text-align:left; padding:12px 16px;">
                <label for="vitesse"
                       style="display:block; font-size:0.8rem; color:rgba(160,200,230,0.6); margin-bottom:4px;">
                    🔄 Vitesse
                </label>
                <input type="text" id="vitesse" name="vitesse"
                       value="<?= $detection['vitesse'] ?>"
                       style="width:100%; background:transparent; border:none; color:#b0e0ff; font-family:inherit; font-size:1rem; outline:none; opacity:0.7;">
            </div>
            <div class="stat-item" style="text-align:left; padding:12px 16px;">
                <label for="txconfiance"
                       style="display:block; font-size:0.8rem; color:rgba(160,200,230,0.6); margin-bottom:4px;">
                    🔄 Taux de Confiance
                </label>
                <input type="text" id="txconfiance" name="txconfiance"
                       value="<?= $detection['txdeconfiance'] ?>"
                       style="width:100%; background:transparent; border:none; color:#b0e0ff; font-family:inherit; font-size:1rem; outline:none; opacity:0.7;">
            </div>

            <div class="stat-item" style="text-align:left; padding:12px 16px;">
                <label for="statut"
                       style="display:block; font-size:0.8rem; color:rgba(160,200,230,0.6); margin-bottom:4px;">
                    🔄 Statut
                </label>
                <select id="statut" name="statut"
                        style="width:100%; background:transparent; border:none; color:#b0e0ff; font-family:inherit; font-size:1rem; outline:none; appearance:none; -webkit-appearance:none;">
                    <option value="actif"
                            style="background:#1a2639;" <?php echo (isset($detection['deletedat']) && $detection['deletedat'] === null) ? 'selected' : ''; ?>>
                        Actif
                    </option>
                    <option value="supprime"
                            style="background:#1a2639;" <?php echo (isset($detection['deletedat']) && $detection['deletedat'] !== null) ? 'selected' : ''; ?>>
                        Supprimer
                    </option>
                </select>
            </div>


            <button type="submit"
                    class="stat-item"
                    style="width:100%; margin-top:18px; text-align:center; border:none; background:rgba(0,150,255,0.08); cursor:pointer; padding:14px; border-radius:20px; border:1px solid rgba(0,180,255,0.15); font-family:inherit; font-size:1.05rem; color:#b0e0ff; transition:all 0.25s;">
                💾 Enregistrer les modifications
            </button>

    </form>

    <!-- Lien Retour -->
    <div style="margin-top:20px; text-align:center;">
        <a href="alertes.php"
           style="color:#6bc9ff; text-decoration:none; font-size:0.9rem; border-bottom:1px dashed rgba(0,180,255,0.3);">
            ← Retour
        </a>
    </div>

</div>

</body>
</html>
