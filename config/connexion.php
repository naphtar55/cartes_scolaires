<?php

$serveur = "localhost";
$utilisateur = "root";
$motdepasse = "";
$base = "cartes_scolaires";

$connexion = new mysqli(
    $serveur,
    $utilisateur,
    $motdepasse,
    $base
);

if ($connexion->connect_error) {
    die("Erreur de connexion à la base de données : " . $connexion->connect_error);
}

$connexion->set_charset("utf8mb4");

?>