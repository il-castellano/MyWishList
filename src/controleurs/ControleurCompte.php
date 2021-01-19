<?php


namespace mywishlist\controleurs;


use mywishlist\models\Compte;
use mywishlist\utils\Authentification;
use mywishlist\vues\VueCompte;

class ControleurCompte {

    public static function pageConnexion() {
        $vue = new VueCompte();
        $vue->afficherPageConnexion();
    }

    /**
     * Tente de se connecter.
     * Affiche une vue affichant le succès de connexion / ou le type de l'erreur.
     * @param $login
     * @param $pass
     */
    public static function connexion($login, $pass) {
        $status = Authentification::connexion($login, $pass);
        $vue = new VueCompte();

        if ($status != 'succes')
            $vue->afficherConnexionAvecInfo($status);
        else
            $vue->afficherPageGestionCompte();
    }

    public static function inscription($login, $pass, $pseudo) {
        $retour = Authentification::creerCompte($login, $pass, $pseudo);
        if ($retour == 'succes')
            ControleurCompte::connexion($login, $pass);
        else { //Sinon afficher le message d'erreur
            $vue = new VueCompte();
            $vue->afficherPageInscription($retour);
        }
    }

    public static function pageInscription() {
        $vue = new VueCompte();
        $vue->afficherPageInscription();
    }

    public static function pageGestionCompte() {
        $vue = new VueCompte();
        $vue->afficherPageGestionCompte();
    }

    public static function deconnexion() {
        if (isset($_SESSION['user_connected'])) unset($_SESSION['user_connected']);
    }

    /**
     * Modifie les informations du compte connecté.
     * Si le mot de passe est modifié, déconnecte le compte.
     * @param $newPseudo string Nouveau pseudo.
     * @param $newPass string Nouveau mot de passe.
     */
    public static function modifierInformations($newPseudo, $newPass) {
        //Vérifie bien qu'un compte est connecté.
        if (!isset($_SESSION['user_connected']))
            return;

        $status = Authentification::modifierInformations($newPseudo, $newPass);
        $vue = new VueCompte();
        switch ($status) {
            case 'mdpNonModifie':
                $vue->afficherGestionCompteAvecInfo($status);
                break;
            case 'mdpModifie':
                self::deconnexion();
                $vue->afficherConnexionAvecInfo($status);
                break;
            default: //Erreur dans ce cas
                $vue->afficherGestionCompteAvecInfo($status);
                break;
        }
    }

    public static function supprimer() {
        if (isset($_SESSION['user_connected'])) {
            $username = $_SESSION['user_connected']['username'];
            $compte = Compte::find($username);
            $compte->delete();
            unset($_SESSION['user_connected']);
        }
    }
}