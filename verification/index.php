<?php

require_once __DIR__ . "/../config/connexion.php";

if (!isset($_GET['token']) || empty($_GET['token'])) {
    die("Token manquant.");
}

$token = $_GET['token'];

$sql = "
    SELECT 
        e.*,
        et.nom AS nom_etablissement,
        et.adresse,
        et.telephone,
        et.annee_scolaire
    FROM eleves e
    LEFT JOIN etablissements et 
        ON e.id_etablissement = et.id
    WHERE e.qr_token = ?
    LIMIT 1
";

$stmt = $connexion->prepare($sql);
$stmt->bind_param("s", $token);
$stmt->execute();

$resultat = $stmt->get_result();

if ($resultat->num_rows === 0) {
    die("Carte scolaire invalide.");
}

$eleve = $resultat->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="fr">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Vérification de carte scolaire</title>

    <style>

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f6f9;
        }

        .conteneur {
            max-width: 500px;
            margin: 40px auto;
            padding: 20px;
        }

        .carte {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.12);
            text-align: center;
        }

        .valide {
            color: #16803c;
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
        }

        .photo {
            width: 130px;
            height: 160px;
            object-fit: cover;
            border-radius: 8px;
            border: 3px solid #ddd;
        }

        .nom {
            font-size: 23px;
            font-weight: bold;
            margin-top: 15px;
        }

        .information {
            text-align: left;
            margin-top: 20px;
        }

        .ligne {
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }

        .label {
            font-weight: bold;
        }

        .securite {
            margin-top: 20px;
            font-size: 13px;
            color: #777;
        }

    </style>

</head>

<body>

<div class="conteneur">

    <div class="carte">

        <div class="valide">
            ✓ CARTE SCOLAIRE VALIDE
        </div>

        <?php
         if(!empty($eleve['photo'])): ?>
          
            <img
                src="/cartes_scolaires/ uploads/eleves/<?php 
                echo htmlspecialchars(basename($eleve['photo'])); ?>"
                alt="photo de l'eleve"
                class= "photo"
            >
            <?php else: ?>
                <p>photo non disponible</p>

        <?php endif; ?>

        <div class="nom">
            <?php echo htmlspecialchars($eleve['nom'] . " " . $eleve['prenom']); ?>
        </div>

        <div class="information">

            <div class="ligne">
                <span class="label">Matricule :</span>
                <?php echo htmlspecialchars($eleve['matricule']); ?>
            </div>

            <div class="ligne">
                <span class="label">Classe :</span>
                <?php echo htmlspecialchars($eleve['classe']); ?>
            </div>

            <div class="ligne">
                <span class="label">Établissement :</span>
                <?php echo htmlspecialchars($eleve['nom_etablissement']); ?>
            </div>

            <div class="ligne">
                <span class="label">Année scolaire :</span>
                <?php echo htmlspecialchars($eleve['annee_scolaire']); ?>
            </div>

        </div>

        <div class="securite">
            Cette carte a été vérifiée à partir d'un identifiant
            numérique unique.
        </div>

    </div>

</div>

</body>

</html>
