<?php

/**
 * Contrôleur : Gère l'action quand un personnage souhaite transformer un point de caractéristique
 * Paramètres : 
 *      GET carac - Caractéristique que l'on souhaite augmenter
 */


//Initialisation : on appelle le programme d'initialisation
require_once "utils/init.php";
require_once "utils/verif_connexion_json.php";


/**
 * Récupération des paramètres
 */
//On récupère la salle courrante
if(!isSet($_GET["carac"])){
    $arrayRetour["succes"] = false;
    $arrayRetour["raison"] = "param";
    $arrayRetour["message"] = "Aucune caractéristique n'est passée en paramètre ! ";
}
else {
    $strCarac = $_GET["carac"];
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
    if($objPersonnage->transformPoint($strCarac)){
        $arrayRetour["succes"] = true;
        $arrayRetour["message"] = "Vous avez transformé votre point ! ";
        $arrayRetour["personnage"] = $objPersonnage->getToTab();
    }
    else {
        $arrayRetour["succes"] = false;
        $arrayRetour["raison"] = "echec";
        $arrayRetour["message"] = "Vous n'avez pas réussi à vous transformer le point !";
    }
}

/**
 * Affichage du résultat
 */
//On encode le résultat en JSON et on l'affiche
echo json_encode($arrayRetour);