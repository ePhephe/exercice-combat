//Récupération des éléments nécessaires
let aTransformPoint = document.querySelectorAll(`a.transform`);
let pointsForce = document.getElementById(`force`); 
let pointsResistance = document.getElementById(`resistance`); 
let pointsAgilite = document.getElementById(`agilite`); 
let pointsVie = document.querySelector(`#pdv span`); 
let tabBodyAdversaires = document.querySelector(`.adversaires tbody`);
let tabBodyActions = document.querySelector(`.evenements tbody`);
let animatedPerso = document.querySelector(`.animated-personnage`);
let divModal = document.querySelector(`.modal`);
let divMessageModal = document.querySelector(`.modal div`);
let main = document.querySelector(`main`);
let h1 = document.querySelector(`h1`);
let btnAvance = document.getElementById(`buttonAvance`);
let aBtnAvance = document.querySelector(`#buttonAvance a`);
let btnRecule = document.getElementById(`buttonRecule`);
let aBtnRecule = document.querySelector(`#buttonRecule a`);

/**
 * Change le stage sur lequel évolue le personnage
 * 
 * @param {object} objPrevStage Stage sur lequel était le joueur avant
 * @param {object} objStage Stage sur lequel est actuellement
 */
function changeStage(objPrevStage,objStage){
    //Initialisation du H1
    let h1HTML = ``;

    //Gestion du fond de la page sur le main
    main.classList.remove(`etage`+objPrevStage.numero);
    main.classList.add(`etage`+objStage.numero);

    //Gestion du H1
    //On affiche le nom du stage
    h1HTML += objStage.nom + ` [` + objStage.numero + `]`;
    //On affiche un message particulier si on est à l'entrée ou à la sortie
    if(objStage.is_sortie == `O`) {
        h1HTML += `<span>Vous êtes arrivé au bout, bravo guerrier !</span>`;
    }
    else if(objStage.is_entree == `O`){
        h1HTML += `<span>Vous êtes à l'entrée, en avant !</span>`;
    }
    //On applique le template au HTML du H1
    h1.innerHTML = h1HTML;

    //Gestion des boutons Avancer et Reculer
    setButtonAvancer(objStage);
    setButtonReculer(objStage);
}

/**
 * Met en place le bouton AVANCER
 * 
 * @param {object} objStage Stage sur lequel est actuellement
 */
function setButtonAvancer(objStage){
    //Si on est pas à la sortie, on affiche le bouton avancer
    if(objStage.is_sortie == `N`) {
        btnAvance.classList.remove(`d-none`);
    }
    else {
        btnAvance.classList.add(`d-none`);
    }
}

/**
 * Met en place le bouton RECULER
 * 
 * @param {object} objStage Stage sur lequel est actuellement
 */
function setButtonReculer(objStage){
    //Si on est pas à l'entrée, on affiche le bouton reculer
    if(objStage.is_entree == `N`) {
        btnRecule.classList.remove(`d-none`);
    }
    else {
        btnRecule.classList.add(`d-none`);
    }
}

/**
 * Remet en static l'animation du personnage
 */
function personnageStatic(etat){
    //On met la classe static et on enlève la classe de l'état actuel
    animatedPerso.classList.add(`static`);
    animatedPerso.classList.remove(etat);
}

/**
 * Masque le message de la modal
 */
function masqueModal(){
    //On masque les messages avec le display none (classe CSS d-none)
    divModal.classList.add(`d-none`);
    divMessageModal.innerHTML = ``;
}

/**
 * Affiche le message de la modal
 */
function afficheModal(message,succes){
    //Selon le succès ou l'échec, on place des classes CSS différentes
    if(succes===true) {
        divModal.classList.add(`succes`);
        divModal.classList.remove(`echec`);
    }
    else {
        divModal.classList.add(`echec`);
        divModal.classList.remove(`succes`);
    }
    
    //On affecte le message au contenu de la div et on enlève le display none
    divModal.classList.remove(`d-none`);
    divMessageModal.innerHTML = message;
    //On laisse 5sec avant de remasquer le message
    setTimeout(masqueModal,5000);
}


/**
 * Mets à jour les informations du personnage à l'écran
 * 
 * @param {object} personnage Informations du personnages à jour
 */
function majInfosPerso(personnage){
    //On met à jours les caractéristiques du perso une à une
    pointsForce.innerText = personnage.points_de_force; 
    pointsResistance.innerText = personnage.points_de_resistance; 
    pointsAgilite.innerText = personnage.points_d_agilite; 
    pointsVie.innerText =personnage.points_de_vie; 
}

/**
 * Fonction pour construire la liste des adversaires
 * 
 * @param {array} adversaires Tableau d'objets d'adversaires
 */
function listAdversaires(adversaires){
    //On initialise le template HTML
    templateHTML = ``;

    //Si des adversaires sont bien présents, on génère la liste
    if(adversaires.length>0){
        //On parcourt chaque adversaire
        adversaires.forEach(unAdversaire => {
            //On initialise le statut de l'adversaire
            let statut = ``;
            //En fonction de ses points de vie, on définit le bon statut
            if(unAdversaire.points_de_vie>75)
                statut = `enforme`;
            else if(unAdversaire.points_de_vie>30)
                statut = `blesse`;
            else
                statut = `malenpoint`;

            //On construit le HTML
            templateHTML += `<tr>
                <td class="pseudo">${unAdversaire.pseudo}</td>
                <td class="pdv ${statut}">${unAdversaire.points_de_vie} / 100 <img src="img/heart_icon.png" alt="Icone de vie"></td>
                <td class="action"><a class="action attaque" href="action_attaquer.php?idAdversaire=${unAdversaire.id}"><img src="img/sword_icon.png" alt="Icone d'attaque"></a></td>
            </tr>`;
        });
    }
    //Sinon on affiche un message qu'aucun adversaire n'est présent
    else {
        templateHTML = `<tr><td>Aucun adversaire trouvé !</td></tr>`;
    }

    //On affecte le template HTML au corps du tableau des adversaires
    tabBodyAdversaires.innerHTML = templateHTML;

    //On récupère les liens d'attaques de chaque adversaire
    let aAttack = document.querySelectorAll(`.adversaires a.attaque`);

    //On parcourt tous les liens d'attaque
    aAttack.forEach(attaque => {
        attaque.addEventListener(`click`,(e)=>{
            //On arrête le fonctionement par défaut
            e.preventDefault();
            //Appel de l'URL en lien de la cible (href de la balise a cliquée)
            fetch(e.target.href).then(res => {
                return res.json();
            }).then(rep => {
                //Si l'action a échoué
                if(rep.succes === false){
                    //Si la raison de l'échec est la mort du personnage ou la déconnexion (ou non connexion), on retourne à l'index
                    if(rep.raison==="mort" || rep.raison==="deconnect"){
                        //On précise en paramètre la raison du logout
                        window.location.href = `index.php?logout=`+rep.raison;
                    } else {
                        //Si la raison de l'échec est autre, on l'affiche dans la modal
                        afficheModal(rep.message,rep.succes);
                    }
                }
                else {
                    //Sinon l'action a réussi, on met à jours les informations
                    majInfos();
                    //On affiche le message de succès dans la modal
                    afficheModal(rep.message,rep.succes);
                    //On met en place l'animation d'attaque
                    animatedPerso.classList.remove(`static`);
                    animatedPerso.classList.add(`attack`);
                    //On laisse l'animation 1sec puis on remet le personnage en position static
                    setTimeout(personnageStatic,1000,`attack`);
                }
                //console.log(rep);
            }).catch(err => {
                console.log(err);
            });
        })
    });
}

/**
 * Fonction pour construire la liste des actions
 * 
 * @param {array} actions Tableau d'objets d'actions
 */
function listActions(actions){
    //On initialise le template HTML
    templateHTML = ``;

    //Si des actions sont bien présentes dans l'historique, on génère la liste
    if(actions.length>0){
        //On parcourt toutes les actions
        actions.forEach(uneAction => {
            //On initialise un objet date avec la valeur de la date de l'action
            let objDateAction = new Date(uneAction.date);
            //On récupère la date au format local
            let dateAction = objDateAction.toLocaleString();

            //En fonction de l'action, on affiche un picto et un message particulier
            switch (uneAction.code) {
                case `ATK`:
                    templateHTML += `<tr>
                            <td>${dateAction} <img src="img/sword_icon.png" alt="Icone d'attaque"> Vous avez attaqué un advsersaire. ${uneAction.description}</td>
                        </tr>`;
                    break;
                case `SBA`:
                    //On affiche uniquement si on est l'initiateur
                    if(uneAction.initiateur == idPerso) {
                        templateHTML += `<tr>
                            <td>${dateAction} <img src="img/shield_icon.png" alt="Icone de défense"> Vous avez subi une attaque. ${uneAction.description}</td>
                        </tr>`;
                    }
                    break;
                case `TFP`:
                    templateHTML += `<tr>
                        <td>${dateAction} <img src="img/potion_icon.png" alt="Icone de potion"> Vous avez transformé un point de caractéristique. ${uneAction.description}</td>
                    </tr>`;
                    break;
                case `DPA`:
                    templateHTML += `<tr>
                        <td>${dateAction} <img src="img/walk_icon.png" alt="Icone de marche"> Vous avez avancé dans la pièce suivante. ${uneAction.description}</td>
                    </tr>`;
                    break;
                case `DPR`:
                    templateHTML += `<tr>
                        <td>${dateAction} <img src="img/walk_back_icon.png" alt="Icone de marche arrière"> Vous avez reculé dans la pièce précédente. ${uneAction.description}</td>
                    </tr>`;
                    break;
                case `ESQ`:
                    //On affiche uniquement si on est l'initiateur
                    if(uneAction.initiateur == idPerso) {
                        templateHTML += `<tr>
                            <td>${dateAction} <img src="img/escape_icon.png" alt="Icone de ninja"> Vous avez esquivé une attaque. ${uneAction.description}</td>
                        </tr>`;
                    }
                    break;
                case `RPT`:
                    //On affiche uniquement si on est l'initiateur
                    if(uneAction.initiateur == idPerso) {
                        templateHTML += `<tr>
                            <td>${dateAction} <img src="img/shield_sword_icon.png" alt="Icone de riposte"> Vous avez tenté de riposté à une attaque. ${uneAction.description}</td>
                        </tr>`;
                    }
                    break;
                default:
                    break;
            }
        });
    }
    //Sinon on affiche un message particulier pour indiquer que l'historique est vide
    else {
        templateHTML = `<tr><td>Votre historique est vide !</td></tr>`;
    }

    //On affecte notre template au corps du tableau des actins
    tabBodyActions.innerHTML = templateHTML;
}

/**
 * Fonction lancée à intervalle régulier pour la mise à jour des informations
 */
function majInfos(){
    //Appel du contrôleur pour mettre à jour la partie
    fetch(`http://combat.mdurand.mywebecom.ovh/maj_partie.php`).then(res => {
        return res.json();
    }).then(rep => {
        //S'il y a un échec
        if(rep.succes === false){
            //Et que l'échec est dû à la mort du personnage ou à une déconnexion
            if(rep.raison==="mort" || rep.raison==="deconnect"){
                //On ramène à la page d'index avec la raison du logout en paramètre
                window.location.href = `index.php?logout=`+rep.raison;
            }
        }
        //Si c'est un succès
        else {
            //On mets à jours les informations du personnage
            majInfosPerso(rep.personnage);
            //On mets à jours la liste des adversaires
            listAdversaires(rep.adversaires);
            //On mets à jours la liste des actions
            listActions(rep.actions);
        }
        //console.log(rep);
    }).catch(err => {
        console.log(err);
    });
}

/**
 * Fonction lancée à intervalle régulier pour l'attente d'un personnage dans une salle
 */
function personnageAttente(){
    //Appel du contrôleur pour gérer l'attente dans la salle
    fetch(`http://combat.mdurand.mywebecom.ovh/action_attendre.php`).then(res => {
        return res.json();
    }).then(rep => {
        //S'il y a un échec
        if(rep.succes === false){
            //Et que l'échec est dû à la mort du personnage ou à une déconnexion
            if(rep.raison==="mort" || rep.raison==="deconnect"){
                //On ramène à la page d'index avec la raison du logout en paramètre
                window.location.href = `index.php?logout=`+rep.raison;
            } else {
                //Si la raison de l'échec est autre, on l'affiche dans la modal
                afficheModal(rep.message,rep.succes);
            }
        }
        //Si c'est un succès
        else {
            //On mets à jours les informations
            majInfos();
            //On affiche le message de succès
            afficheModal(rep.message,rep.succes);
        }
        //console.log(rep);
    }).catch(err => {
        console.log(err);
    });
}

//Timer pour l'attente dans une salle - 10sec
let timerAttente = setInterval(personnageAttente,10000);

//On met un listener sur les boutons qui transforment un point
aTransformPoint.forEach(bouton => {
    bouton.addEventListener(`click`,(e)=>{
        //On arrête le fonctionement par défaut
        e.preventDefault();

        //Appel du contrôleur pour transformer les points
        fetch(e.target.href).then(res => {
            return res.json();
        }).then(rep => {
            //S'il y a un échec
            if(rep.succes === false){
                //Et que l'échec est dû à la mort du personnage ou à une déconnexion
                if(rep.raison==="mort" || rep.raison==="deconnect"){
                    //On ramène à la page d'index avec la raison du logout en paramètre
                    window.location.href = `index.php?logout=`+rep.raison;
                } else {
                    //Si la raison de l'échec est autre, on l'affiche dans la modal
                    afficheModal(rep.message,rep.succes);
                }
            }
            //Si c'est un succès
            else {
                //On mets à jours les informations
                majInfos();
                //On affiche le message de succès
                afficheModal(rep.message,rep.succes);
                //On passe le personnage en mode transform
                animatedPerso.classList.remove(`static`);
                animatedPerso.classList.add(`transform`);
                //On laisse l'animation 1sec avant de le repasser en mode static
                setTimeout(personnageStatic,1000,`transform`);
            }
            //console.log(rep);
        }).catch(err => {
            console.log(err);
        });
    })
});

//Action lorsque l'on clique sur le bouton reculer
aBtnRecule.addEventListener(`click`,(e)=>{
    //On arrête le fonctionement par défaut
    e.preventDefault();

    //Appel de l'URL en lien de la cible (href de la balise a cliquée)
    fetch(e.target.href).then(res => {
        return res.json();
    }).then(rep => {
        //S'il y a un échec
        if(rep.succes === false){
            //Et que l'échec est dû à la mort du personnage ou à une déconnexion
            if(rep.raison==="mort" || rep.raison==="deconnect"){
                //On ramène à la page d'index avec la raison du logout en paramètre
                window.location.href = `index.php?logout=`+rep.raison;
            } else {
                //Si la raison de l'échec est autre, on l'affiche dans la modal
                afficheModal(rep.message,rep.succes);
            }
        }
        //Si c'est un succès
        else {
            //On mets à jours les informations
            majInfos();
            //On appelle la fonction qui met à jours le stage courant
            changeStage(rep.prevStage,rep.stage);
            //On affiche le message de succès
            afficheModal(rep.message,rep.succes);
            //On passe le personnage en mode recule
            animatedPerso.classList.remove(`static`);
            animatedPerso.classList.add(`recule`);
            //On laisse l'animation 2sec avant de le repasser en mode static
            setTimeout(personnageStatic,2000,`recule`);
        }
        //console.log(rep);
    }).catch(err => {
        console.log(err);
    });
});

//Action lorsque l'on clique sur le bouton avancer
aBtnAvance.addEventListener(`click`,(e)=>{
    //On arrête le fonctionement par défaut
    e.preventDefault();

    //Appel du contrôleur pour transformer les points
    fetch(e.target.href).then(res => {
        return res.json();
    }).then(rep => {
        //S'il y a un échec
        if(rep.succes === false){
            //Et que l'échec est dû à la mort du personnage ou à une déconnexion
            if(rep.raison==="mort" || rep.raison==="deconnect"){
                //On ramène à la page d'index avec la raison du logout en paramètre
                window.location.href = `index.php?logout=`+rep.raison;
            } else {
                //Si la raison de l'échec est autre, on l'affiche dans la modal
                afficheModal(rep.message,rep.succes);
            }
        }
        //Si c'est un succès
        else {
            //On mets à jours les informations
            majInfos();
            //On appelle la fonction qui met à jours le stage courant
            changeStage(rep.prevStage,rep.stage);
            //On affiche le message de succès
            afficheModal(rep.message,rep.succes);
            //On passe le personnage en mode avance
            animatedPerso.classList.remove(`static`);
            animatedPerso.classList.add(`avance`);
            //On laisse l'animation 2sec avant de le repasser en mode static
            setTimeout(personnageStatic,2000,`avance`);
        }
        //console.log(rep);
    }).catch(err => {
        console.log(err);
    });
});


//Timer pour la mise à jour régulière des informations
let timerInfos = setInterval(majInfos,5000);

//Initialisation des informations
majInfos();