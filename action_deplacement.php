<?php

/**
 * Contrôleur : Gère l'action quand un personnage souhaite se déplacer
 * Paramètres : 
 *      GET sens - Sens du déplacement
 */


//Initialisation : on appelle le programme d'initialisation
require_once "utils/init.php";
require_once "utils/verif_connexion_json.php";


/**
 * Récupération des paramètres
 */
//On récupère la salle courrante
if(!isSet($_GET["sens"])){
    //On prépare le message d'information
    $arrayRetour["succes"] = false;
    $arrayRetour["raison"] = "echec";
    $arrayRetour["message"] = "Il n'y a pas de sens de déplacement !";
}
else {
    $strSens = $_GET["sens"];
}

/**
 * Traitements
 */
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
    //On récupère les informations du stage de départ
    $objPrevStage = $objPersonnage->get("piece_actuelle");
    $arrayRetour["prevStage"] = $objPrevStage->getToTab();
    if($strSens === "AVA"){
       if(! $objPersonnage->avancer()){
            $arrayRetour["succes"] = false;
            $arrayRetour["raison"] = "echec";
            $arrayRetour["message"] = "Vous n'avez passez assez d'agilité !";
        }
        else {
            $arrayRetour["succes"] = true;
            $arrayRetour["message"] = "Bienvenue dans la salle suivante, bon courage !";
        }
    }
    else if($strSens === "REC"){
        if(! $objPersonnage->reculer()){
            $arrayRetour["succes"] = false;
            $arrayRetour["raison"] = "echec";
            $arrayRetour["message"] = "Il n'y a pas de sens de déplacement !";
        }
        else {
            $arrayRetour["succes"] = true;
            $arrayRetour["message"] = "Vous avez reculer, lâche !"; 
        }
    }
}
//On récupère les informations du nouveau stage
$objStage = $objPersonnage->get("piece_actuelle");
$arrayRetour["stage"] = $objStage->getToTab();

/**
 * Affichage du template
 */
//On encode le résultat en JSON et on l'affiche
echo json_encode($arrayRetour);