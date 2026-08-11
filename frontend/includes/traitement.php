<?php

require_once("dataAccess.php");

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['restore']) && !empty($_GET["vehicule_id"]) && !empty($_GET["photo_id"])) {
        restoreAlerte($_GET["vehicule_id"], $_GET["photo_id"]);
        header('Location: ../alertes.php');
        exit();
    }

    if (!empty($_GET["vehicule_id"]) && !empty($_GET["photo_id"])) {
        $vehicule_id = $_GET["vehicule_id"];
        $photo_id = $_GET["photo_id"];
        suppression_globale($vehicule_id, $photo_id);
        header('Location: ../alertes.php');
        exit();
    }
}

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: ../demo_mode.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST["camera_id"], $_POST["vehicule_id"], $_POST["photo_id"], $_POST["vitesse"], $_POST["txconfiance"], $_POST["statut"])) {
        $camera_id = (int)$_POST["camera_id"];
        $vehicule_id = $_POST["vehicule_id"];
        $photo_id = $_POST["photo_id"];
        $vitesse = $_POST["vitesse"];
        $txdeconfiance = $_POST["txconfiance"];
        $deletedat = $_POST["statut"] === 'supprime' ? date('Y-m-d H:i:s') : null;
        updateAlertes($camera_id, $vehicule_id, $photo_id, $vitesse, $txdeconfiance, $deletedat);
        header('Location: ../alertes.php');
        exit();
    }
}

//
//

?>
