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

// Lorsque le bouton connexion est appuyé
if (isset($_POST['connexion'])) {
    // je vérifie que le champ mail et password ne sont pas vide
    if (empty($_POST['mail']) || empty($_POST['password'])) {
        echo "Veuillez rentrer votre adresse Mail et/ou votre mot de passe !";
    } else {
        // je "netoye" les paramettre donné par l'utilisateur
        $mail = htmlentities($_POST['mail']);
        $password = htmlentities($_POST['password']);
        // préparation de la requête SQL
        $sql = "SELECT * 
        FROM utilisateur
        WHERE mail = '$mail' AND password = '$password'";
        $stmt = $bdd->prepare($sql);
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        // je vérifie si l'utilisateur existe 
        if (empty($users)) {
            echo "Mot de passe ou identifiant incorect !";
            // si utilisateur trouvé je redirige vers la page administrateur
        } else {
            $_SESSION['mail'] = $users[0]['mail'];
            header("Location: http://localhost/gestionnaire-de-menu/pages/admin.php");
        }
    }
};

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="./style.css">
</head>

<body>
    <form action="index.php" method="post">
        <input type="text" name="mail">
        <input type="text" name="password">
        <input type="submit" name="connexion" value="Se Connecter">
    </form>

</body>

</html>