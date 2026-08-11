<?php
session_start();
$mode = isset($_POST['mode']) ? $_POST['mode'] : null;
$error = null;

if (!empty($mode)) {
    $_SESSION['mode'] = $mode;
    if ($mode === 'real') {
        header('Location: real_mode_info.php');
    } else {
        header('Location: login.php');
    }
    exit;
} else {
    $error = "Veuillez choisir un mode";
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>🛰️ StreetEye ESIG / Mode</title>
    <link rel="stylesheet" href="style.css"/>
</head>
<body>

<?php
$clockStatus = (isset($_SESSION['mode']) && $_SESSION['mode'] === 'demo') ? 'mode demo' : 'SYSTÈME OPÉRATIONNEL';
$clockDetails = ['🔒 CHIFFREMENT AES-256'];
$clockShowDashboard = false;
include 'includes/timebar.php';
?>

<div class="card" style="max-width:420px; margin:40px auto;">
    <div class="card-title">
        <span class="icon">⚙️</span>
        Choisir le mode
        <span class="badge">MODE</span>
    </div>

    <?php if ($error): ?>
        <div style="background:rgba(255,80,80,0.1); border:1px solid rgba(255,80,80,0.25); border-radius:12px; padding:10px 14px; margin-bottom:18px; text-align:center; color:#ff9090;">
            ⚠️ <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <form method="post" action="">
        <div class="stat-grid">
            <!-- Option DEMO -->
            <button type="submit" name="mode" value="demo"
                    class="stat-item"
                    style="border:none; background:transparent; cursor:pointer; font-family:inherit; width:100%; text-align:center;">
                <span style="font-size:2.2rem;">🧪</span>
                <span style="font-weight:600; font-size:1.2rem; display:block;">Demo</span>
                <span style="font-size:0.85rem; color:rgba(160,200,230,0.8); display:block; line-height:1.4;">
                    Utilise des données déjà capturées<br>
                    <span style="font-size:0.75rem; opacity:0.7;">(aucune caméra nécessaire)</span>
                </span>
            </button>

            <!-- Option REAL -->
            <button type="submit" name="mode" value="real"
                    class="stat-item"
                    style="border:none; background:transparent; cursor:pointer; font-family:inherit; width:100%; text-align:center;">
                <span style="font-size:2.2rem;">🌐</span>
                <span style="font-weight:600; font-size:1.2rem; display:block;">Real</span>
                <span style="font-size:0.85rem; color:rgba(160,200,230,0.8); display:block; line-height:1.4;">
                    Caméra connectée en direct<br>
                    <span style="font-size:0.75rem; opacity:0.7;">insertions automatiques en temps réel <br>(caméra nécessaire)</span>
                </span>
            </button>
        </div>
    </form>

</div>

</body>
</html>
