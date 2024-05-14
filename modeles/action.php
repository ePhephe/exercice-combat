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
                "ESQ" => "Esquivé une attaque",
                "RPT" => "Riposté",
                "MRT" => "Mort"
            ] 
        ],
        "action" =>  [
            "type"=>"text",
            "libelle"=>"Libellé de l'action",
            "unique" => "N"
        ],
        "description" =>  [
            "type"=>"text",
            "libelle"=>"Description de l'action",
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
            "format" => "Y-m-d H:i:s",
        ]
    ]; 

    /**
     * Méthodes
     */
    
    /**
     * Définit la valeur du champ code et le libellé de l'action correspondante
     *
     * @param  mixed $valeur Valeur du champ
     * @return void
     */
    function set_code($valeur){
        //On vérifie que le code de l'action existe
        if(array_key_exists($valeur,$this->fields["code"]["valeurs"])){
            //On récupère le libellé de l'action correspondant au code
            $this->values["code"] = $valeur;
            $this->values["action"] = $this->fields["code"]["valeurs"][$valeur];
        }
    }

    /**
     * Retourne la liste des actions concernant un personnage
     *
     * @param   integer $id Identifiant du personne dont on veut les actions
     * @return array Tableau indexé sur l'id d'actions qui concernent le personnage
     */
    function listActionsPersonnage($id){
        //On construit la requête
        $arrayFields = [];
        $arrayParam = [];
        foreach ($this->fields as $fieldName => $field) {
            $arrayFields[] = "`$fieldName`";
        }
        $strRequete = "SELECT `id`, " . implode(",", $arrayFields) . " FROM `$this->table` ";
        $strRequete .= "WHERE (`cible` = :idPerso OR `initiateur` = :idPerso) AND `code` <> 'ATT' ORDER BY `id` DESC ";
        $strRequete .= "LIMIT 0,100";
        $arrayParam[":idPerso"] = $id;

        //On prépare la requête
        $bdd = static::bdd();
        $req = $bdd->prepare($strRequete);

        //var_dump($strRequete);

        //On exécute la requête avec ses paramètres et on gère les erreurs
        if ( ! $req->execute($arrayParam)) { 
            var_dump($strRequete);
            var_dump($arrayParam);
            return false;
        }

        //On récupère les résultats et on gère les erreurs
        $arrayResultats = $req->fetchAll(PDO::FETCH_ASSOC);
        if (empty($arrayResultats)) {
            return false;
        }

        // construire le tableau à retourner :
        // Pour chaque élément de $liste, fabriquer un objet contact que l'on met dans le tableau final
        $arrayObjResultat = [];
        foreach ($arrayResultats as $unResultat) {
            $newObj = new $this->table();
            $newObj->loadFromTab($unResultat);

            $arrayObjResultat[$unResultat["id"]] = $newObj;
        }
  
        return $arrayObjResultat;
    }

    /**
     * Retourne le datetime de la dernière action d'un personnage
     *
     * @param  integer $id Identifiant du personne dont on veut les actions
     * @return mixed Objet date de la dernière action ou false si une erreur est rencontrée
     */
    function lastActionPersonnage($id){
        //On construit la requête
        $arrayFields = [];
        $arrayParam = [];
        foreach ($this->fields as $fieldName => $field) {
            $arrayFields[] = "`$fieldName`";
        }
        $strRequete = "SELECT MAX(date) AS lastDate FROM `$this->table` ";
        $strRequete .= "WHERE `initiateur` = :idPerso AND `code` <> 'ATT' ";
        $arrayParam[":idPerso"] = $id;

        //On prépare la requête
        $bdd = static::bdd();
        $req = $bdd->prepare($strRequete);

        //var_dump($strRequete);

        //On exécute la requête avec ses paramètres et on gère les erreurs
        if ( ! $req->execute($arrayParam)) { 
            var_dump($strRequete);
            var_dump($arrayParam);
            return false;
        }

        //On récupère les résultats et on gère les erreurs
        $arrayResultats = $req->fetchAll(PDO::FETCH_ASSOC);
        if (empty($arrayResultats)) {
            return false;
        }

        $objDateAction = new DateTime($arrayResultats[0]["lastDate"]);
  
        return $objDateAction;
    }
}