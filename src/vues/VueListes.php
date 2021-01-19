<?php


namespace mywishlist\vues;


use mywishlist\controleurs\ControleurMessage;
use mywishlist\models\Reservation;

class VueListes
{
    private $params;
    private $app;

    /**
     * VueListe constructor.
     * @param $params
     */
    public function __construct($params)
    {
        $this->params = $params;
        $this->app = \Slim\Slim::getInstance() ;
    }



    private function afficherToutesListes()
    {
        if ($this->params != NULL) {
            $rootUri = $this->app->request->getRootUri();
            $urlRechercheListes = $this->app->urlFor('recherche_listes');

            echo
<<<END
<script src="$rootUri/js/searchBar.js"></script>
<div class="container mt-3 mb-4">
    <form action="$urlRechercheListes" method="post">
        <div class="row d-flex justify-content-center">               
            <div>
                <input type="text" title="Laissez vide pour rechercher toutes les listes" class="form-control" placeholder="Pseudo de l'auteur" id="auteur" name="auteur">
            </div>
            <p class="my-auto pl-2 pr-1">A partir de :</p>
            <div>                         
                <input type="date" disabled required class="form-control" id="dateDebut" name="dateDebut">
            </div>
            <p class="my-auto pl-2 pr-1">jusqu'à :</p>
            <div >
                <input type="date" disabled required class="form-control" id="dateFin" name="dateFin">
            </div>
            <div class="pl-2 pr-2">
                <select onchange="desactiverElemInutile(this.value)" class="form-control search-slt" name="typeRecherche">
                    <option value="auteur">Recherche par auteur</option>
                    <option value="date">Recherche par date</option>
                </select>
            </div>
            <div class="p-0">
                <button type="submit" class="btn btn-primary wrn-btn"><i class="fas fa-search"></i></button>                            
            </div>                
        </div>
    </form>
</div>
<div class="m-3">
    <ul class="list-group">
END;
            //Si aucune listes à afficher
            if (count($this->params['listes']) == 0) {
                echo
                <<<END
                <li class="list-group-item text-center">
                    <h2>Aucune liste ne correspond aux critères de recherche</h2>
                </li>
END;

            }

            else {
                foreach ($this->params['listes'] as $key => $l) {
                    $listeUrl = $this->app->urlFor('route_liste', ['token_liste' => $l['liste']['token']]);
                    $titre = $l['liste']['titre'];
                    $desc = $l['liste']['description'];
                    $privee = $l['liste']['private'] == 1 ? 'Privée' : '';
                    $createur_pseudo = $l['liste']['createur_pseudo'] != null ? $l['liste']['createur_pseudo'] : 'Anonyme' ;
                    $nb_items = $l['nb'];
                    $expiree = date_format(date_create($l['liste']['expiration']), 'd/m/Y');

                    echo
                    <<<END
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                         <a href=" $listeUrl">$titre : $desc par <p class="font-weight-bold custom-control-inline mb-0">$createur_pseudo</p></a>
                         <div>
                             <span class="badge badge-info badge-pill">$privee</span>
                             <span class ="badge badge-success badge-pill">$expiree</span>               
                             <span class="badge badge-primary badge-pill">$nb_items items</span>
                         </div>           
                    </li>
END;
                }
            }
            echo
<<<END
        </ul>
</div>
END;

        }
    }

        private function afficherAllItems(){
        if ($this->params != NULL) {
            $titreListe = $this->params['titreListe'];
            $nomCreateur = $this->params['nomCreateur'];
            $rootUri = $this->app->request->getRootUri();

            echo
<<<END
<div class="text-center">
<p class="h1">$titreListe de $nomCreateur</p>
<button id="partager" class="btn btn-primary">Partager la liste <i class="fas fa-share-square"></i></button>
<script src="$rootUri/js/copyURLToClipboard.js"></script>
</div>

<div class="row row-cols-1 row-cols-md-3 ml-5 mr-5">
END;

            $rootUri = $this->app->urlFor('default', []);

            foreach ($this->params['items'] as $key => $items) {
                $itemUrl = $this->app->urlFor('route_item', ['token_liste' => $this->params['token_liste'], 'id_item' => $items['id']]);
                $num = $items['id'];
                $titre = $items['nom'];
                $desc = $items['descr'];
                if (strlen($desc) > 60){
                    $desc = substr_replace($desc,'..',60);
                }

                $routeImg = $rootUri . 'img/' . 'defaut.jpg';
                if(isset($items['url']) && $items['url'] != ''){
                    $routeImg = $items['url'];
                }
                else if (isset($items['img'])) {
                    $img = $items['img'];
                    $routeImg = $rootUri . 'img/' . $img;
                }

                echo
<<<END
<div class="col mb-4">
<div class="card h-100 m-3" style="width: 18rem;">
  <img class="card-img-top m-auto" src="$routeImg" alt=$desc style="width: 17.9rem;height: 180px ">  
      <div class="card-body">
        <h5 class="card-title">$titre</h5>
        <p class="card-text">$desc.</p>        
END;
                if ($this->params['creator']){
                    $tokenListe = $this->params['token_liste'];
                    $urlSupp = $this->app->urlFor('supprimer_item',['token_liste' => $tokenListe,'id_item' => $items['id']]);
                    $reserve = Reservation::where('tokenListe', '=', $tokenListe)->where('idItem', '=', $items['id'])->first();

                    //date expirée
                    if ($this->params['estExpiree']) {
                        if (isset($reserve)) {
                            $participant = $reserve->nomParticipant;
                            if (!isset($participant) || $participant == '')
                                $participant = 'Anonyme';
                            $message = $reserve->message;

                            $reserve = " btn-success\">Réservé par $participant<br>Message : $message";
                        }
                        else
                            $reserve = ' btn-warning">Non réservé';
                    }
                    else {
                        if (isset($reserve))
                            $reserve = ' btn-success">Réservé';
                        else
                            $reserve = ' btn-warning">Non réservé';

                        echo
                        <<<END
<a href="$urlSupp" class="btn btn-danger">Supprimer</a>
END;

                    }

                    echo
                    <<<END
        <p class="btn mb-0 mr-3$reserve</p>
END;
                }
                else
                    echo
                    <<<END
        <a href="$itemUrl" class="btn btn-primary">Réserver</a>
END;

        echo <<<END
      </div>
  </div>
</div>
END;
            }

            if ($this->params['creator']){
                $urlAdd = $this->app->urlFor('form_ajout_item',['token_liste' => $this->params['token_liste']]);
                $urlImg = $rootUri . 'img/' . 'defaut.jpg';
                echo
<<<END
<div class="col mb-4">
<div class="card h-100 m-3" style="width: 18rem;">
  <img class="card-img-top m-auto" src="$urlImg" alt='Ajouter item' style="width: 17.9rem;height: 180px ">  

      <div class="card-body">
        <h5 class="card-title">Ajouter un item.</h5>
        <p class="card-text">Ajouter un item à votre liste.</p>
        <a href="$urlAdd" class="btn btn-primary">Ajouter</a>
      </div>
  </div>
</div>
END;

            }

            echo
<<<END
</div>
END;

        }

        ControleurMessage::getMessages($this->params['token_liste']);
    }

    private function afficherItem(){
        if ($this->params['item'] != NULL) {
            $rootUri = $this->app->urlFor('default', []);
            $item = $this->params['item'];
            $titre = $item['nom'];
            $desc = $item['descr'];
            $tarif = $item['tarif'];

            $routeImg = $rootUri . 'img/' . 'defaut.jpg';
            if (isset($item['img'])) {
                $img = $item['img'];
                $routeImg = $rootUri . 'img/' . $img;
            }

            $disabled = '';
            $button = 'Réserver';
            $nom = 'Michel';
            $message = '';
            $style = '';
            if (isset($_COOKIE['created'])) {
                $created = unserialize($_COOKIE['created']);
                if (in_array($this->params['token_list'], $created)) {
                    $disabled = 'disabled';
                    $button = 'Réservé';
                }
            }

            //var_dump($this->params['reserve']['message']);
            if($this->params['reserve']){
                $disabled = 'disabled';
                $button = 'Réservé';
            }

            $url_reserv = $this->app->urlFor('reserver_item', ['token_liste' => $this->params['item']['tokenListe'], 'id_item' => $this->params['item']['id']]);;
            if (isset($_COOKIE['reserves'])) {
                $reserves = unserialize($_COOKIE['reserves']);
                //var_dump($reserves);
                if (in_array($this->params['reservation']['tokenReserv'], $reserves)) {
                    $disabled = '';
                    $button = 'Annuler';
                    $nom = $this->params['reservation']['nomParticipant'];
                    $message = $this->params['reservation']['message'];
                    $style = 'style="background-color: #bd2130"';
                    $url_reserv = $this->app->urlFor('annuler_reservation', ['token_liste' => $this->params['item']['tokenListe'], 'id_item' => $this->params['item']['id']]);;
                }
            }


            echo
<<<END
<div class="card m-lg-5 ">
  <img src="$routeImg" class="card-img-top align-self-center" alt=$desc style="height:50%;width: 50%">
  <div class="card-body">
    <h5 class="card-title">$titre</h5>
    <h4>$tarif €</h4>
    <p class="card-text">$desc</p>
    <form class="mt-5" style="max-width: 200px" method="post" action="$url_reserv">
        <div class="form-group">
            <label for="nom">Votre nom</label>
            <input type="text" class="form-control" name="nom" aria-describedby="nom" placeholder="$nom" $disabled>
              <label for="nom">Votre message</label>
            <input type="text" class="form-control" name="message" aria-describedby="message" placeholder="$message" $disabled>
         </div>
        <button type="submit" class="btn btn-primary mb-3 mt-3" $style $disabled>$button</button>
    </form>
  </div>
</div>
END;
        }


    }

    /**
     * Permet d'afficher la vue.
     * @param $type String  type d'affichage :
     *  - 'listes' : affiche toutes les listes.
     *  - 'liste' : affiche une seule liste.
     *  - 'item' : affiche un seul item d'une liste.
     */
    public function afficher($type){

        VueHeaderFooter::afficherHeader("wishlist");

        //affiche le contenu
        switch ($type){
            case 'listes':
                $this->afficherToutesListes();
                break;
            case 'liste':
                $this->afficherAllItems();
                break;
            case 'item':
                $this->afficherItem();
                break;
        }

        //affiche le footer
        VueHeaderFooter::afficherFooter();
    }
}