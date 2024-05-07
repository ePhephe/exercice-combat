<?php

/**
 * Utilitaire : Vérification de la connexion
 */

$objSession = _session::getSession();

if( ! $objSession->isConnected()) {
    $arrayRetour["succes"] = false;
    $arrayRetour["raison"] = "deconnect";
    $arrayRetour["message"] = "Vous n'êtes pas connecté !";
}
else {
    $objUser = $objSession->userConnected();
}