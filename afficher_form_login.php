<?php

/**
 * Contrôleur : Prépare et affiche le formulaire de connexion
 * Paramètres : N.C
 */


//Initialisation : on appelle le programme d'initialisation
require_once "utils/init.php";
$objSession = _session::getSession();


/**
 * Récupération des paramètres
 */
//N.C


/**
 * Traitements
 */
//N.C

/**
 * Affichage du template
 */
//Si on est déjà connecté on affiche directement l'écran de jeu
if($objSession->isConnected()) {
    require_once "templates/pages/ecran_jeu.php";
}
//Sinon on affiche la page d'accueil
else {
    require_once "templates/pages/form_login.php";
}
