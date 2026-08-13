<?php
if (!isset($clockStatus)) {
    $clockStatus = (isset($_SESSION['mode']) && $_SESSION['mode'] === 'demo') ? 'mode demo' : 'SYSTÈME OPÉRATIONNEL';
}
if (!isset($clockDetails)) {
    $clockDetails = [];
}
if (!isset($clockActionHtml)) {
    $clockActionHtml = '';
}
if (!isset($clockShowDashboard)) {
    $clockShowDashboard = (session_status() === PHP_SESSION_ACTIVE && isset($_SESSION['username']));
}
?>
<div class="info-bar">
    <div class="status-led">
        <span class="led"></span>
        <span><?= htmlspecialchars($clockStatus) ?></span>
        <?php foreach ($clockDetails as $clockDetail): ?>
            <span style="margin-left: 20px; opacity:0.6;">|</span>
            <span style="margin-left: 20px;"><?= htmlspecialchars($clockDetail) ?></span>
        <?php endforeach; ?>
        <?= $clockActionHtml ?>
        <?php if ($clockShowDashboard): ?>
            <a href="index.php"
               class="stat-item"
               style="margin-left:12px; background:rgba(0,150,255,0.08); text-decoration:none; color:white;">
                ↩ Retour dashboard
            </a>
        <?php endif; ?>
    </div>
    <div class="timestamp" data-clock="utc2">--:--:-- UTC+2</div>
</div>
<script src="includes/clock.js" defer></script>
