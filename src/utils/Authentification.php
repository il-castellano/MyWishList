<?php

namespace mywishlist\utils;

require_once 'vendor/autoload.php';
use mywishlist\controleurs\ControleurCompte;
use mywishlist\models\Compte;
use ZxcvbnPhp\Zxcvbn as PasswordChecker;


class Authentification {

    /**
     * Créer un compte et l'enregistre dans la base de donnée.
     * @param $username string Username du compte.
     * @param $password string Mot de passe du compte.
     * @param $pseudo string Pseudo du compte.
     * @return string "Code de retour" : soit "succes" soit un message d'erreur.
     */
    public static function creerCompte($username, $password, $pseudo):string {
        if (self::compteExiste($pseudo, $username))
            return 'Un compte avec le même login/pseudo existe déjà';

        if (strlen($pseudo) < 4)
            return 'Le pseudo est trop court (4 caractères au minimum)';

        if (strlen($username) < 4)
            return 'Le nom de compte est trop court (4 caractères au minimum)';

        //---------------- Vérifie la force du mot de passe ----------------\\
        $passChecker = new PasswordChecker();
        $force = $passChecker->passwordStrength($password, array($username));

        if ($force['score'] < 1)
            return 'Veuillez entrer un mot de passe plus sécurisé';

        //---------------- Vérifie la force du mot de passe ----------------\\

        $hash = password_hash($password, PASSWORD_DEFAULT, ['cost'=> 12]);

        $compte = new Compte();
        $compte->username = $username;
        $compte->password = $hash;
        $compte->role = 'user';
        $compte->pseudo = $pseudo;
        $compte->save();

        return 'succes';
    }

    /**
     * Tente de se connecter = créer une variable de session 'user_connected' contenant les infos de l'user.
     * @param $username string Username
     * @param $password string Mot de passe
     * @return string Status de retour (succes/password_incorrect/login_incorrect)
     */
    public static function connexion($username, $password):string {
        $user = Compte::where('username', '=', $username)->first();

        //Si le login est bon
        if (isset($user)) {
            //Si le mot de passe est aussi bon
            if (password_verify($password, $user->password)) {
                self::loadProfile($user);
                return 'succes';
            }

            return 'password_incorrect';
        }
        return 'login_incorrect';
    }

    /**
     * Seulement appeler si on a DEJA VERIFIE les identifiants.
     * Créer une variable de session 'user_connected' contenant les informations de l'user.
     * @param $user Compte déjà vérifié.
     */
    private static function loadProfile($user) {
        unset($_SESSION['user_connected']);
        $_SESSION['user_connected'] = array('username' => "$user->username",
                                            'role' => "$user->role",
                                            'pseudo' => "$user->pseudo");
    }

    public static function checkAccessRights($required) {
        //TODO pour check les droits en fonction du rôle.
    }

    /**
     * Vérifie si le compte existe déjà dans la BDD.
     * @param $pseudo string Pseudo du compte.
     * @param $username string Nom de compte. (laisser vide si on a juste besoin de vérifier le pseudo)
     * @return bool true si le compte existe déjà.
     */
    private static function compteExiste($pseudo, $username = null):bool {
        //Check sur l'username
        $alreadyExist = false;
        if (isset($username))
            $alreadyExist = Compte::where('username', '=', $username)->first() != null;

        //Si l'username n'est pas déjà utilisé vérifie le pseudo
        if (!$alreadyExist)
            $alreadyExist = Compte::where('pseudo', '=', $pseudo)->first() != null;

        return $alreadyExist;
    }

    /**
     * Modifie les informations du compte connecté.
     * @param $newPseudo string Nouveau pseudo.
     * @param $newPass string Nouveau mot de passe.
     * @return string "Code de retour" : soit "succes" soit un message d'erreur.
     */
    public static function modifierInformations(string $newPseudo, string $newPass):string {
        $username = $_SESSION['user_connected']['username'];
        $compte = Compte::where('username', '=', $username)->first(); //récupère le compte dans la BDD
        $status = 'mdpNonModifie';

        //Si aucune info n'a été modifiée c'est inutile de continuer.
        if ($newPseudo == '' && $newPass == '')
            return 'Aucune information modifiée.';

        //Modifie le pseudo
        if ($newPseudo != '') {
            if (strlen($newPseudo) < 4)
                return 'Le pseudo est trop court (4 caractères au minimum)';

            if (self::compteExiste($newPseudo))
                return 'Un compte avec le même pseudo existe déjà';

            $compte->pseudo = $newPseudo;
        }

        //Modifie le mot de passe
        if ($newPass != '') {
            //---------------- Vérifie la force du mot de passe ----------------\\
            $passChecker = new PasswordChecker();
            $force = $passChecker->passwordStrength($newPass, array($username));

            if ($force['score'] < 1)
                return 'Veuillez entrer un mot de passe plus sécurisé';
            //---------------- Vérifie la force du mot de passe ----------------\\

            $hash = password_hash($newPass, PASSWORD_DEFAULT, ['cost' => 12]);

            $compte->password = $hash;
            $status = 'mdpModifie';
        }

        $compte->save();
        self::loadProfile($compte);

        return $status;
    }
}