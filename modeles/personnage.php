<?php

/**
 * Classe personnage : classe de gestion des objets personnages
 */

class personnage extends _model {

    /**
     * Attributs
     */

    //Nom de la table dans la BDD
    protected $table = "personnage";
    //Liste des champs
    protected $fields = [ 
        "pseudo" => [
            "type"=>"text",
            "libelle"=>"Surnom",
            "unique" => "O",
            "max_length" => 50,
            "min_length" => 6
        ],
        "password" =>  [
            "type"=>"password",
            "libelle"=>"Mot de passe",
            "unique" => "N"
        ],
        "points_de_vie" =>  [
            "type"=>"number",
            "libelle"=>"Points de vie",
            "unique" => "N",
            "max" => 100,
            "min" => 0 
        ],
        "points_de_force" =>  [
            "type"=>"number",
            "libelle"=>"Points de force",
            "unique" => "N",
            "max" => 15,
            "min" => 0 
        ],
        "points_de_resistance" =>  [
            "type"=>"number",
            "libelle"=>"Points de résistance",
            "unique" => "N",
            "max" => 15,
            "min" => 0 
        ],
        "points_d_agilite" =>  [
            "type"=>"number",
            "libelle"=>"Points d'agilité",
            "unique" => "N",
            "max" => 15,
            "min" => 0 
        ],
        "piece_actuelle" =>  [
            "type"=>"object",
            "nom_objet"=>"piece",
            "libelle"=>"Pièce actuelle du personnage",
            "unique" => "N"
        ]
    ]; 

    /**
     * Méthodes
     */
    
    /**
     * Connexion au personnage
     *
     * @param  string $strLogin Login de connexion saisi par l'utilisateur
     * @param  string $strPassword Mot de passe de connexion saisi par l'utilisateur
     * @return boolean - True si la connexion réussi sinon False
     */
    function connexionPersonnage($strLogin,$strPassword){
        //On construit la requête SELECT
        $strRequete = "SELECT `id`, `password` FROM `$this->table` WHERE `pseudo` = :pseudo ";
        $arrayParam = [
            ":pseudo" => $strLogin
        ];

        //On prépare la requête
        $bdd = static::bdd();
        $objRequete = $bdd->prepare($strRequete);

        //On exécute la requête avec les parmaètres
        if ( ! $objRequete->execute($arrayParam)) {
            return false;
        }

        //On récupère les résultats
        $arrayResultats = $objRequete->fetchAll(PDO::FETCH_ASSOC);
        //Si le tableau est vide, on retourne une erreur (false)
        if (empty($arrayResultats)) {
            return false;
        }

        //On récupère la ligne de résultat dans une variable
        $arrayInfos = $arrayResultats[0];

        if(password_verify($strPassword,$arrayInfos["password"])) {
            $this->load($arrayInfos["id"]);
            return true;
        }

        return false;
    }

    /**
     * Vérifie si le pseudo est disponible pour être utilisé
     *
     * @param  string $strPseudoTeste Peusdo à tester
     * @return boolean True si le pseudo est disponible sinon False
     */
    function verifDispoPseudo($strPseudoTeste){
        //On construit la requête
        $strRequete = "SELECT id FROM $this->table WHERE `pseudo` = :pseudo ";

        //On valorise le paramètre du pseudo
        $arrayParam = [
            ":pseudo" => $strPseudoTeste
        ];

        //On prépare la requête
        $bdd = static::bdd();
        $objRequete = $bdd->prepare($strRequete);

        //On exécute la requête avec ses paramètres et on gère les erreurs
        if ( ! $objRequete->execute($arrayParam)) { 
            return false;
        }

        //On récupère les résultats
        $arrayResultats = $objRequete->fetchAll(PDO::FETCH_ASSOC);
        //S'il n'y a pas de résultat, le pseudo est disponible
        if(empty($arrayResultats)) {
            return true;
        }
        //Sinon il ne l'est pas
        else {
            return false;
        }
    }

    /**
     * Vérifie si les caractéristiques fournies respectent la répartition
     *
     * @param  integer $intForce Caractéristiques de force
     * @param  integer $intResistance Caractéristiques de résistance
     * @param  integer $intAgilite Caractéristiques d'agilité
     * @return boolean True si la répartition est correcte sinon False
     */
    function verifInitCaracteristiques($force,$resistance,$agilite){
        //On contrôle si le minimum est respecter
        if($force<3 || $resistance<3 || $agilite<3) {
            return false;
        }

        //On contrôle si le maximum est respecter
        if($force>10 || $resistance>10 || $agilite>10) {
            return false;
        }

        $somme = $force + $resistance + $agilite;
        //On contrôle si les 15 points sont répartis
        if( $somme != 15) {
            return false;
        }

        return true;
    }

}