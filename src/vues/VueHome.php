<?php

namespace mywishlist\vues;

class VueHome {

    private $app;

    /**
     * VueHome constructor.
     */
    public function __construct() {
        $this->app = \Slim\Slim::getInstance() ;
    }

    public function afficherDefaultHome() {
        VueHeaderFooter::afficherHeader('home', 'home.css');

        echo
        <<<END
             <header class="main">
              <div class="container h-75">
                <div class="row h-100 align-items-center">
                  <div class="col-12 text-center">
                    <h1 class="font-weight-light">MyWishList</h1>
                    <p class="lead"><i>Par Mayer Gauthier, Mohammed Kesseiri et Thommet Sacha</i></p>
                    <h2>TODO remplir la page</h2>
                  </div>
                </div>
              </div>
            </header>
END;

        VueHeaderFooter::afficherFooter();
    }
}