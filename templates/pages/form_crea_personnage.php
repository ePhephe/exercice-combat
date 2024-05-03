<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer votre personnage et joignez-vous au combot !</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <main class="personnage flex align-center justify-center direction-column">
        <h1><span class="titre">Combot</span><br><br> Création de votre personnage</h1>
        <a class="btn-back" href="index.php">retour</a>
        <form class="creation-personnage flex direction-column gap20" action="creer_personnage.php" method="post" id="creationPersonnage">
            <div>
                <label for="pseudo">Nom du personnage :</label>
                <input class="w300px" type="text" name="pseudo" id="pseudo" minlength="3" maxlength="50" <?php if(isSet($_POST["pseudo"])) { echo "value=\"".$_POST["pseudo"]."\""; } ?>>
                <p class="erreur d-none" id="pseudo-erreur"></p>
            </div>
            <div class="flex gap20">
                <div>
                    <label for="password">Mot de passe : </label>
                    <input type="password" name="password" id="password" minlength="6" maxlength="20" <?php if(isSet($_POST["password"])) { echo "value=\"".$_POST["password"]."\""; } ?>>
                    <p class="erreur d-none" id="password-erreur"></p>
                </div>
                <div>
                    <label for="confirmPassword">Confirmation du mot de passe : </label>
                    <input type="password" name="confirmPassword" id="confirmPassword" minlength="6" maxlength="20" <?php if(isSet($_POST["confirmPassword"])) { echo "value=\"".$_POST["confirmPassword"]."\""; } ?>>
                    <p class="erreur d-none" id="confirmPassword-erreur"></p>
                </div>
            </div>
            <fieldset class="caracteristiques flex justify-around">
                <legend>vos caracteristiques</legend>
                <p>Vous avez 15 points à répartir dans les caractéristiques ci-dessous : vous ne pouvez pas mettre moins de 3 à une caractéristiques ni plus de 10.</p>
                <div class="flex direction-column align-center w30">
                    <label for="pointForce">Force (FOR)</label>
                    <p class="explication">Utiliser pour les attaques sur un adversaire</p>
                    <input type="number" name="pointForce" id="pointForce" min="3" max="10" step="1" placeholder="3" <?php if(isSet($_POST["pointForce"])) { echo "value=\"".$_POST["pointForce"]."\""; } ?>>
                    <p class="erreur d-none" id="pointForce-erreur"></p>
                </div>
                <div class="flex direction-column align-center w30">
                    <label for="pointResistance">Résistance (RES)</label>
                    <p class="explication">Utiliser pour la défense contre les attaques</p>
                    <input type="number" name="pointResistance" id="pointResistance" min="3" max="10" step="1" placeholder="3"  <?php if(isSet($_POST["pointResistance"])) { echo "value=\"".$_POST["pointResistance"]."\""; } ?>>
                    <p class="erreur d-none" id="pointResistance-erreur"></p>
                </div>
                <div class="flex direction-column align-center w30">
                    <label for="pointAgilite">Agilité (AGI)</label>
                    <p class="explication">Utiliser pour les déplacements et les esquives</p>
                    <input type="number" name="pointAgilite" id="pointAgilite" min="3" max="10" step="1" placeholder="3"  <?php if(isSet($_POST["pointAgilite"])) { echo "value=\"".$_POST["pointAgilite"]."\""; } ?>>
                    <p class="erreur d-none" id="pointAgilite-erreur"></p>
                </div>
            </fieldset>
            <p class="erreur d-none" id="caracteristiques-erreur"></p>
            <input type="submit" value="En avant !">
        </form>
        <?php
            if(isSet($strErreur)) {
        ?>
        <div class="erreur" id="erreur-form">
            <?= $strErreur ?>
        </div>
        <?php
            }
        ?>
    </main>

    <script src="js/app.js"></script>
    <script src="js/creation_personnage.js"></script>
</body>
</html>