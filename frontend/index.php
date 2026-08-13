<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

require_once('includes/dataAccess.php');
require_once('includes/traitement.php');

$txConfiance = getTxConfianceMoyen();
$type = ["voiture", "2 roues", "camion", "cycliste", "cheval", "chien", "chat", "pieton"];
$username = $_SESSION['username'];


?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>🛰️ StreetEye ESIG</title>
    <link rel="stylesheet" href="style.css"/>
</head>
<body>
<div class="dashboard">

    <?php
    $clockStatus = (isset($_SESSION['mode']) && $_SESSION['mode'] === 'demo') ? 'mode demo' : 'SYSTÈME OPÉRATIONNEL, Bonjour ' . $username;
    $clockDetails = ['🔒 CHIFFREMENT AES-256'];
    $clockActionHtml = '<a href="includes/traitement.php?logout=1" class="stat-item" style="background-color:red; text-decoration:none; color:white;">⚡ SE DÉCONNECTER</a>';
    $clockShowDashboard = false;
    include 'includes/timebar.php';
    ?>

    <div class="card card-full">
        <div class="card-title-acceuil">
            <span class="icon">📡</span> SURVEILLANCE
            <span class="badge">LIVE</span>
        </div>
        <div class="stat-grid">
            <div class="stat-item">
                <div class="stat-value" id="camCount"><?= getCountCamera() ?></div>
                <div class="stat-label">Caméras actives</div>
            </div>
            <div class="stat-item">
                <div class="stat-value" id="vehicleCount"><?= getCountVehicule() ?></div>
                <div class="stat-label">Véhicules détectés</div>
            </div>
            <a href="alertes.php" class="stat-card-link" style="text-decoration: none;">
                <div class="stat-item">
                    <div class="stat-value" id="alertCount"><?= getCountVehiculeFlash() ?></div>
                    <div class="stat-label">Alertes</div>
                </div>
            </a>
            <div class="stat-item">
                <div class="stat-value" id="stat"><?= round($txConfiance * 100, 2) ?>%</div>
                <div class="stat-label">Taux de confiance moyen</div>
            </div>
        </div>
    </div>

    <div class="card card-full">

        <!-- Caméra 1 -->
        <div class="video-feed">
            <img id="cam1" src="stream/frame.jpg"
                 style="width:100%; height:100%; object-fit:cover; position:absolute; top:0; left:0; z-index:1; background:#0a0e1a;">

            <!-- Effets existants -->
            <div class="scan-effect" style="z-index:2;"></div>
            <div class="sweep" style="z-index:2;"></div>

            <!-- Overlay -->
            <div class="overlay" style="z-index:3;">
                <span class="status-dot"></span> LIVE • <span id="cam1time" data-clock="utc2">--:--:--</span>
            </div>
        </div>

        <div class="card card-full card-map-alerts">
            <!-- Partie gauche : carte -->
            <div class="map-section">
                <div class="card-title" style="border-bottom: none; padding-bottom: 0; margin-bottom: 12px;">
                    <span class="icon">🗺️</span> ZONE SURVEILLÉE
                    <span class="badge">radar actif</span>
                </div>
                <div class="map-placeholder">
                    <div class="grid-lines"></div>
                    <img src="includes/asset/map.png"
                         style="position:absolute; top:0; left:0; width:100%; height:100%; object-fit:cover; z-index:1;">
                    <div class="pulse-point" style="top:5%; left:25%; z-index:2;"></div>
                    <div class="pulse-point" style="top:40%; left:45%; animation-delay: 0.7s; z-index:2;"></div>
                    <div class="pulse-point" style="top:5%; left:35%; animation-delay: 1.3s; z-index:2;"></div>
                    <span class="label" style="z-index:2;">◉ 3 points d'intérêt</span>
                </div>
            </div>  <!-- ✅ Fermeture de map-section -->

            <!-- Partie droite : alertes -->
            <div class="alerts-section">
                <div class="card-title" style="border-bottom: none; padding-bottom: 0; margin-bottom: 12px;">
                    <span class="icon">🚨</span> 3 DERNIERES ALERTES
                    <span class="badge" id="alertBadge"><?= getCountVehiculeFlash() ?></span>
                    <span class="badge">radar actif</span>
                </div>
                <div class="alert-list" id="alertList">
                    <?php $alertes = get3DetectionFlash();
                    if (sizeof($alertes) > 0):
                        foreach ($alertes as $alerte):
                            $id = $alerte['vehicule_id'];
                            $chemin = getCheminPhoto($id);
                            $dateHeure = $alerte['dateheure'] ?? $alerte['dateHeure'] ?? '';
                            ?>
                            <div class="alert-item">
                                <span class="time">Date <?= htmlspecialchars($dateHeure) ?></span>

                                <span class="msg">Vitesse <?= htmlspecialchars($alerte['vitesse']) ?></span>
                                <span class="severity high"
                                      onclick="ouvrirPhoto(<?= htmlspecialchars(json_encode("../" . $chemin), ENT_QUOTES) ?>)">
                Vehicule ID <?= htmlspecialchars($alerte['vehicule_id']) ?>
            </span>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                </div>
            </div>
        </div>

        <div class="card card-full">
            <div class="card-title">
                <span class="icon">📊</span> ANALYSE TRAFIC
                <span class="badge">temps réel</span>
            </div>
            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; text-align: center;">
                <div class="stat-item" style="background: rgba(0,150,255,0.03);">
                    <div class="stat-value" style="font-size:1.6rem;"><?= getNbPassageDerniereHeure() ?> </div>
                    <div class="stat-label">Passage la dernière heure</div>
                </div>
                <?php
                foreach ($type as $t):
                    ?>
                    <div class="stat-item" style="background: rgba(0,150,255,0.03);">
                        <div class="stat-value" style="font-size:1.6rem;"><?= sizeof(getAllTypes($t)) ?></div>
                        <div class="stat-label"><?= $t ?></div>
                    </div>
                <?php
                endforeach;
                ?>

            </div>
        </div>

    </div>

    <script>
        // Rafraîchir l'image de la caméra
        function refreshCamera1() {
            const img = document.getElementById('cam1');
            if (img) {
                img.src = 'stream/frame.jpg?' + new Date().getTime();
            }
        }

        // Rafraîchir toutes les 200ms
        setInterval(refreshCamera1, 200);

        function ouvrirPhoto(chemin) {
            if (!chemin) {
                return;
            }
            window.open(chemin, 'Photo', 'width=800,height=600,scrollbars=yes');
        }
    </script>
</body>
</html>
