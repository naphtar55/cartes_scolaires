<?php
require_once __DIR__ . "/../config/connexion.php";

$resultat = $connexion->query("SELECT id, nom FROM etablissements ORDER BY nom");
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un élève</title>
</head>

<body>

<h1>Ajouter un élève</h1>

<form action="enregistrer.php" method="POST" enctype="multipart/form-data">

    <label>Nom :</label><br>
    <input type="text" name="nom" required><br><br>

    <label>Prénom :</label><br>
    <input type="text" name="prenom" required><br><br>

    <label>Date de naissance :</label><br>
    <input type="date" name="date_naissance"><br><br>

    <label>Lieu de naissance :</label><br>
    <input type="text" name="lieu_naissance"><br><br>

    <label>Sexe :</label><br>
    <select name="sexe">
        <option value="M">Masculin</option>
        <option value="F">Féminin</option>
    </select><br><br>

    <label>Classe :</label><br>
    <input type="text" name="classe" required><br><br>

    <label>Photo :</label><br>
    <input type="file" name="photo" accept="image/*"><br><br>

    <label>Établissement :</label><br>
    <select name="id_etablissement" required>

        <?php while ($etablissement = $resultat->fetch_assoc()) { ?>

            <option value="<?= $etablissement['id'] ?>">
                <?= htmlspecialchars($etablissement['nom']) ?>
            </option>

        <?php } ?>

    </select>

    <br><br>

    <button type="submit">Enregistrer l'élève</button>

</form>

</body>
</html>