<?php
function dbConnect()
{
    try {
        return new PDO('pgsql:host=localhost;dbname=streeteye;port=5432', 'streeteyeuser', 'streetEye', [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
    } catch (Exception $e) {
        die('Erreur : ' . $e->getMessage());
    }
}

function getCountCamera()
{
    $db = dbConnect();
    $req = $db->prepare("SELECT COUNT(*) AS total FROM camera");
    $req->execute();
    $result = $req->fetch(PDO::FETCH_ASSOC);
    return $result['total'];  // ← Retourne un entier
}

function getCountVehicule()
{
    $db = dbConnect();
    $req = $db->prepare("SELECT COUNT(*) AS total FROM vehicule");
    $req->execute();
    $result = $req->fetch(PDO::FETCH_ASSOC);
    return $result['total'];
}

function getCountVehiculeFlash()
{
    $db = dbConnect();
    $req = $db->prepare("SELECT COUNT(*) AS total FROM vehicule where flash = true");
    $req->execute();
    $result = $req->fetch(PDO::FETCH_ASSOC);
    return $result['total'];
}

function getDetectionFlash()
{
    $db = dbConnect();
    $req = $db->prepare("SELECT *  FROM detection  join vehicule on detection.vehicule_id = vehicule.id where vitesse > 20 AND flash = true");
    $req->execute();
    $result = $req->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}

function getCheminPhoto($id){
    $db = dbConnect();
    $req = $db->prepare("SELECT cheminstock  FROM photo join detection on photo.id = detection.photo_id where detection.vehicule_id = :id;");
    $req->execute([
        'id' => $id,
    ]);
    $result = $req->fetch(PDO::FETCH_ASSOC);
    return $result ? $result['cheminstock'] : null;
}

function getTxConfianceMoyen(){
    $db = dbConnect();
    $req = $db->prepare("SELECT AVG(txdeconfiance) FROM detection ");
    $req->execute();
    $result = $req->fetch(PDO::FETCH_ASSOC);
    return $result['avg'];
}

function getNbPassageDerniereHeure() {
    $db = dbConnect();
    $req = $db->prepare("SELECT v.*, d.* FROM vehicule v JOIN detection d ON v.id = d.vehicule_id WHERE d.dateheure >= now() - interval '1 hour'");
    $req->execute();
    $result = $req->fetchAll(PDO::FETCH_ASSOC);
    return $result;

}

function getAllTypes($type) {
    $db = dbConnect();
    $req = $db->prepare("SELECT * FROM vehicule where type = :type;");
    $req->execute([
        'type' => $type,
    ]);
    $result = $req->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}