<?php
// récupérartion des menu avec les plats associé
$getMenu = "SELECT menu.nom AS menu, plat.nom AS plat, menu.id AS id
FROM menu 
JOIN plat_menu  ON menu.id = plat_menu.menu_id
JOIN plat  ON plat_menu.plat_id = plat.id
GROUP BY menu.nom";
$getAllMenu = $bdd->prepare($getMenu);
$getAllMenu->execute();
$menus = $getAllMenu->fetchAll(PDO::FETCH_ASSOC);

// DELETE d'un menu
if (isset($_GET['delete-menu'])) {
    $id = $_GET['delete-menu'];
    $deletePlat = "DELETE FROM menu WHERE id = $id";
    $delete = $bdd->prepare($deletePlat);
    $delete->execute();
};
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>

    <section class="plat">
        <h2>Mes Menu</h2>
        <table>
            <thead>
                <tr>
                    <td>Nom Menu</td>
                    <td>Nom plats</td>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($menus as $menu) {
                    echo "<tr>
                    <td>$menu[menu]</td>
                    <td>$menu[plat]</td> 
                    <td>
                        <form action=\"admin.php\" method=\"get\">
                            <label for=\"delete-menu\">Supprimer</label>
                            <input type=\"submit\" name=\"delete-menu\" value=$menu[id] id=\"delete-menu\">
                        </form>
                    </td>
                    <td>
                        <form action=\"update-menu.php\" method=\"get\">
                            <label for=\"update\">Modifier</label>
                            <input type=\"submit\" name=\"update\" value=$menu[id] id=\"update\">
                        </form
                    </td> 
                </tr> 
            ";
                }
                ?>
            </tbody>
        </table>
    </section>
</body>

</html>