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


// supprimer un menu

if (isset($_POST['supprimer'])) {

    $valueID = $_POST['supprimer'];

    $suppStmt = $bdd->prepare('DELETE FROM menu WHERE id = :id');
    $suppStmt->execute([
        'id' => $valueID,
    ]);
}

?>

<?php include '../composents/navbar_admin.php'; ?>
<!-- main -->
<main>

    <h2 class="titre-ing">Ajouter un menu</h2>
    <form class="ajout-ing" action="" method="POST">
        <input type="text" name="nom" id="nom" placeholder="entrez un nom de menu" required>
        <!-- entrée -->
        <select type="text" name="entree" id="entree" required>
            <option>--Choisissez une entrée--</option>
            <?php foreach ($plats['Entrée'] as $key => $value): ?>
                <option value="<?= $key; ?>"><?= $value; ?></option>
            <?php endforeach; ?>
        </select>
        <!-- plat -->
        <select type="text" name="plat" id="plat" placeholder="entrez un plat" required>
            <option>--Choisissez un plat--</option>
            <?php foreach ($plats['Plat'] as $key => $value): ?>
                <option value="<?= $key; ?>"><?= $value; ?></option>
            <?php endforeach; ?>
        </select>
        <!-- dessert -->
        <select type="text" name="dessert" id="dessert" placeholder="entrez un dessert" required>
            <option>--Choisissez un dessert--</option>
            <?php foreach ($plats['Dessert'] as $key => $value): ?>
                <option value="<?= $key; ?>"><?= $value; ?></option>
            <?php endforeach; ?>
        </select>

        <input type="text" name="prix" placeholder="entrez un prix" required>

        <button name="ajouter">+ Ajouter</button>

        <?php

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
                    echo "Un menu identique existe déjà !";
                    header("refresh:1;url=menu-page.php");
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
                    $messageSucces = "Le menu a bien été créé ! ";
                    echo $messageSucces;
                    // actualise apres 1 secondes
                    header("refresh:1;url=menu-page.php");
                }
            }
        } ?>

    </form>


    <h2 class="titre-ing">Liste des menus</h2>

    <table class="tab-ing">
        <thead>
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
                    <td class="info-menu"> <?= $key ?></td>
                    <td class="info-menu"> <?= $value['Prix'] . " €"; ?> </td>
                    <td class="info-menu">
                        <?= $value['Entrée']['nom'] ?>
                    </td>
                    <td class="info-menu">
                        <?= $value['Plat']['nom'] ?>
                    </td>
                    <td class="info-menu">
                        <?= $value['Dessert']['nom'] ?>
                    </td>

                    <td class="info2-ing">
                        <form class="supp-ing" action="" method="post">
                            <button name="supprimer" value="<?= $value['ID'] ?>"> Supprimer </button>
                        </form>
                    </td>
                </tr>

            <?php endforeach; ?>

        </tbody>
    </table>

</main>
<!-- fin main -->
<?php include '../composents/footer_admin.php'; ?>