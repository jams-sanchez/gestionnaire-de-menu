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

<?php include '../composents/navbar_user.php'; ?>
<!-- main -->
<main>

    <form action="" method="POST">
        <label for="titre">Ingredient</label>
        <input type="text" name="nom" id="nom" placeholder="entrez un nouvel ingredient" required>
        <button name="ajouter">+ Ajouter un ingredient</button>

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

    <h2 class="titre-index">Liste des ingredients</h2>


    <table>
        <thead>
            <th>Nom</th>
        </thead>
        <tbody>
            <?php

            foreach ($listIngredient as $value): ?>
                <tr>
                    <td> <?= $value['nom'] ?></td>
                    <td>
                        <form action="" method="post">
                            <button name="supprimer" value="<?= $value['id'] ?>"> Supprimer </button>
                        </form>
                    </td>
                </tr>

            <?php endforeach; ?>

        </tbody>
    </table>

</main>

<?php include '../composents/footer.php'; ?>