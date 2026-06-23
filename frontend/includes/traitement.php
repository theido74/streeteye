<?php

require_once ("dataAccess.php");
function formatDateUtcPlus2($dateheure) {
    if (empty($dateheure)) {
        return '';
    }

    try {
        $date = new DateTime($dateheure);
        $date->setTimezone(new DateTimeZone('Etc/GMT-2'));
        return $date->format('d/m/Y H:i:s') . ' UTC+2';
    } catch (Exception $e) {
        return $dateheure;
    }
}

?>
