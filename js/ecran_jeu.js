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
    let h1HTML = ``;

    //Gestion du fond de la page
    main.classList.remove(`etage`+objPrevStage.numero);
    main.classList.add(`etage`+objStage.numero);

    //Gestion du H1
    h1HTML += objStage.nom + ` [` + objStage.numero + `]`;
    if(objStage.is_sortie == `O`) {
        h1HTML += `<br><span>Vous êtes arrivé au bout, bravo guerrier !</span>`;
    }
    else if(objStage.is_entree == `O`){
        h1HTML += `<br><span>Vous êtes à l'entrée, en avant !</span>`;
    }
    h1.innerHTML = h1HTML;

    //Gestion des boutons Avancer et Reculer
    setButtonAvancer(objStage);
    setButtonReculer(objStage);
}

/**
 * Met en place le bouton RECULER
 * 
 * @param {object} objStage Stage sur lequel est actuellement
 */
function setButtonAvancer(objStage){
    console.log(objStage);
    if(objStage.is_sortie == `N`) {
        btnAvance.classList.remove(`d-none`);
    }
    else {
        btnAvance.classList.add(`d-none`);
    }
}

/**
 * Met en place le bouton AVANCER
 * 
 * @param {object} objStage Stage sur lequel est actuellement
 */
function setButtonReculer(objStage){
    console.log(objStage);
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
    animatedPerso.classList.add(`static`);
    animatedPerso.classList.remove(etat);
}

/**
 * Masque le message de la modal
 */
function masqueModal(){
    divModal.classList.add(`d-none`);
    divMessageModal.innerHTML = ``;
}

/**
 * Affiche le message de la modal
 */
function afficheModal(message,succes){
    if(succes===true) {
        divModal.classList.add(`succes`);
        divModal.classList.remove(`echec`);
    }
    else {
        divModal.classList.add(`echec`);
        divModal.classList.remove(`succes`);
    }
    
    divModal.classList.remove(`d-none`);
    divMessageModal.innerHTML = message;
    setTimeout(masqueModal,5000);
}


/**
 * Mets à jour les informations du personnage à l'écran
 * 
 * @param {object} personnage Informations du personnages à jour
 */
function majInfosPerso(personnage){
    pointsForce.innerText = personnage.points_de_force; 
    pointsResistance.innerText = personnage.points_de_resistance; 
    pointsAgilite.innerText = personnage.points_d_agilite; 
    pointsVie.innerText =personnage.points_de_vie; 
}

/**
 * Fonction pour construire la liste des adversaires
 */
function listAdversaires(adversaires){
    templateHTML = ``;

    if(adversaires.length>0){
        adversaires.forEach(unAdversaire => {
            let statut = ``;
            if(unAdversaire.points_de_vie>75)
                statut = `enforme`;
            else if(unAdversaire.points_de_vie>30)
                statut = `blesse`;
            else
                statut = `malenpoint`;

            templateHTML += `<tr>
                <td class="pseudo">${unAdversaire.pseudo}</td>
                <td class="pdv ${statut}">${unAdversaire.points_de_vie} / 100 <img src="img/heart_icon.png" alt="Icone de vie"></td>
                <td class="action"><a class="action attaque" href="action_attaquer.php?idAdversaire=${unAdversaire.id}"><img src="img/sword_icon.png" alt="Icone d'attaque"></a></td>
            </tr>`;
        });
    }
    else {
        templateHTML = `<tr><td>Aucun adversaire trouvé !</td></tr>`;
    }

    tabBodyAdversaires.innerHTML = templateHTML;

    let aAttack = document.querySelectorAll(`.adversaires a.attaque`);

    aAttack.forEach(attaque => {
        attaque.addEventListener(`click`,(e)=>{
            //On arrête le fonctionement par défaut
            e.preventDefault();
            //Appel du contrôleur pour transformer les points
            fetch(e.target.href).then(res => {
                return res.json();
            }).then(rep => {
                if(rep.succes === false){
                    if(rep.raison==="mort" || rep.raison==="deconnect"){
                        window.location.href = `index.php?logout=`+rep.raison;
                    } else {
                        afficheModal(rep.message,rep.succes);
                    }
                }
                else {
                    majInfos();
                    afficheModal(rep.message,rep.succes);
                    animatedPerso.classList.remove(`static`);
                    animatedPerso.classList.add(`attack`);
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
 */
function listActions(actions){
    templateHTML = ``;

    if(actions.length>0){
        actions.forEach(uneAction => {
            let objDateAction = new Date(uneAction.date);
            let dateAction = objDateAction.toLocaleString();

            switch (uneAction.code) {
                case `ATK`:
                    templateHTML += `<tr>
                            <td>${dateAction} <img src="img/sword_icon.png" alt="Icone d'attaque"> Vous avez attaqué un advsersaire. ${uneAction.description}</td>
                        </tr>`;
                    break;
                case `SBA`:
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
                    if(uneAction.initiateur == idPerso) {
                        templateHTML += `<tr>
                            <td>${dateAction} <img src="img/escape_icon.png" alt="Icone de ninja"> Vous avez esquivé une attaque. ${uneAction.description}</td>
                        </tr>`;
                    }
                    break;
                case `RPT`:
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
    else {
        templateHTML = `<tr><td>Votre historique est vide !</td></tr>`;
    }

    tabBodyActions.innerHTML = templateHTML;
}

/**
 * Fonction lancée à intervalle régulier pour la mise à jour des informations
 */
function majInfos(){
    //Appel du contrôleur pour gérer l'attente dans la salle
    fetch(`http://combat.mdurand.mywebecom.ovh/maj_partie.php`).then(res => {
        return res.json();
    }).then(rep => {
        if(rep.succes === false){
            if(rep.raison==="mort" || rep.raison==="deconnect"){
                window.location.href = `index.php?logout=`+rep.raison;
            }
        }
        else {
            majInfosPerso(rep.personnage);
            listAdversaires(rep.adversaires);
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
        if(rep.succes === false){
            if(rep.raison==="mort" || rep.raison==="deconnect"){
                window.location.href = `index.php?logout=`+rep.raison;
            } else {
                afficheModal(rep.message,rep.succes);
            }
        }
        else {
            majInfos();
            afficheModal(rep.message,rep.succes);
        }
        //console.log(rep);
    }).catch(err => {
        console.log(err);
    });
}

//Timer pour l'attente dans une salle
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
            if(rep.succes === false){
                if(rep.raison==="mort" || rep.raison==="deconnect"){
                    window.location.href = `index.php?logout=`+rep.raison;
                } else {
                    afficheModal(rep.message,rep.succes);
                }
            }
            else {
                majInfos();
                afficheModal(rep.message,rep.succes);
                animatedPerso.classList.remove(`static`);
                animatedPerso.classList.add(`transform`);
                setTimeout(personnageStatic,1000,`transform`);
            }
            //console.log(rep);
        }).catch(err => {
            console.log(err);
        });
    })
});

aBtnRecule.addEventListener(`click`,(e)=>{
    //On arrête le fonctionement par défaut
    e.preventDefault();

    //Appel du contrôleur pour transformer les points
    fetch(e.target.href).then(res => {
        return res.json();
    }).then(rep => {
        if(rep.succes === false){
            if(rep.raison==="mort" || rep.raison==="deconnect"){
                window.location.href = `index.php?logout=`+rep.raison;
            } else {
                afficheModal(rep.message,rep.succes);
            }
        }
        else {
            majInfos();
            changeStage(rep.prevStage,rep.stage);
            afficheModal(rep.message,rep.succes);
            animatedPerso.classList.remove(`static`);
            animatedPerso.classList.add(`recule`);
            setTimeout(personnageStatic,2000,`recule`);
        }
        //console.log(rep);
    }).catch(err => {
        console.log(err);
    });
});

aBtnAvance.addEventListener(`click`,(e)=>{
    //On arrête le fonctionement par défaut
    e.preventDefault();

    //Appel du contrôleur pour transformer les points
    fetch(e.target.href).then(res => {
        return res.json();
    }).then(rep => {
        if(rep.succes === false){
            if(rep.raison==="mort" || rep.raison==="deconnect"){
                window.location.href = `index.php?logout=`+rep.raison;
            } else {
                afficheModal(rep.message,rep.succes);
            }
        }
        else {
            majInfos();
            changeStage(rep.prevStage,rep.stage);
            afficheModal(rep.message,rep.succes);
            animatedPerso.classList.remove(`static`);
            animatedPerso.classList.add(`avance`);
            setTimeout(personnageStatic,2000,`avance`);
        }
        //console.log(rep);
    }).catch(err => {
        console.log(err);
    });
});


//Timer pour la mise à jour régulière des informations
let timerInfos = setInterval(majInfos,15000);

majInfos();