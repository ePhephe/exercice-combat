//Récupération des éléments nécessaires
let aTransformPoint = document.querySelectorAll(`#actionTransformer a`);

/**
 * Fonction lancée à intervalle régulier pour l'attente d'un personnage dans une salle
 */
function personnageAttente(){
    //Appel du contrôleur pour gérer l'attente dans la salle
    fetch(`http://combat.mdurand.mywebecom.ovh/action_attendre.php?idSalle=`+idSalle).then(res => {
        return res.json();
    }).then(rep => {
        if(rep.success === false){
            //Gestion des erreurs à faire
            //Pas connecté
            //Personnage mort
        }
        else {
            //Gestion du succès
            //Afficher un message d'info
        }
        //console.log(rep);
    }).catch(err => {
        console.log(err);
    });
}

//Timer pour l'attente dans une salle
let timerAttente = setInterval(personnageAttente,3000);

//On met un listener sur les boutons qui transforment un point
aTransformPoint.forEach(bouton => {
    bouton.addEventListener(`click`,(e)=>{
        //On arrête le fonctionement par défaut
        e.preventDefault();

        //Appel du contrôleur pour transformer les points
        fetch(e.target.href).then(res => {
            return res.json();
        }).then(rep => {
            if(rep.success === false){
                //Gestion des erreurs à faire
                //Pas connecté
                //Personnage mort
            }
            else {
                //Gestion du succès
                //Afficher un message d'info
            }
            //console.log(rep);
        }).catch(err => {
            console.log(err);
        });
    })
});
