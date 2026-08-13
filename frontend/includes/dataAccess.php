<?php

require_once __DIR__ . '/../../vendor/autoload.php';

function loadEnv()
{
    static $loaded = false;

    if ($loaded) {
        return;
    }

    $envPath = __DIR__ . '/../../.env';
    if (is_file($envPath)) {
        Dotenv\Dotenv::createImmutable(__DIR__ . '/../../')->safeLoad();
    }

    $loaded = true;
}

function dbConnect()
{
    try {
        loadEnv();

        $host = $_ENV['DB_HOST'];
        $name = $_ENV['DB_NAME'];
        $user = $_ENV['DB_USER'];
        $password = $_ENV['DB_PASSWORD'];
        $port = 5432;

        return new PDO("pgsql:host={$host};dbname={$name};port={$port}", $user, $password, [
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
    $req = $db->prepare("SELECT detection.*, detection.dateheure AS dateheure, detection.deletedAt AS deletedat, vehicule.*
                                FROM detection
                                         JOIN vehicule ON detection.vehicule_id = vehicule.id
                                WHERE vitesse > 20
                                  AND flash = true AND detection.deletedAt IS NULL
                                ORDER BY detection.dateheure DESC
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
    $req = $db->prepare("SELECT AVG(txdeconfiance) AS avg_tx FROM detection");
    $req->execute();
    $result = $req->fetch(PDO::FETCH_ASSOC);
    return $result['avg_tx'];
}

function getNbPassageDerniereHeure()
{
    $db = dbConnect();
    $req = $db->prepare("SELECT COUNT(*) AS total
                         FROM detection d
                         WHERE d.dateheure >= NOW() - INTERVAL '1 hour'");
    $req->execute();
    $result = $req->fetch(PDO::FETCH_ASSOC);
    return (int)($result['total'] ?? 0);

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

function suppression_globale($vehicule_id, $photo_id, $camera_id = 1)
{
    $db = dbConnect();

    try {
        $db->beginTransaction();

        $stmt = $db->prepare("
            UPDATE detection 
            SET deletedat = NOW() 
            WHERE camera_id = :camera_id 
              AND vehicule_id = :vehicule_id 
              AND photo_id = :photo_id 
              AND deletedat IS NULL
        ");
        $stmt->execute([
            'camera_id' => $camera_id,
            'vehicule_id' => $vehicule_id,
            'photo_id' => $photo_id,
        ]);

        $stmt = $db->prepare("
            UPDATE photo 
            SET deletedat = NOW() 
            WHERE id = :photo_id 
              AND deletedat IS NULL
        ");
        $stmt->execute(['photo_id' => $photo_id]);

        $stmt = $db->prepare("
            UPDATE vehicule 
            SET deletedat = NOW() 
            WHERE id = :vehicule_id 
              AND deletedat IS NULL
        ");
        $stmt->execute(['vehicule_id' => $vehicule_id]);

        $db->commit();
    } catch (Exception $e) {
        $db->rollBack();

        throw $e;
    }
}


function getDetectionFlash()
{
    $db = dbConnect();
    $req = $db->prepare("SELECT detection.*, detection.dateheure AS dateheure, detection.deletedAt AS deletedat, vehicule.*
                                FROM detection
                                         JOIN vehicule ON detection.vehicule_id = vehicule.id
                                WHERE vitesse > 20
                                  AND flash = true
                                ORDER BY detection.dateheure DESC;");
    $req->execute();
    $result = $req->fetchAll(PDO::FETCH_ASSOC);
    return $result;
}

function restoreAlerte($vehicule_id, $photo_id)
{
    try {
        $db = dbConnect();
        $req = $db->prepare(
            "UPDATE detection
             SET deletedat = NULL
             WHERE camera_id = 1
               AND vehicule_id = :vehicule_id
               AND photo_id = :photo_id"
        );
        $req->execute([
            ':vehicule_id' => $vehicule_id,
            ':photo_id' => $photo_id,
        ]);
        return $req->rowCount() > 0;
    } catch (PDOException $e) {
        error_log("Erreur restoreAlerte: " . $e->getMessage());
        return false;
    }
}

function getMdpByUsername($username)
{
    try {
        $db = dbConnect();
        $req = $db->prepare("SELECT hashmdp FROM admin WHERE name = :username");
        $req->execute(['username' => $username]);
        $result = $req->fetch(PDO::FETCH_ASSOC);
        return $result;
    } catch (PDOException $e) {
        error_log("Erreur getMdpByUsername: " . $e->getMessage());
        return null;
    }
}

function getDetectionByVehiculeAndPhoto($vehicule_id, $photo_id)
{
    try {
        $db = dbConnect();
        $req = $db->prepare("
            SELECT detection.*, detection.dateheure AS dateheure, detection.deletedAt AS deletedat
            FROM detection
            WHERE camera_id = 1
              AND vehicule_id = :vehicule_id
              AND photo_id = :photo_id
            LIMIT 1
        ");
        $req->execute([
            'vehicule_id' => $vehicule_id,
            'photo_id' => $photo_id,
        ]);
        return $req->fetch(PDO::FETCH_ASSOC) ?: null;
    } catch (PDOException $e) {
        error_log("Erreur getDetectionByVehiculeAndPhoto: " . $e->getMessage());
        return null;
    }
}

function updateAlertes($camera_id, $vehicule_id, $photo_id, $vitesse, $txDeConfiance, $deletedat)
{
    try {
        $db = dbConnect();
        $req = $db->prepare(
            "UPDATE detection 
            SET vitesse = :vitesse, 
                txdeconfiance = :txconfiance,
                deletedat = :deletedat
            WHERE camera_id   = :camera_id
              AND vehicule_id = :vehicule_id
              AND photo_id    = :photo_id"
        );
        $req->execute([
            ':vitesse' => $vitesse,
            ':txconfiance' => $txDeConfiance,
            ':deletedat' => $deletedat,
            ':camera_id' => $camera_id,
            ':vehicule_id' => $vehicule_id,
            ':photo_id' => $photo_id
        ]);
        return $req->rowCount() > 0;
    } catch (PDOException $e) {
        error_log("Erreur updateAlerte: " . $e->getMessage());
        return false;
    }
}
