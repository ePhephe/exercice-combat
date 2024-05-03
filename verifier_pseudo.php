<?php

/**
 * Contrôleur : Vérifie si le pseudo du personne est déjà pris
 * Paramètres : 
 *      GET pseudo - Pseudo à tester
 */


//Initialisation : on appelle le programme d'initialisation
require_once "utils/init.php";

/**
 * Récupération des paramètres
 */
//On initialise un retour
$arrayRetour = [];
//On vérifie que le pseudo à tester est présent et on le récupère
if(isSet($_GET["pseudo"])) {
    $strPseudo = $_GET["pseudo"];
}
//Sinon on retourn false
else {
    $arrayRetour["success"] = false;
}


/**
 * Traitements
 */
//On instancie un objet personnage
$objPersonnage = new personnage();
$arrayRetour["success"] = $objPersonnage->verifDispoPseudo($strPseudo);

/**
 * Retour JSON
 */
echo json_encode($arrayRetour);

