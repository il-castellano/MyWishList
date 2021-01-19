<?php


namespace wishlist\pages;

use wishlist\divers\Bouton as BTN;
use wishlist\divers\Formulaire as FORM;
use wishlist\divers\Outils;

use wishlist\fonction\Authentification as AUTH;
use wishlist\fonction\Compte;


class PageCompte {

    public static function displayCompte() {

        if (!AUTH::isConnect()) {
            Outils::goTo('auth-connexion', 'Redirection en cours...');
        } else if (AUTH::isConnect()) {
            if (isset($_SESSION['compte_action']) && $_SESSION['compte_action']) {
                switch ($_SESSION['compte_action']) {
                    case "edit":
                        $_SESSION['compte_action'] = null;
                        Compte::compteEdit();
                        break;
                    case "change_password":
                        $_SESSION['compte_action'] = null;
                        AUTH::passwordEdit();
                        break;
                }
            }
            Compte::compteDetails();
            FORM::compteEdit();
            FORM::passwordEdit();
            BTN::compteDelete();
        }
    }

}