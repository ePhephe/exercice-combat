<?php

/**
 * Contrôleur : Gère l'action quand un personnage souhaite se déplacer
 * Paramètres : 
 *      GET sens - Sens du déplacement
 */


//Initialisation : on appelle le programme d'initialisation
require_once "utils/init.php";
require_once "utils/verif_connexion.php";


/**
 * Récupération des paramètres
 */
//On récupère la salle courrante
if(!isSet($_GET["sens"])){
    header("Location:charger_partie.php");
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
    header("Location:index.php");
}
else {
    if($strSens === "AVA"){
        $objPersonnage->avancer();
    }
    else if($strSens === "REC"){
        $objPersonnage->reculer();
    }
}

/**
 * Affichage du template
 */
//On encode le résultat en JSON et on l'affiche
header("Location:charger_partie.php");