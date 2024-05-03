<?php

/**
 * Contrôleur : Création d'un personnage
 * Paramètres : 
 *      POST Informations du formulaire
 */


//Initialisation : on appelle le programme d'initialisation
require_once "utils/init.php";
$objSession = _session::getSession();


/**
 * Récupération des paramètres
 */
$boolResultat = true;
$arrayErreurs = [];
if(isSet($_POST["pseudo"]) && isSet($_POST["password"]) && isSet($_POST["confirmPassword"]) && isSet($_POST["pointForce"]) && isSet($_POST["pointResistance"]) && isSet($_POST["pointAgilite"])) {
    $strPseudo = $_POST["pseudo"];
    $strPassword = $_POST["password"];
    $strConfirmPassword = $_POST["confirmPassword"];
    $intForce = intval($_POST["pointForce"]);
    $intResistance = intval($_POST["pointResistance"]);
    $intAgilite = intval($_POST["pointAgilite"]);

}
else {
    $boolResultat = false;
    $arrayErreurs[] = "Aucune donnée n'a été trouvé en entrée.";
}

/**
 * Traitements
 */
//Si on est en résultat true, on execute la suite
if($boolResultat === true) {
    //On instancie un objet de personnage
    $objNewPersonnage = new personnage();

    //On vérifie que le pseudo est disponible
    if( ! $objNewPersonnage->verifDispoPseudo($strPseudo)) {
        $boolResultat = false;
        $arrayErreurs[] = "Le pseudo pour votre personnage n'est pas disponible.";
    }
    //On vérifie que les mots de passes sont ok
    if( $strPassword != $strConfirmPassword) {
        $boolResultat = false;
        $arrayErreurs[] = "Les mots de passe ne sont pas identiques.";
    }
    //On vérifie que les points sont correctement répartis
    if( ! $objNewPersonnage->verifInitCaracteristiques($intForce,$intResistance,$intAgilite)) {
        $boolResultat = false;
        $arrayErreurs[] = "Les caractéristiques ne sont pas réparties selon les règles définies.";
    }

    if($boolResultat === true) {
        //On initialise les informations
        $objNewPersonnage->set("pseudo",$strPseudo);
        $objNewPersonnage->set("password",password_hash($strPassword,PASSWORD_BCRYPT));
        $objNewPersonnage->set("points_de_vie",100);
        $objNewPersonnage->set("points_de_force",$intForce);
        $objNewPersonnage->set("points_de_resistance",$intResistance);
        $objNewPersonnage->set("points_d_agilite",$intAgilite);
        $objNewPersonnage->set("piece_actuelle",1);

        //On appelle l'insert pour insertion dans la base de données
        $boolResultat = $objNewPersonnage->insert();
    }
}

/**
 * Affichage du template
 */
//Si on est déjà connecté on affiche directement l'écran de jeu
if($objSession->isConnected()) {
    require_once "templates/pages/ecran_jeu.php";
}
//Sinon on affiche la page d'accueil
else if($boolResultat === true){
    $message = "Votre personnage a été créé avec succès !";
    $success= $boolResultat;
    require_once "templates/pages/form_login.php";
}
else {
    $strErreur = implode("<br>",$arrayErreurs);
    require_once "templates/pages/form_crea_personnage.php";
}
