<?php

/**
 * Contrôleur : Met à jour toutes les informations nécessaires pour le jeu
 * Paramètres : 
 *      Néant
 */


//Initialisation : on appelle le programme d'initialisation
require_once "utils/init.php";
require_once "utils/verif_connexion_json.php";


/**
 * Récupération des paramètres
 */
//N.C

/**
 * Traitements
 */
//On initialise notre tableau de retour
$arrayRetour = [];
//On récupère les informations du personnage
$objPersonnage = $objSession->userConnected();
//Si le personnage est mort, on déconnecte et on affiche la page de connexion
if( ! $objPersonnage->stillAlive()) {
    $objSession->deconnect();
    //On prépare le message d'information
    $arrayRetour["succes"] = false;
    $arrayRetour["raison"] = "mort";
    $arrayRetour["message"] = "Vous êtes mort au combat !";
}
else {
    $arrayPersonnage = $objPersonnage->getToTab();

    //On récupère tous les adversaires dans la salle
    $arrayTempAdversaires = $objPersonnage->listAdversaires();
    $arrayAdversaires = [];
    foreach ($arrayTempAdversaires as $key => $adversaire) {
        $arrayAdversaires[] = $adversaire->getToTab();
    }

    //On récupère les évènements de l'utilisateur
    $arrayTempEvenements = $objPersonnage->listEvenements();
    $arrayEvenements = [];
    foreach ($arrayTempEvenements as $key => $evenement) {
        $arrayEvenements[] = $evenement->getToTab();
    }

    //On met en forme le retour
    $arrayRetour["succes"] = true;
    $arrayRetour["message"] = "Mise à jour des informations ";
    $arrayRetour["personnage"] = $arrayPersonnage;
    $arrayRetour["adversaires"] = $arrayAdversaires;
    $arrayRetour["actions"] = $arrayEvenements;
}

/**
 * Affichage du template
 */
echo json_encode($arrayRetour);