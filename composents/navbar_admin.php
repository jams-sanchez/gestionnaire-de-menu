<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionnaire de Menus</title>
    <link rel="stylesheet" href="../assets/css/style_composants.css">
    <link rel="stylesheet" href="../assets/css/stylesIngredients.css">
    <link rel="stylesheet" href="../assets/css/connexion.css">
    <link rel="stylesheet" href="../assets/css/gestionPlat.css">
    <link rel="stylesheet" href="../assets/css/stylesIndex.css">
</head>

<body>
    <header class="header">
        <nav>
            <ul class="barre_de_nav">
                <li><a href="../index.php"><img src="../assets/img/accueil.png" alt="accueil"></a></li>
                <!-- si pas connecter on ne propose pas les liens vers les pages admin -->
                <?php if (isset($_SESSION['user'])) : ?>
                    <li><a href="">Gestion des menus</a></li>
                    <li><a href="./gestionPlat.php">Gestion des plats</a></li>
                    <li><a href="./ingredients-page.php">Gestion des ingrédients</a></li>
                <?php endif ?>
                <form method="post">
                    <button type="submit" name="deconnexion"><img src="../assets/img/deconnexion-de-lutilisateur.png" alt="déconnexion"></button>
                </form>
                <?php
                if (isset($_POST['deconnexion'])) {
                    $_SESSION = array();
                    session_destroy();
                    header("Location:../index.php");
                }
                ?>
                </a></li>

            </ul>
        </nav>
    </header>