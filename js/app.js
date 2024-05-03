/**
 * Affiche une erreur sur le champ dont l'id est passé en paramètre
 * @param {string} id - id unique de l'élément dans le DOM
 * @param {string} messageErreur - Message d'erreur à afficher
 */
function afficheErreur(id,messageErreur){
    //On récupère l'élément et son élément message d'erreur
    let element = document.getElementById(id);
    let message = document.getElementById(id + `-erreur`);

    //On lui ajoute la classe d'erreur sur un input
    element.classList.add(`input-error`);
    //On insère le message d'erreur et on l'affiche en lui retirant la classe display none
    message.innerHTML += messageErreur + `<br>`;
    message.classList.remove(`d-none`);

}
/**
 * Enlève les erreurs sur le champ dont l'id est passé en paramètre
 * @param {string} id - id unique de l'élément dans le DOM
 */
function enleveErreur(id){
    //On récupère l'élément et son élément message d'erreur
    let element = document.getElementById(id);
    let message = document.getElementById(id + `-erreur`);

    //On enlève la classe d'erreur sur l'input
    element.classList.remove(`input-error`);
    //On enlève le message d'erreur et on le masque en lui remettant la classe display none
    message.innerHTML = ``;
    message.classList.add(`d-none`);
    
}