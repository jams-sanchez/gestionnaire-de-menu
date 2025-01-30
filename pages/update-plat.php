<?php
session_start();
$host = "localhost";
$username = "root";
$password = "";
$message = "";
try {
    $bdd  = new PDO("mysql:host=$host;dbname=gestion_de_menu;charset=utf8", $username, $password);
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
};
if (isset($_GET['update']) && !empty($_GET['update'])) {
    $_SESSION['id'] = $_GET['update'];
    $id = $_GET['update'];
    $sql = "SELECT * FROM plat WHERE id = $_SESSION[id]";
    $getAll = $bdd->prepare($sql);
    $getAll->execute();
    $result = $getAll->fetchAll(PDO::FETCH_ASSOC);
}
if (isset($_GET['valid'])) {
    if (empty($_GET['nom'])) {
    }
    $id =  $_SESSION['id'];
    $nom = htmlspecialchars($_GET['nom']);
    $descript = htmlspecialchars($_GET['description']);
    $sqlUpdate = "UPDATE plat SET nom = :nom, description = :description WHERE id = :id";
    $update = $bdd->prepare($sqlUpdate);
    $update->execute([
        ':nom' => $nom,
        ':description' => $descript,
        ':id' => $id
    ]);
    $sql = "SELECT * FROM plat WHERE id = $_SESSION[id]";
    $getAll = $bdd->prepare($sql);
    $getAll->execute();
    $result = $getAll->fetchAll(PDO::FETCH_ASSOC);
    $message = "Modification Réussi";
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <p>retour à la page admin</p>
    <a href="admin.php"> <- </a>
            <h1>Modifier les information du plat</h1>
            <form action="update-plat.php" method="get">
                <label for="name">Nom du Plat</label>
                <input type="text" name="nom" value="<?= $result[0]['nom'] ?>" id="name" placeholder="<?= $result[0]['nom'] ?>">
                <label for="description">Description du Plat</label>
                <textarea type="textarea" rows="4" colls="33" name="description" id="description"><?= $result[0]['description'] ?></textarea>
                <input type="submit" name="valid" id="Valider">
            </form>
            <h2><?= $message ?></h2>
</body>

</html>