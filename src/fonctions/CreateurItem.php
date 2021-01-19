<?php


namespace wishlist\fonction;

use wishlist\divers\Outils;

use wishlist\fonction\Alerte;

use wishlist\modele\Item;
use wishlist\modele\Liste;


class CreateurItem {

    public static function itemDetails($item) {
        echo '
		<div class= "row column align-center medium-6 large-4">
			<div class="card-flex-article card">';

        if ($item->img) {
            echo'
				<div class="card-image">
					<img src="../' . $item->img .'">
				</div>';
        }
        echo '
			<div class="card-section">
				<h3 class="article-title">' . $item->nom . '</h3>
				<p class="article-summary">' . $item->descr . '</p>
				<p class="article-summary">Prix : ' . $item->tarif . '€</p>
			</div>
			<div class="card-divider align-middle">';
        if (Outils::listeExpiration($item->liste->expiration))
        {
            echo 'Reservation : ';
            if ( $item->reservation == 0) {
                echo 'Non reservé';
            }
            else if ($item->cagnotte == 0){
                echo 'Reservé par '. $item->participant_name;
                if($item->mesage){
                    echo ' son message '. $item->message;
                }
            }
            else if ($item->cagnotte == 1){
                echo 'Reservé par cagnotte
							<ul>';
                $contribution = $item->cagnottes;
                foreach ($contribution as $key) {
                    echo '<li>'.$key->name. ' a contribué à une hauteur de '. $key->montant . '€';
                }
                echo '</ul>';
            }

        } else {
            echo '<p>Veuillez attendre l\'expiration de la liste</p>';
        }
        echo '
				</div>
			</div>
		</div>';
    }


    public static function itemAdd() {
        if (SELF::itemVerify()) {
            $liste = Liste::select('no')
                ->where('token_private', 'like', $_SESSION['wishlist_liste_token'])
                ->first();

            $item = new Item();
            $item->liste_id = $liste->no;
            $item->nom = strip_tags($_POST['nom']);
            $item->descr = strip_tags($_POST['descr']);
            $item->tarif = strip_tags($_POST['tarif']);
            $item->token_private = Outils::generateToken();
            $item->save();
            Alerte::set('item_added');
        }
        Outils::goTo(Outils::getArbo(). 'liste/' . $_SESSION['wishlist_liste_token'], 'Retour a la liste');
    }

    public static function itemEdit($item) {
        if ($_POST['nom'] && $_POST['nom'] != '') $item->nom = strip_tags($_POST['nom']);
        if ($_POST['descr'] && $_POST['descr'] != '') $item->descr = strip_tags($_POST['descr']);
        if ($_POST['tarif'] && $_POST['tarif'] != '') $item->tarif = strip_tags($_POST['tarif']);
        if ($_POST['url'] && $_POST['url'] != '') $item->url = strip_tags($_POST['url']);
        $item->save();
    }

    public static function itemDelete($item) {
        $item->delete();
        Outils::goTo('../liste/' . $_SESSION['wishlist_liste_token'], 'Item supprimé, retour a la liste', 1);
        exit();
    }

    public static function itemVerify() {
        // erreur si un champ requis vide
        if (!$_POST['nom'] || !$_POST['descr'] || !$_POST['tarif']) {
            Alerte::set('empty_field');
            return false;
        }

        // erreur si token invalide
        $liste = Liste::select('no')
            ->where('token_private', 'like', $_SESSION['wishlist_liste_token'])
            ->first();
        if (!$liste) {
            Alerte::set('liste_not_found');
            return false;
        }

        // erreur si un item avec le même nom existe deja
        $test_item = Item::where('nom', 'like', $_POST['nom'])
            ->where('liste_id', "=", $liste->no)
            ->first();
        if ($test_item) {
            Alerte::set('already_exists');
            return false;
        }

        if (!is_numeric($_POST['tarif'])) {
            Alerte::set('invalide_price');
            return false;
        }
        return true;
    }

}