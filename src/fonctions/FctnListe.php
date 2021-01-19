namespace wishlist\fonction;

use wishlist\divers\Outils;
use wishlist\divers\Formulaire;

use wishlist\fonction\Authentification as AUTH;
use wishlist\fonction\CreateurItem as CI;

use wishlist\modele\Item;
use wishlist\modele\Liste;
use wishlist\modele\Message;
use wishlist\modele\Reservation;
use wishlist\modele\Save_liste;
use wishlist\modele\User;


class FctnListe {

//Rediriger vers cette fonction par listeAddForm, crée la liste dans la base de donnée
public static function cree()
{
// stop si un champ requis vide
if (!$_POST['titre']) {
echo 'Création impossible, le champ requis est vide.'; //alerte
exit();
}
// stop si une liste avec le même nom existe deja
$test = Liste::where('titre', 'like', $_POST['titre'])->first();
if ($test) {
Alerte::set('existing_list');
Outils::goTo(Outils::getArbo().'add-liste-form', 'Une liste avec le même nom existe déjà !');
exit(); //Evite de crée une nouvelle liste
}

//Verifie que la date saisie soit pas expirer et qu'elle est un format valide (ex : 2019-13-052, ne sera pas accepter)
if(Outils::listeExpiration($_POST['expiration']) || preg_match('#^([0-9]{4})([/-])([0-9]{2})\2([0-9]{2})$#', $_POST['expiration'], $m) == 1 && checkdate($m[4], $m[3], $m[1]))
{
Alerte::set('date_fault');
Outils::goTo(Outils::getArbo().'add-liste-form', 'Date saisie invalide');
}
else {
// creation d'une liste
$liste = new Liste();
$liste->titre = strip_tags($_POST['titre']);
$liste->description = strip_tags($_POST['description']);
if(isset($_SESSION['wishlist_userid']))
$liste->user_id = strip_tags($_SESSION['wishlist_userid']);
$liste->expiration = strip_tags($_POST['expiration']);
$liste->token_private = Outils::generateToken();
$liste->token_publique = Outils::generateToken();
$liste->save();
$_SESSION['wishlist_liste_token'] = $liste->token_private;
Outils::goTo('liste/'. $liste->token_private, 'Redirection vers la liste crée', 1);
}
}

//Rediriger par un bouton lorsqu'on édite une liste, rend la liste public ou privée selon son état actuel
public static function publication($token)
{
$liste = Liste::where('token_private', 'like', $token)
->first();
if ($liste->published == true)//Si la liste est publique elle deviendra privée
{
$liste->published = false;
$liste->save();
Outils::goTo('../liste/'. $token, 'La liste a été rendu privée.', 2);
}
else
{
$liste->published = true;
$liste->save();
Outils::goTo('../liste/'. $token, 'La liste a été rendu publique.', 2);
}

}

public static function ajoutUtilisateur()
{
if($_SESSION["wishlist_userid"] != null)
{
if($_POST['token'] != null){
$liste = Liste::where('token_private', 'like', $_POST['token'])->first();
if($liste)
{
if ($liste->user_id == $_SESSION["wishlist_userid"]) {
echo "Cette liste vous appartient déjà.";
}
else if($liste->user_id == null)
{
$liste->user_id = strip_tags($_SESSION["wishlist_userid"]);
$liste->save();
echo 'La liste a bien été ajouter à votre compte';
}
else{
echo "Cette liste appartient déjà à un autre utilisateur.";
}
}
else {
echo "Aucune liste correspond au token indiquer";
}
}
else {
echo "Aucun token introduit";
}
}
}

public static function modifier($token)//A MODIFIER !
{
// stop si pas de token renseigné
if (!isset($_SESSION['wishlist_liste_token']))
{
echo 'Token erroné';
}
if (!$_POST['titre'] && !$_POST['description'] && !$_POST['user_id'] && !$_POST['url'])
{
echo 'Aucunes modification effectué, pas de champs renseigné.'; //alerte
}
$liste = SELF::getCurrentPrivateList();
// stop si aucuns item trouvé
if (!$liste)
{
echo 'Aucune liste trouvé';
exit();
}
echo "Modifications effectuées sur la liste " . $liste->titre;
$liste->titre = strip_tags($_POST['titre']);
$liste->description = strip_tags($_POST['description']);
$liste->save();
}


public static function ajouterMessage($token)
{
$liste = Liste::where('token_private', 'like', $token)->orWhere('token_publique', 'like', $token)->first();
$message = new Message;
$message->no_liste=strip_tags($liste->no);
$message->msg=strip_tags($_POST['message']);
$message->save();
Alerte::set('add_message');
Outils::goTo('../liste/'. $token, 'Message ajouté à la liste');
}



//PARTIE PAGE DE LISTE

//Affiche chaque liste publiques existante avec leur items correspondants
public static function displayAll() {
//Lorsqu'on utilise la recherche de liste
if(isset($_GET["token"])) {
Outils::goTo('liste/'. $_GET['token'], 'Recherche en cours...', 1);
}
else {
$_SESSION['wishlist_liste_token'] = null;
$listes=Liste::where('published', 'like', '1')
->orderBy('expiration', 'asc')
->whereDate('expiration', '>', date('Y-m-d'))
->get();
echo "<h1>Listes de souhaits</h1>"; // HTML CODE titre
if (sizeof($listes) == 0) {
echo 'Aucune liste publique existante';
}

foreach ($listes as $liste) { // Si le token privée d'une liste est dans la variable de session, le lien menera vers la liste en mode édition
echo "<li>";
    if(AUTH::isConnect() && $liste->user_id == $_SESSION['wishlist_userid']) {
    echo '<a href="liste/' . $liste->token_private . '">' . $liste->titre . '</a></br>';
    } else {
    echo '<a href="liste/' . $liste->token_publique . '">' . $liste->titre . '</a></br>';
    }
    echo "</li>";
}
}
}

public static function displayOwnListe() {
if (!AUTH::isConnect()) {
Outils::goTo('compte', "Veuillez vous connecter.");
}

$user = User::where('id', '=', $_SESSION['wishlist_userid'])->first();
if (!$user->listes) {
echo '<h4>Aucunes listes crée pour le moment</h4>';
}

foreach($user->listes as $liste) {
echo '<a href="liste/'. $liste->token_private . '"><h4>' . $liste->titre .'</h4></a>';
if (Outils::listeExpiration($liste->expiration)) {
echo "Etat : expiré";
} else {
echo "Etat : en cours...";
}
}
}

public static function displaySaveListe() {
if (!AUTH::isConnect()) {
Outils::goTo('compte', "Veuillez vous connecter.");
}

Alerte::getErrorAlert('token_empty', "Veuillez insérer un token publique");
Alerte::getWarningAlert('own_token', "Vous ne pouvez pas enregistrer vos propres listes");
Alerte::getErrorAlert('token_error', "Aucune liste correspondant");
Alerte::getWarningAlert('already_save', "Vous ne pouvez pas enregistrer une liste plusieurs fois");
Alerte::getSuccesAlert('liste_save', "Liste enregistré");
Alerte::getWarningAlert('liste_unsave', "Liste oublié");

Formulaire::saveListe();
echo '<hr>';

$save_listes = Save_liste::select('no_liste')
->where('user_id', '=', $_SESSION['wishlist_userid'])
->get();

if (sizeof($save_listes) == 0) {
echo '<h4>Aucunes listes enregistré pour le moment</h4>';
}

foreach ($save_listes as $save_liste) {
$liste = Liste::where('no', '=', $save_liste->no_liste)->first();
echo '<a href="liste/'. $liste->token_publique . '"><h4>' . $liste->titre .'</h4></a>';

if ($liste->user) echo '<p>Createur : ' . $liste->user->email . '</p>';

if (Outils::listeExpiration($liste->expiration)) echo "Etat : expiré";
else echo "Etat : en cours...";

SELF::unsaveListeButton($liste->token_publique);
}
}

public static function saveListe() {
if (!Outils::checkSession(array('wishlist_userid'))) {
Outils::goTo('index.php', 'Redirection en cours..');
}
if (!Outils::checkPost(array('token'))) {
Alerte::set('token_empty');
Outils::goTo('saveliste', 'Redirection en cours..');
exit();
}

$liste = Liste::where('user_id', '=', $_SESSION['wishlist_userid'])
->where('token_publique', '=', $_POST['token'])
->first();
if ($liste) {
Alerte::set('own_token');
Outils::goTo('saveliste', 'Redirection en cours..');
exit();
}

$liste = Liste::where('token_publique', '=', $_POST['token'])
->first();
if (!$liste) {
Alerte::set('token_error');
Outils::goTo('saveliste', 'Redirection en cours..');
exit();
}

$save_liste = Save_liste::where('user_id', '=', $_SESSION['wishlist_userid'])
->where('no_liste', '=', $liste->no)
->first();
if ($save_liste) {
Alerte::set('already_save');
Outils::goTo('saveliste', 'Redirection en cours..');
exit();
}

$save_liste = new Save_liste();
$save_liste->user_id = $_SESSION['wishlist_userid'];
$save_liste->no_liste = $liste->no;
$save_liste->save();

Alerte::set('liste_save');
Outils::goTo('saveliste', 'Redirection en cours..');
}

public static function unsaveListeButton($token) {
echo '
<form action="saveliste-remove/' . $token . '" method="POST">
    <button type="submit" class="alert button">
        <div class ="row">
            <div class="columns small-2 fi-trash"></div>
            <div class="columns">Oublier la liste</div>
        </div>
    </button>
</form>';
}

public static function unsaveListe($token) {
if (!Outils::checkSession(array('wishlist_userid'))) {
Outils::goTo('../index.php', 'Redirection en cours..');
}

$liste = Liste::select('no')->where('token_publique', '=', $token)->first();
if (!$liste) {
Outils::goTo('../index.php', "Redirection en cours..");
}

$save_liste = Save_liste::where('user_id', '=', $_SESSION['wishlist_userid'])
->where('no_liste', '=', $liste->no)
->first();

if (!$save_liste) {
Outils::goTo('../index.php', "Redirection en cours..");
}

$save_liste->delete();
Alerte::set('liste_unsave');
Outils::goTo('../saveliste', 'Redirection en cours..');
}

//Affiche une liste particulière, gére la modification de la liste si..
//..token_private dans la variable de session
public static function liste($token) {
$liste = Liste::where('token_private', 'like', $token)->first();

//Si la liste n'existe pas
if (!$liste) {
$liste = Liste::where('token_publique', 'like', $token)->first();
if (!$liste) {
Alerte::set('list_missing');
Outils::goTo('../index.php', 'Aucune liste trouvée');
exit();
} else {
$_SESSION['wishlist_liste_token'] = $liste->token_publique;
}
} else {
$_SESSION['wishlist_liste_token'] = $liste->token_private;
//Bouton permettant de basculer entre privée et publique
SELF::boutonPublication();
}

//Affiche l'ensemble des items pour chaque liste
$itemlist=$liste->item;
$messlist=$liste->message;
echo "<h1>Nom de la liste : " . $liste->titre . "</h1>";
if($liste->description)
echo "<h2>Description : " . $liste->description. "</h2>";

//Affiche l'expiration de la liste
if(Outils::listeExpiration($liste->expiration))
echo '<h2>La liste est expirer.</h2>';
else
echo '<h2>Expiration de la liste : ' . $liste->expiration . '</h2>';

echo "<ul>"; // HTML CODE debut liste

    foreach($itemlist as $item) {
    SELF::affichageItemListe($item);
    if($liste->token_private == $_SESSION['wishlist_liste_token']){
    echo '<form action="../delete-item/'. $item->nom .'" method="POST">
        <button class="button tiny" type="submit">Supprimer l`item</button>
    </form>';
    }

    }
    SELF::affichageMsgListe($liste);

    echo "</ul>"; // HTML CODE fin liste

//Ajout d'un message dans la liste
echo 'Ajouter un message à la liste</br>
<form action="../add-mess/'. $token .'" method="post">
    <p>Message : <input type="text" name="message" /></p>
    <p><input class="button" type="submit" name="Poster" value="Poster"></p>
</form></br>';

//Si le tokenprivé est renseigner, on peut modifier la liste et ajouter des items
if($liste->token_private == $_SESSION['wishlist_liste_token']) {
//Aucun ajout d'item ou de modification de liste si elle est expirer
if(!Outils::listeExpiration($liste->expiration)){
echo 'Modification de la liste</br>
<form action="../edit-liste/'. $token .'" method="post">
    <p>Titre : <input type="text" name="titre" /></p>
    <p>Description : <br/><input type="text" name="description" /></p>
    <p><input class="button" type="submit" name="Modifier" value="Modifier"></p>
</form></br>
Ajout d un item dans votre liste';
Formulaire::ajoutItem();
}

//Partage de la liste via le token
echo '<h3>Token de partage<h3>
        <input type="text" value="'. $liste->token_publique .'" id="publicListe">
        <button class="button" id="bouttonCopie">Copier le lien de la liste</button>
        <script>
            function copyListeLink() {
                var copyText = document.getElementById("publicListe");
                copyText.select();
                document.execCommand("copy");
            }
            let copy = document.getElementById("bouttonCopie");
            copy.addEventListener("click", copyListeLink);
        </script>';
        echo '<h3>Token du créateur<h3>
                <input type="text" value="'. $liste->token_private .'" id="privListe">
                <button class="button" id="bouttonCopie">Copier le lien de la liste</button>
                <script>
                    function copyListeLink() {
                        var copyText = document.getElementById("privListe");
                        copyText.select();
                        document.execCommand("copy");
                    }
                    let copy = document.getElementById("bouttonCopie");
                    copy.addEventListener("click", copyListeLink);
                </script>';
                }
                }

                //Affiche les message de la liste
                public static function affichageMsgListe($liste){
                $messlist=$liste->message;
                echo "<h2>Message : </h2>";
                foreach ($messlist as $message) {
                echo '- ' . $message->msg . '<br/>';
                }
                }

                public static function affichageItemListe($item){
                echo '<li>
                    Nom de l\'objet : <a href="../item/'. $item->nom .'">'. $item->nom .'</a>
                    <br/>Description : '. $item->descr . '<br/>
                </li>';
                }


                public static function getCurrentPrivateList(){
                $list = Liste::where('token_private', 'like', $_SESSION['wishlist_liste_token'])
                ->first();
                return $list;
                }

                public static function getCurrentPublicList(){
                $list = Liste::where('token_publique', 'like', $_SESSION['wishlist_liste_token'])
                ->first();
                return $list;
                }

                public static function returnBouton() {
                echo '<a href="../liste/' . $_SESSION['wishlist_liste_token'] . '" class="button">Retour à la liste</a>';
                }

                public static function boutonPublication(){
                $liste = SELF::getCurrentPrivateList();
                $token = $_SESSION['wishlist_liste_token'];
                if($liste->published == true) {
                echo '<form action="../liste-published/'. $token .'" method="post">
                    <button class="button" type="submit">Rend la liste privée</button>
                </form>';
                } else {
                echo '<form action="../liste-published/'. $token .'" method="post">
                    <button class="button" type="submit">Rend la liste publique</button>
                </form>';
                }
                }
                }