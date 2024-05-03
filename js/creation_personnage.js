//On récupère les éléments nécessaires aux vérifications
let inputPseudo = document.getElementById(`pseudo`);
let inputPassword = document.getElementById(`password`);
let inputConfirmPassword = document.getElementById(`confirmPassword`);
let tabInputCaracteristiques = document.querySelectorAll(`form.creation-personnage fieldset.caracteristiques input`);

//Vérification sur le pseudo
inputPseudo.addEventListener(`change`,(e)=>{
    //Appel du contrôleur pour vérifier la disponibilité du pseudo
    fetch(`http://combat.mdurand.mywebecom.ovh/verifier_pseudo.php?pseudo=`+inputPseudo.value).then(res => {
        return res.json();
    }).then(rep => {
        if(rep.success === false){
            afficheErreur(`pseudo`,`Le pseudo n'est pas disponible !`);
        }
        else {
            enleveErreur(`pseudo`);
        }
        //console.log(rep);
    }).catch(err => {
        console.log(err);
    });
});

//Vérification sur la confirmation du mot de passe
inputConfirmPassword.addEventListener(`change`,(e)=>{
    //On réinitialise les erreurs
    enleveErreur(`confirmPassword`);
    //On compare si les deux mot de passe sont identiques
    if(inputPassword.value!=inputConfirmPassword.value){
        afficheErreur(`confirmPassword`,`Les mots de passe ne sont pas identiques !`);
    }
    else {
        enleveErreur(`confirmPassword`);
    }
});

//Vérification sur la table des caractéristiques
tabInputCaracteristiques.forEach(uneCaracteristique => {
    //On ajoute un listener sur chaque input de caractéristique
    uneCaracteristique.addEventListener(`change`,(e)=>{
        //On initialise une variable de test à true
        enleveErreur(uneCaracteristique.id);
        let boolTest = true;
        //On récupère la valeur du champ
        let valeurCaracteristique = uneCaracteristique.value;
        //Si le champ est inférieur à 3, il y a une erreur
        if(valeurCaracteristique<3){
            boolTest = false;
            afficheErreur(uneCaracteristique.id,`La caractéristique ne peut pas être inférieure à 3<br>`);
        }
        //Si le champ est supérieur à 10, il y a une erreur
        if(valeurCaracteristique>10){
            boolTest = false;
            afficheErreur(uneCaracteristique.id,`La caractéristique ne peut pas être supérieur à 10<br>`);
        }

        //On récupère la valeur de toutes les caractéristiques
        let sommeCaractertistiques = 0;
        let messageCaracteristique = document.getElementById(`caracteristiques-erreur`);
        tabInputCaracteristiques.forEach(caracteristique => {
            sommeCaractertistiques += parseInt(caracteristique.value);
        });
        //On vérifie que leur somme n'est pas supérieur à 15
        if(sommeCaractertistiques>15) {
            messageCaracteristique.innerText = `La somme des caractéristiques ne peut pas être supérieure à 15 !`;
            messageCaracteristique.classList.remove(`d-none`);
        }
        else {
            messageCaracteristique.innerText = ``;
            messageCaracteristique.classList.add(`d-none`);
        }

        if(boolTest === true) {
            enleveErreur(uneCaracteristique.id);
        }
    });
});
