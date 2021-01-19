<?php

namespace mywishlist\controleurs;

use mywishlist\vues\VueHome;

class ControleurHome {

    public static function default() {
        $vue = new VueHome();
        $vue->afficherDefaultHome();
    }
}