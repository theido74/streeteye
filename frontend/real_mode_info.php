<?php
session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>🛰️ StreetEye ESIG / Mode Real</title>
    <link rel="stylesheet" href="style.css"/>
</head>
<body>

<?php
$clockStatus = (isset($_SESSION['mode']) && $_SESSION['mode'] === 'demo') ? 'mode demo' : 'SYSTÈME OPÉRATIONNEL';
$clockDetails = ['🔒 CHIFFREMENT AES-256'];
$clockShowDashboard = false;
include 'includes/timebar.php';
?>

<div class="card" style="max-width:480px; margin:40px auto;">

    <div class="card-title">
        <span class="icon">🌐</span>
        Mode Real
        <span class="badge">ACTIF</span>
    </div>

    <p style="color:#b0e0ff; font-size:1.05rem; margin-bottom:18px; line-height:1.5;">
        Vous avez choisi le mode <strong>Real</strong> – la caméra est connectée et les insertions se font
        automatiquement.
    </p>

    <div style="background:rgba(0,150,255,0.04); border:1px solid rgba(0,180,255,0.12); border-radius:16px; padding:18px 20px; margin-bottom:24px;">
        <p style="color:#b0e0ff; font-weight:500; margin-bottom:12px;">
            📋 Pour utiliser le flux réel, suivez ces étapes :
        </p>
        <ul style="color:rgba(160,200,230,0.85); list-style:none; padding:0; margin:0; font-size:0.95rem; line-height:1.8;">
            <li style="padding-left:28px; position:relative; margin-bottom:6px;">
                <span style="position:absolute; left:0;">1.</span>
                Téléchargez le programme d’analyse depuis GitHub :
                <br/>
                <a href="https://github.com/theido74/streeteye" target="_blank"
                   style="color:#6bc9ff; text-decoration:underline; font-weight:500; word-break:break-all;">
                    github.com/votre-repo/streeteye-analyse
                </a>
            </li>
            <li style="padding-left:28px; position:relative; margin-bottom:6px;">
                <span style="position:absolute; left:0;">2.</span>
                Placez-vous dans le dossier du projet et copiez le fichier <code
                        style="background:rgba(255,255,255,0.06); padding:2px 8px; border-radius:6px;">.env.example</code>
                vers <code style="background:rgba(255,255,255,0.06); padding:2px 8px; border-radius:6px;">.env</code>.
            </li>
            <li style="padding-left:28px; position:relative;">
                <span style="position:absolute; left:0;">3.</span>
                Renseignez vos identifiants (API, caméra, base de données) dans le fichier <code
                        style="background:rgba(255,255,255,0.06); padding:2px 8px; border-radius:6px;">.env</code>.
                et lancer launcher.sh
            </li>
        </ul>
    </div>

    <a href="demo_mode.php"
       class="stat-item"
       style="display:block; text-align:center; text-decoration:none; border:none; background:rgba(0,150,255,0.04); cursor:pointer; padding:16px; border-radius:20px; border:1px solid rgba(0,180,255,0.12); transition:all 0.25s;">
        ← Retour à la sélection du mode
    </a>
    <a href="login.php"
       class="stat-item"
       style="display:block; text-align:center; text-decoration:none; border:none; background:rgba(0,150,255,0.04); cursor:pointer; padding:16px; border-radius:20px; border:1px solid rgba(0,180,255,0.12); transition:all 0.25s;">
        Accès au login →
    </a>

    <div style="margin-top:18px; text-align:center; font-size:0.7rem; color:rgba(160,200,230,0.25);">
        StreetEye ESIG · analyse en temps réel
    </div>

</div>

</body>
</html>
