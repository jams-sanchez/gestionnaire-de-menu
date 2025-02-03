<?php
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

// requete pour afficher la liste des menus

$listMenuStmt = $bdd->prepare("
SELECT menu.nom AS menu, menu.prix, plat.nom AS plat, 
categorie.nom AS categorie, plat.image
FROM menu 
JOIN plat_menu ON plat_menu.menu_id = menu.id 
JOIN plat ON plat_menu.plat_id = plat.id 
JOIN categorie ON plat.id_categorie = categorie.id;
");
$listMenuStmt->execute();
$listMenu = $listMenuStmt->fetchAll(PDO::FETCH_ASSOC);

$menus = [];
foreach ($listMenu as $key => $value) {
    $menusNom = $value['menu'];
    $menuPrix = $value['prix'] . " €";
    $menuPlat = $value['plat'];
    $platImage = $value['image'];

    $menus[$menusNom]['Prix'] = $menuPrix;
    $menus[$menusNom][$value['categorie']] = ['nom' => $menuPlat, 'image' => $platImage];
}

// requete pour afficher la liste des plats

$listPlatStmt = $bdd->prepare("
SELECT plat.nom, plat.description, plat.prix, plat.image, categorie.nom AS categorie 
FROM plat 
INNER JOIN categorie ON plat.id_categorie = categorie.id;
");
$listPlatStmt->execute();
$listPlat = $listPlatStmt->fetchAll(PDO::FETCH_ASSOC);

?>
<?php include './composents/navbar_user.php'; ?>
<!-- main -->
<main class="gestion-plat">
    <section class="food-management add-food">

        <h2 class="titre-index">Les menus</h2>

        <section class="boite">

            <?php foreach ($menus as $key => $value):  ?>

                <div class="card-menu">
                    <h3 class="card-sub-title"><?= $key; ?></h3>

                    <div class="info-menu">
                        <?php if (isset($value['Entrée']['nom'])) : ?>
                            <img src="<?= $value['Entrée']['image']; ?>" />
                            <p><?= $value['Entrée']['nom']; ?></p>
                        <?php else:
                            echo "";
                        endif; ?>
                    </div>
                    <div class="info-menu">
                        <?php if (isset($value['Plat']['nom'])) : ?>
                            <img src="<?= $value['Plat']['image']; ?>" />
                            <p><?= $value['Plat']['nom']; ?></p>
                        <?php else:
                            echo "";
                        endif; ?>
                    </div>
                    <div class="info-menu">
                        <?php if (isset($value['Dessert']['nom'])) : ?>
                            <img src="<?= $value['Dessert']['image']; ?>" />
                            <p><?= $value['Dessert']['nom']; ?></p>
                        <?php else:
                            echo "";
                        endif; ?>
                    </div>

                    <div class="prix-menu">
                        <p>Prix : </p>
                        <p><?= $value['Prix']; ?></p>
                    </div>

                </div>

            <?php endforeach; ?>

        </section>

        <h2 class="titre-index">Les plats</h2>

        <section class="boite">

            <?php foreach ($listPlat as $value): ?>

                <div class="card-plat">
                    <div class="img-plat">
                        <!-- <img src="./assets/img/salade-sunny.jpg"/> -->
                        <img src="<?= $value['image']; ?>" />
                    </div>

                    <article class="card-desc">
                        <h3><?= $value['nom'] ?></h3>
                        <p><?= $value['description'] ?></p>
                        <div class="prix-cat">
                            <p><span class="bold">Prix:</span>
                                <?= $value['prix'] ?> €</p>
                            <p><span class="bold">Categorie:</span>
                                <?= $value['categorie'] ?></p>
                        </div>

                    </article>

                </div>

            <?php endforeach; ?>

        </section>
    </section>
</main>
<!-- footer -->
<?php include './composents/footer.php'; ?>