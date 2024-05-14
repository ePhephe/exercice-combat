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
        <h1 class="ingame flex justify-center align-center">
            <?= $objPersonnage->get("piece_actuelle")->get("nom") ?> (<?= $objPersonnage->get("piece_actuelle")->get("numero") ?>)<br>
            <span><?php if($objPersonnage->get("piece_actuelle")->get("is_sortie")==="O") { ?>Vous êtes arrivé au bout, bravo guerrier ! <?php } ?></span>
        </h1>
        <a class="btn-deconnexion" href="deconnecter.php"><img src="img/icon-logout.png" alt="Icone de déconnexion"></a>
        <section class="personnage flex justify-center">
            <div class="deplacement flex justify-center align-center <?php if($objPersonnage->get("piece_actuelle")->get("is_entree")==="O") { ?> d-none <?php } ?>" id="buttonRecule"><a href="action_deplacement.php?sens=REC">reculer</a></div>
            <div class="personnage flex align-center gap20">
                <div class="animated-personnage static"></div>
                <div class="animated-personnage recule"></div>
                <div class="animated-personnage avance"></div>
            </div>
            <div class="deplacement flex justify-center align-center <?php if($objPersonnage->get("piece_actuelle")->get("is_sortie")==="O") { ?> d-none <?php } ?>" id="buttonAvance"><a href="action_deplacement.php?sens=AVA">avancer</a></div>
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
                                <td class="libelle">FOR  <a class="action transform flex align-center gap20" href="action_transformer.php?carac=FOR"><img src="img/transform_icon.png" alt="Icone d'échange de point"></a></td>
                                <td id="force"><?= $objPersonnage->get("points_de_force") ?></td>
                                <td class="libelle">RES <a class="action transform flex align-center gap20" href="action_transformer.php?carac=RES"><img src="img/transform_icon.png" alt="Icone d'échange de point"></a></td>
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
                    <tbody>

                    </tbody>
                </table>
            </div>
            <div class="evenements">
                <table>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </section>
        <!-- Message de retour AJAX -->
        <div class="modal d-none">
            <div>
                
            </div>
        </div>
    </main>
    <script>
        let idSalle = <?= $objPersonnage->get("piece_actuelle")->id() ?>;
        let idPerso = <?= $objPersonnage->id() ?>;
    </script>
    <script src="js/app.js"></script>
    <script src="js/ecran_jeu.js"></script>
</body>
</html>