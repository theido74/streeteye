<?php

require_once("dataAccess.php");

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!empty($_GET["id"])) {
        $id = $_GET["id"];
        suppression_globale($id);
        header('Location: ../alertes.php');
        echo "Detection supprimée";
        exit();
    }
}
//
//

?>
