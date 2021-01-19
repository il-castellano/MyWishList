<?php


namespace mywishlist\vues;


use Slim\Slim;

class VueMessages
{

    private $params;
    private $app;

    public function __construct($p)
    {
        $this->params = $p;
        $this->app = \Slim\Slim::getInstance();
    }

    private function AfficherMessages(){
        echo
        <<<END
<hr>
<div class="list-group align-items-center align-content-center">
END;
        foreach ($this->params['messages'] as $id => $message){
            $nom = $message['nom'];
            $date = $message['date'];
            $mes = $message['message'];
            echo
            <<<END
<a class="list-group-item list-group-item-action w-50" >
<div class="d-flex w-100 justify-content-between">
      <h5 class="mb-1">De $nom</h5>
      <small>le $date</small>
    </div>
    <p class="mb-1">$mes</p>
</a>
END;
        }

        echo
        <<<END
</div>
END;

    }

    private function afficherForumulaire(){
        $route = $this->app->urlFor('ajouter_message', ['token_liste' => $this->params['tokenListe']]);
        echo

        <<<END
<div class="container mt-5 w-50 mb-5">
<form method="post" action="$route" class="">
  <div class="form-group">
    <label for="exampleFormControlInput1">Nom</label>
    <input type="text" class="form-control" id="exampleFormControlInput1" placeholder="Michel" name="nom">
  </div>
  <div class="form-group">
    <label for="exampleFormControlTextarea1">Message</label>
    <textarea class="form-control" id="exampleFormControlTextarea1" rows="3" name="message"></textarea>
  </div>
  <button type="submit" class="btn btn-primary mb-4">Envoyer</button>
</form>
</div>
END;

    }


    public function afficher($selector){
        switch ($selector){
            case 'messages':
                $this->AfficherMessages();
                break;
            case 'form':
                $this->afficherForumulaire();
        }



    }

}