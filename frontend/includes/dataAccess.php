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

function get3DetectionFlash()
{
    $db = dbConnect();
    $req = $db->prepare("SELECT *
                                FROM detection
                                         JOIN vehicule ON detection.vehicule_id = vehicule.id
                                WHERE vitesse > 20
                                  AND flash = true
                                ORDER BY dateheure DESC
                                LIMIT 3;");
    $req->execute();
    $result = $req->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}

function getCheminPhoto($id)
{
    $db = dbConnect();
    $req = $db->prepare("SELECT cheminstock  FROM photo join detection on photo.id = detection.photo_id where detection.vehicule_id = :id;");
    $req->execute([
        'id' => $id,
    ]);
    $result = $req->fetch(PDO::FETCH_ASSOC);
    return $result ? $result['cheminstock'] : null;
}

function getTxConfianceMoyen()
{
    $db = dbConnect();
    $req = $db->prepare("SELECT AVG(txdeconfiance) FROM detection ");
    $req->execute();
    $result = $req->fetch(PDO::FETCH_ASSOC);
    return $result['avg'];
}

function getNbPassageDerniereHeure()
{
    $db = dbConnect();
    $req = $db->prepare("SELECT v.*, d.* FROM vehicule v JOIN detection d ON v.id = d.vehicule_id WHERE d.dateheure >= now() - interval '1 hour'");
    $req->execute();
    $result = $req->fetchAll(PDO::FETCH_ASSOC);
    return $result;

}

function getAllTypes($type)
{
    $db = dbConnect();
    $req = $db->prepare("SELECT * FROM vehicule where type = :type;");
    $req->execute([
        'type' => $type,
    ]);
    $result = $req->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}

function suppression_globale($vehicule_id, $photo_id)
{

    $db = dbConnect();
    $req = $db->prepare("SELECT suppression_globale(:camera_id,:vehicule_id,:photo_id);");
    $req->execute(['camera_id' => 1,
        'vehicule_id' => $vehicule_id,
        'photo_id' => $photo_id,]);
}


function getDetectionFlash()
{
    $db = dbConnect();
    $req = $db->prepare("SELECT *
                                FROM detection
                                         JOIN vehicule ON detection.vehicule_id = vehicule.id
                                WHERE vitesse > 20
                                  AND flash = true AND detection.deletedAt IS NULL AND vehicule.deletedAt IS NULL
                                ORDER BY dateheure DESC;");
    $req->execute();
    $result = $req->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}

function getMdpByUsername($username)
{
    $db = dbConnect();
    $req = $db->prepare("SELECT hashmdp FROM admin WHERE name = :username;");
    $req->execute([
        'username' => $username
    ]);
    $result = $req->fetch(PDO::FETCH_ASSOC);
    return $result;
}