<?php
session_start();
$host = "localhost";
$username = "root";
$password = "";

// CONNEXION à la base de donnée
try {
    $bdd  = new PDO("mysql:host=$host;dbname=gestion_de_menu;charset=utf8", $username, $password);
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}

//CREATE plat
if (isset($_POST['create'])) {
    var_dump($_POST);
    if (empty($_POST['nom']) || empty($_POST['description'])  || empty($_POST['prix']) || empty($_POST['categorie']) || empty($_POST['image'])) {
        echo "<h3> Veuillez remplir tout les champs !</h3>";
    } else {
        $sql = 'INSERT INTO plat (image, nom, description, prix, id_categorie) VALUES (:image, :nom, :description, :prix, :id_categorie)';
        $create = $bdd->prepare($sql);
        $create->execute([
            ':image' => $_POST['image'],
            ':nom' => $_POST['nom'],
            ':description' => $_POST['description'],
            ':prix' => $_POST['prix'],
            ':id_categorie' => $_POST['categorie']
        ]);
    }
}

// GET ALL plats
$sql = "SELECT plat.id, plat.nom, plat.description, plat.prix, plat.image, categorie.nom AS categorie
FROM plat
INNER JOIN categorie ON plat.id_categorie = categorie.id
ORDER BY categorie DESC";
$stmt = $bdd->prepare($sql);
$stmt->execute();
$plats = $stmt->fetchAll(PDO::FETCH_ASSOC);

// DELETE plat
if (isset($_GET['delete-plat'])) {
    $id = $_GET['delete-plat'];
    $deletePlat = "DELETE FROM plat WHERE id = $id";
    $delete = $bdd->prepare($deletePlat);
    $delete->execute();
};

//UPDATE plat
if (isset($_GET['update']) && !empty($_GET['update'])) {
    $_SESSION['id'] = $_GET['update'];
    $id = $_GET['update'];
    $sql = "SELECT * FROM plat WHERE id = $_SESSION[id]";
    $getAll = $bdd->prepare($sql);
    $getAll->execute();
    $result = $getAll->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../style.css">
</head>

<body>
    <h1>Gestion des Plats</h1>
    <section class="addFood">
        <h3>Ajouter un plat</h3>
        <form action="admin.php" method="post">
            <input type="text" name="image" placeholder="votre image">
            <input type="text" name="nom" id="name" placeholder="Nom du Plat">
            <input type="text" name="description" id="description" placeholder="Description du plats">
            <input type="text" name="prix" placeholder="9.99€">
            <select name="categorie" id="">
                <option value="">--Choisir la catégorie--</option>
                <option value="1">Entrée</option>
                <option value="2">Plat</option>
                <option value="3">Dessert</option>
            </select>
            <input type="submit" name="create" value="Valider">
        </form>
    </section>
    <section class="plat">
        <h2>Mes Plats</h2>
        <table>
            <thead>
                <tr>
                    <td>Image</td>
                    <td>Nom</td>
                    <td>description</td>
                    <td>prix</td>
                    <td>categorie</td>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($plats as $plat) {
                    echo "<tr>
                    <td>$plat[image]</td>
                    <td>$plat[nom]</td>
                    <td>$plat[description]</td>
                    <td>$plat[prix] €</td>
                    <td>$plat[categorie]</td>
                    <td>
                        <form action=\"admin.php\" method=\"get\">
                            <label for=\"delete-plat\">Supprimer</label>
                            <input type=\"submit\" name=\"delete-plat\" value=$plat[id] id=\"delete-plat\">
                        </form>
                    </td>
                    <td>
                        <form action=\"update-plat.php\" method=\"get\">
                            <label for=\"update\">Modifier</label>
                            <input type=\"submit\" name=\"update\" value=$plat[id] id=\"update\">
                        </form
                    </td>
                </tr> 
            ";
                }
                ?>
            </tbody>
        </table>
    </section>

</body>

</html>