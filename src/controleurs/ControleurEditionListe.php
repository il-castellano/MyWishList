<?php


namespace mywishlist\controleurs;


use mywishlist\models\Item;
use mywishlist\models\Liste;
use mywishlist\vues\VueEditionCreationListe;

class ControleurEditionListe
{

    public static function afficherCreerListe(){
        $vue = new VueEditionCreationListe();
        $vue->afficher('creationListe');
    }

    public static function afficherEditerListe(){
        $vue = new VueEditionCreationListe();
        $vue->afficher('edition');
    }

    public static function afficherCreerItem(){
        $vue = new VueEditionCreationListe();
        $vue->afficher('creationItem');
    }

    public static function creerListe(){
        if (isset($_POST['date']) && $_POST['date'] != ''){
            $date = $_POST['date'];
            if (date('Y-m-d') >= $date)
                return;
        }
        else{
            $date = \DateTime::createFromFormat('d/m/Y', '31/12/2099');
        }

        if (isset($_POST['liste_name']) && $_POST['liste_name'] != ''){
            $nom = filter_var($_POST['liste_name'],FILTER_SANITIZE_SPECIAL_CHARS);
        }
        else{
            $nom = 'Liste sans nom';
        }

        //vérifie si une liste porte déjà ce nom
        $listeExistante = Liste::where('titre', '=', $nom)->first();
        if (isset($listeExistante)) {
            //$vue = new VueEditionCreationListe();
            //vue->afficher('creationListe');
            //TODO afficher erreur
            return;
        }

        if (isset($_POST['liste_desc']) && $_POST['liste_desc'] != ''){
            $desc = filter_var($_POST['liste_desc'],FILTER_SANITIZE_SPECIAL_CHARS);
        }
        else{
            $desc = '';
        }

        if (isset($_POST['private'])){
            $private = 1;
        }
        else{
            $private = 0;
        }

        $liste = new Liste();

        //Lie la liste à son créateur
        if (isset($_SESSION['user_connected']))
            $liste->createur_pseudo = $_SESSION['user_connected']['pseudo'];

        $liste->titre = $nom;
        $liste->description = $desc;
        $liste->expiration = $date;
        $token = "";
        try {
            $token = bin2hex(random_bytes(5));
        } catch (\Exception $e) {

        }
        $liste->token = $token;
        $liste->private = $private;


        $cookie = [];
        if (isset($_COOKIE['created'])){
            $cookie = unserialize($_COOKIE['created']);
            array_push($cookie,$token);
            $cookie = serialize($cookie);
        }
        else{
            $cookie = serialize([$token]);
        }

        setcookie('created',$cookie,time()+60*60*24*365);


        $liste->save();

        //redirection vers la page d'affichage de la liste
        header("Location: ./$liste->token");
        exit(0);
    }

    public static function ajouterItem($token_item){
        $liste = Liste::where('token', '=', $token_item)->first();
        $token = $liste['token'];
        if (isset($_COOKIE['created'])) {
            $created = unserialize($_COOKIE['created']);
            if (in_array($token, $created)) {
                if (isset($_POST['nom']) && $_POST['nom'] != '') {
                    $nom = filter_var($_POST['nom'], FILTER_SANITIZE_SPECIAL_CHARS);
                } else {
                    $nom = 'Cadeau surprise';
                }

                if (isset($_POST['desc']) && $_POST['desc'] != '') {
                    $desc = filter_var($_POST['desc'], FILTER_SANITIZE_SPECIAL_CHARS);
                } else {
                    $desc = '';
                }

                if (isset($_POST['prix']) && $_POST['prix'] != ''){
                    $prix = filter_var($_POST['prix'], FILTER_SANITIZE_SPECIAL_CHARS);
                }
                else {
                    $prix = 0;
                }

                if (isset($_POST['image'])) {
                    $image = filter_var($_POST['image'], FILTER_SANITIZE_SPECIAL_CHARS);
                } else {
                    $image = NULL;
                }

                if (isset($_POST['url_image'])) {
                    $url_image = filter_var($_POST['url_image'], FILTER_SANITIZE_SPECIAL_CHARS);
                } else {
                    $url_image = NULL;
                }

                $item = new Item();
                $item->nom = $nom;
                $item->descr = $desc;
                $item->img = $image;
                $item->url = $url_image;
                $item->tarif = $prix;
                $item->tokenListe = $token_item;

                $item->save();
            }
        }
    }

    public static function supprimerItem($token,$id_item){
        $item = Item::where('id','=',$id_item)->first();
        if(isset($_COOKIE['created']) && $item != null){
            $item->delete();
        }
        ControleurListes::getAllItems($token);
    }

}