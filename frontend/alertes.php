<?php
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
    <div class="info-bar">
        <div class="status-led">
            <span class="led"></span>
            <span>SYSTÈME OPÉRATIONNEL</span>
            <span style="margin-left: 20px; opacity:0.6;">|</span>
            <span style="margin-left: 20px;">🔒 CHIFFREMENT AES-256</span>
        </div>
        <div class="timestamp" id="timestamp">--:--:-- UTC+2</div>
    </div>

    <div class="alerts-section" style="width: 100%;">
        <div class="card-title" style="border-bottom: none; padding-bottom: 0; margin-bottom: 12px;">
            <span class="icon">🚨</span> DETECTIONS
            <span class="badge" id="alertBadge"><?= sizeof(getDetectionFlash()) ?></span>
        </div>
        <div class="alert-list" id="alertList">
            <?php
            $alertes = getDetectionFlash();
            if (sizeof($alertes) > 0):
                foreach ($alertes as $alerte):
                    ?>
                    <div class="alert-item">
                        <span class="time">Date <?= htmlspecialchars($alerte['dateheure']) ?></span>
                        <span class="msg">Vitesse <?= htmlspecialchars($alerte['vitesse']) ?></span>
                        <a href="includes/traitement.php?id=<?= $alerte['id'] ?>" class="alert-item"
                           style="text-decoration: none;color: white">Supprimer</a>
                    </div>
                <?php
                endforeach;
            else:
                ?>
                <div class="alert-item" style="text-align: center; padding: 20px;">
                    Aucune détection à afficher
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>

