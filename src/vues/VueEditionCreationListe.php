<?php


namespace mywishlist\vues;


class VueEditionCreationListe
{

    private $app;
    public static $images = ['animateur','apparthotel','bijoux','boitedenuit','bonroi','bricolage','cadeaux','champagne','cocktail','concert','connaissance','contact','diner','film','fort','gouter','grandrue','hotel_haussonville_logo','laser','musique','opera','origami','place','poirelregarder','rose'];

    public function __construct()
    {
        $this->app = \Slim\Slim::getInstance() ;
    }

    private function afficherCreation(){
        $rootUri = $this->app->request->getRootUri();
        echo
<<<END
<form class="container mt-5" style="max-width: 700px" method="post" action="">
  <div class="form-group">
    <label for="liste_name">Nom de la liste</label>
    <input type="text" class="form-control" name="liste_name" id="liste_name" aria-describedby="list_name" placeholder="Pour notre mariage...">    
  </div>
  <div class="form-group">
    <label for="liste_desc">Description</label>
    <input type="text" class="form-control" name="liste_desc" id="liste_desc" placeholder="Description">
  </div>
  
  <div class="form-group">
    <label for="date">Date d'expiration</label>
    <input size="16" class="form-control" type="date" name ="date" id="date">
    <div id="erreurDate" style="display: none" class="alert alert-danger">Veuillez rentrer une date d'expiration après aujourd'hui</div>    
  </div>
  <script src="$rootUri/js/dateChecker.js"></script>  
   
  <div class="form-check">
    <input type="checkbox" name="private" class="form-check-input" id="private">
    <label class="form-check-label" for="private">Privée</label>
  </div>
    
  <button type="submit" class="btn btn-primary mb-3 mt-3">Envoyer</button>
</form>
END;
    }

    private function afficherAjoutItem(){
        echo
        <<<END
<form class="container mt-5" style="max-width: 700px" method="post" action="">
  <div class="form-group">
    <label for="nom">Nom</label>
    <input type="text" class="form-control" name="nom" placeholder="Place de concert...">    
  </div>
  <div class="form-group">
    <label for="desc">Description</label>
    <input type="text" class="form-control" name="desc" placeholder="Description de l'item">
  </div>
  
  <div class="form-group">
    <label for="prix">Prix</label>  
    <input size="16" class="form-control" type="text" placeholder="19.99€" name ="prix">    
  </div> 

    <div class="mt-5 input-group mb-3">
      <div class="input-group-prepend">
        <label class="input-group-text" for="inputGroupSelect01">Image</label>
      </div>
      <select class="custom-select" name="image">
END;
        foreach (VueEditionCreationListe::$images as $img){
            echo '<option value="' . $img . '.jpg">' . $img . '</option>';
        }
        echo
<<<END
      </select>
    </div>
    <div class="form-group">
    <label for="url_img">Ou url de l'image :</label>  
    <input class="form-control" type="text" name ="url_image">    
  </div> 
 
  <button type="submit" class="btn btn-primary mb-3 mt-3">Ajouter l'item</button>
</form>  

END;
    }

    public function afficher($type){
        VueHeaderFooter::afficherHeader('create');

        switch ($type){
            case 'creationListe':
                $this->afficherCreation();
                break;
            case 'creationItem':
                $this->afficherAjoutItem();
                break;
        }

        VueHeaderFooter::afficherFooter();
    }
}