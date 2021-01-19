var partager = document.getElementById("partager");

partager.addEventListener('click', function () {
    var url = document.createElement('input'),
        text = window.location.href;

    document.body.appendChild(url);
    url.value = text;
    url.select();
    document.execCommand('copy');
    document.body.removeChild(url);

    alert("L'URL de la liste a bien été copiée !");
})