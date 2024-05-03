<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connectez-vous au Combot et traversez l'enfer</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <main class="connexion flex align-center justify-center direction-column">
        <h1><span class="titre">Combot</span><br><br> Connexion</h1>
        <a class="btn-back" href="index.php">retour</a>
        <form class="connexion flex direction-column" action="verifier_connexion.php" method="post" id="connexion">
            <div>
                <label for="pseudo">nom du personnage :</label>
                <input type="text" name="pseudo" id="pseudo" minlength="3" maxlength="50">
                <p class="erreur d-none" id="pseudo-erreur"></p>
            </div>
            <div>
                <label for="password">mot de passe : </label>
                <input type="password" name="password" id="password" minlength="6" maxlength="20">
                <p class="erreur d-none" id="password-erreur"></p>
            </div>
            <input type="submit" value="Au combat !">
        </form>
        <div class="erreur" id="erreur-form">

        </div>
        <?php
            if(isSet($success)) {
        ?>
        <div class="info">
                <?= $message ?>
        </div>
        <?php
            }
        ?>
    </main>
</body>
</html>