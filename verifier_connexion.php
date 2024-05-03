<?php

/**
 * Contrôleur : Connexion au personnage
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
if(isSet($_POST["pseudo"]) && isSet($_POST["password"])) {
    $strPseudo = $_POST["pseudo"];
    $strPassword = $_POST["password"];
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
    $objPersonnage = new personnage();
    $boolResultat = $objPersonnage->connexionPersonnage($strPseudo,$strPassword);

    if($boolResultat === false) {
        $arrayErreurs[] = "La connexion a échouée !";
    }
    else {
        $objSession->connect($objPersonnage->id());
    }
}

/**
 * Affichage du template
 */
//Si on est déjà connecté on affiche directement l'écran de jeu
if($objSession->isConnected()) {
    header("Location:charger_partie.php");
}
else {
    $strErreur = implode("<br>",$arrayErreurs);
    require_once "templates/pages/form_login.php";
}
