<?php

/**
 * Classe pièce : classe de gestion des objets pièces
 */

class piece extends _model {

    /**
     * Attributs
     */

    //Nom de la table dans la BDD
    protected $table = "piece";
    //Liste des champs
    protected $fields = [ 
        "nom" => [
            "type"=>"text",
            "libelle"=>"Nom de la pièce",
            "unique" => "N",
            "max_length" => 150,
            "min_length" => 10
        ],
        "numero" =>  [
            "type"=>"number",
            "libelle"=>"Numéro de la pièce",
            "unique" => "N",
            "max" => 999,
            "min" => 0 
        ],
        "is_entree" =>  [
            "type"=>"text",
            "libelle"=>"Entrée",
            "unique" => "N",
            "max_length" => 1,
            "min_length" => 1 
        ],
        "is_sortie" =>  [
            "type"=>"text",
            "libelle"=>"Sortie",
            "unique" => "N",
            "max_length" => 1,
            "min_length" => 1 
        ]
    ]; 

    /**
     * Méthodes
     */

}