<?php


namespace wishlist\fonction;

use Cartalyst\Sentinel\Native\Facades\Sentinel;
use wishlist\divers\Outils;

use wishlist\modele\User;


class Authentification {

    public static function Identification() {
        if (SELF::isConnect()) {
            $user = Sentinel::findById($_SESSION['wishlist_userid']);

            if ($user) return $user;
        }
        return null;
    }

    public static function Connection() {
        if (!Outils::checkPost(array('username', 'password'))) {
            Alerte::set('field_missing');
            Outils::goTo('auth-connexion', "Un ou plusieurs champs requis sont vide");
            exit();
        }

        $username = strip_tags($_POST['username']);
        $password = strip_tags($_POST['password']);
        $credentials = [
            'email'    => $username,
            'password' => $password,
        ];
        $user = Sentinel::authenticate($credentials);
        if (!$user) {
            Alerte::set('authentification_fail');
            Outils::goTo('auth-connexion', 'Erreur authentification');
        } else {
            $_SESSION["wishlist_userid"] = $user->id;
            $_SESSION["wishlist_username"] = $user->email;
            Outils::goTo('index.php', 'Authentification reussi, Redirection en cours..');
        }
    }

    public static function Inscription() {

        if (!Outils::checkPost(array('username', 'password', 'passwordConf', 'last_name', 'first_name'))) {
            Alerte::set('field_missing');
            Outils::goTo('auth-inscription', "Un ou plusieurs champs requis sont vide");
            exit();
        }

        $username = strip_tags($_POST['username']);
        $password = strip_tags($_POST['password']);
        $passwordConf = strip_tags($_POST['passwordConf']);
        $last_name = strip_tags($_POST['last_name']);
        $first_name = strip_tags($_POST['first_name']);

        if (!SELF::usernameIsConform($username)) {
            Alerte::set('username_invalid');
            Outils::goTo('auth-inscription', "Nom d'utilisateur invalide");
            exit();
        } else if ($password != $passwordConf) {
            Alerte::set('pass_not_match');
            Outils::goTo('auth-inscription', "Nouveaux mot de passe pas identiques");
            exit();
        } else if (!SELF::passwordIsConform($password)) {
            Alerte::set('password_invalid');
            Outils::goTo('auth-inscription', "Mot de passe invalide");
            exit();
        } else if (!SELF::usernameIsUnique($username)) {
            Alerte::set('username_already_existe');
            Outils::goTo('auth-inscription', "Nom d'utilisateur déjà utilisé");
            exit();
        } else if (!SELF::nameIsValide($last_name) && !SELF::nameIsValide($first_name)) {
            Alerte::set('nom_invalide');
            Outils::goTo('auth-inscription', "Nom ou prénom invalide");
            exit();
        } else {
            Sentinel::registerAndActivate([
                'email'    => $username,
                'password' => $password,
                'last_name' => $last_name,
                'first_name' => $first_name,
            ]);
            Alerte::set('user_signup');
            Outils::goTo('auth-connexion', 'Compte crée ! Veuillez vous authentifier.');
        }
    }

    public static function Deconnection() {
        $_SESSION["wishlist_userid"] = null;
        Outils::goTo('index.php', 'Deconnecté. Redirection en cours..');
    }

    public static function passwordEdit() {
        if (!Outils::checkPost(array('oldPassword', 'newPassword', 'newPasswordConf'))) {
            Alerte::set('field_missing');
            Outils::goTo('compte', "Un ou plusieurs champs requis sont vide");
            exit();
        }

        $oldPassword = strip_tags($_POST['oldPassword']);
        $newPassword = strip_tags($_POST['newPassword']);
        $newPasswordConf = strip_tags($_POST['newPasswordConf']);

        if ($newPassword != $newPasswordConf) {
            Alerte::set('pass_not_match');
            Outils::goTo("compte", "Nouveaux mot de passe pas identiques");
            exit();
        } else if (!SELF::passwordIsConform($newPassword)) {
            Alerte::set('password_invalid');
            Outils::goTo("compte", "Mot de passe invalide");
            exit();
        }

        $user = Sentinel::findById($_SESSION['wishlist_userid']);

        $hasher = Sentinel::getHasher();
        if (!$hasher->check($_POST['oldPassword'], $user->password)) {
            Alerte::set('authentification_fail');
            Outils::goTo("compte", "Mot de passe érroné");
            exit();
        }
        Sentinel::update($user, array('password' => $newPassword));
        $_SESSION["wishlist_userid"] = null;
        Alerte::set('password_change');
        Outils::goTo('auth-connexion', 'Mot de passe modifié ! Veuillez vous réauthentifier');
    }

    public static function isConnect() {
        if (isset($_SESSION["wishlist_userid"]) && $_SESSION["wishlist_userid"] != null) {
            return true;
        } else {
            return false;
        }
    }

    public static function deleteUser() {
        $user = Sentinel::findById($_SESSION['wishlist_userid']);
        $user->delete();
        $_SESSION["wishlist_userid"] = null;
    }

    public static function usernameIsConform($username) {
        $size = strlen($username);
        if (($size < 3 || $size > 20) || (preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $username))) {
            return false;
        }
        return true;
    }

    public static function passwordIsConform($password) {
        $size = strlen($password);
        if ($size < 6 && $size > 30) {
            return false;
        }
        if (!preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $password)) {
            return false;
        }
        return true;
    }

    public static function usernameIsUnique($username) {
        $user = User::select('id')
            ->where('email', 'like', $username)
            ->first();

        if ($user) {
            return false;
        }
        return true;
    }

    public static function nameIsValide($name) {
        if (!$name && $name == "") {
            return false;
        }
        return true;

    }

}