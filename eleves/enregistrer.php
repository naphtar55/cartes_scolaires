<?php
require_once __DIR__ . "/../config/connexion.php";

$nom = trim($_POST['nom'] ?? '');
$prenom = trim($_POST['prenom'] ?? '');
$date_naissance = $_POST['date_naissance'] ?? null;
if ($date_naissance === '') $date_naissance = null;
$lieu_naissance = trim($_POST['lieu_naissance'] ?? '');
$sexe = $_POST['sexe'] ?? 'M';
$classe = trim($_POST['classe'] ?? '');
$id_etablissement = (int) ($_POST['id_etablissement'] ?? 0);

if ($nom === '' || $prenom === '' || $classe === '' || $id_etablissement === 0) {
    die("Champs obligatoires manquants.");
}

// Génération du matricule
$annee = date("Y");
$resultat = $connexion->query("SELECT COUNT(*) AS total FROM eleves");
if (!$resultat) {
    die("Erreur COUNT : " . $connexion->error);
}
$ligne = $resultat->fetch_assoc();
$numero = $ligne['total'] + 1;
$matricule = "ELE-" . $annee . "-" . str_pad($numero, 4, "0", STR_PAD_LEFT);

// Génération du token QR
$qr_token = bin2hex(random_bytes(32));

// Gestion de la photo
$photo = null;

if (isset($_FILES['photo']) && $_FILES['photo']['error'] === 0) {

    $dossier = __DIR__ . "/../uploads/eleves/";

    if (!is_dir($dossier)) {
        mkdir($dossier, 0777, true);
    }

    $extension = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
    $extensions_autorisees = ['jpg', 'jpeg', 'png', 'webp'];

    if (!in_array($extension, $extensions_autorisees)) {
        die("Format de photo non autorisé. Autorisés : jpg, jpeg, png, webp");
    }

    // Limite 3Mo
    if ($_FILES['photo']['size'] > 3 * 1024 * 1024) {
        die("Photo trop lourde (max 3Mo)");
    }

    $nom_photo = $qr_token . "." . $extension;
    $chemin_complet = $dossier . $nom_photo;

    if (!move_uploaded_file($_FILES['photo']['tmp_name'], $chemin_complet)) {
        die("Erreur : impossible de déplacer la photo. Vérifie les permissions du dossier uploads/eleves/");
    }

    $photo = $nom_photo;
}

// Enregistrement
$sql = "INSERT INTO eleves
(matricule, nom, prenom, date_naissance, lieu_naissance, sexe, classe, photo, qr_token, id_etablissement)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $connexion->prepare($sql);

if (!$stmt) {
    die("Erreur prepare : " . $connexion->error);
}

$stmt->bind_param(
    "sssssssssi",
    $matricule,
    $nom,
    $prenom,
    $date_naissance,
    $lieu_naissance,
    $sexe,
    $classe,
    $photo,
    $qr_token,
    $id_etablissement
);

if ($stmt->execute()) {
    echo "<h2>Élève enregistré avec succès !</h2>";
    echo "<p>Matricule : <strong>" . htmlspecialchars($matricule) . "</strong></p>";
    if ($photo) {
        echo "<p>Photo : $photo</p>";
        echo "<img src='../uploads/eleves/" . htmlspecialchars($photo) . "' width='150' style='border-radius:8px'><br><br>";
    } else {
        echo "<p>Aucune photo envoyée.</p>";
    }
    echo '<a href="ajouter.php">Ajouter un autre élève</a> | <a href="../index.php">Accueil</a>';
} else {
    echo "Erreur SQL : " . $stmt->error;
}

$stmt->close();
$connexion->close();
?>