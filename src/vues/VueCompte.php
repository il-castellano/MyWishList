<?php


namespace mywishlist\vues;


class VueCompte {

    private $app;

    /**
     * VueCompte constructor.
     */
    public function __construct() {
        $this->app = \Slim\Slim::getInstance() ;
    }

    public function afficherPageConnexion($status = null) {
        VueHeaderFooter::afficherHeader('login');

        $rootUri = $this->app->request->getRootUri();
        $urlConnexion = $this->app->urlFor('connexion');
        $urlInscription = $this->app->urlFor('form_inscription');

        $html_page =
        <<<END
            <div class="text-center container mt-5" style="max-width: 330px">
                <!-- Formulaire login -->
                <form method="post" action="$urlConnexion">
                    <img class="mb-4" src="$rootUri/img/login.png" alt="" width="120" height="120">
                    <h1 class="h3 mb-3 font-weight-normal">Connexion</h1>                    
                    <input type="text" name="login" class="form-control" placeholder="Nom d'utilisateur" required autofocus>                   
                    <input type="password" name="password" class="form-control" placeholder="Mot de passe" required>
                    <button class="btn btn-success btn-primary btn-block" type="submit">Se connecter</button>                                                                      
                </form>
END;
        //------------------- Ajoute le message de retour si l'utilisateur à tenter de se connecter -------------------\\
        if (isset($status))
            $html_page = $html_page . $status;
        //------------------- Ajoute le message de retour si l'utilisateur à tenter de se connecter -------------------\\
        $html_page = $html_page .
        <<<END
                <hr>                            
                <!-- Bouton créer compte -->                             
                <form method="get" action="$urlInscription">
                    <button class="btn btn-primary btn-block" type="submit" id="btn-signup">Créer un compte </button>
                </form>                                       
            </div>
END;

        echo $html_page;
        VueHeaderFooter::afficherFooter();
    }

    public function afficherPageInscription($erreurInscription = null) {
        VueHeaderFooter::afficherHeader('login', 'compte');

        $rootUri = $this->app->request->getRootUri();
        $urlInscription = $this->app->urlFor('inscription');
        $urlLogin = $this->app->urlFor('login');

        $html_page =
        <<<END
            <div class="text-center container mt-5" style="max-width: 330px">
                <!-- Formulaire inscription -->
                <form method="post" action="$urlInscription">
                    <img class="mb-4" src="$rootUri/img/login.png" alt="" width="120" height="120">
                    <h1 class="h3 mb-3 font-weight-normal">Inscription</h1>           
                    <input type="text" name="pseudo" id ="pseudo" class="form-control" placeholder="Pseudo" required autofocus>             
                    <input type="text" name="login" id ="login" class="form-control" placeholder="Nom d'utilisateur" required>                    
                    <input type="password" name="password" id="password" class="form-control" placeholder="Mot de passe" required>                                                        
                    
                    <!-- Indique à l'utilisateur la force du mot de passe -->
                    <script src="https://cdnjs.cloudflare.com/ajax/libs/zxcvbn/4.2.0/zxcvbn.js"></script>               
                    <p class="w-100">Force du mot de passe : <meter class="w-25"  max="4" id="password-strength-meter"></meter></p>                    
                    <script src="$rootUri/js/passwordMeter.js"></script>
                    <!-- Indique à l'utilisateur la force du mot de passe -->
                
                    <button class="btn btn-success btn-primary btn-block" type="submit">S'inscrire</button>                                                                      
                </form>                                                                                          
END;

        //------------------- Ajoute le message d'erreur si problème lors de l'inscription -------------------\\
        if (isset($erreurInscription))
            $html_page = $html_page . "<div class=\"alert alert-danger\" role=\"alert\">$erreurInscription</div>";
        //------------------- Ajoute le message d'erreur si problème lors de l'inscription -------------------\\
        $html_page = $html_page .
            <<<END
                <hr>                            
                <!-- Bouton retour login -->                             
                <form method="get" action="$urlLogin">
                    <button class="btn btn-primary btn-block" type="submit" id="btn-signup">Retour vers la connexion</button>
                </form>                                       
            </div>
END;

        echo $html_page;
        VueHeaderFooter::afficherFooter();
    }

    /**
     * Affiche la page de connexion avec une information supplémentaire.
     * @param string $status Contient un message d'information (password_incorrect/login_incorrect)
     */
    public function afficherConnexionAvecInfo(string $status) {
        switch ($status) {
            case 'login_incorrect' :
                $html_code = '<div class="alert alert-danger" role="alert">Login incorrect</div>';
                break;
            case 'password_incorrect':
                $html_code = '<div class="alert alert-danger" role="alert">Mot de passe incorrect</div>';
                break;
            case 'mdpModifie':
                $html_code = '<div class="alert alert-success" role="alert">Le mot de passe a bien été modifié</div>';
                break;
        }

        self::afficherPageConnexion($html_code);
    }

    /**
     * Affiche la page de gestion de compte de l'utilisateur connecté.
     * Si aucun utilisateur est connecté affiche une erreur.
     */
    public function afficherPageGestionCompte($status = null) {
        if (!isset($_SESSION['user_connected'])) {
            VueHeaderFooter::afficherHeader('login', '404');
            echo
            <<<END
            <section class="error-container">
                <span>4</span>
                <span><span class="screen-reader-text">0</span></span>
                <span>4</span>
            </section>
END;
        }

        else {
            $urlModificationCompte = $this->app->urlFor('modificationCompte');
            $urlConnexion = $this->app->urlFor('login');
            $rootUri = $this->app->request->getRootUri();
            VueHeaderFooter::afficherHeader('login', 'compte');

            $htmlPage =
                <<<END
                <div class="text-center container mt-3" style="max-width: 330px">
                    <!-- Formulaire modification compte -->
                    <form method="post" action="$urlModificationCompte">
                        <img class="mb-4" src="$rootUri/img/login.png" alt="" width="120" height="120">                    
                        <h1 class="h3 mb-3 font-weight-normal">Modification des données</h1>    
                        <p class="font-italic">Laissez un champ vide pour ne pas modifier l'information associée</p>                
                        <input type="text" name="pseudo" class="form-control" placeholder="Nouveau pseudo" autofocus>                   
                        <input type="password" name="password" id="password" class="form-control" placeholder="Nouveau mot de passe" >
                        
                        <!-- Indique à l'utilisateur la force du mot de passe -->
                        <script src="https://cdnjs.cloudflare.com/ajax/libs/zxcvbn/4.2.0/zxcvbn.js"></script>
                        <p class="w-100">Force du mot de passe : <meter class="w-25"  max="4" id="password-strength-meter"></meter></p>                    
                        <script src="$rootUri/js/passwordMeter.js"></script>
                        <!-- Indique à l'utilisateur la force du mot de passe -->  
                                                
                        <button class="btn btn-success btn-primary btn-block" type="submit">Modifier ses informations</button>                                                                      
                    </form>                                         
END;
            //------------------- Ajoute le message -------------------\\
            if (isset($status))
                $htmlPage = $htmlPage . $status;
            //------------------- Ajoute le message -------------------\\

            $htmlPage = $htmlPage .
                <<<END
                    <hr>
                
                    <form method="get" action="$urlConnexion">
                        <button class="btn btn-primary btn-block">Se déconnecter</button>
                    </form>  
                    <form method="post" action="$urlConnexion">         
                        <input type="submit" value="Supprimer le compte" class="btn btn-danger btn-block mt-1"></input>
                    </form>
                </div>
END;
            echo $htmlPage;
        }

        VueHeaderFooter::afficherFooter();
    }

    public function afficherGestionCompteAvecInfo(string $status) {
        switch ($status) {
            case 'mdpNonModifie' :
                $html_code = '<div class="alert alert-success" role="alert">Le pseudo a bien été modifié</div>';
                break;
            default: //erreur dans ce cas
                $html_code = "<div class=\"alert alert-danger\" role=\"alert\">$status</div>";
                break;
        }
        self::afficherPageGestionCompte($html_code);
    }
}