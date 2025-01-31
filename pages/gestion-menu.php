<?php
session_start();

try {
    $bdd = new PDO('mysql:host=localhost;dbname=gestionnaire_de_menu', "root", "");
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}

// requete pour récuperer toutes les infos des menus

$listMenuStmt = $bdd->prepare("
SELECT menu.nom AS menu, menu.prix, plat.nom AS plat, 
categorie.nom AS categorie, menu.id, plat.image
FROM menu 
JOIN plat_menu ON plat_menu.menu_id = menu.id 
JOIN plat ON plat_menu.plat_id = plat.id 
JOIN categorie ON plat.id_categorie = categorie.id;
");
$listMenuStmt->execute();
$listPlatsMenus = $listMenuStmt->fetchAll(PDO::FETCH_ASSOC);

$menus = [];
foreach ($listPlatsMenus as $key => $value) {
    $menusNom = $value['menu'];
    $menuPrix = $value['prix'];
    $menuPlat = $value['plat'];
    $platImage = $value['image'];
    $menuID = $value['id'];

    $menus[$menusNom]['Prix'] = $menuPrix;
    $menus[$menusNom]['ID'] = $menuID;
    $menus[$menusNom][$value['categorie']] = ['nom' => $menuPlat, 'image' => $platImage];
}

// requete pour recuperer la liste des plats 

$listPlatStmt = $bdd->prepare("SELECT plat.nom, plat.id, categorie.nom AS categorie 
FROM plat 
INNER JOIN categorie ON plat.id_categorie = categorie.id
");
$listPlatStmt->execute();
$listPlat = $listPlatStmt->fetchAll(PDO::FETCH_ASSOC);

$plats = [];
foreach ($listPlat as $key => $value) {
    $platCat = $value['categorie'];
    $platNom = $value['nom'];
    $plats[$platCat][$value['id']] = $platNom;
}

// ajouter un menu 

$messageSuccesAjout = "";
$messageErrorAjout = "";


if (isset($_POST["ajouter"])) {
    //verifie que les champs sont bien rempli
    if (
        empty($_POST['nom']) or empty($_POST['entree']) or empty($_POST['plat'])
        or empty($_POST['dessert']) or empty($_POST['prix'])
    ) {
        echo "Veuillez remplir tous les champs ! ";
    } else {
        //recup les infos rempli
        $nom = htmlspecialchars($_POST['nom']);
        $entree = $_POST['entree'];
        $plat = $_POST['plat'];
        $dessert = $_POST['dessert'];
        $prix = htmlspecialchars($_POST['prix']);

        // vérifie si un menu identique existe 
        $menuCheckStmt = $bdd->prepare("SELECT COUNT(*) FROM menu
                            JOIN plat_menu AS pmEntree ON menu.id = pmEntree.menu_id AND pmEntree.plat_id = :entree
                            JOIN plat_menu AS pmPlat ON menu.id = pmPlat.menu_id AND pmPlat.plat_id = :plat
                            JOIN plat_menu AS pmDessert ON menu.id = pmDessert.menu_id AND pmDessert.plat_id = :dessert
                            ");

        $menuCheckStmt->execute([
            'entree' => $entree,
            'plat' => $plat,
            'dessert' => $dessert
        ]);
        // si plus d'1 résultat
        if ($menuCheckStmt->fetchColumn() > 0) {
            // affiche message d'erreur
            $messageErrorAjout = "Un menu identique existe déjà !";
            header("refresh:1;url=gestion-menu.php");
        } else {

            // sinon créer le menu
            $menuAjoutStmt = $bdd->prepare("INSERT INTO menu(nom, prix) VALUES (:nom, :prix)");
            $menuAjoutStmt->execute([
                'nom' => $nom,
                'prix' => $prix
            ]);

            $menuId = $bdd->lastInsertId(); // Récupère l'ID du nouveau menu

            $platMenuAjoutStmt = $bdd->prepare("INSERT INTO plat_menu(menu_id, plat_id) VALUES (:menu_id, :plat_id)");
            $platMenuAjoutStmt->execute([
                'menu_id' => $menuId,
                'plat_id' => $entree
            ]);
            $platMenuAjoutStmt->execute([
                'menu_id' => $menuId,
                'plat_id' => $plat
            ]);
            $platMenuAjoutStmt->execute([
                'menu_id' => $menuId,
                'plat_id' => $dessert
            ]);
            // defini un message de succes
            $messageSuccesAjout = "Le menu a bien été créé ! ";
            // actualise apres 1 secondes
            header("refresh:1;url=gestion-menu.php");
        }
    }
}

// modifier un menu 

// requete pour récuperer toutes les infos du menu a modifier


if (isset($_POST['modifier'])) {
    $id = $_POST['modifier'];
    $_SESSION['idMenu'] = $id;

    $listMenuStmt = $bdd->prepare("
SELECT menu.nom AS menu, menu.prix, plat.nom AS plat, 
categorie.nom AS categorie, menu.id, plat.image, plat.id AS platID
FROM menu 
JOIN plat_menu ON plat_menu.menu_id = menu.id 
JOIN plat ON plat_menu.plat_id = plat.id 
JOIN categorie ON plat.id_categorie = categorie.id
WHERE menu.id = $id");
    $listMenuStmt->execute();
    $listPlatsMenus = $listMenuStmt->fetchAll(PDO::FETCH_ASSOC);

    $menuUpdate = [];
    foreach ($listPlatsMenus as $key => $value) {
        $menusNom = $value['menu'];
        $menuPrix = $value['prix'];
        $menuPlat = $value['plat'];
        $platImage = $value['image'];
        $menuID = $value['id'];
        $platID = $value['platID'];

        $menuUpdate[$menusNom]['Prix'] = $menuPrix;
        $menuUpdate[$menusNom]['ID'] = $menuID;
        $menuUpdate[$menusNom][$value['categorie']] = ['nom' => $menuPlat, 'image' => $platImage, 'platID' => $platID];
    }

    $nomMenuKey = key($menuUpdate);
}

// recuperation des infos menu modifier

$messageErrorModif = "";
$messageSuccesModif = "";

if (isset($_POST['validModif'])) {
    $idMenu = $_SESSION['idMenu'];
    $nomUpdate = htmlspecialchars($_POST['nomUpdate']);
    $entreeUpdate = ($_POST['entreeUpdate']);
    $platUpdate = ($_POST['platUpdate']);
    $dessertUpdate = ($_POST['dessertUpdate']);
    $prixUpdate = htmlspecialchars($_POST['prixUpdate']);

    // vérifie si un menu identique existe 
    $menuCheckStmt = $bdd->prepare("SELECT COUNT(*) FROM menu
    JOIN plat_menu AS pmEntree ON menu.id = pmEntree.menu_id AND pmEntree.plat_id = :entree
    JOIN plat_menu AS pmPlat ON menu.id = pmPlat.menu_id AND pmPlat.plat_id = :plat
    JOIN plat_menu AS pmDessert ON menu.id = pmDessert.menu_id AND pmDessert.plat_id = :dessert
    ");

    $menuCheckStmt->execute([
        'entree' => $entreeUpdate,
        'plat' => $platUpdate,
        'dessert' => $dessertUpdate
    ]);
    // si plus d'1 résultat
    if ($menuCheckStmt->fetchColumn() > 0) {
        // affiche message d'erreur
        $messageErrorModif = "Un menu identique existe déjà !";
        header("refresh:1;url=gestion-menu.php");
    } else {

        $menuUpdate = $bdd->prepare("UPDATE menu SET nom = :nom, prix = :prix 
        WHERE id = $idMenu");
        $menuUpdate->execute([
            ':nom' => $nomUpdate,
            ':prix' => $prixUpdate
        ]);
        $menuEntreeUpdate = $bdd->prepare("UPDATE plat_menu, plat SET plat_id = :idPlat
        WHERE menu_id = :idMenu AND plat_id IN (SELECT id FROM plat WHERE id_categorie = 1)");
        $menuEntreeUpdate->execute([
            ':idPlat' => $entreeUpdate,
            ':idMenu' => $idMenu
        ]);
        $menuPlatUpdate = $bdd->prepare("UPDATE plat_menu, plat SET plat_id = :idPlat, menu_id = :idMenu
        WHERE menu_id = :idMenu AND plat_id IN (SELECT id FROM plat WHERE id_categorie = 2)");
        $menuPlatUpdate->execute([
            ':idPlat' => $platUpdate,
            ':idMenu' => $idMenu
        ]);
        $menuDessertUpdate = $bdd->prepare("UPDATE plat_menu, plat SET plat_id = :idPlat, menu_id = :idMenu
        WHERE menu_id = :idMenu AND plat_id IN (SELECT id FROM plat WHERE id_categorie = 3)");
        $menuDessertUpdate->execute([
            ':idPlat' => $dessertUpdate,
            ':idMenu' => $idMenu
        ]);

        // defini un message de succes
        $messageSuccesModif = "Le menu a bien été modifié ! ";
        // actualise apres 1 secondes
        header("refresh:1;url=gestion-menu.php");
    }
}

// supprimer un menu

if (isset($_POST['supprimer'])) {

    $valueID = $_POST['supprimer'];

    $suppStmt = $bdd->prepare('DELETE FROM menu WHERE id = :id');
    $suppStmt->execute([
        'id' => $valueID,
    ]);

    header("location: gestion-menu.php");
}

?>

<?php include '../composents/navbar_admin.php'; ?>
<!-- main -->
<main class="gestion-plat">
    <section class="food-management add-food">


        <!-- modifier menu -->
        <?php if (isset($_POST['modifier'])) : ?>
            <h2 class="titre-ing">Modifier un menu</h2>

            <form class="ajout-ing" action="" method="POST">

                <input class="button-james" type="text" name="nomUpdate" id="nom" value="<?= key($menuUpdate) ?>" required>
                <!-- entrée -->
                <select class="button-james" type="text" name="entreeUpdate" id="entree" required>
                    <!-- Entrée du menu a modifier -->
                    <?php if (isset($menuUpdate[$nomMenuKey]['Entrée']['nom'])) : ?>
                        <option value="<?= $menuUpdate[$nomMenuKey]['Entrée']['platID'] ?>"><?= $menuUpdate[$nomMenuKey]['Entrée']['nom']; ?></option>
                    <?php else: ?>
                        <option> **** </option>
                    <?php endif; ?>
                    <!-- Liste des entrées -->
                    <?php foreach ($plats['Entrée'] as $key => $value): ?>
                        <?php if ($value != $menuUpdate[$nomMenuKey]['Entrée']['nom']): ?>
                            <option value="<?= $key; ?>"><?= $value; ?></option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
                <!-- plat -->
                <select class="button-james" type="text" name="platUpdate" id="plat" placeholder="entrez un plat" required>
                    <!-- plat du menu a modifier -->
                    <?php if (isset($menuUpdate[$nomMenuKey]['Plat']['nom'])) : ?>
                        <option value="<?= $menuUpdate[$nomMenuKey]['Plat']['platID'] ?>"><?= $menuUpdate[$nomMenuKey]['Plat']['nom']; ?></option>
                    <?php else: ?>
                        <option> **** </option>
                    <?php endif; ?>
                    <!-- Liste plat -->
                    <?php foreach ($plats['Plat'] as $key => $value): ?>
                        <?php if ($value != $menuUpdate[$nomMenuKey]['Plat']['nom']): ?>
                            <option value="<?= $key; ?>"><?= $value; ?></option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
                <!-- dessert -->
                <select class="button-james" type="text" name="dessertUpdate" id="dessert" placeholder="entrez un dessert" required>
                    <!-- dessert du menu a modifier -->
                    <?php if (isset($menuUpdate[$nomMenuKey]['Dessert']['nom'])) : ?>
                        <option value="<?= $menuUpdate[$nomMenuKey]['Dessert']['platID'] ?>"><?= $menuUpdate[$nomMenuKey]['Dessert']['nom']; ?></option>
                    <?php else: ?>
                        <option> **** </option>
                    <?php endif; ?>
                    <!-- liste dessert -->
                    <?php foreach ($plats['Dessert'] as $key => $value): ?>
                        <?php if ($value != $menuUpdate[$nomMenuKey]['Dessert']['nom']): ?>
                            <option value="<?= $key; ?>"><?= $value; ?></option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>

                <input class="button-james" type="text" name="prixUpdate" value="<?= $menuUpdate[$nomMenuKey]['Prix'] ?> €" required>

                <button class="ajout-but" name="validModif">Modifier</button>

                <?php if (!empty($messageErrorModif)) : ?>
                    <div class="error">
                        <p><?= $messageErrorModif; ?></p>
                    </div>
                <?php else : ?>
                    <div class="error">
                        <p><?= $messageSuccesModif; ?></p>
                    </div>
                <?php endif; ?>


            </form>
        <?php else : ?>

            <h2 class="titre-ing">Ajouter un menu</h2>

            <form class="ajout-ing" action="" method="POST">
                <input class="button-james" type="text" name="nom" id="nom" placeholder="entrez un nom de menu" required>
                <!-- entrée -->
                <select class="button-james" type="text" name="entree" id="entree" required>
                    <option>--Choisissez une entrée--</option>
                    <?php foreach ($plats['Entrée'] as $key => $value): ?>
                        <option value="<?= $key; ?>"><?= $value; ?></option>
                    <?php endforeach; ?>
                </select>
                <!-- plat -->
                <select class="button-james" type="text" name="plat" id="plat" placeholder="entrez un plat" required>
                    <option>--Choisissez un plat--</option>
                    <?php foreach ($plats['Plat'] as $key => $value): ?>
                        <option value="<?= $key; ?>"><?= $value; ?></option>
                    <?php endforeach; ?>
                </select>
                <!-- dessert -->
                <select class="button-james" type="text" name="dessert" id="dessert" placeholder="entrez un dessert" required>
                    <option>--Choisissez un dessert--</option>
                    <?php foreach ($plats['Dessert'] as $key => $value): ?>
                        <option value="<?= $key; ?>"><?= $value; ?></option>
                    <?php endforeach; ?>
                </select>

                <input class="button-james" type="text" name="prix" placeholder="entrez un prix" required>

                <button class="ajout-but" name="ajouter">+ Ajouter</button>

                <?php if (!empty($messageErrorAjout)) : ?>
                    <div class="error">
                        <p><?= $messageErrorAjout; ?></p>
                    </div>
                <?php else : ?>
                    <div class="error">
                        <p><?= $messageSuccesAjout; ?></p>
                    </div>
                <?php endif; ?>

            </form>


        <?php endif; ?>


        <h2 class="titre-ing">Liste des menus</h2>

        <table class="table-menu">
            <thead class="titre-tab">
                <th>Nom</th>
                <th>Prix</th>
                <th>Entrée</th>
                <th>Plat</th>
                <th>Dessert</th>

            </thead>
            <tbody>
                <?php

                foreach ($menus as $key => $value): ?>
                    <tr>
                        <td class="table-info-menu"> <?= $key ?></td>
                        <td class="table-info-menu"> <?= $value['Prix'] . " €"; ?> </td>

                        <td class="table-info-menu">
                            <?php if (isset($value['Entrée']['nom'])) : ?>
                                <?= $value['Entrée']['nom'] ?>
                            <?php else:
                                echo "";
                            endif; ?>
                        </td>
                        <td class="table-info-menu">
                            <?php if (isset($value['Plat']['nom'])) : ?>
                                <?= $value['Plat']['nom'] ?>
                            <?php else:
                                echo "";
                            endif; ?>
                        </td>
                        <td class="table-info-menu">
                            <?php if (isset($value['Dessert']['nom'])) : ?>
                                <?= $value['Dessert']['nom'] ?>
                            <?php else:
                                echo "";
                            endif; ?>
                        </td>

                        <td class="info2-ing">
                            <form class="supp-ing" action="" method="post">
                                <button class="supp-but" name="modifier" value="<?= $value['ID'] ?>"> Modifier </button>
                            </form>
                            <form class="supp-ing" action="" method="post">
                                <button class="supp-but" name="supprimer" value="<?= $value['ID'] ?>"> Supprimer </button>
                            </form>
                        </td>
                    </tr>

                <?php endforeach; ?>

            </tbody>
        </table>
    </section>
</main>
<!-- fin main -->
<?php include '../composents/footer_admin.php'; ?>