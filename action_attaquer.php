<?php

/**
 * Contrôleur : Gère l'action quand un personnage souhaite attaquer un adversaire
 * Paramètres : 
 *      GET idAdversaire - identifiant de l'adversaire attaqué
 */


//Initialisation : on appelle le programme d'initialisation
require_once "utils/init.php";
require_once "utils/verif_connexion_json.php";


/**
 * Récupération des paramètres
 */
//On récupère la salle courrante
if(!isSet($_GET["idAdversaire"])){
    $arrayRetour["succes"] = false;
    $arrayRetour["raison"] = "param";
    $arrayRetour["message"] = "Aucun adversaire n'est passée en paramètre ! ";
}
else {
    $idAdversaire = $_GET["idAdversaire"];
}

/**
 * Traitements
 */
//On prépare le retour json
$arrayRetour = [];
//On récupère les informations du personnage
$objPersonnage = $objUser;
//Si le personnage est mort, on déconnecte et on affiche la page de connexion
if( ! $objPersonnage->stillAlive()) {
    $objSession->deconnect();
    //On prépare le message d'information
    $arrayRetour["succes"] = false;
    $arrayRetour["raison"] = "mort";
    $arrayRetour["message"] = "Vous êtes mort au combat !";
}
else {
    if($objPersonnage->attaquer($idAdversaire)){
        $arrayRetour["succes"] = true;
        $arrayRetour["message"] = "Le combat est terminé ! ";
        $arrayRetour["personnage"] = $objPersonnage->getToTab();
    }
    else {
        $arrayRetour["succes"] = false;
        $arrayRetour["raison"] = "echec";
        $arrayRetour["message"] = "Vous n'avez pas réussi à attaquer votre adversaire !";
    }
}

/**
 * Affichage du résultat
 */
//On encode le résultat en JSON et on l'affiche
echo json_encode($arrayRetour);