<?php
session_start();
$host = "localhost";
$username = "root";
$password = "";

// CONNEXION à la base de donnée
try {
    $bdd  = new PDO("mysql:host=$host;dbname=gestionnaire_de_menu;charset=utf8", $username, $password);
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}

//CREATE plat
if (isset($_POST['create'])) {
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
ORDER BY CASE 
WHEN categorie = 'Entrée' THEN 1
WHEN categorie = 'Plat' THEN 2
ELSE 3 END
";
$stmt = $bdd->prepare($sql);
$stmt->execute();
$plats = $stmt->fetchAll(PDO::FETCH_ASSOC);

// DELETE plat
if (isset($_GET['delete-plat'])) {
    var_dump($_GET['delete-plat']);
    $id = $_GET['delete-plat'];
    $deletePlat = "DELETE FROM plat WHERE id = $id";
    $delete = $bdd->prepare($deletePlat);
    $delete->execute();
    header("location: gestionPlat.php");
};

//UPDATE plat
// Récupération des informations du plat sélectionné
if (isset($_GET['update']) && !empty($_GET['update'])) {
    $_SESSION['id'] = $_GET['update'];
    $id = $_GET['update'];
    $sql = "SELECT * FROM plat WHERE id = $_SESSION[id]";
    $getAll = $bdd->prepare($sql);
    $getAll->execute();
    $result = $getAll->fetchAll(PDO::FETCH_ASSOC);
}
// Modification des information du plat sélectionné
if (isset($_POST['valid'])) {
    if (empty($_POST['nom'])) {
    }
    $id =  $_SESSION['id'];
    $image = htmlspecialchars($_POST['image']);
    $nom = htmlspecialchars($_POST['nom']);
    $descript = htmlspecialchars($_POST['description']);
    $prix = htmlspecialchars($_POST['prix']);
    $categorie = htmlspecialchars($_POST['categorie']);
    $sqlUpdate = "UPDATE plat SET image = :image, nom = :nom, description = :description, prix = :prix, id_categorie = :categorie WHERE id = :id";
    $update = $bdd->prepare($sqlUpdate);
    $update->execute([
        ':image' => $image,
        ':nom' => $nom,
        ':description' => $descript,
        ':prix' => $prix,
        ':categorie' => $categorie,
        ':id' => $id
    ]);
    $sql = "SELECT * FROM plat WHERE id = $_SESSION[id]";
    $getAll = $bdd->prepare($sql);
    $getAll->execute();
    $result = $getAll->fetchAll(PDO::FETCH_ASSOC);
    header("location:gestionPlat.php");
}

?>
<!-- Insertion du header et navBar -->
<?php include "../composents/navbar_admin.php" ?>

<!-- le code de la page Gestion Plat -->
<main class="gestion-plat">
    <section class="food-management">
        <section class="add-food">
            <h1 class="title">Gestion des Plats</h1>
            <?php if (isset($_GET['update'])): ?>
                <h2 class="sub-title">Modifier les informations du plat sélectionné</h2>
                <?php foreach ($result as $value) : ?>
                    <form class="food-form" action="gestionPlat.php" method="post">
                        <input class="food-form-item" type="text" name="image" placeholder="votre image" value=<?= $value['image'] ?>>
                        <input class="food-form-item" type="text" name="nom" id="name" placeholder="<?= $value['nom'] ?>" value="<?= $value['nom'] ?>">
                        <input class="food-form-item" type="text" name="description" id="description" placeholder="<?= $value['description'] ?>" value="<?= $value['description'] ?>">
                        <input class="food-form-item" type="text" name="prix" placeholder="<?= $value['prix'] ?>" value="<?= $value['prix'] ?>">
                        <select class="food-form-item" name="categorie" id="">
                            <option value=<?= $value['id_categorie'] ?>>--Choisir la catégorie--</option>
                            <option value="1">Entrée</option>
                            <option value="2">Plat</option>
                            <option value="3">Dessert</option>
                        </select>
                        <input class="food-form-submit" type="submit" name="valid" id="Valider">
                    </form>
                <?php endforeach ?>
            <?php else: ?>
                <h3 class="sub-title">Ajouter un plat</h3>
                <form class="food-form" action="gestionPlat.php" method="post">
                    <input class="food-form-item" type="text" name="image" placeholder="votre image">
                    <input class="food-form-item" type="text" name="nom" id="name" placeholder="Nom du Plat">
                    <input class="food-form-item" type="text" name="description" id="description" placeholder="Description du plats">
                    <input class="food-form-item" type="text" name="prix" placeholder="9.99€">
                    <select class="food-form-item" name="categorie" id="">
                        <option value="">--Choisir la catégorie--</option>
                        <option value="1">Entrée</option>
                        <option value="2">Plat</option>
                        <option value="3">Dessert</option>
                    </select>
                    <input class="food-form-submit" type="submit" name="create" value="Valider">
                </form>
            <?php endif ?>
        </section>
        <section class="plat">
            <h2 class="sub-title">Mes Plats</h2>
            <table class="table-food">
                <thead class="table-head">
                    <tr class="table-head-line">
                        <td class="food-item-title">Image</td>
                        <td class="food-item-title">Nom</td>
                        <td class="food-item-title">description</td>
                        <td class="food-item-title">prix</td>
                        <td class="food-item-title">categorie</td>
                        <td class="food-item-title"></td>
                        <td class="food-item-title"></td>
                    </tr>
                </thead>
                <tbody class="table-body">
                    <?php
                    foreach ($plats as $plat): ?>
                        <tr class="table-line">
                            <td class="food-item"><img class="food-present" src=".<?= $plat['image'] ?>" alt=<?= $plat['nom'] ?>></td>
                            <td class="food-item"><?= $plat['nom'] ?></td>
                            <td class="food-item"><?= $plat['description'] ?></td>
                            <td class="food-item small"><?= $plat['prix'] ?> €</td>
                            <td class="food-item small"><?= $plat['categorie'] ?></td>
                            <td class="food-item small">
                                <form action="" method="get">
                                    <button value=<?= $plat['id'] ?> name="delete-plat" class="food-form-submit">Supprimer</button>
                                </form>
                            </td>
                            <td class="food-item-small">
                                <form action="gestionPlat.php" method="get">
                                    <button value=<?= $plat['id'] ?> name="update" class="food-form-submit"> Modifier </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                </tbody>
            </table>
        </section>
    </section>
</main>
<!-- insertion du footer -->
<?php include "../composents/footer_admin.php" ?>