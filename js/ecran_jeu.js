//Récupération des éléments nécessaires
let aTransformPoint = document.querySelectorAll(`a.transform`);
let pointsForce = document.getElementById(`force`); 
let pointsResistance = document.getElementById(`resistance`); 
let pointsAgilite = document.getElementById(`agilite`); 
let pointsVie = document.querySelector(`#pdv span`); 
let tabBodyAdversaires = document.querySelector(`.adversaires tbody`);
let tabBodyActions = document.querySelector(`.evenements tbody`);
let animatedPerso = document.querySelector(`.animated-personnage`);

/**
 * Remet en static l'animation du personnage
 */
function personnageStatic(etat){
    animatedPerso.classList.add(`static`);
    animatedPerso.classList.remove(etat);
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
            console.log(e.target);
            //Appel du contrôleur pour transformer les points
            fetch(e.target.href).then(res => {
                return res.json();
            }).then(rep => {
                if(rep.succes === false){
                    if(rep.raison==="mort" || rep.raison==="deconnect"){
                        window.location.href = `index.php?logout=`+rep.raison;
                    }
                }
                else {
                    majInfosPerso(rep.personnage);
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
    fetch(`http://combat.mdurand.mywebecom.ovh/action_attendre.php?idSalle=`+idSalle).then(res => {
        return res.json();
    }).then(rep => {
        if(rep.succes === false){
            if(rep.raison==="mort" || rep.raison==="deconnect"){
                window.location.href = `index.php?logout=`+rep.raison;
            }
        }
        else {
            majInfosPerso(rep.personnage);
        }
        //console.log(rep);
    }).catch(err => {
        console.log(err);
    });
}

//Timer pour l'attente dans une salle
let timerAttente = setInterval(personnageAttente,30000);

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
                }
            }
            else {
                majInfosPerso(rep.personnage);
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



//Timer pour la mise à jour régulière des informations
let timerInfos = setInterval(majInfos,15000);

majInfos();