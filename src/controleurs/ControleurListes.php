<?php


namespace mywishlist\controleurs;


use Cassandra\Date;
use mywishlist\models\Item;
use mywishlist\models\Liste;
use mywishlist\models\Reservation;
use mywishlist\vues\VueListes;

class ControleurListes {

    /**
     * Affiche toutes les listes valides et non privées (sauf si le créateur de la liste privée est connecté)
     * @param null $listes Listes à afficher, null si on veut afficher toutes les listes.
     */
    public static function getListes($listes = null) {
        //Récupère l'utilisateur connecté pour afficher ses listes privées
        $userConnected = 'null';
        if (isset($_SESSION['user_connected']))
            $userConnected = $_SESSION['user_connected']['pseudo'];

        $params = ['listes' => []];

        if ($listes == null)
            $listes = Liste::all()->toArray();

        foreach ($listes as $key => $liste){
            //Pour afficher : soit la liste n'est pas privée, soit son créateur est connecté
            if( ($liste['private'] != 1) && (date('Y-m-d') <= $liste['expiration'])) { //N'affiche que les listes encore valides

                $nb_items = Item::all()->where('tokenListe', '=', $liste['token'])->count();
                array_push($params['listes'], ['liste' => $liste, 'nb' => $nb_items]);
            }
            else {
                if (($userConnected == $liste['createur_pseudo'])) {
                    $nb_items = Item::all()->where('tokenListe', '=', $liste['token'])->count();
                    array_push($params['listes'], ['liste' => $liste, 'nb' => $nb_items]);
                }
            }
        }

        self::trierListes($params['listes']);
        $vue = new VueListes($params);
        $vue->afficher("listes");
    }

    /**
     * Trie le tableau de listes pour que les listes privées se retrouvent en premières dans le tableau.
     * Ensuite trie par ordre croissant de dates de validité.
     * @param $array array Tableau de listes.
     */
    private static function trierListes(& $array) {
        usort($array, function ($listeA, $listeB) {
            $a = $listeA['liste']['private']; //Soit 0 (non privée) soit 1 (privée)
            $b = $listeB['liste']['private'];

            if ($b - $a != 0) //Car on priorise l'attribut privé pour le tri
                return $b - $a;
            else {
                $a = $listeA['liste']['expiration'];
                $b = $listeB['liste']['expiration'];

                return $a > $b ? 1 : -1;
            }
        });
    }

    /**
     * Vérifie si l'utilisateur est le créateur de la liste.
     * @param $token_liste string Token de la liste.
     * @return bool true si c'est le créateur, false sinon.
     */
    private static function isCreator($token_liste) {
        $liste = Liste::where('token', '=', $token_liste)->first();

        //test d'abord avec le compte
        if (isset($_SESSION['user_connected']) &&
            $_SESSION['user_connected']['pseudo'] == $liste->createur_pseudo)
                return true;

        //sinon avec le cookie created si l'utilisateur n'est pas connecté
        if (isset($_COOKIE['created'])) {
            $cookie = unserialize($_COOKIE['created']);
            return in_array($token_liste, $cookie);
        }

        return false;
    }

    public static function getAllItems($token_liste) {
        $liste = Liste::where('token', '=', $token_liste)->first();
        $token = $liste->token;
        $items = Item::all()->where('tokenListe','=',$token)->toArray();

        $isCreator = self::isCreator($token_liste);
        if(isset($liste->createur_pseudo))
            $nomCreateur = $liste->createur_pseudo;
        else
            $nomCreateur = 'Anonyme';

        $dateCourante = date('Y-m-d');
        $dateExpiration = $liste->expiration;
        if ($dateCourante > $dateExpiration)
            $expiration = true;
        else
            $expiration = false;

        $vue = new VueListes(['items' => $items, 'creator' => $isCreator, 'titreListe' => $liste->titre,
                                'token_liste' => $token, 'nomCreateur' => $nomCreateur, 'estExpiree' => $expiration]);
        $vue->afficher("liste");
    }

    public static function getItem($id_item) {
        $reserv = Reservation::all()->where('idItem','=',$id_item)->toArray();
        if(sizeof($reserv) != 0){
            foreach ($reserv as $key => $val){
                $reserv = $val;
            }
        }
        else{
            $reserv = NULL;
        }
        $item = Item::all()->find($id_item);
        $vue = new VueListes(['item' => $item, 'token_list' => $item->tokenListe, 'reserve' => $reserv != NULL, 'reservation' => $reserv]);
        $vue->afficher("item");
    }

    /**
     * Affiche les listes correspondantes à la recherche dans la search-bar.
     */
    public static function getListesRecherchees() {
        switch ($_POST['typeRecherche']) {
            case 'auteur':
                $auteur = $_POST['auteur'];

                if ($auteur == 'Anonyme' || $auteur == 'anonyme')
                    self::getListes(Liste::whereNull('createur_pseudo')->get());
                else if ($auteur == '')
                    self::getListes(Liste::get());
                else
                    self::getListes(Liste::where('createur_pseudo', '=', $auteur)->get());
                break;

            case 'date':
                $dateDebut = $_POST['dateDebut'];
                $dateFin = $_POST['dateFin'];
                self::getListes(Liste::whereBetween('expiration', [$dateDebut, $dateFin])->get());
                break;
        }
    }
}