<?php

/**
 * Contrôleur : Index de l'application, vérifie si une connexion existe et affiche la page d'accueil ou du jeu
 * Paramètres :
 *      GET logout - Raison de la déconnexion si l'utilisateur l'a été
 */


//Initialisation : on appelle le programme d'initialisation
require_once "utils/init.php";
$objSession = _session::getSession();


/**
 * Récupération des paramètres
 */
if(isset($_GET["logout"])){
    $strRaisonLogout = $_GET["logout"];
}


/**
 * Traitements
 */
if(isset($strRaisonLogout)) {
    switch ($strRaisonLogout) {
        case 'mort':
            $success = false;
            $message = "Vous êtes mort...<br> Créez un nouveau personnage et retournez au combat !";
            break;
        case 'deconnect':
            $success = false;
            $message = "Vous n'êtes pas connecté !<br> Créez un nouveau personnage et retournez au combat !";
            break;
        
        default:
            break;
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
else {
    require_once "templates/pages/accueil.php";
}
