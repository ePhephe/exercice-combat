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
        "etat" =>  [
            "type"=>"text",
            "libelle"=>"Etat",
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

    /**
     * Vérifie que le personnage est vivant !
     *
     * @return boolean True si le personnage est vivant sinon False
     */
    function stillAlive(){
        if($this->values["etat"] === "M"){
            return false;
        }
        else if($this->values["points_de_vie"] <= 0) {
            $this->set("etat","M");
            $this->update();
            $this->enregistrerAction("MRT",$this);
            return false;
        }
        else {
            return true;
        }
    }
    
    /**
     * Retourne la liste des adversaires disponibles pour le personnage
     *
     * @return array Tableau indexé sur l'id d'objets personnage
     */
    function listAdversaires(){
        //On récupère la liste des personnages qui sont dans la même pièce que le personnage courant et vivant
        $arrayPersonnages = $this->list([
            ["champ"=>"piece_actuelle","valeur"=>$this->values["piece_actuelle"],"operateur"=>"="],
            ["champ"=>"id","valeur"=>$this->id(),"operateur"=>"<>"],
            ["champ"=>"etat","valeur"=>"V","operateur"=>"="]
        ]);

        if($arrayPersonnages === false){
            $arrayPersonnages= [];
        }

        return $arrayPersonnages;
    }

    /**
     * Retourne la liste des actions concernant un personnage
     *
     * @return array Tableau indexé sur l'id d'actions qui concernent le personnage
     */
    function listEvenements(){
        //On instancie un objet de la classe action
        $objAction = new action();
        //On récupère la liste des actions dont le personnage est à l'initiative ou dont il est la cible
        $arrayActions = $objAction->listActionsPersonnage($this->id());

        if($arrayActions === false){
            $arrayActions= [];
        }

        return $arrayActions;
    }
    
    /**
     * Déclenche l'action d'attente pour le personnage
     *
     * @return boolean True si l'action est validée sinon False
     */
    function waiting(){
        //On vérifie que le personnage n'a pas d'action dans les 10 dernières secondes
        $objAction = new action();
        $objDateNow = new DateTime();
        $objDateLastAction = $objAction->lastActionPersonnage($this->id());

       if($objDateNow->getTimestamp()-$objDateLastAction->getTimestamp() > 10) {
            //On calcule les points d'agilités
            $newAgilite = $this->get("points_d_agilite") + 1;
            $this->set("points_d_agilite",$newAgilite);

            $this->update();

            $this->enregistrerAction("ATT",$this);

            return true;
       }
       else {
            return false;
       }
    }

    /**
     * Déclenche l'action d'avancer d'une pièce
     *
     * @return boolean True si l'action est validée sinon False
     */
    function avancer(){
        //On instancie l'objet de la pièce à atteindre
        $objNewRoom = new piece($this->get("piece_actuelle")->id()+1);

        //On calcule les nouveaux point d'agilité
        $newPda = $this->get("points_d_agilite") - $objNewRoom->get("numero");
        if($newPda>=0) {
            $this->set("points_d_agilite",$newPda);
                
            //On modifier la pièce actuelle du personnage
            $this->set("piece_actuelle",$objNewRoom->id());
            //On met à jour le personnage
            $this->update();

            $this->enregistrerAction("DPA",$this);

            return true;
        }
        else {
            return false;
        }
    }

    /**
     * Déclenche l'action de reculer d'une pièce
     *
     * @return boolean True si l'action est validée sinon False
     */
    function reculer(){
        //On instancie l'objet de la pièce à atteindre
        $objNewRoom = new piece($this->get("piece_actuelle")->id()-1);

        //On calcule les nouveaux point de vie
        $newPdv = $this->get("points_de_vie") + $objNewRoom->get("numero");
        $this->set("points_de_vie",$newPdv);
            
        //On modifier la pièce actuelle du personnage
        $this->set("piece_actuelle",$objNewRoom->id());
        //On met à jour le personnage
        $this->update();

        $this->enregistrerAction("DPR",$this);

        return true;
    }
    
    /**
     * Transforme un point de caractéristiques en un autre (FOR <-> RES)
     *
     * @param  mixed $strCarac Caractéristique à augmenter
     * @return boolean True si l'action a été validé sinon False
     */
    function transformPoint($strCarac) {
        if($this->get("points_d_agilite")>=3) {
            if($strCarac === "FOR") {
                $newForce = $this->get("points_de_force") + 1;
                $newRes = $this->get("points_de_resistance") - 1;
            }
            else {
                $newForce = $this->get("points_de_force") - 1;
                $newRes = $this->get("points_de_resistance") + 1;
            }
            $newAgilite = $this->get("points_d_agilite") - 3;

            $this->set("points_d_agilite",$newAgilite);
            $this->set("points_de_force",$newForce);
            $this->set("points_de_resistance",$newRes);

            $this->update();

            $this->enregistrerAction("TFP",$this);

            return true;
        }
        else {
            return false;
        }
    }

    /**
     * Attaque un adversaire
     *
     * @param  integer $idAdversaire Identifiant de l'adversaire
     * @return boolean True si l'action a été validé sinon False
     */
    function attaquer($idAdversaire) {
        //On instance un objet du personnage adverse
        $objAdversaire = new personnage($idAdversaire);
        //On stocke les points de vie adverses actuels
        $intPdvAdversaire = $objAdversaire->get("points_de_vie");

        //On vérifie que les personnages sont bien dans la même pièce
        if($this->get("piece_actuelle")->id() === $objAdversaire->get("piece_actuelle")->id()){
            //On détermine la puissance de l'attaque
            $intPuissanceAttaque = $this->get("points_de_force");

            //On récupère le résultat de l'attaque subie par l'adversaire
            $resultatEsquive = $objAdversaire->subirAttaque($this->id(),$intPuissanceAttaque);
            //Selon le résultat
            switch ($resultatEsquive) {
                case 'victoire':
                    //En cas de victoire
                    $resultat = "You win !";
                    //On gagne 1 point d'agilité
                    $intNewPda = $this->get("points_d_agilite") + 1;
                    $intNewPdv = $this->get("points_de_vie");
                    //Si on est déjà au maximum de l'agilité
                    if($intNewPda > $this->fields["points_d_agilite"]["max"])
                        $intNewPdv = $this->get("points_de_vie") + 1;

                    //On vérifie si on a tué l'adversaire
                    if( ! $objAdversaire->stillAlive()){
                        //Si l'adversaire est mort, on récupère ses points de vie pré-combat
                        $intNewPdv = $this->get("points_de_vie") + $intPdvAdversaire;
                        //On enregistre le résultalt
                        $resultat = "You win ! Overkill !";
                    }
                    //On initialise les nouvelles valeurs
                    $this->set("points_de_vie",$intNewPdv);
                    $this->set("points_d_agilite",$intNewPda);
                    //On sauvegarde tous les changements
                    $this->update();
                    
                    break;
                case 'egalite':
                    # code...
                    break;
                case 'defaite':
                    //On calcul les nouveau points de vie
                    $intNewPdv = $this->get("points_de_vie") - 1;
                    //On met à jour les points de vie
                    $this->set("points_de_vie",$intNewPdv);
                    //On enregistre ces changements
                    $this->update();
                    //On vérifie que le personnage est toujours vivant
                    $this->stillAlive();
                    //On enregistre le résultalt
                    $resultat = "You loose !";
                    break;
                case 'esquive':
                    //L'adversaire esquive
                    //Si on a 10 en force ou plus
                    if($this->get("points_de_force")>=10){
                        //On perd un point de force
                        $this->set("points_de_force",$this->get("points_de_force")-1);
                        //On gagne un point de résistance
                        $this->set("points_de_resistance",$this->get("points_de_resistance")+1);
                        //On enregistre ces changements
                        $this->update();
                    }
                    //On enregistre le résultalt
                    $resultat = "Votre adversaire a esquivé ! ";
                    break;
                default:
                    break;
            }

            //On enregistre l'action
            $this->enregistrerAction("ATK",$this,$resultat,$objAdversaire);

            return true;
        }
        else {
            return false;
        }        
    }

    /**
     * Subit une attaque adverse
     *
     * @param  integer $idAdversaire Identifiant de l'adversaire
     * @param  integer $intPuissanceAttaque Puissance de l'attaque adverse
     * @return string Résultat du combat (victoire,egalite,defaite,esquive)
     */
    function subirAttaque($idAdversaire,$intPuissanceAttaque) {
        //On instance un objet du personnage adverse
        $objAdversaire = new personnage($idAdversaire);

        //Si l'agilité est supérieure ou égale à l'attaque + 3
        if($this->get("points_d_agilite") >= $intPuissanceAttaque+3){
            //On perds un point d'agilité
            $intNewPda = $this->get("points_d_agilite") - 1;
            $this->set("points_d_agilite",$intNewPda);
            $this->update();
            
            //On enregistre l'action
            $this->enregistrerAction("ESQ",$this,"Esquive réussie !",$objAdversaire);

            return "esquive";
        }
        //Si la force est supérieure à l'attaque
        else if ($this->get("points_de_force") > $intPuissanceAttaque){
            $boolResultatRispote = $this->riposte($idAdversaire);
            //Si la riposte réussit
            if($boolResultatRispote === true) {
                //On gagne un point de vie
                $intNewPdv = $this->get("points_de_vie") + 1;
                $this->set("points_de_vie",$intNewPdv);
                $this->update();

                //On enregistre l'action
                $this->enregistrerAction("RPT",$this,"Riposte réussie !",$objAdversaire);

                return "defaite";
            }
            else {
                //On perd 2 points de vie
                $intNewPdv = $this->get("points_de_vie") - 2;
                $this->set("points_de_vie",$intNewPdv);
                $this->update();

                //On enregistre l'action
                $this->enregistrerAction("RPT",$this,"Riposte échouée !",$objAdversaire);

                return "victoire";
            }
        }
        //On se défend
        else {
            //Si la résistance est supérieure ou égale à l'attaque
            if($this->get("points_de_resistance") >= $intPuissanceAttaque){
                //On enregistre l'action
                $this->enregistrerAction("SBA",$this,"Défense réussie !",$objAdversaire);
                return "defaite";
            }
            else {
                //On subit en dégâts la différence entre l'attaque et la résistance
                $intNewPdv = $this->get("points_de_vie") - ($intPuissanceAttaque - $this->get("points_de_resistance"));
                $this->set("points_de_vie",$intNewPdv);
                $this->update();
                //On enregistre l'action
                $this->enregistrerAction("SBA",$this,"Défense échouée !",$objAdversaire);
                return "victoire";
            }
        }
    }

    /**
     * Riposte à une attaque adverse
     *
     * @param  integer $idAdversaire Identifiant de l'adversaire
     * @return boolean True si la ripose réussie sinon False
     */
    function riposte($idAdversaire) {
        //On instance un objet du personnage adverse
        $objAdversaire = new personnage($idAdversaire);

        //Notre adversaire subit donc une attaque
        $resultatRiposte = $objAdversaire->subirAttaque($this->id(),$this->get("points_de_force"));
        
        if($resultatRiposte === "victoire")
            return true;
        else
            return false;
    }
    
    
    /**
     * Enregistre une action dans le journal
     *
     * @param  string $strAction Code de l'action à enregistrer
     * @param  object $objPersoInit Objet du personnage à l'initiative de l'action
     * @param  mixed $objPersoCible Objet du personnage cible de l'action (facultatif)
     * @return void
     */
    function enregistrerAction($strAction,$objPersoInit,$strDescription = "",$objPersoCible = null){
        //On instancie un objet action
        $objAction = new action();

        //On initialise les variables
        $objAction->set("code",$strAction);
        $objAction->set("description",$strDescription);
        $objAction->set("date",date("Y-m-d H:i:s"));
        $objAction->set("initiateur",$objPersoInit->id());
        if($objPersoCible != null)
            $objAction->set("cible",$objPersoCible->id());

        //On insère l'action
        $objAction->insert();
    }
}