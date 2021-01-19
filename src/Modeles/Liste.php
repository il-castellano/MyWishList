<?php



namespace wishlist\Modeles;

class Liste extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'liste';
    protected $primaryKey = 'no';
    public $timestamps = false;

    public function user() {
        return $this->belongsTo('wishlist\modele\User' , 'id');
    }

    public function item() {
        return $this->hasMany('wishlist\modele\Item', 'liste_id');
    }

    public function message() {
        return $this->hasMany('wishlist\modele\Message', 'no_liste');
    }


}