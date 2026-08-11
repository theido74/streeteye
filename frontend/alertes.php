<?php
session_start();
require_once('includes/dataAccess.php');
require_once('includes/traitement.php');
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
<div class="dashboard">

    <!-- ============ BANDEAU D'INFORMATIONS ============ -->
    <?php
    $clockStatus = (isset($_SESSION['mode']) && $_SESSION['mode'] === 'demo') ? 'mode demo' : 'SYSTÈME OPÉRATIONNEL';
    $clockDetails = ['🔒 CHIFFREMENT AES-256'];
    include 'includes/timebar.php';
    ?>

    <div class="alerts-section" style="width: 100%;">
        <div class="card-title" style="border-bottom: none; padding-bottom: 0; margin-bottom: 12px;">
            <span class="icon">🚨</span> DETECTIONS
            <?php $alertes = getDetectionFlash(); ?>
            <span class="badge" id="alertBadge"><?= count(array_filter($alertes, function ($alerte) {
                    return $alerte['deletedat'] === null;
                })) ?></span>
        </div>
        <div class="card-title" style="border-bottom:none; padding-bottom:0; margin:18px 0 12px;">
            <span class="icon">✅</span> ALERTES ACTIVES
        </div>
        <div class="alert-list" id="alertList">
            <?php $actives = array_filter($alertes, function ($alerte) {
                return $alerte['deletedat'] === null;
            }); ?>
            <?php if (sizeof($actives) > 0): ?>
                <?php foreach ($actives as $alerte): ?>
                    <div class="alert-item">
                        <span class="time">vehicule id <?= htmlspecialchars($alerte['vehicule_id']) ?></span>
                        <span class="time">Date <?= htmlspecialchars($alerte['dateheure']) ?></span>
                        <span class="msg">Vitesse <?= htmlspecialchars($alerte['vitesse']) ?></span>
                        <a href="includes/traitement.php?vehicule_id=<?= $alerte['vehicule_id'] ?>&photo_id=<?= $alerte['photo_id'] ?>"
                           class="alert-item"
                           style="text-decoration: none;color: white">Supprimer</a>
                        <a href="modifier_alertes.php?vehicule_id=<?= $alerte['vehicule_id'] ?>&photo_id=<?= $alerte['photo_id'] ?>"
                           class="alert-item"
                           style="text-decoration: none;color: white">Modifier</a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="alert-item" style="text-align: center; padding: 20px;">
                    Aucune alerte active
                </div>
            <?php endif; ?>
        </div>

        <div class="card-title" style="border-bottom:none; padding-bottom:0; margin:18px 0 12px;">
            <span class="icon">🗑️</span> ALERTES SUPPRIMÉES
        </div>
        <div class="alert-list">
            <?php $supprimees = array_filter($alertes, function ($alerte) {
                return $alerte['deletedat'] !== null;
            }); ?>
            <?php if (sizeof($supprimees) > 0): ?>
                <?php foreach ($supprimees as $alerte): ?>
                    <div class="alert-item">
                        <span class="time">vehicule id <?= htmlspecialchars($alerte['vehicule_id']) ?></span>
                        <span class="time">Date <?= htmlspecialchars($alerte['dateheure']) ?></span>
                        <span class="msg">Vitesse <?= htmlspecialchars($alerte['vitesse']) ?></span>
                        <a href="includes/traitement.php?restore=1&vehicule_id=<?= $alerte['vehicule_id'] ?>&photo_id=<?= $alerte['photo_id'] ?>"
                           class="alert-item"
                           style="text-decoration: none;color: white">Réactiver</a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="alert-item" style="text-align: center; padding: 20px;">
                    Aucune alerte supprimée
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>
