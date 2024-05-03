<?php

/**
 * Utilitaire : Vérification de la connexion
 */

$objSession = _session::getSession();

if( ! $objSession->isConnected()) {
    include_once "templates/pages/form_login.php";
    exit;
}
else {
    $objUser = $objSession->userConnected();
}