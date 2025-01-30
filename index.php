<?php

try {
    $bdd = new PDO('mysql:host=localhost;dbname=gestionnaire_de_menu', "root", "");
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}

// requete pour afficher la liste des menus

$listMenuStmt = $bdd->prepare("
SELECT menu.nom AS menu, menu.prix, plat.nom 
FROM menu 
JOIN plat_menu ON plat_menu.menu_id = menu.id 
JOIN plat ON plat_menu.plat_id = plat.id
");
$listMenuStmt->execute();
$listMenu = $listMenuStmt->fetchAll(PDO::FETCH_ASSOC);

$menus = [];
foreach ($listMenu as $value) {
    $menus_nom = $value['menu'];
    $menuPrix = $value['prix'];
    if (!isset($menus[$menus_nom . "\n" . $menuPrix])) {
        $menus[$menus_nom . "\n" . $menuPrix] = [];
    }
    $menus[$menus_nom . "\n" . $menuPrix][] = $value['nom'];
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
<main>

    <h2 class="titre-index">Les Menus</h2>

    <section class="boite">

        <?php foreach ($menus as $key => $value):  ?>

            <div class="card-menu">
                <h3><?= $key . "€"; ?></h3>
                <?php foreach ($value as $plat):  ?>

                    <p><?= $plat ?></p>
                <?php endforeach; ?>

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

</main>
<!-- footer -->
<?php include './composents/footer.php'; ?>