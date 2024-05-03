<?php

/**
 * Contrôleur : Connexion au personnage
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
//On se déconnecte
$objSession->deconnect();

/**
 * Affichage du template
 */
//Si on est déjà connecté on affiche directement l'écran de jeu
header("Location:index.php");