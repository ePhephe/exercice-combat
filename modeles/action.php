<?php

/**
 * Classe action : classe de gestion des objets actions
 */

class action extends _model {

    /**
     * Attributs
     */

    //Nom de la table dans la BDD
    protected $table = "action";
    //Liste des champs
    protected $fields = [ 
        "code" => [
            "type"=>"enum",
            "libelle"=>"Action réalisée",
            "unique" => "N",
            "valeurs" => [
                "TFP" => "Point transformé",
                "DPA" => "Déplacement vers l'avant",
                "DPR" => "Déplacement vers l'arrière",
                "ATT" => "Attente dans la pièce",
                "ATK" => "Attaqué un adversaire",
                "SBA" => "Subit une attaque",
                "RPT" => "Riposté"
            ] 
        ],
        "action" =>  [
            "type"=>"text",
            "libelle"=>"Libellé de l'action",
            "unique" => "N"
        ],
        "initiateur" =>  [
            "type"=>"object",
            "nom_objet"=>"personnage",
            "libelle"=>"Initiateur",
            "unique" => "N"
        ],
        "cible" =>  [
            "type"=>"object",
            "nom_objet"=>"personnage",
            "libelle"=>"Cible",
            "unique" => "N"
        ],
        "date" =>  [
            "type"=>"datetime",
            "libelle"=>"Date et heure de l'action",
            "unique" => "N",
            "format" => "Y-m-d H:m:s",
        ]
    ]; 

    /**
     * Méthodes
     */

}