<?php

require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . "/../config/connexion.php";

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

if (!isset($_GET['id'])) {
    die("Élève introuvable.");
}

$id = (int) $_GET['id'];

$stmt = $connexion->prepare(
    "SELECT id, qr_token FROM eleves WHERE id = ?"
);

$stmt->bind_param("i", $id);
$stmt->execute();

$resultat = $stmt->get_result();
$eleve = $resultat->fetch_assoc();

if (!$eleve) {
    die("Élève introuvable.");
}

$token = $eleve['qr_token'];

$url_verification =
    "http://localhost/cartes_scolaires/verification/?token="
    . urlencode($token);


// Dossier de sauvegarde
$dossier = __DIR__ . "/../uploads/qr/";

if (!is_dir($dossier)) {
    mkdir($dossier, 0777, true);
}


// Nom du fichier
$nom_fichier = "eleve_" . $eleve['id'] . ".png";

$chemin = $dossier . $nom_fichier;


// Création du QR
$qrCode = new QrCode(
    data: $url_verification,
    size: 500,
    margin: 10
);


// Création de l'image PNG
$writer = new PngWriter();

$resultat_qr = $writer->write($qrCode);


// Sauvegarde
$resultat_qr->saveToFile($chemin);


echo "<h2>QR code généré avec succès !</h2>";

echo "<p>Élève ID : " . htmlspecialchars($eleve['id']) . "</p>";

echo "<p>URL de vérification :</p>";

echo "<p>" . htmlspecialchars($url_verification) . "</p>";

echo "<br>";

echo '<img src="../uploads/qr/'
    . htmlspecialchars($nom_fichier)
    . '" width="300">';

?>