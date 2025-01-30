<?php
$host = "localhost";
$username = "root";
$password = "";
try {
    $bdd  = new PDO("mysql:host=$host;dbname=gestion_de_menu;charset=utf8", $username, $password);
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
};

if (isset($_POST['create'])) {
    if (empty($_POST['nom']) || empty($_POST['description'])) {
        echo "<h3> Veuillez remplir tout les champs !</h3>";
    } else {
        $sql = 'INSERT INTO plat (nom, description) VALUES (:nom, :description)';
        $create = $bdd->prepare($sql);
        $create->execute([
            ':nom' => $_POST['nom'],
            ':description' => $_POST['description']
        ]);
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <p>retour à la page admin</p>
    <a href="admin.php"> <- </a>
            <h3>Ajouter un plat</h3>

            <form action="create-plat.php" method="post">
                <label for="name">Nom du Plat</label>
                <input type="text" name="nom" id="name">
                <label for="description">Description du plat</label>
                <textarea type="textarea" rows="4" colls="33" name="description" id="description"></textarea>
                <!-- <input type="text" name="description" id="dscription"> -->
                <input type="submit" name="create" value="Valider">
            </form>
</body>

</html>