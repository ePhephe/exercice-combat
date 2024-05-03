<?php

/**
 * Utilitaire : Vérification de la connexion
 */

if( ! session_isconnected()) {
    include_once "templates/pages/form_login.php";
    exit;
}
else {
    $objCompte = session_userconnected();
}