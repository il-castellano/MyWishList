<?php


namespace wishlist\fonction;

use wishlist\modele\Item;
use wishlist\modele\Liste;
use wishlist\modele\Cagnotte;

use wishlist\fonction\FctnListe as FL;

use wishlist\divers\Outils;
class FctnCagnotte{

    public static function setCagnotte($item_name){
        $liste = FL::getCurrentPrivateList();
        $item = Item::where('nom', 'like', $item_name)->where('liste_id', '=', $liste->no)->first();


        if($item->cagnotte == 0) {
            $item->cagnotte = 1;
            $item->save();
            echo 'Cagnotte crée pour '. $item_name.' ! </br>';
        }
        else {
            echo "Cagnotte déjà crée pour cette item </br>";
        }
        echo '<a href="/MyWishList/item/' . $item_name .'">Retour à l`item</a>';
    }

    public static function addCagnotteForm($item_name){
        $liste = FL::getCurrentPublicList();
        $item = Item::where('nom', 'like', $item_name)->where('liste_id', '=', $liste->no)->first();
        $cagnotte = Cagnotte::where('item_id', '=', $item->id)->first();

        if(SELF::calculPrixRestant($item) > 0)
        {
            echo '<h3>Ajout participation</h3>
            <form action="../add-cagnotte/' . $item_name . '" method="post">';
            if($cagnotte){
                echo $item->tarif - SELF::calculPrixRestant($item) . '€ on déjà été cotiser, il reste : '. SELF::calculPrixRestant($item). '€ à payer.';
            }
            if(!isset($_SESSION['wishlist_userid']))
                echo '<p><input type="text" name="name" placeholder="Nom" required/></p>';

            echo   '<p><input type="number" name="montant" max="'.SELF::calculPrixRestant($item).'" placeholder="Montant..." required/></p>
              <p><textarea type="text" name="message" placeholder="Laissez votre message..."/></textarea></p>
              <p><input type="submit" name="Make a present"></p>
            </form>';
        }
        else {
            echo "La cagnotte est terminé.";
        }

    }

    public static function addCagnotte($item_name){
        $liste = FL::getCurrentPublicList();
        if($liste){
            $item = Item::where('nom', 'like', $item_name)->where('liste_id', '=', $liste->no)->first();
            $participation = Cagnotte::where('item_id', '=', $item->id)->where('name', 'like', $_POST['name'])->first();
            if(!$participation){
                $cagnotte = new Cagnotte();
                $cagnotte->item_id = $item->id;
                if(isset($_SESSION['wishlist_userid'])){
                    $cagnotte->user_id = strip_tags($_SESSION['wishlist_userid']);
                    $user = User::where('id', 'like', $_SESSION['wishlist_userid']);
                    $cagnotte->name = strip_tags($user->email);
                }
                else {
                    $cagnotte->name = strip_tags($_POST['name']);
                }
                $cagnotte->montant = strip_tags($_POST['montant']);
                $cagnotte->message = strip_tags($_POST['message']);
                $cagnotte->save();
                echo 'Participation effectué !';
            }
            else
                echo "Participation déjà effectué ! </br>";
            echo '<a href="/MyWishList/item/' . $item_name .'">Retour à l`item</a>';
            if(SELF::calculPrixRestant($item) == 0)
            {
                $item->reservation == 1;
                $item->save();
            }
        }
        else {
            echo "Erreur, veuillez ressayer !";
        }
    }

    public static function calculPrixRestant($item){
        $participation = $item->cagnottes;
        $cotiser = 0;
        foreach ($participation as $cagn) {
            if($cagn->item_id == $item->id)
                $cotiser += $cagn->montant;
        }
        return $item->tarif - $cotiser;
    }


    public static function boutonDel($item_name){
        echo '
      <div class= "row column align-center medium-6 large-4">
        <a class="button" href="/MyWishList/del-cagnotte/' . $item_name .'">Supprimer la cagnotte</a>
      </div>';
    }

    public static function boutonCreate($item_name){
        echo '
      <div class= "row column align-center medium-6 large-4">
        <a class="button" href="/MyWishList/set-cagnotte/' . $item_name .'">Crée une cagnotte</a>
      </div>';
    }
}