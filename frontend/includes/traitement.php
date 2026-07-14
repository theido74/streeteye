<?php

require_once("dataAccess.php");

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!empty($_GET["vehicule_id"]) && !empty($_GET["photo_id"])) {
        $vehicule_id = $_GET["vehicule_id"];
        $photo_id = $_GET["photo_id"];
        suppression_globale($vehicule_id, $photo_id);
        header('Location: ../alertes.php');
        exit();
    }
}
//
//

?>
