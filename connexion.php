<?php
// include 'navbar_user.php';


session_start();
$bdd = new PDO(
    'mysql:host=localhost;dbname=gestionnaire_de_menus','root',''
);

if(isset($_POST['submit'])){
if(!empty($_POST['email']) && !empty($_POST['password'])){
    $email = htmlentities($_POST['email']);
    $password = htmlentities($_POST['password']);    
    $req = $bdd->prepare("SELECT * FROM utilisateur WHERE mail = '$email' AND password = '$password'");
    $req -> execute();
    $req = $req->fetchAll();   
    
    if(empty($req)){
        echo '<p class="alert">Email ou mot de passe incorrect !</p>';
    }
    else{
        header("location:admin.php");
    }

}else{
    echo '<p class="alert">Veuillez remplir tous les champs</p>';
}
}



?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./assets/css/connexion.css">
    <title>Gestionnaire de menus</title>
</head>
<body>
<h1 class="titre">Connexion</h1>

<section class="bloc">
    <form method="post" action="" class="form">
    <input class="input" type="email" name="email" id="email" value="" placeholder="Entrez votre email" required><br/><br/>
    <input class="input" type="password" name="password" id="password" value="" placeholder="Entrez votre mot de passe" required><br/><br/>
    <button type="submit" name="submit" class="bouton">Valider</button>
    </form>
</section>
</body>
</html>