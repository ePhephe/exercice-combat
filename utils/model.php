<?php

/**
 * Classe _model : classe générique des objets du modèle de données
 */

class _model {

    /**
     * Attributs
     */

    //Nom de la table dans la BDD
     protected $table = "";
     //Liste des champs
     // Chaque champ doit être sous la forme ["nom_champ" => ["type"=>"type_champ","libelle"=>"libelle_champ"]]
    protected $fields = [];
    
    //Identifiant de l'objet
    protected $id = 0;
    //Valeurs des champs
    protected $values = [];
    
    //Base de données ouverte
    protected static $bdd;


    /**
     * Constructeur
     */
    
    /**
     * Constructeur de l'objet
     *
     * @param  integer $id Identifiant de l'objet à charger
     * @return void
     */
    function __construct($id = null) {
        // Si l'identifiant n'est pas null
        if ( ! is_null($id)) {
            //On charge l'objet
            $this->load($id);
        }
    }

    /**
     * Méthodes
     */
    
    /**
     * Retourne l'information si l'objet est chargé
     *
     * @return boolean True si l'objet est chargé, sinon False
     */
    function is() {
        return ! empty($this->id);      
    }

    /**
     * Getters
     */
    
    /**
     * Retourne la valeur pour l'attribut passé en paramètre
     *
     * @param  string $fieldName - Nom de l'attribut
     * @return mixed Valeur de l'attribut
     */
    function get($fieldName) {
        //On vérifie si une méthode get_fieldname existe dans la classe, dans ce cas on l'appelle
        if(method_exists($this,"get_$fieldName"))
            return call_user_func([$this,"get_$fieldName"]);

        // Si la valeur existe (isset(....)) retourne la valeur sinon on retourne une valeur par défaut en fonction du type du champ
        if (isset($this->values[$fieldName])) {
            //On regarde si le type du champ est un objet(lien)
            if($this->fields[$fieldName]["type"] === "object") {
                //On vérifie si on a stocké un objet pour ce champ dans le tableau values avec la clé name_object
                if(!isset($this->values[$fieldName."_object"])) {
                    //Si name_object n'existe pas, on créé l'objet et on le stocke à cet emplacement
                    $obj = new $this->fields[$fieldName]["nom_objet"]();
                    $obj->load($this->values[$fieldName]);
                    $this->values[$fieldName."_object"] = $obj;
                }

                return $this->values[$fieldName."_object"];
            }
            else {
                return $this->values[$fieldName];
            }
        } else {
            switch ($this->fields[$fieldName]["type"]) {
                case 'text':
                    return "";
                case 'number':
                    return 0;
                case 'object':
                    return new $this->fields[$fieldName]["nom_objet"]();
                default:
                    return "";
            }
        }
    }

    /**
     * Retourne la valeur pour tous les attributs sous forme d'un tableau
     *
     * @return array Ensemble des champs dans un tableau associatif
     */
    function getToTab() {
        //Initialisation du tableau
        $arrayFields = [];
        //On parcourt tous les champs
        foreach ($this->fields as $cle => $champ) {
            $arrayFields[$cle] = $this->values[$cle];
        }
        $arrayFields["id"] = $this->id();

        return $arrayFields;
    }
    
    /**
     * S'execute lorsque l'on utilise $obj->name
     * Permet de retourner la valeur d'un attribut
     *
     * @param  string $name Attribut concerné
     * @return mixed Valeur de l'attribut $name
     */
    function __get($name){
        if(array_key_exists($name,$this->fields)){
            return $this->values[$name];
        }
        else if($name === "id") {
            return $this->id;
        }
    }
    
    /**
     * Retourne l'identifiant de l'objet courant
     *
     * @return integer - Identifiant de l'objet courant
     */
    function id() {
        return $this->id;
    }

    /**
     * Setters
     */
    
    /**
     * Définit la valeur d'un champ
     *
     * @param  string $fieldName Nom du champ à modifier
     * @param  mixed $value Nouvelle valeur du champ
     * @return boolean - True si la valeur est acceptée sinon False
     */
    function set($fieldName, $value) {
        //On vérifie si une méthode get_fieldname existe dans la classe, dans ce cas on l'appelle
        if(method_exists($this,"set_$fieldName"))
            return call_user_func([$this,"set_$fieldName"],$value);

        if(array_key_exists("max",$this->fields[$fieldName])) {
            if($value > $this->fields[$fieldName]["max"])                
                $value = $this->fields[$fieldName]["max"];
        }

        

        if(array_key_exists("min",$this->fields[$fieldName])) {
            if($value < $this->fields[$fieldName]["min"])
                $value = $this->fields[$fieldName]["min"];
        }

        $this->values[$fieldName] = $value;
        return true;
    }

    /**
     * S'execute lorsque l'on utilise $obj->name = valeur
     * Permet de mettre à jour la valeur d'un attribut
     *
     * @param  string $name Attribut concerné
     * @param  mixed $value Valeur de l'attribut concerné
     * @return void
     */
    function __set($name,$value){
        if(array_key_exists($name,$this->fields)){
            $this->values[$name] = $value;
        }
    }
    
    /**
     * Charge l'objet à partir d'un tableau
     *
     * @param  array $data Informations à charger dans l'objet
     * @return boolean - True si le chargement s'est bien passé sinon False
     */
    function loadFromTab($data) {
        //On parcourt tous les champs
        foreach($this->fields as $fieldName => $field){
            //Pour chaque champ on indique la valeur dans l'attribut values
            $this->values[$fieldName] = $data[$fieldName];

            if($field["type"] === "object") {
                //On vérifie si on a stocké un objet pour ce champ dans le tableau values avec la clé name_object
                //Si name_object n'existe pas, on créé l'objet et on le stocke à cet emplacement
                $objNew = new $field["nom_objet"]();
                $objNew->load($this->values[$fieldName]);
                $this->values[$fieldName."_object"] = $objNew;
            }
        }

        //Puis on enregistre l'id dans son attribut dédié
        $this->id = $data["id"];

        return true;
    }

    /**
     * Méthodes de gestion avec la BDD
     */
     
     /**
      * Retourne la connexion à la base de données ou crée la connexion si elle n'est pas existante
      *
      * @return object Objet PDO de la base de données
      */
     static function bdd() {
        if(empty(static::$bdd)) {
            static::$bdd = new PDO("mysql:host=localhost;dbname=projets_combat_mdurand;charset=UTF8","mdurand","ac2dmTM8q?M");;
            static::$bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
            return static::$bdd;
        }
        else {
            return static::$bdd;
        }
     }
    
    /**
     * Charge un objet à partir d'un identifiant
     *
     * @param  integer $id Identifiant de l'objet à charger
     * @return boolean - True si le chargement s'est bien passé sinon False
     */
    function load($id) {
        //On débute la requête avec le SELECT
        $strRequete = "SELECT " ;

        //On génère un tableau composés des noms des champs encadrés par ` ` 
        $arrayFields = [];
        foreach($this->fields as $fieldName => $field) {
            $arrayFields[] = "`$fieldName`";
        }
        $strRequete .= implode(", ", $arrayFields);
        
        //On construit le FROM avec le nom de la table
        $strRequete .= " FROM `$this->table` ";

        //On construit le WHERE avec l'id que l'on passe en tableau de paramètre
        $strRequete .= " WHERE `id` = :id";
        $arrayParam = [ ":id" => $id];

        //On prépare la requête
        $bdd = static::bdd();
        $objRequete = $bdd->prepare($strRequete);

        //On execute la requête avec ses paramètres
        if ( ! $objRequete->execute($arrayParam)) {
            // On a une erreur de requête (on peut afficher des messages en phase de debug)
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

        // Pour chaque champ de l'objet, on valorise $this->values[champ];
        foreach($this->fields as $fieldName => $field) {
            $this->values[$fieldName] = $arrayInfos[$fieldName];
        }

        // On renseigne l'id :
        $this->id = $id;

        return true;
    }
    
    /**
     * Insertion de l'objet dans la base de données
     *
     * @return boolean - True si le chargement s'est bien passé sinon False
     */
    function insert() {
        //On construit la requête INSERT
        $strRequete = "INSERT INTO `$this->table` SET " . $this->makeRequestSet();
        $arrayParam  = $this->makeRequestParamForSet();

        //On prépare la requête
        $bdd = static::bdd();
        $objRequete = $bdd->prepare($strRequete);

        //On execute la requête et on gère les erreurs
        if ( ! $objRequete->execute($arrayParam)) {
            // Erreur sur la requête
            return false;
        }

        //On récupère l'identifiant qui a été créé par l'INSERT
        $this->id = $bdd->lastInsertId();

        return true;
    }

    
    /**
     * Mise à jour de l'objet dans la base de données
     *
     * @return boolean - True si le chargement s'est bien passé sinon False
     */
    function update() {
        //On construit la requête d'UPDATE
        $strRequete = "UPDATE  `$this->table` SET " . $this->makeRequestSet() . " WHERE `id` = :id ";
        $arrayParam = $this->makeRequestParamForSet();
        $arrayParam[":id"] = $this->id;
           

        //On prépare la requête
        $bdd = static::bdd();
        $objRequete = $bdd->prepare($strRequete);

        //On exécute la requête et on gère les erreurs
        if ( ! $objRequete->execute($arrayParam)) {
            // Erreur sur la requête
            return false;
        }

        return true;
    }

    /**
     * Suppression de l'objet dans la base de données
     *
     * @return boolean - True si le chargement s'est bien passé sinon False
     */
    function delete() {
        //On construit la requête du DELETE
        $strRequete = "DELETE FROM `$this->table` WHERE `id` = :id";
        $arrayParam = [":id" => $this->id];
    
        //On prépare la requête
        $bdd = static::bdd();
        $req = $bdd->prepare($strRequete);

        //On exécute la requête avec les parmaètres
        if ( ! $req->execute($arrayParam)) {
            //Erreur sur la requête
            return false;
        }

        //On remet l'id de l'objet à 0
        $this->id = 0;

        return true;
    }
    
    /**
     * Liste tous les éléments de la base de données
     *
     * @param  array $arrayCriteresTri Tableau des critère de tri (facultatif)
     * @return mixed - Tableau d'objets indexé sur l'id, s'il y a une erreur False
     */
    function listAll($arrayCriteresTri = []) {
        //On construit la requête SELECT
        $arrayFields = [];
        // Pour chaque champ, on ajoute un elt `nomChamp` dans le tableau
        foreach ($this->fields as $fieldName => $field) {
            $arrayFields[] = "`$fieldName`";
        }

        $strRequete = "SELECT `id`, " . implode(",", $arrayFields) . " FROM `$this->table` ";
        
        //Si des crtières de tri sont présents
        $arrayTri = [];
        $strRequete .= "ORDER BY ";
        if(!empty($arrayCriteresTri)) {
            $arrayTri = [];
            foreach ($arrayCriteresTri as $critere => $sens) {
                if(array_key_exists($critere,$this->fields))
                    $arrayTri[] = "$critere $sens";
            }
        }
        $arrayTri[] = "`id` desc";
        $strRequete .= implode(",",$arrayTri). " ";

        //On prépare la requête SQL
        $bdd = static::bdd();
        $objRequete = $bdd->prepare($strRequete);

        //On exécute la requête et on gère les erreurs
        if ( ! $objRequete->execute()) { 
            return false;
        }

        //On récupère les enregistrements et on gère les erreurs si le tableau est vide
        $arrayResultats = $objRequete->fetchAll(PDO::FETCH_ASSOC);
        if (empty($arrayResultats)) {
            return false;
        }

        //On construit le tableau à retourner
        $arrayObjResultat = [];
        foreach ($arrayResultats as $unResultat) {
            $newObj = new $this->table();
            $newObj->loadFromTab($unResultat);
            $arrayObjResultat[$unResultat["id"]] = $newObj;
        }
  
        return $arrayObjResultat;
    }
    
    /**
     * Retourne un tableau d'objet selon les critères fournis
     *
     * @param  array $arrayFiltres Tableau des critères de filtre au format [["champ"=>"nom","valeur"=>"test","operateur"=>"LIKE"]] (facultatif)
     * @param  array $arrayCriteresTri Tableau des crtières de tri au format [["champ" => "sens"]] (facultatif)
     * @param  array $arrayLimit Tableau des crtières pagination ["limit" => 10,"offset" => 0] (facultatif)
     * @return mixed - Tableau d'objets indexé sur l'id, s'il y a une erreur False
     */
    function list($arrayFiltres = [],$arrayCriteresTri = [], $arrayLimit = []) {
        //On construit la requête SELECT
        $arrayFields = [];
        $arrayParam = [];
        foreach ($this->fields as $fieldName => $field) {
            $arrayFields[] = "`$fieldName`";
        }
        $strRequete = "SELECT `id`, " . implode(",", $arrayFields) . " FROM `$this->table` ";

        //Si des crtières de filtre sont présents
        if(!empty($arrayFiltres)) {
            $arrayReqFiltres = [];
            foreach ($arrayFiltres as $index => $filtre) {
                if(array_key_exists($filtre["champ"],$this->fields) || $filtre["champ"]==="id") {
                    if($filtre["champ"]==="id") {
                        $arrayReqFiltres[] = $filtre["champ"] . " " . $filtre["operateur"] . " :".$filtre["champ"].$index;
                        $arrayParam[":".$filtre["champ"].$index] = $filtre["valeur"];
                    }
                    else if($this->fields[$filtre["champ"]]["type"] === "text") {
                        $arrayReqFiltres[] = "UPPER(".$filtre["champ"].") " . $filtre["operateur"] . " :".$filtre["champ"].$index;
                        if($filtre["operateur"] === "LIKE"){
                            $arrayParam[":".$filtre["champ"].$index] = "%".strtoupper($filtre["valeur"])."%";
                        }
                        else {
                            $arrayParam[":".$filtre["champ"].$index] = strtoupper($filtre["valeur"]);
                        }
                    }
                    else {
                        $arrayReqFiltres[] = $filtre["champ"] . " " . $filtre["operateur"] . " :".$filtre["champ"].$index;
                        $arrayParam[":".$filtre["champ"].$index] = $filtre["valeur"];
                    }
                }
            }
            $strRequete .= "WHERE ". implode(" AND ", $arrayReqFiltres) . " ";
        }

        //Si des crtières de tri sont présents
        $arrayTri = [];
        $strRequete .= "ORDER BY ";
        if(!empty($arrayCriteresTri)) {
            $arrayTri = [];
            foreach ($arrayCriteresTri as $critere => $sens) {
                if(array_key_exists($critere,$this->fields))
                    $arrayTri[] = "`$critere` $sens";
            }
        }
        $arrayTri[] = "`id` desc";
        $strRequete .= implode(",",$arrayTri). " ";

        //Si un critère de pagination est présent
        if(!empty($arrayLimit)) {
            $strRequete .= "LIMIT ".$arrayLimit["offset"].", ".$arrayLimit["limit"];
        }

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
     * Génère la partie SET d'une requête INSERT/UPDATE
     *
     * @return string - Chaîne de caractères de la partie SET de la requête
     */
    function makeRequestSet() {
        //On va chercher le tableau des champs
        $tableau = $this->makeTableauSimpleSet();

        // Générer le texte final grâce à implode
        return implode(", ", $tableau);
    }
    
    /**
     * Construit un tableau des champs de l'objet au format requête " `champ` = :champ "
     *
     * @return array - Tableau des champs au format requête " `champ` = :champ "
     */
    function makeTableauSimpleSet() {
        // Faire un tableau : on part d'un tableau vide
        $arrayResultat = [];

        // Pour chaque champ : ajouter dans $result un élément `nomChamp` = :nomChamp
        foreach($this->fields as $fieldName => $field) {
            // On a le nom du champ dans $nomchamp
            $arrayResultat[] = "`$fieldName` = :$fieldName";
        }

        return $arrayResultat;
    }
    
    /**
     * Retourne un tableau de paramètre à passer en pramètre d'un requête INSERT/UPDATE
     *
     * @return array - Tableau des paramètres de la requête au format ":champ" => valeurchamp
     */
    function makeRequestParamForSet() {
        //On initialise un tableau vide
        $arrayResult = [];
        
        //On parcourt tous les champs
        foreach($this->fields as $fieldName => $field) {
            $strCle = ":$fieldName";          
            // Valeur : elle est dans le tableau des valeurs, l'attribut values ($this->values)
            // Si on a une valeur pour $nomChamp, on crée l'élément de tableau avec cette valeur,
            // Sinon, on crée avec null
            if (isset($this->values[$fieldName])) {
                $arrayResult[$fieldName] = $this->values[$fieldName];
            } else {
                $arrayResult[$fieldName] = null;
            }
        }

        return $arrayResult;
    }
}