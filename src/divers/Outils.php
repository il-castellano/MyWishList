<?php


namespace wishlist\divers;


namespace wishlist\divers;

use wishlist\fonction\Authentification as AUTH;

class Outils
{

    public static function getArbo() {
        $arbo = '/MyWishList/'; // indiquer le chemin depuis la source du serveur apache jusqu'au fichier index.php
        return $arbo;
    }

    public static function headerHTML($title) {
        $arbo = SELF::getArbo();
        echo
            '<!DOCTYPE html>
            <html lang=\"fr\">
            <head>
                <meta charset=\"UTF-8\">
                <title>'.$title.'</title>
                <link href="' . $arbo .'src/css/foundation.css" rel="stylesheet" type="text/css">
				<link href="' . $arbo .'src/css/foundation-icons.css" rel="stylesheet"/>
				<link href="' . $arbo .'src/css/style.css" rel="stylesheet" type="text/css">
            </head>
            <body>';
    }

    public static function menuHTML() {
        $arbo = SELF::getArbo();
        echo
            '<div data-sticky-container>
  			<div class="title-bar" data-sticky data-options="marginTop:0;" style="width:100%">
		    	<div class="title-bar-left">
					<a class="item" href="' . $arbo .'"><i class="fi-home"></i> Accueil</a>';

        if (!AUTH::isConnect()) {
            echo '<ul class="dropdown menu" data-dropdown-menu>
					<li>
						<a href="">Liste</i></strong></a>
						<ul class="menu">
							<li><a href="' . $arbo .'add-liste-form">Créer une liste</a></li>
						</ul>
					</li>
				</ul>';
        } else {
            echo '
				<ul class="dropdown menu" data-dropdown-menu>
					<li>
						<a href="">Liste</i></strong></a>
						<ul class="menu">
							<li><a href="' . $arbo .'myliste">Mes listes</a></li>
							<li><a href="' . $arbo .'saveliste">Listes enregistrée</a></li>
							<li><a href="' . $arbo .'add-liste-form">Créer une liste</a></li>
						</ul>
					</li>
				</ul>';
        }

        echo 	'</div>
		        <div class="title-bar-center">
		          <span class="title-bar-title">MyWishList</span>
		        </div>
		    	<div class="title-bar-right"> ';

        if (!AUTH::isConnect()) {
            echo '<a href="' . $arbo .'auth-connexion">Connexion</a>';
        } else {
            echo '
				<ul class="dropdown menu align-right" data-dropdown-menu>
					<li>
						<a href="">Connecté en tant que <strong><i>' . $_SESSION['wishlist_username'] . '</i></strong></a>
						<ul class="menu">
							<li><a href="' . $arbo .'compte">Mon Compte</a></li>
							<li><a href="' . $arbo .'deconnection">Deconnexion <i class="step fi-power size-24"></i></a></li>
						</ul>
					</li>
				</ul>';
        }

        echo '</div>
  			</div>
		</div>
		<div class="app">';
    }

    public static function footerHTML() {
        $arbo = SELF::getArbo();
        echo '
				</div>
			<script src="' .$arbo. 'src/js/jquery.min.js"></script>
	    	<script src="' .$arbo. 'src/js/what-input.js"></script>
	    	<script src="' .$arbo. 'src/js/foundation.min.js"></script>
	    	<script src="' .$arbo. 'src/js/app.js"></script>
		</body></html>';
    }

    public static function goTo($link, $message, $time=0) {
        echo '
		<div class= "row column align-center medium-6 large-6">
			<h4>' . $message . '</h4>
		</div>';
        header('Refresh: ' . $time . '; url='. $link);
    }

    public static function generateToken() {
        return base_convert(hash('sha256', time() . mt_rand()), 16, 36);
    }

    public static function listeExpiration($date_expiration) {
        $date = date('Y-m-d');
        if (date($date_expiration) < date($date)) return true; // si la date expiration est passé
        else return false; // si la date expiration n'est pas passé
    }

    public static function checkSession($session_name_tab) {
        foreach ($session_name_tab as $session_name) {
            if (!isset($_SESSION[$session_name]) || !$_SESSION[$session_name])
                return false;
        }
        return true;
    }

    public static function checkPost($post_name_tab) {
        foreach ($post_name_tab as $post_name) {
            if (!isset($_POST[$post_name]) || !$_POST[$post_name])
                return false;
        }
        return true;
    }

    public static function clearSession($session_name_tab) {
        foreach ($session_name_tab as $session_name)
            $_SESSION[$session_name];
    }

}