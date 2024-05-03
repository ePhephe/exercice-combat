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
include_once "modeles/artiste.php";
include_once "modeles/compte.php";
include_once "modeles/concert.php";
include_once "modeles/conversation.php";
include_once "modeles/message.php";
include_once "modeles/organisateur.php";
include_once "modeles/representation.php";

/**
 * Gestion de la session
 */
include_once "utils/session.php";