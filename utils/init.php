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

//Modèle pour les classes
include_once "utils/model.php";

//Classes de nos objets de base de données
include_once "modeles/action.php";
include_once "modeles/personnage.php";
include_once "modeles/piece.php";

/**
 * Gestion de la session
 */
include_once "utils/session.php";