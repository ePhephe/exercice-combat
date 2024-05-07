<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $objPersonnage->get("piece_actuelle")->get("nom") ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <main class="etage<?= $objPersonnage->get("piece_actuelle")->get("numero") ?> flex align-center direction-column">
        <h1 class="ingame flex justify-center align-center"><?= $objPersonnage->get("piece_actuelle")->get("nom") ?> (<?= $objPersonnage->get("piece_actuelle")->get("numero") ?>)</h1>
        <a class="btn-deconnexion" href="deconnecter.php"><img src="img/icon-logout.png" alt="Icone de déconnexion"></a>
        <section class="personnage flex justify-center">
            <?php if($objPersonnage->get("piece_actuelle")->get("is_entree")==="N") { ?>
            <div class="deplacement flex justify-center align-center"><a href="action_deplacement.php?sens=REC">reculer</a></div>
            <?php } ?>
            <div class="personnage flex justify-center align-center direction-column gap20">
                <img class="personnage" src="img/sprite-personnage.png" alt="Image du personnage">
                <div id="actionTransformer" class="flex gap20">
                    <a class="flex align-center gap20" href="action_transformer.php?carac=RES">for <img src="img/transform_icon.png" alt="Icone d'échange de point"> res</a> 
                    <a class="flex align-center gap20" href="action_transformer.php?carac=FOR">res <img src="img/transform_icon.png" alt="Icone d'échange de point"> for</a>
                </div>
            </div>
            <?php if($objPersonnage->get("piece_actuelle")->get("is_sortie")==="N") { ?>
            <div class="deplacement flex justify-center align-center"><a href="action_deplacement.php?sens=AVA">avancer</a></div>
            <?php } ?>
        </section>
        <section class="infos flex justify-between align-center">
            <div class="infos-personnage flex justify-around align-center">
                <div class="flex gap20 align-center">
                    <div id="pseudo">
                        <?= $objPersonnage->get("pseudo") ?>
                    </div>
                    <div id="pdv" class="pdv <?php if($objPersonnage->get("points_de_vie")>75) echo "enforme"; elseif($objPersonnage->get("points_de_vie")>30) echo "blesse"; else echo "malenpoint"; ?>">
                        <span><?= $objPersonnage->get("points_de_vie") ?></span>/100
                    </div>
                </div>
                <div>
                    <table class="stats-personnage">
                        <tbody>
                            <tr>
                                <td class="libelle">FOR</td>
                                <td id="force"><?= $objPersonnage->get("points_de_force") ?></td>
                                <td class="libelle">RES</td>
                                <td id="resistance"><?= $objPersonnage->get("points_de_resistance") ?></td>
                                <td class="libelle">AGI</td>
                                <td id="agilite"><?= $objPersonnage->get("points_d_agilite") ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="adversaires">
                <table>
                    <thead>
                        <tr>
                            <th>Adversaire</th>
                            <th>Point de vie</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        foreach ($arrayAdversaires as $id => $unAdversaire) {
                    ?>
                        <tr>
                            <td class="pseudo"><?= $unAdversaire->get("pseudo") ?></td>
                            <td class="pdv <?php if($unAdversaire->get("points_de_vie")>75) echo "enforme"; elseif($unAdversaire->get("points_de_vie")>30) echo "blesse"; else echo "malenpoint"; ?>"><?= $unAdversaire->get("points_de_vie") ?> / 100</td>
                            <td class="action"><a class="action attaque" href="action_attaquer.php?idAdversaire=<?= $unAdversaire->id() ?>"><img src="img/sword_icon.png" alt="Icone d'attaque"></a></td>
                        </tr>
                    <?php
                        }
                    ?>
                    </tbody>
                </table>
            </div>
            <div class="evenements">
                <table>
                    <thead>
                        <tr>
                            <th>Initiateur</th>
                            <th>Action</th>
                            <th>Description</th>
                            <th>Cible</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        foreach ($arrayEvenements as $id => $unEvenement) {
                    ?>
                        <tr>
                            <td><?= $unEvenement->get("initiateur")->get("pseudo") ?></td>
                            <td><?= $unEvenement->get("action") ?></td>
                            <td><?= $unEvenement->get("description") ?></td>
                            <td><?= ($unEvenement->get("cible")->id() != 0)?$unEvenement->get("cible")->get("pseudo") : "" ?></td>
                            <td><?= $unEvenement->get("date") ?></td>
                        </tr>
                    <?php
                        }
                    ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
    <script>
        let idSalle = <?= $objPersonnage->get("piece_actuelle")->id() ?>
    </script>
    <script src="js/app.js"></script>
    <script src="js/ecran_jeu.js"></script>
</body>
</html>