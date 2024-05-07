<?php

// Code d'initialisation à inclure en début de contrôleur

/**
 * Paramètrage des messages d'erreur
 */
//On affiche les erreurs
ini_set("display_errors", 1);   
//On affiche toutes les erreurs
error_reporting(E_ALL);

/**
 * Chargement des librairies
 */

/**
 * Charger la classe passée en paramètre
 *
 * @param  string $nameClass Nom de la classe
 * @return void
 */
function autoLoadClass($nameClass) {
    //
    if(substr($nameClass,0,1) == "_") {
        include_once "utils/".substr($nameClass,1).".php";
    }
    else if(file_exists("modeles/$nameClass.php")) {
        include_once "modeles/$nameClass.php";
    }
}
//Enregistrement de la fonction de chargement automatique dans le système
spl_autoload_register("autoLoadClass");