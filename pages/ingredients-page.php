<?php
session_start();

try {
    $bdd = new PDO('mysql:host=localhost;dbname=gestionnaire_de_menu', "root", "");
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}

// requete pour afficher la liste des plats

$listStmt = $bdd->prepare("SELECT * FROM ingredient");
$listStmt->execute();
$listIngredient = $listStmt->fetchAll(PDO::FETCH_ASSOC);


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
<main>

    <form class="ajout-ing" action="" method="POST">
        <label for="titre">
            <h2 class="titre-ing">Ajouter un ingrédient</h2>
        </label>
        <input type="text" name="nom" id="nom" placeholder="entrez un nouvel ingredient" required>
        <button name="ajouter">+ Ajouter</button>

        <?php

        if (isset($_POST["ajouter"])) {
            //verifie que les champs sont bien rempli
            if (empty($_POST["nom"])) {
                echo "Veuillez entrez un ingrédient.";
            } else {
                //recup les infos rempli
                $nom = htmlspecialchars($_POST["nom"]);
                $platStmt = $bdd->prepare("INSERT INTO ingredient(nom) VALUES (:nom)");
                $platStmt->execute([
                    'nom' => $nom,
                ]);
                // defini un message de succes
                $messageSucces = "L'ingrédient a bien été ajouté ! ";
                echo $messageSucces;
                // actualise apres 2 secondes
                header("refresh:1;url=ingredients-page.php");
            }
        } ?>

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
                    <td class="info2-ing">
                        <form class="supp-ing" action="" method="post">
                            <button class="supp-but" name="supprimer" value="<?= $value['id'] ?>"> Supprimer </button>
                        </form>
                    </td>
                </tr>

            <?php endforeach; ?>

        </tbody>
    </table>

</main>

<?php include '../composents/footer_admin.php'; ?>