<?php
require '../config.php';
session_start();
$host = $_ENV['BDD_URL'];
$username = $_ENV['BDD_USERNAME'];
$password = $_ENV['BDD_PASSWORD'];

// CONNEXION à la base de donnée
try {
    $bdd  = new PDO("mysql:host=$host;dbname=gestionnaire_de_menu;charset=utf8", $username, $password);
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}

// requete pour afficher la liste des plats

$listStmt = $bdd->prepare("SELECT * FROM ingredient");
$listStmt->execute();
$listIngredient = $listStmt->fetchAll(PDO::FETCH_ASSOC);

// ajouter un plat 

$messageSucces = "";
$messageError = "";

if (isset($_POST["ajouter"])) {
    //verifie que les champs sont bien rempli
    if (empty($_POST["nom"])) {
        echo "Veuillez entrez un ingrédient.";
    } else {
        //recup les infos rempli
        $nom = htmlspecialchars($_POST["nom"]);
        // verifie que l'ingredient est unique
        $ingredientCheckStmt = $bdd->prepare('SELECT COUNT(*) FROM ingredient
        WHERE ingredient.nom = :nom');
        $ingredientCheckStmt->execute([
            'nom' => $nom
        ]);
        if ($ingredientCheckStmt->fetchColumn() > 0) {
            $messageError = "L'ingredient existe déjà ! ";
            header("refresh:1;url=ingredients-page.php");
        } else {
            // ajoute l'ingredient
            $platStmt = $bdd->prepare("INSERT INTO ingredient(nom) VALUES (:nom)");
            $platStmt->execute([
                'nom' => $nom,
            ]);
            // defini un message de succes
            $messageSucces = "L'ingrédient a bien été ajouté ! ";
            // actualise apres 2 secondes
            header("refresh:1;url=ingredients-page.php");
        }
    }
}

// supprimer un plat

if (isset($_POST['supprimer'])) {

    $valueID = $_POST['supprimer'];

    $suppStmt = $bdd->prepare('DELETE FROM ingredient WHERE id = :id');
    $suppStmt->execute([
        'id' => $valueID,
    ]);
    header("location: ingredients-page.php");
}

?>

<?php include '../composents/navbar_admin.php'; ?>
<!-- main -->
<main class="gestion-plat">
    <section class="food-management add-food">

        <form class="ajout-ing" action="" method="POST">
            <label for="titre">
                <h2 class="titre-ing">Ajouter un ingrédient</h2>
            </label>
            <input class="button-james" type="text" name="nom" id="nom" placeholder="entrez un nouvel ingredient" required>
            <button class="ajout-but" name="ajouter">+ Ajouter</button>

            <?php if (!empty($messageError)) : ?>
                <div class="error">
                    <p><?= $messageError; ?></p>
                </div>
            <?php else : ?>
                <div class="error">
                    <p><?= $messageSucces; ?></p>
                </div>
            <?php endif; ?>


        </form>

        <table class="tab-ing">
            <thead>
                <th class="titre-ing">Liste des ingrédients</th>
            </thead>
            <tbody>
                <?php

                foreach ($listIngredient as $value): ?>
                    <tr>
                        <td class="info-ing"> <?= $value['nom'] ?></td>
                        <td>
                            <form class="supp-ing" action="" method="post">
                                <button class="supp-but" name="supprimer" value="<?= $value['id'] ?>"> Supprimer </button>
                            </form>
                        </td>
                    </tr>

                <?php endforeach; ?>

            </tbody>
        </table>
    </section>
</main>

<?php include '../composents/footer_admin.php'; ?>