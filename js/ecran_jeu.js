//Récupération des éléments nécessaires
let aTransformPoint = document.querySelectorAll(`#actionTransformer a`);
let pointsForce = document.getElementById(`force`); 
let pointsResistance = document.getElementById(`resistance`); 
let pointsAgilite = document.getElementById(`agilite`); 
let pointsVie = document.querySelector(`#pdv span`); 
let aAttack = document.querySelectorAll(`.adversaires a.attaque`);

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
        console.log(rep);
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
            }
            console.log(rep);
        }).catch(err => {
            console.log(err);
        });
    })
});

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
            }
            console.log(rep);
        }).catch(err => {
            console.log(err);
        });
    })
});