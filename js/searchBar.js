function desactiverElemInutile(typeRecherche) {
    const dateDebut = document.getElementById("dateDebut");
    const dateFin = document.getElementById("dateFin");
    const auteur = document.getElementById("auteur");

    auteur.value = '';
    dateDebut.value = '';
    dateFin.value = '';

    switch (typeRecherche) {
        case 'auteur':
            dateDebut.disabled  = true;
            dateFin.disabled  = true;
            auteur.disabled  = false;
            break;

        case 'date':
            dateDebut.disabled  = false;
            dateFin.disabled  = false;
            auteur.disabled  = true;
            break;
    }
}