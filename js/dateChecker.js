/**
 * Vérifie si la date de validité rentrée dans le formulaire de création de liste est bien conforme.
 * --> Encore valide aujourd'hui.
 */

var dateInput = document.getElementById('date');

dateInput.addEventListener('input', function() {
    var today = new Date();
    var date = new Date(dateInput.value);

    if (date.setHours(0,0,0,0) <= today.setHours(0,0,0,0))
        document.getElementById('erreurDate').style.display = null;
    else
        document.getElementById('erreurDate').style.display = 'none';
});