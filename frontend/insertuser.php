<?php
require_once('includes/dataAccess.php');
$username = "Arnaud";
$mdp = "123456789";
$hash = password_hash($mdp, PASSWORD_DEFAULT);


$db = dbConnect();
$req = $db->prepare("INSERT INTO admin (name,hashmdp) VALUES (:name,:hashmdp) ");
$req->execute(['name' => $username,
    'hashmdp' => $hash,]);
echo "USER:" . $username . "Hash" . $mdp;


?>