var login = document.getElementById('login');
var password = document.getElementById('password');
var meter = document.getElementById('password-strength-meter');

password.addEventListener('input', function() {
    var mdp = password.value;
    var resultat;
    if (login != null) {
        var nomDeCompte = login.value;
        resultat = zxcvbn(mdp, [nomDeCompte]);
    }
    else
        resultat = zxcvbn(mdp);

    //Update la barre de force
    meter.value = resultat.score;
});