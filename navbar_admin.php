<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="./style_composants.css">
    <title>Gestionnaire de Menus</title>
</head>
<body>
    <header class="header">
    <nav>
            <ul class="barre_de_nav">
                <li><a href=""><img src="./img/accueil.png" alt="accueil"></a></li>
                <li><a href="">Gestion des menus</a></li>
                <li><a href="">Gestion des plats</a></li>
                <li><a href="">Gestion des ingrédients</a></li>
                <form method="post">
                <button type="submit" name="deconnexion"><img src="./img/deconnexion-de-lutilisateur.png" alt="déconnexion"></button>
                </form>
                <?php 
                    if (isset($_POST['deconnexion'])) {
                        $_SESSION = array();
                        session_destroy();
                        header("Location:./index.php");
                    }
                 ?>
            </a></li>
            </ul>
        </nav>
    </header>
