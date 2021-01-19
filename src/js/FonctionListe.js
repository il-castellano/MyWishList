function copyListePublicLink() {
    /* Get the text field */
    var copyText = document.getElementById("publicListe");

    /* Select the text field */
    copyText.select();

    /* Copy the text inside the text field */
    document.execCommand("copy");

    /* Alert the copied text */
    alert("Texte copié : " + copyText.value);
}

window.onload = () =>
{
    let copy = document.getElementById("bouttonCopie");
    copy.addEventListener("click", copyListePublicLink);
}