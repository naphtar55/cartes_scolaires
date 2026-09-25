<?php
require_once __DIR__ . "/config.php";

// --- CONFIG LOCALE (pour ton PC) ---
// $host = "localhost";
// $user = "root";
// $pass = "";
// $db   = "cartes_scolaires";

// --- CONFIG INFINITYFREE (à remplir après création de la DB) ---
$host = "sqlXXX.infinityfree.com"; // tu remplaceras par ton Host
$user = "if0_XXXXXXXX"; // tu remplaceras
$pass = "TON_MDP_INFINITY"; // tu remplaceras
$db   = "if0_XXXXXXXX_carte"; // tu remplaceras

$connexion = new mysqli($host, $user, $pass, $db);
if ($connexion->connect_error) {
    die("Erreur connexion DB : " . $connexion->connect_error);
}
$connexion->set_charset("utf8mb4");
?>