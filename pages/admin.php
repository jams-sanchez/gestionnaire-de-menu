<?php
session_start();
$host = "localhost";
$username = "root";
$password = "";
try {
    $bdd  = new PDO("mysql:host=$host;dbname=gestion_de_menu;charset=utf8", $username, $password);
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}

// récupérarion des plats
$sql = "SELECT plat.id, plat.nom, plat.description, plat.prix, plat.image, categorie.nom AS categorie
FROM plat
INNER JOIN categorie ON plat.id_categorie = categorie.id";
$stmt = $bdd->prepare($sql);
$stmt->execute();
$plats = $stmt->fetchAll(PDO::FETCH_ASSOC);
var_dump($plats);

// récupérartion des menu avec les plats associé
$getMenu = "SELECT menu.nom AS menu, plat.nom AS plat, menu.id AS id
FROM menu 
JOIN plat_menu  ON menu.id = plat_menu.menu_id
JOIN plat  ON plat_menu.plat_id = plat.id
GROUP BY menu.nom";
$getAllMenu = $bdd->prepare($getMenu);
$getAllMenu->execute();
$menus = $getAllMenu->fetchAll(PDO::FETCH_ASSOC);
// DELETE d'un plat
if (isset($_GET['delete-plat'])) {
    $id = $_GET['delete-plat'];
    $deletePlat = "DELETE FROM plat WHERE id = $id";
    $delete = $bdd->prepare($deletePlat);
    $delete->execute();
};
// DELETE d'un menu
if (isset($_GET['delete-menu'])) {
    $id = $_GET['delete-menu'];
    $deletePlat = "DELETE FROM menu WHERE id = $id";
    $delete = $bdd->prepare($deletePlat);
    $delete->execute();
};


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

        <form action="create-plat.php" method="post">
            <input type="text" name="prix" placeholder="9.99€">
            <input type="text" name="nom" id="name" placeholder="Nom du Plat">
            <input type="text" name="description" id="description" placeholder="Description du plats"></input>
            <input type="submit" name="create" value="Valider">
        </form>
    </section>
    <section class="plat">
        <h2>Mes Plats</h2>
        <form action="create-plat.php" method="get">
            <input type="submit" value="ajouter un plat" name="create">
        </form>
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
    <section class="plat">
        <h2>Mes Menu</h2>
        <table>
            <thead>
                <tr>
                    <td>Nom Menu</td>
                    <td>Nom plats</td>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($menus as $menu) {
                    echo "<tr>
                    <td>$menu[menu]</td>
                    <td>$menu[plat]</td> 
                    <td>
                        <form action=\"admin.php\" method=\"get\">
                            <label for=\"delete-menu\">Supprimer</label>
                            <input type=\"submit\" name=\"delete-menu\" value=$menu[id] id=\"delete-menu\">
                        </form>
                    </td>
                    <td>
                        <form action=\"update-menu.php\" method=\"get\">
                            <label for=\"update\">Modifier</label>
                            <input type=\"submit\" name=\"update\" value=$menu[id] id=\"update\">
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