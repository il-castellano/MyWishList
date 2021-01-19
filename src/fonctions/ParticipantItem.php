<?php


namespace wishlist\fonction;

use wishlist\modele\User;
use wishlist\modele\Item;
use wishlist\modele\Liste;
use wishlist\modele\Reservation;

use wishlist\fonction\Alerte;
use wishlist\fonction\FctnCagnotte as CG;

use wishlist\divers\Formulaire as FORM;
use wishlist\divers\Outils;


class ParticipantItem {

    public static function itemDetails ($item) {
        if ($item->reservation == 0) $reservation_state = 'non';
        else $reservation_state = 'oui';

        echo '
		<div class= "row column align-center medium-6 large-4">
			<div class="card-flex-article card">';
        Alerte::getSuccesAlert("item_reserve", "Item reservé !");

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
				<div class="card-divider align-middle">
					<br/>Reservé : ' . $reservation_state . '
				</div>
			</div>
		</div>';

        $liste = Liste::select('expiration')
            ->where('token_publique', '=', $_SESSION['wishlist_liste_token'])
            ->first();
        if (!Outils::listeExpiration($liste->expiration)) {
            if($item->reservation == 0 && $item->cagnotte == 0) FORM::itemReserve($item->nom);
            else if ($item->reservation == 0 && $item->cagnotte == 1) {
                CG::addCagnotteForm($item->nom);
            }
        }

    }

    public static function itemReserve ($item)
    {
        $item->reservation = 1;
        $item->participant_name = strip_tags($_POST['name']);
        $item->message = strip_tags($_POST['message']);
        $item->save();
        Alerte::set('item_reserve');
    }

}