<?php

/**
 * Contrôleur : Charge toutes les informations nécessaires pour l'écran de jeu et l'affiche
 * Paramètres : 
 *      Néant
 */


//Initialisation : on appelle le programme d'initialisation
require_once "utils/init.php";
require_once "utils/verif_connexion.php";


/**
 * Récupération des paramètres
 */
//N.C

/**
 * Traitements
 */
//On récupère les informations du personnage
$objPersonnage = $objSession->userConnected();
//Si le personnage est mort, on déconnecte et on affiche la page de connexion
if( ! $objPersonnage->stillAlive()) {
    $objSession->deconnect();
    //On prépare le message d'information
    $success = false;
    $message = "Vous êtes mort au combat !<br> Créez un nouveau personnage !";
    require_once "templates/pages/accueil.php";
    exit;
}

//On récupère tous les adversaires dans la salle
$arrayAdversaires = $objPersonnage->listAdversaires();

//On récupère les évènements de l'utilisateur
//$arrayEvenements = $objPersonnage->listEvenements();

/**
 * Affichage du template
 */
//Si on est déjà connecté on affiche directement l'écran de jeu
require_once "templates/pages/ecran_jeu.php";