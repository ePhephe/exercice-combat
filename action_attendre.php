<?php

/**
 * Contrôleur : Gère l'action quand un personnage attend dans une salle
 * Paramètres : 
 *      GET idSalle - Salle dans laquelle on se trouve
 */


//Initialisation : on appelle le programme d'initialisation
require_once "utils/init.php";
require_once "utils/verif_connexion_json.php";


/**
 * Récupération des paramètres
 */
//On récupère la salle courrante
if(!isSet($_GET["idSalle"])){
    $arrayRetour["succes"] = false;
    $arrayRetour["message"] = "Aucune salle passée en paramètre ! ";
}
else {
    $idSalle = $_GET["idSalle"];
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
    $arrayRetour["message"] = "Vous êtes mort au combat !";
}
else {
    if($objPersonnage->waiting($idSalle)){
        $arrayRetour["succes"] = true;
        $arrayRetour["message"] = "Vous avez récupéré 1 point d'agilité ! ";
    }
    else {
        $arrayRetour["succes"] = false;
        $arrayRetour["message"] = "Vous n'avez pas réussi à vous reposer !";
    }
}

/**
 * Affichage du résultat
 */
//On encode le résultat en JSON et on l'affiche
echo json_encode($arrayRetour);